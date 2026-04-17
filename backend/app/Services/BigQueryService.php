<?php

namespace App\Services;

use Google\Cloud\BigQuery\BigQueryClient;
use Google\Cloud\BigQuery\QueryJobConfig;

class BigQueryService
{
    protected $bigQuery;
    protected $projectId = 'adoc-bi-prd';
    protected $table = 'adoc-bi-prd.OPB.gerente_pais';
    protected $masterStore = 'adoc-bi-prd.BI_Repo_Qlik.New_Store_Master';
    protected $masterLideres = 'adoc-bi-prd.OPB.dim_lideres_correo';
    protected $masterGerentes = 'adoc-bi-dev.DEV_OPB.dim_gerentes';

    public function __construct()
    {
        $credentialsPath = base_path('credenciales_bq.json');

        if (!file_exists($credentialsPath)) {
            throw new \Exception("Credenciales de BigQuery no encontradas en: $credentialsPath");
        }

        $this->bigQuery = new BigQueryClient([
            'projectId' => $this->projectId,
            'keyFilePath' => $credentialsPath,
        ]);
    }

    /**
     * Obtener países disponibles
     */
    public function getCountries()
    {
        $sql = "
            SELECT DISTINCT
                country,
                country_code,
                code
            FROM `adoc-bi-prd.OPB.dim_country_code`
            WHERE country IN ('El Salvador', 'Guatemala', 'Costa Rica', 'Honduras', 'Nicaragua', 'Panama')
            ORDER BY country ASC
        ";

        return $this->executeQuery($sql);
    }

    /**
     * Obtener tiendas por país y formato
     */
    public function getStoresByCountryAndFormat($country, $format)
    {
        $sql = "
            SELECT DISTINCT
                TRIM(Nombre) AS nombre,
                TRIM(Pais_Tienda) AS pais_tienda,
                TRIM(Pais) AS pais,
                TRIM(Zona) AS zona,
                TRIM(Email) AS email,
                Ubicacion
            FROM `{$this->masterStore}`
            WHERE UPPER(Pais) = UPPER(@country)
                AND Formato = @format
            ORDER BY Nombre ASC
        ";

        $jobConfig = new QueryJobConfig();
        $jobConfig->parameters([
            ['name' => 'country', 'parameterType' => ['type' => 'STRING'], 'parameterValue' => ['value' => $country]],
            ['name' => 'format', 'parameterType' => ['type' => 'STRING'], 'parameterValue' => ['value' => $format]],
        ]);

        return $this->executeQueryWithConfig($sql, $jobConfig);
    }

    /**
     * Obtener información de correo del líder de zona
     */
    public function getZoneLeaderEmail($zona)
    {
        $sql = "
            SELECT
                TRIM(Correo) AS correo,
                TRIM(Nombre) AS nombre,
                TRIM(Zona) AS zona
            FROM `{$this->masterLideres}`
            WHERE UPPER(TRIM(Zona)) = UPPER(@zona)
            LIMIT 1
        ";

        $jobConfig = new QueryJobConfig();
        $jobConfig->parameters([
            ['name' => 'zona', 'parameterType' => ['type' => 'STRING'], 'parameterValue' => ['value' => $zona]],
        ]);

        $result = $this->executeQueryWithConfig($sql, $jobConfig);
        return $result[0] ?? null;
    }

    /**
     * Obtener datos de gerente de país
     */
    public function getCountryManager($country)
    {
        $sql = "
            SELECT
                TRIM(Correo) AS correo,
                TRIM(Nombre) AS nombre
            FROM `{$this->masterGerentes}`
            WHERE UPPER(TRIM(Pais)) = UPPER(@country)
            LIMIT 1
        ";

        $jobConfig = new QueryJobConfig();
        $jobConfig->parameters([
            ['name' => 'country', 'parameterType' => ['type' => 'STRING'], 'parameterValue' => ['value' => $country]],
        ]);

        $result = $this->executeQueryWithConfig($sql, $jobConfig);
        return $result[0] ?? null;
    }

    /**
     * Guardar o actualizar visita (insertar nueva fila)
     */
    public function saveVisit($data)
    {
        $dataset = $this->bigQuery->dataset('OPB');
        $table = $dataset->table('gerente_pais');

        try {
            $table->insertRows([$data]);
            return true;
        } catch (\Exception $e) {
            throw new \Exception("Error al guardar en BigQuery: " . $e->getMessage());
        }
    }

    /**
     * Actualizar visita existente (por ID)
     */
    public function updateVisit($id, $data)
    {
        // BigQuery usa MERGE para actualizaciones
        $sql = "
            MERGE `{$this->table}` T
            USING (SELECT @id AS id_visita) S
            ON T.id_visita = S.id_visita
            WHEN MATCHED THEN
                UPDATE SET " . $this->buildUpdateClause($data) . "
        ";

        return $this->executeQueryWithoutReturn($sql, $data, $id);
    }

    /**
     * Obtener visita por ID
     */
    public function getVisitById($id)
    {
        $sql = "
            SELECT *
            FROM `{$this->table}`
            WHERE id_visita = @id
            LIMIT 1
        ";

        $jobConfig = new QueryJobConfig();
        $jobConfig->parameters([
            ['name' => 'id', 'parameterType' => ['type' => 'STRING'], 'parameterValue' => ['value' => $id]],
        ]);

        $result = $this->executeQueryWithConfig($sql, $jobConfig);
        return $result[0] ?? null;
    }

    /**
     * Ejecutar query simple
     */
    protected function executeQuery($sql)
    {
        $job = $this->bigQuery->query($sql);
        $results = $this->bigQuery->runQuery($job);

        $data = [];
        foreach ($results->rows() as $row) {
            $data[] = $row;
        }

        return $data;
    }

    /**
     * Ejecutar query con configuración
     */
    protected function executeQueryWithConfig($sql, $jobConfig)
    {
        $job = $this->bigQuery->query($sql, $jobConfig);
        $results = $this->bigQuery->runQuery($job);

        $data = [];
        foreach ($results->rows() as $row) {
            $data[] = $row;
        }

        return $data;
    }

    /**
     * Ejecutar query sin retorno
     */
    protected function executeQueryWithoutReturn($sql, $data = [], $id = null)
    {
        try {
            $job = $this->bigQuery->query($sql);
            $this->bigQuery->runQuery($job);
            return true;
        } catch (\Exception $e) {
            throw new \Exception("Error en query: " . $e->getMessage());
        }
    }

    /**
     * Construir cláusula UPDATE
     */
    protected function buildUpdateClause($data)
    {
        $clauses = [];
        foreach ($data as $key => $value) {
            if ($key !== 'id_visita') {
                $clauses[] = "$key = @$key";
            }
        }
        return implode(', ', $clauses);
    }
}
