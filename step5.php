<?php
require_once 'connection.php';
$area = 4;
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
//Preguntas activas: //15, //58, //59, //60, //61, //62,69,70,71,99,//21,102,103, //23, //24, //25, //26, //63, //64, //65, //66, //67
$PREG_15 = $_POST['PREG_15'] ?? null; //1
$PREG_58 = $_POST['PREG_58'] ?? null; //2
$PREG_59 = $_POST['PREG_59'] ?? null; //3
$PREG_60 = $_POST['PREG_60'] ?? null; //4
$PREG_61 = $_POST['PREG_61'] ?? null; //5
$PREG_62 = $_POST['PREG_62'] ?? null; //6
$PREG_69 = $_POST['PREG_69'] ?? null; //7
$PREG_70 = $_POST['PREG_70'] ?? null; //8
$PREG_71 = $_POST['PREG_71'] ?? null; //9
$PREG_21 = $_POST['PREG_21'] ?? null; //10
$PREG_23 = $_POST['PREG_23'] ?? null; //11
$PREG_24 = $_POST['PREG_24'] ?? null; //12
$PREG_25 = $_POST['PREG_25'] ?? null; //13
$PREG_26 = $_POST['PREG_26'] ?? null; //14
$PREG_63 = $_POST['PREG_63'] ?? null; //15
$PREG_64 = $_POST['PREG_64'] ?? null; //hasta aqui estaba esto antes 16
$PREG_65 = $_POST['PREG_65'] ?? null; //17
$PREG_66 = $_POST['PREG_66'] ?? null; //18
$PREG_67 = $_POST['PREG_67'] ?? null; //19
$PREG_90 = $_POST['PREG_90'] ?? null; //20
$PREG_91 = $_POST['PREG_91'] ?? null; //21
$PREG_92 = $_POST['PREG_92'] ?? null; //22
$PREG_93 = $_POST['PREG_93'] ?? null; //23
$PREG_94 = $_POST['PREG_94'] ?? null; //24
$PREG_95 = $_POST['PREG_95'] ?? null; //25
$PREG_99 = $_POST['PREG_99'] ?? null; //26
$PREG_102 = $_POST['PREG_102'] ?? null; //27
$PREG_103 = $_POST['PREG_103'] ?? null; //28
$PREG_105 = $_POST['PREG_105'] ?? null; //29
$PREG_106 = $_POST['PREG_106'] ?? null; //30
$PREG_107 = $_POST['PREG_107'] ?? null; //30
$OBS_03 = $_POST['OBS_03'] ?? null; //31 
//$PREG_02 = isset($_POST['PREG_02']) && $_POST['PREG_02'] !== '' ? $_POST['PREG_02'] : null;
//$PREG_03 = isset($_POST['PREG_03']) && $_POST['PREG_03'] !== '' ? $_POST['PREG_03'] : null;

if (is_null($bv_pais_tienda_get)) {
    // Prepare and bind statement
    //SE AGREGAN LAS PREGUNTAS FALTANTES DEL POST ACA |
    $stmt = $conn2->prepare("UPDATE fact_visitas SET PREG_15 = ?,PREG_58 = ?,PREG_59 = ?,PREG_60 = ?,PREG_61 = ?,
PREG_62 = ?, PREG_69 = ?,PREG_70 = ?,PREG_71 = ?,PREG_21 = ?,PREG_23 = ?,PREG_24 = ?,PREG_25 = ?,PREG_26 = ?,
PREG_63 = ?, PREG_64 = ?,PREG_65=?,PREG_66=?,PREG_67=?,PREG_90=?,PREG_91=?,PREG_92=?,PREG_93=?,PREG_94=?,PREG_95=?,PREG_99=?,PREG_102=?,PREG_103=?,PREG_105=?,PREG_106=?,PREG_107=?,OBS_03 = ? WHERE ID_VISITA = ?");
    $stmt->bind_param(
        "ssssssssssssssssssssssssssssssssi", //aqui se agregan las s como pregunta la i como id de registro!!
        $PREG_15,
        $PREG_58,
        $PREG_59,
        $PREG_60,
        $PREG_61,
        $PREG_62,
        $PREG_69,
        $PREG_70,
        $PREG_71,
        $PREG_21,
        $PREG_23,
        $PREG_24,
        $PREG_25,
        $PREG_26,
        $PREG_63,
        $PREG_64,
        $PREG_65,
        $PREG_66,
        $PREG_67,
        $PREG_90,
        $PREG_91,
        $PREG_92,
        $PREG_93,
        $PREG_94,
        $PREG_95,
        $PREG_99,
        $PREG_102,
        $PREG_103,
        $PREG_105,
        $PREG_106,
        $PREG_107,
        $OBS_03,
        $id_registered_visit
    );
    // Execute statement
    $success = $stmt->execute();
    if ($success) {
        $insertMessage = "Información de Evaluación: Exhibición y Planograma ingresada con éxito";
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
          ORDER BY FIELD(id_pregunta, 27, 28, 29, 30, 31, 72, 73, 74, 35, 76, 77, 96, 78, 79, 80, 97, 81, 82, 83, 98, 84, 85, 86, 101, 36, 37, 39, 40)"; //va antes de poner el order
$result = mysqli_query($conn2, $query);

//Obtain questions from Area = 1 and Type "Si/No = 0"
$query2 = "SELECT id_pregunta, pregunta 
            FROM `dim_preguntas` 
            WHERE ID_AREA = $area
            AND SI_NO = 0 
            and INCLUIR = 1 
            and $format = 1
            ORDER BY FIELD(id_pregunta, 27, 28, 29, 30, 31, 72, 73, 74, 35, 76, 77, 96, 78, 79, 80, 97, 81, 82, 83, 98, 84, 85, 86, 101, 36, 37, 39, 40)";
$result2 = mysqli_query($conn2, $query2); //condicionformato va antes del order para validar

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
        <form id="form1" action="step6.php" method="post">
            <h2>One Playbook - Visita a Tiendas</h2>
            <input type="hidden" name="id_registered_visit" value="<?php echo $id_registered_visit; ?>">
            <input type="hidden" name="formato" value="<?php echo $format; ?>">
            <input type="hidden" name="bv_pais_tienda" value="<?php echo $bv_pais_tienda; ?>">
            <p class="required-fields">*Valores Obligatorios<br>Te encuentras realizando la vista en la tienda <?php echo $bv_pais_tienda; ?>.<br>Visita con ID <?php echo $id_registered_visit; ?>.</p>
            <div class="intro-text">
                <H3>SECCIÓN E - Evaluación: Experiencia Sensorial | Visual Merchandising.</H3>
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
                    <input type="submit" value="Continuar"><br />
                    <input type="button" class="back-button" onclick="redirigirPagina('step4.php?id_registered_visit=<?php echo $id_registered_visit; ?>&format=<?php echo $format; ?>&bv_pais_tienda=<?php echo $bv_pais_tienda; ?>')" value="Regresar" />
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