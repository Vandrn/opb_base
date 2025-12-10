<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
    echo "✅ El archivo vendor/autoload.php SÍ existe.<br>";
} else {
    echo "❌ No se encontró vendor/autoload.php en: " . __DIR__ . "/vendor/autoload.php<br>";
}

use Google\Cloud\BigQuery\BigQueryClient;
echo "Intentando crear cliente...<br>";

try {
    $client = new BigQueryClient();
    echo "✅ Se pudo instanciar la clase BigQueryClient";
} catch (Throwable $e) {
    echo "❌ Error al crear cliente:<br>" . $e->getMessage();
}
?>
