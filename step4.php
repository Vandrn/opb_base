<?php
require_once 'connection.php';
$area = 3;
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

//Preguntas activas: 5,6,9,10,11,12,13,14
$PREG_05 = $_POST['PREG_05'] ?? null;
$PREG_06 = $_POST['PREG_06'] ?? null;
$PREG_09 = $_POST['PREG_09'] ?? null;
$PREG_10 = $_POST['PREG_10'] ?? null;
$PREG_11 = $_POST['PREG_11'] ?? null;
$PREG_12 = $_POST['PREG_12'] ?? null;
$PREG_13 = $_POST['PREG_13'] ?? null;
$PREG_14 = $_POST['PREG_14'] ?? null;
$OBS_02 = $_POST['OBS_02'] ?? null;
//$PREG_02 = isset($_POST['PREG_02']) && $_POST['PREG_02'] !== '' ? $_POST['PREG_02'] : null;
//$PREG_03 = isset($_POST['PREG_03']) && $_POST['PREG_03'] !== '' ? $_POST['PREG_03'] : null;

if (is_null($bv_pais_tienda_get)) {
    // Prepare and bind statement
    $stmt = $conn2->prepare("UPDATE fact_visitas SET PREG_05 = ?, PREG_06 = ?, PREG_09 = ?, PREG_10 = ? , PREG_11 = ?, 
PREG_12 = ?, PREG_13 = ?, PREG_14 = ?, OBS_02 = ?  WHERE ID_VISITA = ?");
    $stmt->bind_param(
        "sssssssssi",
        $PREG_05,
        $PREG_06,
        $PREG_09,
        $PREG_10,
        $PREG_11,
        $PREG_12,
        $PREG_13,
        $PREG_14,
        $OBS_02,
        $id_registered_visit
    );

    // Execute statement
    $success = $stmt->execute();

    if ($success) {
        $insertMessage = "Información de Evaluación: Experiencia de Servicio ingresada con éxito.";
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
          ORDER BY FIELD(id_pregunta,15, 99, 107, 58, 59, 90, 60, 91, 108, 109, 110, 62, 69, 106, 70, 71, 21, 102, 103, 23, 24, 25, 26, 105, 63, 64, 65, 66, 67, 92, 93, 94, 95)";
//va antes de poner el order

$result = mysqli_query($conn2, $query);

//Obtain questions from Area = 1 and Type "Si/No = 0"
$query2 = "SELECT id_pregunta, pregunta 
            FROM `dim_preguntas` 
            WHERE ID_AREA = $area
            AND SI_NO = 0 
            and INCLUIR = 1 
            and $format = 1
            ORDER BY FIELD(id_pregunta,15, 99, 107, 58, 59, 90, 60, 91, 108, 109, 110, 62, 69, 106, 70, 71, 21, 102, 103, 23, 24, 25, 26, 105, 63, 64, 65, 66, 67, 92, 93, 94, 95)";
$result2 = mysqli_query($conn2, $query2);

//Obtain observation label
$query3 = "SELECT OBSERVACION FROM `dim_observaciones` WHERE ID_AREA = $area";
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
        <form id="form1" action="step5.php" method="post">
            <h2>One Playbook - Visita a Tiendas</h2>
            <input type="hidden" name="id_registered_visit" value="<?php echo $id_registered_visit; ?>">
            <input type="hidden" name="formato" value="<?php echo $format; ?>">
            <input type="hidden" name="bv_pais_tienda" value="<?php echo $bv_pais_tienda; ?>">
            <p class="required-fields">*Valores Obligatorios<br>Te encuentras realizando la vista en la tienda <?php echo $bv_pais_tienda; ?>.<br>Visita con ID <?php echo $id_registered_visit; ?>.</p>
            <div class="intro-text">
                <H3>SECCIÓN D - Evaluación: Exhibición y Planograma.</H3>
                <?php
                // Check if the query was executed successfully and has at least one row
                if ($result && mysqli_num_rows($result) > 0) {
                    // Start HTML table
                    echo '<label class="label-with-border">Preguntas "Si" o "No":<span class="required-field">*</span></label>';
                    echo '<table>';
                    echo '<thead><tr><th>PREGUNTA</th><th>RESPUESTA</th></tr></thead>';
                    echo '<tbody>';

                    // Loop through query results
                    $rowNumber = 0;
                    while ($row = mysqli_fetch_assoc($result)) {
                        // Generate table rows
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

                    // End HTML table
                    echo '</tbody></table>';
                }
                ?>

                <?php
                // Check if the query was executed successfully and has at least one row
                if ($result2 && mysqli_num_rows($result2) > 0) {
                    // Start HTML table
                    echo '<label class="label-with-border">Evaluación Likert (1:Bajo, 5:Alto)<span class="required-field">*</span></label>';
                    echo '<table>';
                    echo '<thead><tr><th>PREGUNTA</th><th>RESPUESTA</th></tr></thead>';
                    echo '<tbody>';

                    // Loop through query results
                    $rowNumber = 0;
                    while ($row2 = mysqli_fetch_assoc($result2)) {
                        // Generate table rows
                        $id_pregunta2 = str_pad($row2['id_pregunta'], 2, '0', STR_PAD_LEFT);
                        $pregunta2 = $row2['pregunta'];
                        $rowNumber++;

                        echo '<tr id="PREG_' . $id_pregunta2 . '" class="' . ($rowNumber % 2 == 0 ? 'even' : 'odd') . '">';
                        echo '<td>' . $pregunta2 . '</td>';
                        echo '<td class="response-cell2">';
                        echo '<label><input type="radio" name="PREG_' . $id_pregunta2 . '" value="1" required>1</label>';
                        echo '<label><input type="radio" name="PREG_' . $id_pregunta2 . '" value="2">2</label>';
                        echo '<label><input type="radio" name="PREG_' . $id_pregunta2 . '" value="3">3</label>';
                        echo '<label><input type="radio" name="PREG_' . $id_pregunta2 . '" value="4">4</label>';
                        echo '<label><input type="radio" name="PREG_' . $id_pregunta2 . '" value="5">5</label>';
                        echo '</td>';
                        echo '</tr>';
                    }

                    // End HTML table
                    echo '</tbody></table>';
                }
                ?>
                <?php
                // Check if the query was executed successfully and has at least one row
                if ($result3 && mysqli_num_rows($result3) > 0) {
                    // Fetch the row from $result3
                    $row3 = mysqli_fetch_assoc($result3);

                    // Get the value of OBSERVACION column
                    $observacion = $row3['OBSERVACION'];
                    $id_area = str_pad($area, 2, '0', STR_PAD_LEFT);

                    // Start HTML table
                    echo '<table>';
                    echo '<thead class="label-with-border"><tr><th>' . $observacion . '</th></tr></thead>';
                    echo '<tbody>';

                    // Generate table row for OBS_
                    echo '<tr>';
                    echo '<td><textarea name="OBS_' . $id_area . '" rows="3"></textarea></td>';
                    echo '</tr>';

                    // End HTML table
                    echo '</tbody></table>';
                }
                ?>
                <p></p>
                <div>
                    <input type="submit" value="Continuar"<br />
                    <input type="button" class="back-button" onclick="redirigirPagina('step3.php?id_registered_visit=<?php echo $id_registered_visit; ?>&format=<?php echo $format; ?>&bv_pais_tienda=<?php echo $bv_pais_tienda; ?>')" value="Regresar" />
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
                    timer: 2000 // Oculta automáticamente después de 3 segundos
                });
            }
        });
    </script>


</body>

</html>