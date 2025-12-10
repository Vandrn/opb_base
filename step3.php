<?php
require_once 'connection.php';
$area = 2;
//Definición de Variable $format
$format_get = $_GET['format'] ?? null;
$format_post = $_POST['formato'] ?? null;
if (is_null($format_get)) {
    $format = $format_post;
} else {
    $format = $format_get;
}

//Definición de variable $bv_pais_tienda
$bv_pais_tienda_get = $_GET['bv_pais_tienda'] ?? null;
$bv_pais_tienda_post = $_POST['bv_pais_tienda'] ?? null;
//BV_PAIS_TIENDA
if (is_null($bv_pais_tienda_get)) {
    $bv_pais_tienda = $bv_pais_tienda_post;
} else {
    $bv_pais_tienda = $bv_pais_tienda_get;
}

//Definición de variable $id_registered_visit
$id_registered_visit_get = $_GET['id_registered_visit'] ?? null;
$id_registered_visit_post = $_POST['id_registered_visit'] ?? null;
// Get the ID of the last inserted record
if (is_null($id_registered_visit_get)) {
    $id_registered_visit = $id_registered_visit_post;
} else {
    $id_registered_visit = $id_registered_visit_get;
}

//$PREG_01 = isset($_POST['PREG_01']) && $_POST['PREG_01'] !== '' ? $_POST['PREG_01'] : null;
$PREG_01 = $_POST['PREG_01'] ?? null;
$PREG_03 = $_POST['PREG_03'] ?? null;
$OBS_01 = $_POST['OBS_01'] ?? null;
//$PREG_02 = isset($_POST['PREG_02']) && $_POST['PREG_02'] !== '' ? $_POST['PREG_02'] : null;
//$PREG_03 = isset($_POST['PREG_03']) && $_POST['PREG_03'] !== '' ? $_POST['PREG_03'] : null;

if (is_null($bv_pais_tienda_get)) {
    // Prepare and bind statement
    $stmt = $conn2->prepare("UPDATE fact_visitas SET PREG_01 = ?, PREG_03 = ?, OBS_01 = ? WHERE ID_VISITA = ?");
    $stmt->bind_param("sssi", $PREG_01, $PREG_03, $OBS_01, $id_registered_visit);

    // Execute statement
    $success = $stmt->execute();

    if ($success) {
        $insertMessage = "Información de Evaluación: Código de vestimenta ADOCKER Ingresada Correctamente.";
    } else {
        $insertMessage = "Hubo un error al ingresar la información: " . $stmt->error;
    }
}
// Obtener preguntas de tipo "Sí/No" que son válidas para el formato en $format
$query = "SELECT id_pregunta, pregunta
          FROM dim_preguntas
          WHERE ID_AREA = $area
          AND SI_NO = 1
          AND INCLUIR = 1
          and $format = 1
          ORDER BY FIELD(id_pregunta, 5,6,9,10,11,12,13,14,111,112)";

$result = mysqli_query($conn2, $query);

// Obtener preguntas de tipo "Si/No = 0" que son válidas para el formato en $format
$query2 = "SELECT id_pregunta, pregunta
           FROM dim_preguntas
           WHERE ID_AREA = $area
           AND SI_NO = 0
           AND INCLUIR = 1
           and $format = 1
           ORDER BY FIELD(id_pregunta, 5,6,9,10,11,12,13,14,111,112)";

$result2 = mysqli_query($conn2, $query2);

// Obtener observación
$query3 = "SELECT OBSERVACION FROM dim_observaciones WHERE ID_AREA = $area";
$result3 = mysqli_query($conn2, $query3);

$conn->close();
$conn2->close();
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
    <script src="listen.js"></script>
</head>

