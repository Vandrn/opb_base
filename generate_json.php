<?php
require_once __DIR__ . '/connection.php';
require_once 'connection_bq.php';

// === 1. Obtener ID de visita ===
$ID = $_GET['ID'] ?? null;
if (!$ID) die("❌ Falta el parámetro ID.");

// === 2. Leer datos base desde MySQL ===
$query_fact = "SELECT * FROM fact_visitas WHERE ID_VISITA = ?";
$stmt_fact = $conn2->prepare($query_fact);
$stmt_fact->bind_param("i", $ID);
$stmt_fact->execute();
$result_fact = $stmt_fact->get_result();
$row_fact = $result_fact->fetch_assoc();

if (!$row_fact) die("❌ No se encontró la visita ID $ID.");

// === 3. Formar identificador de tienda ===
$COUNTRY = $row_fact['COUNTRY'];
$STORE = $row_fact['STORE'];
$BV_PAIS_TIENDA = $COUNTRY . $STORE;

// === 4. Consultar en BigQuery ===
$STORE_EMAIL = null;
$LIDER_ZONA = null;
$CORREO_GERENTE = null;

// --- a) Correo de tienda desde New_Store_Master ---
$queryStore = "
  SELECT
    TRIM(Email) AS Email,
    TRIM(Zona)  AS Zona,
    Pais,
    Pais_Tienda
  FROM `adoc-bi-prd.BI_Repo_Qlik.New_Store_Master`
  WHERE Pais_Tienda = @pais_tienda
  LIMIT 1
";
$jobStore = $bigQuery->query($queryStore)->parameters([
  'pais_tienda' => $BV_PAIS_TIENDA
]);
$resultStore = $bigQuery->runQuery($jobStore);

$ZONA = null;
$STORE_EMAIL = null;
$PAIS_BQ = null;

foreach ($resultStore->rows() as $row) {
  $STORE_EMAIL = ($row['Email'] ?? null) ? trim($row['Email']) : null;
  $ZONA        = $row['Zona'] ?? null;
  $PAIS_BQ     = $row['Pais'] ?? null; // por si querés validar contra $COUNTRY
}

// --- b) Correo del líder de zona desde dim_lideres_correo ---
// --- b) Correo del líder de zona usando Gerente_Retail (match exacto y confiable) ---
$queryLider = "
  WITH s AS (
    SELECT
      TRIM(Gerente_Retail) AS gerente,
      TRIM(Pais) AS pais
    FROM `adoc-bi-prd.BI_Repo_Qlik.New_Store_Master`
    WHERE Pais_Tienda = @pais_tienda
    LIMIT 1
  )
  SELECT l.Correo_gerente_retail
  FROM s
  JOIN `adoc-bi-prd.OPB.dim_lideres_correo` l
    ON TRIM(UPPER(l.Nombre_gerenteretail)) = TRIM(UPPER(s.gerente))
   AND TRIM(UPPER(l.Pais)) = TRIM(UPPER(s.pais))
  LIMIT 1
";

$jobLider = $bigQuery->query($queryLider)->parameters([
  'pais_tienda' => $BV_PAIS_TIENDA
]);

$resultLider = $bigQuery->runQuery($jobLider);

$LIDER_ZONA = null;
foreach ($resultLider->rows() as $row) {
    $LIDER_ZONA = $row['Correo_gerente_retail'] ?? null;
}


// --- c) Correo del gerente país desde dim_gerentes ---
$queryGerente = "
  SELECT CORREO_GERENTE
  FROM `adoc-bi-dev.DEV_OPB.dim_gerentes`
  WHERE BV_PAIS = @pais
  LIMIT 1
";
$jobGerente = $bigQuery->query($queryGerente)->parameters([
  'pais' => $COUNTRY
]);
$resultGerente = $bigQuery->runQuery($jobGerente);
foreach ($resultGerente->rows() as $row) {
  $CORREO_GERENTE = $row['CORREO_GERENTE'] ?? null;
}

// === 5. Agregar los tres campos al registro ===
$row_fact['STORE_EMAIL']    = $STORE_EMAIL ?: null;  // evita string vacío
$row_fact['LIDER_ZONA']     = $LIDER_ZONA;
$row_fact['CORREO_GERENTE'] = $CORREO_GERENTE;
$row_fact['ZONA']           = $ZONA;   

// === 6. Guardar el JSON localmente ===
unset($row_fact['JSON_GENERATED'], $row_fact['JSON_GENERATED_AT']);
$jsonContent = json_encode($row_fact, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
$jsonFilename = $ID . '.json';
$jsonPath = __DIR__ . '/data/' . $jsonFilename;

if (!is_dir(__DIR__ . '/data')) mkdir(__DIR__ . '/data', 0755, true);

// === Guardar el JSON localmente ===
if (!file_put_contents($jsonPath, $jsonContent)) {
    echo "❌ Error al guardar el archivo JSON.";
} else {
    echo "🟢 Visita registrada correctamente. JSON almacenado.";
}

// Cerrar conexiones SOLO si este script se ejecuta directamente,
// no cuando es incluido desde otro (como step9.php)
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
    $conn->close();
    $conn2->close();
}

?>
