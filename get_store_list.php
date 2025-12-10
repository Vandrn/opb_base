<?php
require_once 'connection_bq.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

$country = $_POST['country'] ?? '';
$formato = $_POST['formato'] ?? '';

// Normalizar pa铆s (para aceptar nombres o siglas)
$paisMap = [
    "El Salvador" => "SV",
    "Guatemala" => "GT",
    "Nicaragua" => "NI",
    "Honduras" => "HN",
    "Costa Rica" => "CR",
    "Panama" => "PA",
    "Panam谩" => "PA"
];
$countryCode = $paisMap[$country] ?? strtoupper($country);

// === Consulta en BigQuery basada en la vista que pasaste ===
$query = <<<SQL
SELECT
  T001W.LAND1 AS Pais,
  CONCAT(T001W.LAND1, ADRC.SORT1) AS Pais_Tienda,
  ADRC.NAME2 AS Ubicacion,
  ADRC.NAME1 AS Nombre_Tienda,
  (CASE
    WHEN SUBSTR(TVKTT.VTEXT, 9) = 'Caterpillar' THEN 'CAT'
    WHEN SUBSTR(TVKTT.VTEXT, 9) = 'Par-2' THEN 'PAR2'
    WHEN SUBSTR(TVKTT.VTEXT, 9) = 'Hush Puppies' THEN 'HP'
    WHEN SUBSTR(TVKTT.VTEXT, 9) = 'North Face' THEN 'TNF'
    ELSE SUBSTR(TVKTT.VTEXT, 9)
  END) AS Formato,
  ADRC.NAME3 AS Zona,
  ADRC.NAME_CO AS Gerente_Retail,
  ADR6.SMTP_ADDR AS Email,
  KNVV.VKORG AS Sociedad
FROM
  `adoc-bi-prd.SAP_ECC.T001W` AS T001W
LEFT JOIN
  `adoc-bi-prd.SAP_ECC.KNVV` AS KNVV
ON
  KNVV.KUNNR = T001W.KUNNR
  AND T001W.VKORG = KNVV.VKORG
  AND T001W.VTWEG = KNVV.VTWEG
LEFT JOIN
  `adoc-bi-prd.SAP_ECC.KNA1` AS KNA1
ON
  KNA1.KUNNR = KNVV.KUNNR
LEFT JOIN
  `adoc-bi-prd.SAP_ECC.ADRC` AS ADRC
ON
  ADRC.ADDRNUMBER = KNA1.ADRNR
LEFT JOIN
  `adoc-bi-prd.SAP_ECC.ADR6` AS ADR6
ON
  ADR6.ADDRNUMBER = ADRC.ADDRNUMBER
LEFT JOIN
  `adoc-bi-prd.SAP_ECC.TVKTT` AS TVKTT
ON
  TVKTT.MANDT = T001W.MANDT
  AND KNVV.KTGRD = TVKTT.KTGRD
WHERE
  ADRC.COUNTRY IN ('SV','GT','HN','CR','NI','PA')
  AND T001W.VLFKZ = 'A'
  AND ADRC.PO_BOX <> 'CL'
  AND ADRC.SORT1 NOT IN ('WHS','BT1')
  AND TVKTT.SPRAS = 'S'
  AND ADR6.SMTP_ADDR IS NOT NULL
  AND (@pais = '' OR T001W.LAND1 = @pais)
  AND (@formato = '' OR 
      (CASE
        WHEN SUBSTR(TVKTT.VTEXT, 9) = 'Caterpillar' THEN 'CAT'
        WHEN SUBSTR(TVKTT.VTEXT, 9) = 'Par-2' THEN 'PAR2'
        WHEN SUBSTR(TVKTT.VTEXT, 9) = 'Hush Puppies' THEN 'HP'
        WHEN SUBSTR(TVKTT.VTEXT, 9) = 'North Face' THEN 'TNF'
        ELSE SUBSTR(TVKTT.VTEXT, 9)
      END) = @formato)
ORDER BY ADRC.NAME1 ASC
SQL;

$job = $bigQuery->query($query)->parameters([
  'pais' => $countryCode,
  'formato' => strtoupper($formato)
]);
$result = $bigQuery->runQuery($job);

// === Construcci贸n del HTML ===
$html = '';

foreach ($result->rows() as $row) {
    $nombre = $row['Nombre_Tienda'] ?? '';
    $ubicacion = $row['Ubicacion'] ?? '';
    $email = $row['Email'] ?? '';
    $zona = $row['Zona'] ?? '';
    $gerente = $row['Gerente_Retail'] ?? '';
    $paisTienda = $row['Pais_Tienda'] ?? '';
    $sociedad = $row['Sociedad'] ?? '';

    // 馃敼 Extraer el c贸digo de tienda 
    $codigoTienda = explode(' ', trim($nombre))[0];

    // 馃敼 Guardar solo el c贸digo como value
    $value = htmlspecialchars(json_encode([ 
        'pais_tienda' => $paisTienda, 
        'nombre' => $nombre, 
        'ubicacion' => $ubicacion, 
        'correo' => $email, 
        'zona' => $zona, 
        'gerente' => $gerente, 
        'sociedad' => $sociedad 
    ])); 
        
    // Texto visible (nombre + ubicación) 
    $label = htmlspecialchars($codigoTienda . ' - ' . $ubicacion);
    $html .= "<option value='{$value}'>{$label}</option>";
}

if (!$html) {
    $html = '<option value="">No stores found</option>';
}

echo $html;
?>
