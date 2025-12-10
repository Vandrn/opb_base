<?php
require_once 'connection_local.php'; // conexión local o simulada

// Simular IP del usuario
$ip_address = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

// Recibir variables del formulario
$country = $_POST['country'] ?? '';
$formato = $_POST['formato'] ?? '';
$store = substr($_POST['stores'] ?? '', 0, 3);
$first_name = $_POST['first_name'] ?? '';
$last_name = $_POST['last_name'] ?? '';
$document_number = $_POST['documento'] ?? '';
$document_type = $_POST['document_type'] ?? '';
$nit = $_POST['nit'] ?? '';
$email = $_POST['email'] ?? '';
$phone_code = $_POST['country_code'] ?? '';
$phone_number = $_POST['phone'] ?? '';
$join_loyalty = $_POST['puntos'] ?? '';
$adocker_registry = $_POST['employee_list'] ?? '';
$start_datetime = $_POST['survey_start'] ?? date('Y-m-d H:i:s');

// ---------------------------------------------------------------------
// 1️⃣ Insertar registro base en CRM_CLIENTS (modo local / simulado)
// ---------------------------------------------------------------------
if ($conn) {
    $stmt = $conn->prepare("INSERT INTO CRM_CLIENTS 
        (COUNTRY, FORMAT, STORE, FIRST_NAME, LAST_NAME, DOCUMENT_NUMBER, DOCUMENT_TYPE, NIT_NUMBER, EMAIL, PHONE_CODE, PHONE_NUMBER, JOIN_LOYALTY, ADOCKER_REGISTRY, start_datetime, IP_ADDRESS)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );

    $stmt->bind_param("sssssssssssssss", 
        $country, $formato, $store, $first_name, $last_name, $document_number,
        $document_type, $nit, $email, $phone_code, $phone_number,
        $join_loyalty, $adocker_registry, $start_datetime, $ip_address
    );

    $success = $stmt->execute();
    $id_registered_client = $conn->insert_id;
} else {
    // Modo sin base real (simulación)
    $success = true;
    $id_registered_client = rand(1000, 9999);
}

// ---------------------------------------------------------------------
// 2️⃣ Simular datos complementarios (en producción vienen de CRM_STORES)
// ---------------------------------------------------------------------
$BV_PAIS = strtoupper($country);
$BV_PAIS_TIENDA = $country . $store;
$ID_DOC_EMAIL_CEL = $document_number . $email . $phone_number;
$PROGRAMA_LEALTAD_C = ($join_loyalty === 'Si') ? 'true' : 'false';
$ID_SUGAR_TIENDA = 'SIMULADO-' . $BV_PAIS_TIENDA;
$CORREO_BU = $email;

// ---------------------------------------------------------------------
// 3️⃣ Generar el JSON local (sin FTP ni SugarCRM)
// ---------------------------------------------------------------------
$data = [
    'ID_REGISTERED_CLIENT' => $id_registered_client,
    'COUNTRY' => $country,
    'FORMAT' => $formato,
    'STORE' => $store,
    'FIRST_NAME' => $first_name,
    'LAST_NAME' => $last_name,
    'DOCUMENT_NUMBER' => $document_number,
    'DOCUMENT_TYPE' => $document_type,
    'EMAIL' => $email,
    'PHONE' => $phone_code . $phone_number,
    'JOIN_LOYALTY' => $join_loyalty,
    'ADOCKER_REGISTRY' => $adocker_registry,
    'BV_PAIS' => $BV_PAIS,
    'ID_DOC_EMAIL_CEL' => $ID_DOC_EMAIL_CEL,
    'PROGRAMA_LEALTAD_C' => $PROGRAMA_LEALTAD_C,
    'ID_SUGAR_TIENDA' => $ID_SUGAR_TIENDA,
    'CORREO_BU' => $CORREO_BU,
    'IP_ADDRESS' => $ip_address,
    'GENERATED_AT' => date('Y-m-d H:i:s')
];

// Guardar en carpeta local /data
if (!file_exists(__DIR__ . '/data')) mkdir(__DIR__ . '/data', 0755, true);
$jsonFile = __DIR__ . "/data/cliente_{$id_registered_client}.json";
file_put_contents($jsonFile, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

$record_exists = null;
if ($success) {
    $message = "Registro guardado correctamente (modo local).";
} else {
    $message = "Error al guardar registro.";
}

// ---------------------------------------------------------------------
// 4️⃣ Cerrar conexiones
// ---------------------------------------------------------------------
if (isset($stmt)) $stmt->close();
if (isset($conn)) $conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Clientes - Modo Local</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1><?php echo htmlspecialchars($message); ?></h1>
    <p>Archivo JSON generado: <strong><?php echo basename($jsonFile); ?></strong></p>
    <pre><?php echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE); ?></pre>

    <div class="button-container">
        <button class="submit-btn" onclick="location.href='index.php'">Ingresar Otro</button>
    </div>
</body>
</html>
