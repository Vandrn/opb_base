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
ob_start();
$_GET['ID'] = $id_registered_visit;
include 'generate_json.php'; // lo ejecuta internamente
$generateOutput = ob_get_clean(); // captura lo que imprime


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
