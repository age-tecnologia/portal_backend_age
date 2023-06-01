<?php

namespace App\Http\Controllers\AgeTools\Tools\Schedule;

use App\Http\Controllers\Controller;
use App\Models\AgeReport\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ScheduleController extends Controller
{

    public function getData(Request $request)
    {


        $query = $this->getQuery();

        $query .= ' WHERE (
                        SELECT DATE(s.start_date)
                        FROM erp.schedules s
                        WHERE s.assignment_id = a.id
                        ORDER BY s.id DESC
                        LIMIT 1
                    ) = \''.$request->dateSchedule.'\''; // 2021-05-10


        if($request->typeNote > 0) {
            $query .= ' AND ai.incident_type_id = '.$request->typeNote;
        }

        if($request->region > 0) {
            $query .= ' AND a.region_id = '.$request->region;
        }


        $result = DB::connection('pgsql')->select($query);

        return response()->json($result, 200);

    }



    public function getFilters()
    {
        $data = [];

        $typeNotes = $this->getTypesNotes();

        foreach($typeNotes as $key => $value) {
            $data['typeNotes'][] = $value;
        }

        $regions = $this->getRegions();

        foreach($regions as $key => $value) {
            $data['regions'][] = $value;
        }

        return $data;
    }

    private function getTypesNotes()
    {

        $result = DB::connection('pgsql')->select('select id, title from erp.incident_types order by title asc');

        return $result;
    }

    private function getRegions()
    {
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
                        select tech.v_name
                        from erp.reports r3
                        left join erp.people tech on tech.id = r3.person_id
                        where r3.assignment_id = a.id and tech.technical is true order by r3.id desc limit 1
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
                        p.name AS "name_client",
                        c.id AS "contract_id",
                        c.v_stage  AS "stage_contract",
                        c.v_status  AS "status_contract",
                        sc.title as "context",
                        sp.title as "problem"
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
