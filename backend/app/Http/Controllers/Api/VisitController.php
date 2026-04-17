<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\BigQueryService;
use Illuminate\Http\Request;

class VisitController extends Controller
{
    protected $bqService;

    public function __construct(BigQueryService $bqService)
    {
        $this->bqService = $bqService;
    }

    /**
     * GET /api/countries
     * Obtener lista de países
     */
    public function getCountries()
    {
        try {
            $countries = $this->bqService->getCountries();
            return response()->json([
                'success' => true,
                'data' => $countries
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET /api/stores?country=XX&format=ADOC
     * Obtener tiendas por país y formato
     */
    public function getStores(Request $request)
    {
        try {
            $country = $request->query('country');
            $format = $request->query('format');

            if (!$country || !$format) {
                return response()->json([
                    'success' => false,
                    'error' => 'País y formato son requeridos'
                ], 400);
            }

            $stores = $this->bqService->getStoresByCountryAndFormat($country, $format);
            return response()->json([
                'success' => true,
                'data' => $stores
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * POST /api/visits
     * Crear nueva visita (guardado inicial)
     */
    public function createVisit(Request $request)
    {
        try {
            $validated = $request->validate([
                'country' => 'required|string',
                'format' => 'required|string',
                'store' => 'required|string',
                'visit_email' => 'required|email',
                'bv_pais' => 'required|string',
                'id_sugar_tienda' => 'required|string',
            ]);

            // Agregar datos automáticos
            $visitData = array_merge($validated, [
                'id_visita' => uniqid('visit_'),
                'start_datetime' => date('Y-m-d H:i:s'),
                'ip_address' => $request->ip(),
            ]);

            // Obtener información adicional
            if (!empty($validated['store'])) {
                $zona = $this->extractZonaFromStore($validated['store']);
                if ($zona) {
                    $zoneLeader = $this->bqService->getZoneLeaderEmail($zona);
                    if ($zoneLeader) {
                        $visitData['lider_zona'] = $zoneLeader['correo'] ?? null;
                    }
                }
            }

            // Obtener gerente de país
            $countryManager = $this->bqService->getCountryManager($validated['country']);
            if ($countryManager) {
                $visitData['preg_69'] = $countryManager['correo'] ?? null; // O el campo correcto
            }

            // Guardar en BigQuery
            $this->bqService->saveVisit($visitData);

            return response()->json([
                'success' => true,
                'visit_id' => $visitData['id_visita'],
                'data' => $visitData
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * PATCH /api/visits/{id}
     * Actualizar visita (guardado por pasos)
     */
    public function updateVisit(Request $request, $id)
    {
        try {
            $data = $request->all();

            // Agregar timestamp de actualización
            $data['end_datetime'] = date('Y-m-d H:i:s');

            // Validar que solo tengamos campos de BQ
            $allowedFields = $this->getAllowedFields();
            $filteredData = array_filter($data, function ($key) use ($allowedFields) {
                return in_array($key, $allowedFields);
            }, ARRAY_FILTER_USE_KEY);

            // Actualizar en BigQuery
            $this->bqService->updateVisit($id, $filteredData);

            return response()->json([
                'success' => true,
                'message' => 'Visita actualizada correctamente',
                'visit_id' => $id
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET /api/visits/{id}
     * Obtener datos de visita existente
     */
    public function getVisit($id)
    {
        try {
            $visit = $this->bqService->getVisitById($id);

            if (!$visit) {
                return response()->json([
                    'success' => false,
                    'error' => 'Visita no encontrada'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $visit
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Extraer zona del nombre de tienda (ej: "H85" -> zona)
     */
    protected function extractZonaFromStore($store)
    {
        // Ajustar según tu lógica
        return substr($store, 0, 1);
    }

    /**
     * Campos permitidos en BigQuery (según tu esquema)
     */
    protected function getAllowedFields()
    {
        return [
            'country', 'format', 'store', 'visit_email', 'start_datetime', 'end_datetime',
            'ip_address', 'bv_pais', 'id_sugar_tienda', 'store_email', 'lider_zona',
            'ubicacion', 'preg_01', 'preg_03', 'preg_05', 'preg_06', 'preg_09', 'preg_10',
            'preg_11', 'preg_12', 'preg_13', 'preg_14', 'preg_15', 'preg_21', 'preg_23',
            'preg_24', 'preg_25', 'preg_26', 'preg_27', 'preg_28', 'preg_29', 'preg_30',
            'preg_31', 'preg_35', 'preg_36', 'preg_37', 'preg_39', 'preg_40', 'preg_41',
            'preg_44', 'preg_45', 'preg_47', 'preg_48', 'preg_49', 'preg_55', 'preg_56',
            'preg_57', 'obs_01', 'obs_02', 'obs_03', 'obs_04', 'obs_05', 'obs_06', 'obs_07',
            'preg_58', 'preg_59', 'preg_60', 'preg_61', 'preg_62', 'preg_63', 'preg_64',
            'preg_65', 'preg_66', 'preg_67', 'preg_69', 'preg_70', 'preg_71', 'preg_72',
            'preg_73', 'preg_74', 'preg_76', 'preg_77', 'preg_78', 'preg_79', 'preg_80',
            'preg_81', 'preg_82', 'preg_83', 'preg_84', 'preg_85', 'preg_86', 'preg_88',
            'preg_89', 'preg_90', 'preg_91', 'preg_92', 'preg_93', 'preg_94', 'preg_95',
            'preg_96', 'preg_97', 'preg_98', 'preg_99', 'preg_101', 'preg_102', 'preg_103',
            'preg_104', 'preg_105', 'preg_106', 'preg_107', 'preg_108', 'preg_109', 'preg_110',
            'preg_111', 'preg_112', 'lat', 'lon'
        ];
    }
}
