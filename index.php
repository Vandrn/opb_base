<?php
require_once 'connection.php';

// Get user's IP address
$ip_address = $_SERVER['REMOTE_ADDR'];

// Query to get country codes and selection status
$sql_country = "SELECT z.code, z.country_code, z.country,
    CASE WHEN z.country = (
        SELECT c.country_name
        FROM ip_ranges a
        LEFT JOIN ip_country_blocks b ON a.network = b.network
        LEFT JOIN ip_country_locations c ON b.registered_country_geoname_id = c.geoname_id
        WHERE INET_ATON('$ip_address') BETWEEN INET_ATON(a.first_address) AND INET_ATON(a.last_address)
        LIMIT 1
    ) THEN 'Selected' ELSE '' END AS SELECTION
    FROM dim_country_code z";


$result_country = mysqli_query($conn, $sql_country);

$options = '';
while ($row_country = mysqli_fetch_assoc($result_country)) {
    $options .= '<option value="' . $row_country['code'] . '" ' . (isset($row_country['SELECTION']) ? $row_country['SELECTION'] : '') . '>' . $row_country['country_code'] . '</option>';
}

// Query to get country details
$sql_c = "SELECT CONCAT('country_',z.code) AS code, TRIM(z.country) AS country,
    CASE WHEN z.country = 'El Salvador' THEN 'required' ELSE '' END AS REQUIRED,
    CASE 
        WHEN TRIM(z.country) = 'El Salvador' THEN 'DUI'
        WHEN TRIM(z.country) = 'Costa Rica' THEN 'CDE'
        WHEN TRIM(z.country) = 'Guatemala' THEN 'DPI'
        WHEN TRIM(z.country) = 'Honduras' THEN 'DNI'
        WHEN TRIM(z.country) = 'Nicaragua' THEN 'CDE'
        WHEN TRIM(z.country) = 'Panama' THEN 'CIP'
    END AS DOCUMENT_TYPE,
    CASE 
        WHEN TRIM(z.country) = 'El Salvador' THEN '9'
        WHEN TRIM(z.country) = 'Costa Rica' THEN '12'
        WHEN TRIM(z.country) = 'Guatemala' THEN '13'
        WHEN TRIM(z.country) = 'Honduras' THEN '13'
        WHEN TRIM(z.country) = 'Nicaragua' THEN '14'
        WHEN TRIM(z.country) = 'Panama' THEN '9'
    END AS MAX_DIGITS,
    CASE 
        WHEN TRIM(z.country) = 'El Salvador' THEN '9'
        WHEN TRIM(z.country) = 'Costa Rica' THEN '9'
        WHEN TRIM(z.country) = 'Guatemala' THEN '13'
        WHEN TRIM(z.country) = 'Honduras' THEN '13'
        WHEN TRIM(z.country) = 'Nicaragua' THEN '14'
        WHEN TRIM(z.country) = 'Panama' THEN '9'
    END AS MIN_DIGITS,
    CASE 
        WHEN TRIM(z.country) = 'El Salvador' THEN 'DUI'
        WHEN TRIM(z.country) = 'Costa Rica' THEN 'CDE'
        WHEN TRIM(z.country) = 'Guatemala' THEN 'DPI'
        WHEN TRIM(z.country) = 'Honduras' THEN 'DNI'
        WHEN TRIM(z.country) = 'Nicaragua' THEN 'CDE'
        WHEN TRIM(z.country) = 'Panama' THEN 'CIP'
    END AS DOCUMENT_TYPE
FROM dim_country_code z
WHERE z.country IN ('El Salvador', 'Guatemala', 'Costa Rica', 'Honduras', 'Nicaragua', 'Panama')";

$result_c = mysqli_query($conn, $sql_c);

$country_list = '';
while ($row_c_list = mysqli_fetch_assoc($result_c)) {
    $selection = isset($row_c_list['SELECTION']) ? $row_c_list['SELECTION'] : '';
    $required = $row_c_list['REQUIRED'];
    $document_type = $row_c_list['DOCUMENT_TYPE'];
    $max_digits = $row_c_list['MAX_DIGITS'];
    $min_digits = $row_c_list['MIN_DIGITS'];
    $nicaragua = $row_c_list['country'] === 'Nicaragua' ? 'true' : 'false';

    $country_list .= '<input type="radio" id="' . $row_c_list["code"] . '" 
        data-document-type="' . $document_type . '" 
        data-max-digits="' . $max_digits . '" 
        data-min-digits="' . $min_digits . '" 
        data-nicaragua="' . $nicaragua . '"
        name="country" 
        value="' . $row_c_list["country"] . '"' . $selection . ' ' . $required . '>
        <label for="' . $row_c_list["code"] . '">' . $row_c_list["country"] . '</label>';
}

$conn->close();
?>

<!DOCTYPE html>
<html>

<head>
    <link rel="icon" type="images/x-icon" href="../images/adoc-favicon.ico" />
    <title>One Playbook - Visita a Tiendas</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="listen.js"></script>
</head>

<body>
    <div class="container">
        <form id="form1" action="step2.php" method="post">
            <h2>One Playbook - Visita a Tiendas</h2>
            <p class="required-fields">*Valores Obligatorios</p>
            <div class="intro-text">
                <h3>SECCIÓN A: Seleccionar País, Formato y Tienda.</h3>
                <label for="country" class="label-with-border">1. Elige tu país:<span class="required-field">*</span></label>
                <div class="radio-group-country">
                    <?php echo $country_list; ?>
                </div>
                <label for="formato" class="label-with-border">2. Formato:<span class="required-field">*</span></label>
                <div class="radio-group-country">
                    <input type="radio" id="ADOC" name="formato" value="ADOC">
                    <label for="ADOC">ADOC</label>
                    <input type="radio" id="PAR2" name="formato" value="PAR2" required="">
                    <label for="PAR2">PAR2</label>
                    <input type="radio" id="CAT" name="formato" value="CAT">
                    <label for="CAT">CAT</label>
                    <input type="radio" id="TNF" name="formato" value="TNF">
                    <label for="TNF">TNF</label>
                    <input type="radio" id="HP" name="formato" value="HP">
                    <label for="HP">HP</label>
                    <input type="radio" id="CG" name="formato" value="CG">
                    <label for="CG">CG</label>
                    <input type="radio" id="Vans" name="formato" value="Vans">
                    <label for="Vans">Vans</label>
                </div>
                <label for="stores" class="label-with-border">3. Selecciona tu tienda:<span class="required-field">*</span></label>
                <select id="stores" name="stores">
                    <option value="">-- Selecciona una Tienda --</option>
                </select>
                <!-- Correo Electrónico -->
                <label for="email" class="label-with-border">4. Correo Electrónico (de quien realiza la evaluación):<span class="required-field">*</span></label>
                <input type="text" id="email" name="email" required>
                <span id="email-error" style="color: red;"></span>
                <p></p>
                <input type="submit" value="Ingresar">
        </form>
    </div>
    <!-- JS Para validar correo que coloquen siempre @ y . y cualquier tipo de correo-->
    <script>
        var emailInput = document.getElementById("email");
        var emailError = document.getElementById("email-error");
        var emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;

        emailInput.addEventListener("input", function() {
            var email = emailInput.value;
            if (emailRegex.test(email)) {
                emailError.textContent = "";
            } else {
                emailError.textContent = "El correo electrónico no es válido.";
            }
        });
    </script>
</body>

</html>