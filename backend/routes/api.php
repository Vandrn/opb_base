<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use Google\Cloud\BigQuery\BigQueryClient;

// Rutas públicas - Mock data para testing
Route::get('/countries', function () {
    return response()->json([
        'success' => true,
        'data' => [
            ['country' => 'Costa Rica', 'country_code' => 'CR', 'code' => 'CR'],
            ['country' => 'El Salvador', 'country_code' => 'SV', 'code' => 'SV'],
            ['country' => 'Guatemala', 'country_code' => 'GT', 'code' => 'GT'],
            ['country' => 'Honduras', 'country_code' => 'HN', 'code' => 'HN'],
            ['country' => 'Nicaragua', 'country_code' => 'NI', 'code' => 'NI'],
            ['country' => 'Panama', 'country_code' => 'PA', 'code' => 'PA'],
        ]
    ]);
});

Route::get('/stores', function (Request $request) {
    try {
        $country = trim($request->query('country', ''));
        $format = trim($request->query('format', ''));

        // Mapeo de países
        $paisMap = [
            'EL SALVADOR' => 'SV',
            'GUATEMALA'   => 'GT',
            'NICARAGUA'   => 'NI',
            'HONDURAS'    => 'HN',
            'COSTA RICA'  => 'CR',
            'PANAMA'      => 'PA',
            'PANAMÁ'      => 'PA',
            'SV' => 'SV',
            'GT' => 'GT',
            'NI' => 'NI',
            'HN' => 'HN',
            'CR' => 'CR',
            'PA' => 'PA',
        ];

        $countryCode = $paisMap[mb_strtoupper($country, 'UTF-8')] ?? $country;
        $formatUpper = mb_strtoupper($format, 'UTF-8');

        // Conectar a BigQuery
        $credentialsPath = base_path('credenciales_bq.json');
        if (!file_exists($credentialsPath)) {
            return response()->json(['success' => false, 'error' => 'Credenciales no encontradas'], 500);
        }

        $bigQuery = new BigQueryClient([
            'projectId' => 'adoc-bi-prd',
            'keyFilePath' => $credentialsPath,
        ]);

        // Construir WHERE conditions
        $whereConditions = [];
        if (!empty($countryCode)) {
            $whereConditions[] = "UPPER(Pais) = '" . addslashes($countryCode) . "'";
        }
        if (!empty($formatUpper)) {
            $whereConditions[] = "UPPER(Formato) = '" . addslashes($formatUpper) . "'";
        }
        $whereConditions[] = "Email IS NOT NULL AND TRIM(Email) <> ''";

        $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';

        $query = <<<SQL
        SELECT
          Pais,
          Centro,
          Pais_Tienda,
          Ubicacion,
          Formato,
          Canal,
          Sociedad,
          Latitud,
          Longitud,
          mts2_bodega,
          mts2_sala,
          Gerente_Retail,
          Capacidad_sala,
          Capacidad_bodega,
          Capacidad_total,
          Tipo_tienda,
          nombre1,
          calle1,
          calle2,
          calle3,
          TRANSPZONE,
          Telefono,
          Zona,
          TIER,
          Tipo_tienda_108,
          Ciudad,
          Departamento,
          Email
        FROM `adoc-bi-prd.BI_Repo_Qlik.New_Store_Master`
        {$whereClause}
        ORDER BY Centro ASC, nombre1 ASC
        SQL;

        $jobConfig = $bigQuery->query($query);
        $queryResults = $bigQuery->runQuery($jobConfig);

        if (!$queryResults->isComplete()) {
            return response()->json(['success' => false, 'error' => 'Query incomplete'], 500);
        }

        $stores = [];
        foreach ($queryResults->rows() as $row) {
            $stores[] = [
                'pais' => $row['Pais'] ?? '',
                'codigo' => $row['Centro'] ?? '',
                'pais_tienda' => $row['Pais_Tienda'] ?? '',
                'nombre' => $row['nombre1'] ?? '',
                'ubicacion' => $row['Ubicacion'] ?? '',
                'correo' => $row['Email'] ?? '',
                'zona' => $row['Zona'] ?? '',
                'gerente' => $row['Gerente_Retail'] ?? '',
                'sociedad' => $row['Sociedad'] ?? '',
                'canal' => $row['Canal'] ?? '',
                'formato' => $row['Formato'] ?? '',
                'latitud' => $row['Latitud'] ?? '',
                'longitud' => $row['Longitud'] ?? '',
                'telefono' => $row['Telefono'] ?? '',
                'ciudad' => $row['Ciudad'] ?? '',
                'departamento' => $row['Departamento'] ?? '',
                'tipo_tienda' => $row['Tipo_tienda'] ?? '',
                'tier' => $row['TIER'] ?? '',
            ];
        }

        return response()->json(['success' => true, 'data' => $stores]);

    } catch (\Exception $e) {
        Log::error('Error fetching stores: ' . $e->getMessage());
        return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
    }
});

