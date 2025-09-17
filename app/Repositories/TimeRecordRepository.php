<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class TimeRecordRepository
{
    /**
     * Busca registros de ponto com informações completas usando SQL puro
     *
     * @param string|null $dataInicio Data de início no formato Y-m-d
     * @param string|null $dataFim Data de fim no formato Y-m-d
     * @return array
     */
    public function buscarRegistrosCompletos(?string $dataInicio = null, ?string $dataFim = null): array
    {
        $query = "
            SELECT
                pr.id as registro_id,
                u.name as nome_funcionario,
                u.cargo,
                TIMESTAMPDIFF(YEAR, u.data_nascimento, CURDATE()) as idade,
                COALESCE(admin.name, 'N/A') as nome_gestor,
                DATE_FORMAT(pr.created_at, '%d/%m/%Y %H:%i:%s') as data_hora_completa,
                pr.created_at as timestamp_original
            FROM ponto_registros pr
            INNER JOIN users u ON pr.user_id = u.id
            LEFT JOIN users admin ON u.admin_id = admin.id
            WHERE 1=1
        ";

        $params = [];

        if ($dataInicio) {
            $query .= " AND DATE(pr.created_at) >= ?";
            $params[] = $dataInicio;
        }

        if ($dataFim) {
            $query .= " AND DATE(pr.created_at) <= ?";
            $params[] = $dataFim;
        }

        $query .= " ORDER BY pr.created_at DESC";

        return DB::select($query, $params);
    }

    /**
     * Conta o total de registros para paginação
     *
     * @param string|null $dataInicio
     * @param string|null $dataFim
     * @return int
     */
    public function contarRegistros(?string $dataInicio = null, ?string $dataFim = null): int
    {
        $query = "
            SELECT COUNT(*) as total
            FROM ponto_registros pr
            INNER JOIN users u ON pr.user_id = u.id
            WHERE 1=1
        ";

        $params = [];

        if ($dataInicio) {
            $query .= " AND DATE(pr.created_at) >= ?";
            $params[] = $dataInicio;
        }

        if ($dataFim) {
            $query .= " AND DATE(pr.created_at) <= ?";
            $params[] = $dataFim;
        }

        $result = DB::selectOne($query, $params);
        return (int) $result->total;
    }

    /**
     * Busca registros com paginação
     *
     * @param int $page
     * @param int $perPage
     * @param string|null $dataInicio
     * @param string|null $dataFim
     * @return array
     */
    public function buscarRegistrosPaginados(int $page = 1, int $perPage = 15, ?string $dataInicio = null, ?string $dataFim = null): array
    {
        $offset = ($page - 1) * $perPage;

        $query = "
            SELECT
                pr.id as registro_id,
                u.name as nome_funcionario,
                u.cargo,
                TIMESTAMPDIFF(YEAR, u.data_nascimento, CURDATE()) as idade,
                COALESCE(admin.name, 'N/A') as nome_gestor,
                DATE_FORMAT(pr.created_at, '%d/%m/%Y %H:%i:%s') as data_hora_completa,
                pr.created_at as timestamp_original
            FROM ponto_registros pr
            INNER JOIN users u ON pr.user_id = u.id
            LEFT JOIN users admin ON u.admin_id = admin.id
            WHERE 1=1
        ";

        $params = [];

        if ($dataInicio) {
            $query .= " AND DATE(pr.created_at) >= ?";
            $params[] = $dataInicio;
        }

        if ($dataFim) {
            $query .= " AND DATE(pr.created_at) <= ?";
            $params[] = $dataFim;
        }

        $query .= " ORDER BY pr.created_at DESC LIMIT ? OFFSET ?";
        $params[] = $perPage;
        $params[] = $offset;

        return DB::select($query, $params);
    }

    /**
     * Busca estatísticas dos registros por período
     *
     * @param string|null $dataInicio
     * @param string|null $dataFim
     * @return array
     */
    public function buscarEstatisticas(?string $dataInicio = null, ?string $dataFim = null): array
    {
        $query = "
            SELECT
                COUNT(*) as total_registros,
                COUNT(DISTINCT pr.user_id) as funcionarios_unicos,
                MIN(pr.created_at) as primeiro_registro,
                MAX(pr.created_at) as ultimo_registro
            FROM ponto_registros pr
            INNER JOIN users u ON pr.user_id = u.id
            WHERE 1=1
        ";

        $params = [];

        if ($dataInicio) {
            $query .= " AND DATE(pr.created_at) >= ?";
            $params[] = $dataInicio;
        }

        if ($dataFim) {
            $query .= " AND DATE(pr.created_at) <= ?";
            $params[] = $dataFim;
        }

        $result = DB::selectOne($query, $params);
        return (array) $result;
    }
}
