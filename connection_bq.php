<?php
require_once __DIR__ . '/vendor/autoload.php';
use Google\Cloud\BigQuery\BigQueryClient;

// 🔹 Ruta a credenciales
$credentialsPath = __DIR__ . '/credenciales_bq.json';
if (!file_exists($credentialsPath)) {
    die("❌ No se encontró el archivo de credenciales: $credentialsPath");
}

// 🔹 Cliente BigQuery
$bigQuery = new BigQueryClient([
    'projectId' => 'adoc-bi-prd',
    'keyFilePath' => $credentialsPath
]);

// 🔹 Tablas relevantes
$TABLE_STORE_MASTER   = 'adoc-bi-prd.BI_Repo_Qlik.New_Store_Master';
$TABLE_LIDERES_CORREO = 'adoc-bi-prd.OPB.dim_lideres_correo';
$TABLE_GERENTES       = 'adoc-bi-dev.DEV_OPB.dim_gerentes';
?>
