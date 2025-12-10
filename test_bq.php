<?php
require_once 'connection_bq.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

try {
    echo "<pre>";
    echo "Conectando a BigQuery...\n";

    $query = "SELECT 'Conexión OK' AS estado";
    $job = $bigQuery->query($query);
    $results = $bigQuery->runQuery($job);

    foreach ($results->rows() as $row) {
        echo "Resultado: " . $row['estado'] . "\n";
    }

    echo "✅ Todo bien con BigQuery.\n";
} catch (Exception $e) {
    echo "❌ Error al conectar o ejecutar consulta:\n";
    echo $e->getMessage();
}
?>
