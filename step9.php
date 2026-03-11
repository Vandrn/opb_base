<?php
require_once 'connection.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// =========================
// 1️⃣ Variables iniciales
// =========================
$format = $_GET['format'] ?? $_POST['formato'] ?? null;
$BV_PAIS_TIENDA = $_GET['bv_pais_tienda'] ?? $_POST['bv_pais_tienda'] ?? null;
$id_registered_visit = $_GET['id_registered_visit'] ?? $_POST['id_registered_visit'] ?? null;

// =========================
// 2️⃣ Terminar ingreso de datos
// =========================
$survey_end = date('Y-m-d H:i:s') . substr((string)microtime(), 1, 4);
$dateTime = new DateTime($survey_end);
$dateTime->sub(new DateInterval('PT6H'));
$survey_end = $dateTime->format('Y-m-d H:i:s');

$PREG_55 = $_POST['PREG_55'] ?? null;
$PREG_56 = $_POST['PREG_56'] ?? null;
$PREG_57 = $_POST['PREG_57'] ?? null;
$OBS_07  = $_POST['OBS_07']  ?? null;

$stmt = $conn2->prepare("
    UPDATE fact_visitas 
    SET PREG_55 = ?, PREG_56 = ?, PREG_57 = ?, OBS_07 = ?, END_DATETIME = ? 
    WHERE ID_VISITA = ?
");
$stmt->bind_param("sssssi", $PREG_55, $PREG_56, $PREG_57, $OBS_07, $survey_end, $id_registered_visit);
$success = $stmt->execute();
$insertMessage = $success 
    ? "Información de Evaluación: ¡ADOCKERS A BORDO! ingresada con éxito."
    : "Hubo un error al ingresar la información: " . $stmt->error;

// =========================
// 3️⃣ Ejecutar generate_json.php internamente (sin salir de la vista)
// =========================
if (!$success) {
    $generateOutput = "❌ No se generó JSON porque falló el guardado: " . $stmt->error;
} else {

    $conn2->begin_transaction();

    try {
        // Bloquea la fila
        $check = $conn2->prepare("
            SELECT JSON_GENERATED
            FROM fact_visitas
            WHERE ID_VISITA = ?
            FOR UPDATE
        ");
        $check->bind_param("i", $id_registered_visit);
        $check->execute();
        $res = $check->get_result();

        if ($res->num_rows === 0) {
            throw new Exception("No existe la visita con ID $id_registered_visit.");
        }

        $row = $res->fetch_assoc();
        $status = (int)($row['JSON_GENERATED'] ?? 0);

        if ($status === 1) {
            $generateOutput = "⚠️ Ya se había generado el JSON para esta visita. No se generó nuevamente.";
            $conn2->commit();

        } elseif ($status === 2) {
            $generateOutput = "⏳ Esta visita ya está en proceso de generación de JSON. Intentá de nuevo en unos segundos.";
            $conn2->commit();

        } else {
            // 0 => lo marcamos como "generando" (2) para bloquear doble click
            $lock = $conn2->prepare("
                UPDATE fact_visitas
                SET JSON_GENERATED = 2,
                    JSON_GENERATED_AT = NOW()
                WHERE ID_VISITA = ?
            ");
            $lock->bind_param("i", $id_registered_visit);
            if (!$lock->execute()) {
                throw new Exception("No se pudo bloquear generación: " . $lock->error);
            }

            $conn2->commit();

            // Generar JSON
            ob_start();
            $_GET['ID'] = $id_registered_visit;
            include 'generate_json.php';
            $generateOutput = ob_get_clean();

            // Si llegó aquí, marcamos como generado (1)
            $done = $conn2->prepare("
                UPDATE fact_visitas
                SET JSON_GENERATED = 1,
                    JSON_GENERATED_AT = NOW()
                WHERE ID_VISITA = ?
            ");
            $done->bind_param("i", $id_registered_visit);
            $done->execute();
        }

    } catch (Throwable $e) {
        // 1) Intentar rollback SOLO si la conexión sigue viva
        try {
            if (isset($conn2) && $conn2 instanceof mysqli) {
                // ping() devuelve false si está cerrada/caída
                if (@$conn2->ping()) {
                    @$conn2->rollback();
                }
            }
        } catch (Throwable $ignored) {
            // no hacemos nada, solo evitamos fatal error
        }
    
        // 2) Intentar "desbloquear" SOLO si la conexión sigue viva
        try {
            if (isset($conn2) && $conn2 instanceof mysqli && @$conn2->ping()) {
                $unlock = $conn2->prepare("
                    UPDATE fact_visitas
                    SET JSON_GENERATED = 0
                    WHERE ID_VISITA = ?
                ");
                if ($unlock) {
                    $unlock->bind_param("i", $id_registered_visit);
                    $unlock->execute();
                }
            }
        } catch (Throwable $ignored) {
            // igual, evitamos fatal
        }
    
        $generateOutput = "❌ Error en control anti-duplicado: " . $e->getMessage();
    }

}


//cerrar conexiones

?>
<!DOCTYPE html>
<html>
<head>
    <link rel="icon" type="images/x-icon" href="../images/adoc-favicon.ico" />
    <title>One Playbook - Visita a Tiendas</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
</head>

<body>
    <div class="container">
        <h2>One Playbook - Visita a Tiendas</h2>
        <div class="intro-text">
            <h3>
                ¡Muchas gracias por la evaluación en la tienda 
                <?php echo htmlspecialchars($BV_PAIS_TIENDA); ?>!<br>
                Visita con ID <?php echo htmlspecialchars($id_registered_visit); ?>.
            </h3>

            <!-- 🔹 Mensaje del guardado -->
            <p class="msg-guardado">
                <?php echo htmlspecialchars($insertMessage); ?>
            </p>

            <!-- 🔹 Resultado del JSON -->
            <div class="json-result">
                <?php echo nl2br($generateOutput); ?>
            </div>

            <br>
            <input type="button" class="back-button" onclick="window.location.href='index.php';" value="Ingresar otra Visita" />
        </div>
    </div>

    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const msg = "<?php echo addslashes($insertMessage); ?>";
        if (msg.includes("éxito") || msg.includes("Éxito")) {
            Swal.fire({
                icon: 'success',
                title: 'Visita guardada correctamente',
                text: msg,
                showConfirmButton: false,
                timer: 1800
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: msg
            });
        }
    });
    </script>
</body>
</html>