Route::post('/visits', function (Request $request) {
    try {
        $visitId = 'visit_' . uniqid();

        // Conectar a BigQuery
        $credentialsPath = base_path('credenciales_bq.json');
        if (!file_exists($credentialsPath)) {
            return response()->json(['success' => false, 'error' => 'Credenciales no encontradas'], 500);
        }

        $bigQuery = new BigQueryClient([
            'projectId' => 'adoc-bi-prd',
            'keyFilePath' => $credentialsPath,
        ]);

        // Obtener datos del request
        $data = $request->all();

        // Construir fila para BigQuery
        $row = [
            'id_visita' => $visitId,
            'country' => $data['country'] ?? '',
            'format' => $data['format'] ?? '',
            'store' => $data['store'] ?? '',
            'visit_email' => $data['visit_email'] ?? '',
            'start_datetime' => now()->toDateTimeString(),
            'bv_pais' => $data['bv_pais'] ?? '',
            'id_sugar_tienda' => $data['id_sugar_tienda'] ?? '',
            'ubicacion' => $data['ubicacion'] ?? '',
            'store_email' => $data['store_email'] ?? '',
            'lider_zona' => $data['lider_zona'] ?? '',
            'ip_address' => $request->ip(),
            'lat' => $data['lat'] ?? null,
            'lon' => $data['lon'] ?? null,
            'section_1' => ['preg_01' => null, 'preg_03' => null, 'observations' => null],
            'section_2' => ['preg_05' => null, 'preg_111' => null, 'preg_112' => null, 'observations' => null],
            'section_3' => ['preg_15' => null, 'preg_21' => null, 'preg_24' => null, 'preg_25' => null, 'preg_26' => null, 'preg_58' => null, 'preg_59' => null, 'preg_60' => null, 'preg_62' => null, 'preg_63' => null, 'preg_64' => null, 'preg_65' => null, 'preg_66' => null, 'preg_67' => null, 'preg_69' => null, 'preg_70' => null, 'preg_71' => null, 'preg_72' => null, 'preg_73' => null, 'preg_74' => null, 'preg_102_likert' => null, 'observations' => null],
            'section_4' => ['preg_27' => null, 'preg_28' => null, 'preg_31' => null, 'preg_35' => null, 'preg_76' => null, 'preg_79' => null, 'preg_84' => null, 'preg_85' => null, 'preg_96' => null, 'preg_97' => null, 'preg_98' => null, 'preg_27_likert' => null, 'preg_29_likert' => null, 'preg_36_likert' => null, 'preg_37_likert' => null, 'preg_39_likert' => null, 'preg_40_likert' => null, 'observations' => null],
            'section_5' => ['preg_88' => null, 'preg_89' => null, 'observations' => null],
            'section_6' => ['preg_48' => null, 'observations' => null],
            'section_7' => ['observations' => null],
            'section_8' => ['observations' => null, 'planes' => null],
        ];

        // Insertar en BigQuery
        $dataset = $bigQuery->dataset('OPB');
        $table = $dataset->table('gp_nuevo');
        $table->insertRows([['data' => $row]]);

        return response()->json([
            'success' => true,
            'id_visita' => $visitId,
            'message' => 'Visita creada correctamente'
        ], 201);

    } catch (\Exception $e) {
        Log::error('Error creating visit: ' . $e->getMessage());
        return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
    }
});

Route::patch('/visits/{id}', function ($id, Request $request) {
    try {
        $credentialsPath = base_path('credenciales_bq.json');
        if (!file_exists($credentialsPath)) {
            return response()->json(['success' => false, 'error' => 'Credenciales no encontradas'], 500);
        }

        $bigQuery = new BigQueryClient([
            'projectId' => 'adoc-bi-prd',
            'keyFilePath' => $credentialsPath,
        ]);

        $data = $request->all();
        $questionsMapping = require base_path('config/questions_mapping.php');

        // Construir query UPDATE para BigQuery
        $updateClauses = [];
        $params = [];

        // Procesar cada sección
        for ($section = 1; $section <= 8; $section++) {
            $sectionKey = "section_{$section}";
            $sectionMapping = $questionsMapping[$sectionKey] ?? [];
            $sectionUpdates = [];

            // Mapear preguntas de la sección
            foreach ($sectionMapping as $frontendId => $bqColumn) {
                if (isset($data[$frontendId])) {
                    $value = is_bool($data[$frontendId]) ? ($data[$frontendId] ? 'Sí' : 'No') : $data[$frontendId];
                    $sectionUpdates[$bqColumn] = $value;
                }
            }

            // Agregar observaciones
            $obsKey = "observations_{$section}";
            if (isset($data[$obsKey])) {
                $sectionUpdates['observations'] = $data[$obsKey];
            }

            // Si hay actualizaciones para esta sección, agregarlas
            if (!empty($sectionUpdates)) {
                $structFields = [];
                foreach ($sectionUpdates as $field => $value) {
                    $structFields[] = "{$field}: '" . addslashes($value) . "'";
                }
                $updateClauses[] = "{$sectionKey} = STRUCT(" . implode(', ', $structFields) . ")";
            }
        }

        // Agregar end_datetime
        $updateClauses[] = "end_datetime = CURRENT_DATETIME()";

        if (empty($updateClauses)) {
            return response()->json(['success' => true, 'message' => 'No data to update']);
        }

        // Ejecutar UPDATE
        $updateQuery = "UPDATE `adoc-bi-prd.OPB.gp_nuevo` SET " . implode(', ', $updateClauses) . " WHERE id_visita = '" . addslashes($id) . "'";

        $jobConfig = $bigQuery->query($updateQuery);
        $queryResults = $bigQuery->runQuery($jobConfig);

        if (!$queryResults->isComplete()) {
            return response()->json(['success' => false, 'error' => 'Query incomplete'], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Visita actualizada correctamente',
            'visit_id' => $id
        ]);

    } catch (\Exception $e) {
        Log::error('Error updating visit: ' . $e->getMessage());
        return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
    }
});

Route::get('/visits/{id}', function ($id) {
    return response()->json([
        'success' => true,
        'data' => ['id_visita' => $id]
    ]);
});

