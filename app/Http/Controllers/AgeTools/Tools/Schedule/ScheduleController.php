<?php

namespace App\Http\Controllers\AgeTools\Tools\Schedule;

use App\Exports\ReportExport;
use App\Http\Controllers\Controller;
use App\Models\AgeReport\Report;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Nette\Utils\Random;

class ScheduleController extends Controller
{

    public function getData(Request $request)
    {

        $query = $this->getQuery();

        // Verifica se um nome foi enviado
        if ($request->has('name')) {
            // Adiciona uma cláusula WHERE para filtrar pela região selecionada
            $query .= ' where LOWER(p.v_name) LIKE \'%'.mb_convert_case($request->name, MB_CASE_LOWER, 'UTF-8').'%\' limit 50';
        } else {



        // Adiciona uma cláusula WHERE para filtrar pela data da agenda
        $query .= ' WHERE (
        SELECT DATE(s.start_date)
        FROM erp.schedules s
        WHERE s.assignment_id = a.id
        ORDER BY s.id DESC
        LIMIT 1
    ) = \''.$request->dateSchedule.'\''; // 2021-05-10

        // Verifica se há algum tipo de nota selecionado
        if (!empty($request->typeNote)) {
            $multipleId = [];

            // Transforma os tipos de nota em um array de IDs inteiros
            foreach ($request->typeNote as $key => $value) {
                $multipleId[] = intval($value);
            }

            // Transforma o array de IDs em uma string separada por vírgulas
            $multipleId = implode(',', $multipleId);

            // Adiciona uma cláusula WHERE para filtrar pelos tipos de nota selecionados
            $query .= ' AND ai.incident_type_id IN ('.$multipleId.')';
        }

        // Verifica se uma região foi selecionada
        if ($request->region > 0) {
            // Adiciona uma cláusula WHERE para filtrar pela região selecionada
            $query .= ' AND a.region_id = '.$request->region;
        }

        }
        // Executa a consulta e obtém o resultado
        $result = DB::connection('pgsql')->select($query);

        // Retorna os resultados como uma resposta JSON
        return response()->json($result, 200);
    }

    public function downloadExcel(Request $request)
    {
        $headers = [];

        // Obtém os cabeçalhos para o arquivo Excel
        foreach ($request->headersExcel as $key => $value) {
            $headers[] = $value;
        }

        // Gera o arquivo Excel usando a biblioteca Maatwebsite\Excel
        return \Maatwebsite\Excel\Facades\Excel::download(new ReportExport($request->data, $headers), 'agenda.xlsx');
    }

    public function getFilters()
    {
        $data = [];

        // Obtém os tipos de notas
        $typeNotes = $this->getTypesNotes();

        foreach ($typeNotes as $key => $value) {
            $data['typeNotes'][] = $value;
        }

        // Obtém as regiões
        $regions = $this->getRegions();

        foreach ($regions as $key => $value) {
            $data['regions'][] = $value;
        }

        // Retorna os dados filtrados
        return $data;
    }

    private function getTypesNotes()
    {
        // Obtém os tipos de notas do banco de dados
        $result = DB::connection('pgsql')->select('select id, title from erp.incident_types order by title asc');

        return $result;
    }

    private function getRegions()
    {
        // Obtém as regiões do banco de dados
        $result = DB::connection('pgsql')->select('select id, title from erp.regions order by title asc');

        return $result;
    }


    private function getQuery()
    {
        $query = 'SELECT
                        ai.protocol as "protocol",
                        is2.title  AS "status",
                        it.title as "type_note",
                        t.title as "team",
                         (
                        SELECT
                        CASE
                            WHEN tech.v_name IS NULL THEN tech2.v_name
                            ELSE tech.v_name
                        END AS "Técnico"
                        FROM erp.reports r3
                        JOIN erp.assignments a2 ON r3.assignment_id = a2.id
                        LEFT JOIN erp.people tech ON tech.id = r3.person_id AND tech.technical IS TRUE
                        LEFT JOIN erp.people tech2 ON tech2.id = a.responsible_id AND tech2.technical IS TRUE
                        WHERE (tech.technical IS TRUE OR tech2.technical IS TRUE)
                        ORDER BY r3.id DESC
                        LIMIT 1
                        ) as "technical",
                        (
                        select r3.beginning_date  from erp.reports r3 left join erp.people tech on tech.id = r3.person_id
                        where r3.assignment_id = a.id and tech.technical is true order by r3.id desc limit 1
                        ) as "date_start_attendance",
                        (
                        select r3.final_date
                        from erp.reports r3 left join erp.people tech on tech.id = r3.person_id
                        where r3.assignment_id = a.id and tech.technical is true order by r3.id desc limit 1
                        ) as "date_end_attendance",
                        (
                        select s.start_date
                        from erp.schedules s
                        where s.assignment_id = a.id order by s.id desc limit 1
                        ) as "date_start_schedule",
                        (
                        select s.end_date
                        from erp.schedules s
                        where s.assignment_id = a.id order by s.id desc limit 1
                        ) as "date_end_schedule",
                        p.id as "id_client",
                        p.name AS "name_client",
                        c.id AS "contract_id",
                        c.v_stage  AS "stage_contract",
                        c.v_status  AS "status_contract",
                        sc.title as "context",
                        sp.title as "problem",
                        cpa.neighborhood  as "region"
                    FROM erp.assignment_incidents ai
                    left JOIN erp.assignments a ON a.id = ai.assignment_id
                    left join erp.teams t on t.id = ai.team_id
                    left JOIN erp.incident_types it ON it.id = ai.incident_type_id
                    left JOIN erp.contract_service_tags cst ON cst.id = ai.contract_service_tag_id
                    left join erp.incident_status is2 on is2.id = ai.incident_status_id
                    left JOIN erp.contracts c ON c.id = cst.contract_id
                    left JOIN erp.contract_types ct ON ct.id = c.contract_type_id
                    left JOIN erp.companies_places cp ON cp.id = c.company_place_id
                    left JOIN erp.people p ON p.id = ai.client_id
                    left join erp.regions r2 on r2.id = a.region_id
                    LEFT JOIN erp.authentication_contracts ac ON ac.service_tag_id = cst.id
                    LEFT JOIN erp.people_addresses cpa ON cpa.id = c.people_address_id
                    LEFT JOIN erp.people r ON r.id = a.responsible_id
                    left join erp.solicitation_problems sp on sp.id = ai.solicitation_problem_id
                    left join erp.solicitation_classifications sc on sc.id = ai.solicitation_classification_id';

        return $query;
    }


}
