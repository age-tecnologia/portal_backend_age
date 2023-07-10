<?php

namespace App\Http\Controllers\AgeTools\Tools\Schedule;

use App\Exports\ReportExport;
use App\Http\Controllers\Controller;
use App\Models\AgeReport\Report;
use App\Models\AgeTools\Tools\Schedule\Note\Executed;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Nette\Utils\Random;

class ScheduleController extends Controller
{

    public function getData(Request $request)
    {

        $query = $this->getQuery2();

        // Verifica se um nome foi enviado
        if ($request->has('name')) {
            // Adiciona uma cláusula WHERE para filtrar pela região selecionada
            $query .= ' where LOWER(p.v_name) LIKE \'%'.mb_convert_case($request->name, MB_CASE_LOWER, 'UTF-8').'%\' order by ai.protocol asc limit 50';
        } else {



        // Adiciona uma cláusula WHERE para filtrar pela data da agenda
        $query .= ' where DATE(s.start_date) = \''.$request->dateSchedule.'\''; // 2021-05-10

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
            $query .= 'and tech.technical is false and c.v_stage != \'Cancelado\'
                        and sc.title != \'CONCLUÍDA\' and is2.title != \'Encerramento\' ORDER BY ai.protocol ASC';

        }


        // Executa a consulta e obtém o resultado
        $result = DB::connection('pgsql')->select($query);

        $result = $this->checkExecutedNote($result);

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

    private function checkExecutedNote($data)
    {
        $executedNote = new Executed();

        foreach($data as $key => $value) {
            $result = $executedNote->whereProtocolo($value->protocol)->first();

            if(isset($result->id)) {
                $data[$key]->executed = true;
            } else {
                $data[$key]->executed = false;
            }
        }

        return $data;
    }

    private function getQuery()
    {
        $query = 'select
                ai.protocol as "protocol",
                it.title as "type_note",
                p.name AS "name_client",
                CASE
                    WHEN EXTRACT(HOUR FROM s.start_date) < 12 THEN \'Manhã\'
                    WHEN EXTRACT(HOUR FROM s.start_date) >= 12 AND EXTRACT(HOUR FROM s.start_date) < 18 THEN \'Tarde\'
                    ELSE \'Noite\'
                END AS "Turn",
                cpa.neighborhood  as "region",
                c.id AS "contract_id",
                t.title as "team",
                case
                    when tech.technical is true then tech."name"
                    else null
                end as "technical",
                (
                    select report.beginning_date from erp.reports report
                    where report.assignment_id = a.id
                    and tech.technical is true and tech.id = report.person_id
                    limit 1
                ) as "date_start_attendance",
                (
                    select report.final_date  from erp.reports report
                    where report.assignment_id = a.id
                    and tech.technical is true and tech.id = report.person_id
                    limit 1
                ) as "date_end_attendance",
                s.start_date as "date_start_schedule",
                s.end_date as "date_end_schedule",
                is2.title  AS "status",
                c.v_stage  AS "stage_contract",
                c.v_status  AS "status_contract",
                sc.title as "context",
                sp.title as "problem",
                p.phone as "Telefone",
                p.cell_phone_1 as "Celular"
            from erp.schedules s
            left join erp.people tech on tech.id = s.person_id
            left join erp.assignment_incidents ai on ai.assignment_id = s.assignment_id
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

    private function getQuery2()
    {

        $query = '
                select
                distinct(p."name") as "name_client",
                ai.protocol as "protocol",
                        it.title as "type_note",
                        CASE
                            WHEN EXTRACT(HOUR FROM s.start_date) < 12 THEN \'Manhã\'
                            WHEN EXTRACT(HOUR FROM s.start_date) >= 12 AND EXTRACT(HOUR FROM s.start_date) < 18 THEN \'Tarde\'
                            ELSE \'Noite\'
                        END AS "Turn",
                        cpa.neighborhood  as "region",
                        t.title as "team",
                        s.start_date as "date_start_schedule",
                        s.end_date as "date_end_schedule",
                        is2.title  AS "status",
                        c.id as "contract_id",
                        c.v_stage  AS "stage_contract",
                        c.v_status  AS "status_contract",
                        sc.title as "context",
                        sp.title as "problem",
                        p.phone as "Telefone",
                        p.cell_phone_1 as "Celular"
                        from erp.schedules s
                        left join erp.assignment_incidents ai on ai.assignment_id = s.assignment_id
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
                        left join erp.people tech on tech.id = a.responsible_id
                        left join erp.solicitation_problems sp on sp.id = ai.solicitation_problem_id
                        left join erp.solicitation_classifications sc on sc.id = ai.solicitation_classification_id';

        return $query;

    }


}
