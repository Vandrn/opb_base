<?php
require_once 'connection.php';
$area = 1;

$survey_start = date('Y-m-d H:i:s') . substr((string)microtime(), 1, 4);

// Create a DateTime object from the survey start time
$dateTime = new DateTime($survey_start);

// Subtract 6 hours
$dateTime->sub(new DateInterval('PT6H'));

// Update the survey start variable with the new value
$survey_start = $dateTime->format('Y-m-d H:i:s');

// Get form data
$ip_address = $_SERVER['REMOTE_ADDR'];
$country = $_POST['country'];
// Map full country names to abbreviations
$countryAbbreviations = array(
    "El Salvador" => "SV",
    "Guatemala" => "GT",
    "Nicaragua" => "NI",
    "Honduras" => "HN",
    "Costa Rica" => "CR",
    "Panama" => "PA"
);
$bv_pais = strtoupper($country);

// Get the country abbreviation
$countryAbbreviation = $countryAbbreviations[$country];
$format_get = $_GET['format'] ?? null;
$format_post = $_POST['formato'] ?? null;
//Format
if (is_null($format_get)) {
    $format = $format_post;
} else {
    $format = $format_get;
}

$email = $_POST['email'];

// Decodificar el valor del combo de tiendas (JSON del get_store_list.php)
$storeData = json_decode($_POST['stores'], true);
if (json_last_error() === JSON_ERROR_NONE && isset($storeData['nombre'])) {
    $store = $storeData['nombre'];               // Ejemplo: "H85"
    $bv_pais_tienda = $storeData['pais_tienda']; // Ejemplo: "NIH85"
} else {
    // Compatibilidad si el select manda texto plano
    $store = substr($_POST['stores'], 0, 3);
    $bv_pais_tienda = $countryAbbreviation . $store;
}

// Query the database to get the country codes
$getCrmTienda = "select CRM_ID_TIENDA from CRM_STORES where PAIS_TIENDA = '$bv_pais_tienda'";
$resultCrmTienda = $conn->query($getCrmTienda);
$row = $resultCrmTienda->fetch_assoc();
$idTienda = $row['CRM_ID_TIENDA'];

// Insertar SOLO si no venís de un paso posterior
if (!isset($_GET['id_registered_visit'])) {

    $stmt = $conn2->prepare("INSERT INTO fact_visitas (COUNTRY, FORMAT, STORE, VISIT_EMAIL, START_DATETIME, IP_ADDRESS, BV_PAIS, ID_SUGAR_TIENDA) 
                             VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssss", 
        $countryAbbreviation, 
        $format, 
        $store, 
        $email, 
        $survey_start, 
        $ip_address, 
        $bv_pais, 
        $idTienda
    );

    $success = $stmt->execute();

    if ($success) {
        $insertMessage = "Información de País, Formato, Tienda y Evaluador ingresada correctamente.";
    } else {
        $insertMessage = "Hubo un error al ingresar la información: " . $stmt->error;
    }
}

$id_registered_visit_get = $_GET['id_registered_visit'] ?? null;
$id_registered_visit_post = 0;

// Si hubo un INSERT, usamos el autoincremental real
if (isset($success) && $success) {
    $id_registered_visit_post = $conn2->insert_id;
}

// Si no hubo INSERT o el ID vino vacío, buscamos el último ID manualmente
if ($id_registered_visit_post == 0) {
    $queryLastId = "SELECT MAX(ID_VISITA) AS last_id FROM fact_visitas";
    $resultLastId = $conn2->query($queryLastId);
    $lastId = 0;
    if ($resultLastId && $rowLast = $resultLastId->fetch_assoc()) {
        $lastId = (int)$rowLast['last_id'];
    }
    $id_registered_visit_post = $lastId + 1; // Generar el siguiente ID
}

// Usar el ID final
if (is_null($id_registered_visit_get)) {
    $id_registered_visit = $id_registered_visit_post;
} else {
    $id_registered_visit = $id_registered_visit_get;
}


//Obtain questions from Area = 1 and Type "Si/No = 1"
$query = "SELECT id_pregunta, pregunta 
          FROM `dim_preguntas` 
          WHERE ID_AREA = $area 
          AND SI_NO = 1 
          and INCLUIR = 1
          and $format = 1
          ORDER BY FIELD(id_pregunta, 1,3)";
$result = mysqli_query($conn2, $query);

//Obtain questions from Area = 1 and Type "Si/No = 0"
$query2 = "SELECT id_pregunta, pregunta 
            FROM `dim_preguntas` 
            WHERE ID_AREA = $area 
            AND SI_NO = 0 
            and INCLUIR = 1
            and $format = 1
            ORDER BY FIELD(id_pregunta, 1,3)";
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
        <form id="form1" action="step3.php" method="post">
            <h2>One Playbook - Visita a Tiendas</h2>
            <input type="hidden" name="id_registered_visit" value="<?php echo $id_registered_visit; ?>">
            <input type="hidden" name="formato" value="<?php echo $format; ?>">
            <input type="hidden" name="bv_pais_tienda" value="<?php echo $bv_pais_tienda; ?>">
            <p class="required-fields">*Valores Obligatorios<br>Te encuentras realizando la vista en la tienda <?php echo $bv_pais_tienda; ?>.<br>Visita con ID <?php echo $id_registered_visit; ?>.</p>
            <div class="intro-text">
                <H3>SECCIÓN B - Evaluación: Código de vestimenta ADOCKER.</H3>
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
                    echo '<label class="label-with-border">Evaluación Likert (1:Bajo, 5:Alto):<span class="required-field">*</span></label>';
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
                    <input type="submit" value="Continuar"> <br />
                    <input type="button" class="back-button" onclick="redirigirPagina('index.php')" value="Regresar" />
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