<body>
    <div class="container">
        <form id="form1" action="step4.php" method="post">
            <h2>One Playbook - Visita a Tiendas</h2>
            <input type="hidden" name="id_registered_visit" value="<?php echo $id_registered_visit; ?>">
            <input type="hidden" name="formato" value="<?php echo $format; ?>">
            <input type="hidden" name="bv_pais_tienda" value="<?php echo $bv_pais_tienda; ?>">
            <div class="intro-text">
                <h3>SECCIÓN C - Evaluación: Experiencia de Servicio.</h3>
                <p class="required-fields">*Valores Obligatorios<br>Te encuentras realizando la vista en la tienda <?php echo $bv_pais_tienda; ?>.<br>Visita con ID <?php echo $id_registered_visit; ?>.</p>
                <?php if ($result && mysqli_num_rows($result) > 0) : ?>
                    <label class="label-with-border">Preguntas "Si" o "No":<span class="required-field">*</span></label>
                    <table>
                        <thead>
                            <tr>
                                <th>PREGUNTA</th>
                                <th>RESPUESTA</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $rowNumber = 0;
                            while ($row = mysqli_fetch_assoc($result)) {
                                $id_pregunta = str_pad($row['id_pregunta'], 2, '0', STR_PAD_LEFT);
                                $pregunta = $row['pregunta'];
                                $rowNumber++;

                                echo '<tr id="PREG_' . $id_pregunta . '" class="' . ($rowNumber % 2 == 0 ? 'even' : 'odd') . '">';
                                echo '<td>' . $pregunta . '</td>';
                                echo '<td class="response-cell">';
                                echo '<label><input type="radio" name="PREG_' . $id_pregunta . '" value="Si" required> Si</label>';
                                echo '<label><input type="radio" name="PREG_' . $id_pregunta . '" value="No"> No</label>';
                                echo '</td>';
                                echo '</tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                <?php endif; ?>

                <?php if ($result2 && mysqli_num_rows($result2) > 0) : ?>
                    <label class="label-with-border">Evaluación Likert (1:Bajo, 5:Alto)<span class="required-field">*</span></label>
                    <table>
                        <thead>
                            <tr>
                                <th>PREGUNTA</th>
                                <th>RESPUESTA</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $rowNumber = 0;
                            while ($row2 = mysqli_fetch_assoc($result2)) {
                                $id_pregunta2 = str_pad($row2['id_pregunta'], 2, '0', STR_PAD_LEFT);
                                $pregunta2 = $row2['pregunta'];
                                $rowNumber++;

                                echo '<tr id="PREG_' . $id_pregunta2 . '" class="' . ($rowNumber % 2 == 0 ? 'even' : 'odd') . '">';
                                echo '<td>' . $pregunta2 . '</td>';
                                echo '<td class="response-cell2">';
                                for ($i = 1; $i <= 5; $i++) {
                                    echo '<label><input type="radio" name="PREG_' . $id_pregunta2 . '" value="' . $i . '" required>' . $i . '</label>';
                                }
                                echo '</td>';
                                echo '</tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                <?php endif; ?>

                <?php if ($result3 && mysqli_num_rows($result3) > 0) : ?>
                    <?php
                    $row3 = mysqli_fetch_assoc($result3);
                    $observacion = $row3['OBSERVACION'];
                    $id_area = str_pad($area, 2, '0', STR_PAD_LEFT);
                    ?>

                    <table>
                        <thead class="label-with-border">
                            <tr>
                                <th><?= $observacion; ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><textarea name="OBS_<?= $id_area; ?>" rows="3"></textarea></td>
                            </tr>
                        </tbody>
                    </table>
                <?php endif; ?>

                <p></p>
                <div>
                    <input type="submit" value="Continuar"><br />
                    <input type="button" class="back-button" onclick="redirigirPagina('step2.php?id_registered_visit=<?php echo $id_registered_visit; ?>&format=<?php echo $format; ?>&bv_pais_tienda=<?php echo $bv_pais_tienda; ?>')" value="Regresar" />
                </div>
            </div>
        </form>
    </div>

    <script>
        // script para mostrar un SweetAlert en lugar del elemento HTML de éxito
        document.addEventListener("DOMContentLoaded", function() {
            var successMessage = "<?php echo isset($insertMessage) ? $insertMessage : ''; ?>";

            if (successMessage) {
                Swal.fire({
                    icon: 'success',
                    title: 'Éxito',
                    text: successMessage,
                    showConfirmButton: false,
                    timer: 2000 // Oculta automáticamente después de 2 segundos
                });
            }
        });
    </script>
</body>

</html>