<?php

namespace App\Http\Controllers\DataWarehouse\Voalle;

use App\Http\Controllers\Controller;
use App\Models\DataWarehouse\Voalle\RequestAndBreak;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RequestAndBreaksController extends Controller
{

    public function __invoke()
    {
        return $this->create();
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }


    public function create()
    {

        set_time_limit(200000);

        $query = '
        select
            client.id as "id_client",
            client."name" as "client_name",
            (select id from erp.contracts c where c.client_id = client.id limit 1) as "id_contract",
            (select c.v_stage from erp.contracts c where c.client_id = client.id limit 1) as "stage_contract",
            (select c.v_status from erp.contracts c where c.client_id = client.id limit 1) as "status_contract",
            (select c.created from erp.contracts c where c.client_id = client.id limit 1) as "date_created_contract",
            (select c.approval_date  from erp.contracts c where c.client_id = client.id limit 1) as "date_approval_contract",
            (
            select ac."user"  from erp.contracts c
            left join erp.authentication_contracts ac on ac.contract_id = c.id
            where c.client_id = client.id
            limit 1) as "connection",
            it.title  as "type_assignment",
            is2.title as "status_assignment",
            tags as "protocol",
            t.title as "team",
            p."name" as "responsible_name",
            r.description as "description",
            s.beginning_date as "date_beginning_assignment",
            s.final_date  as "date_final_assignment",
            r.beginning_date as "date_beginning_report",
            r.final_date as "date_final_report",
            r.seconds_worked as "time_report",
            sc.title as "context",
            sp.title as "problem"
            from erp.reports r
            left join erp.teams t on t.id = team_id
            left join erp.people p on r.person_id = p.id
            left join erp.assignments s on s.id = r.assignment_id
            left join erp.people client on s.requestor_id = client.id
            left join erp.assignment_incidents ai on ai.assignment_id = s.id
            left join erp.incident_types it on it.id = ai.incident_type_id
            left join erp.incident_status is2 on ai.incident_status_id = is2.id
            left join erp.solicitation_problems sp on sp.id = ai.solicitation_problem_id
            left join erp.solicitation_classifications sc on sc.id = ai.solicitation_classification_id
            where r.type = 2';


        $result = DB::connection('pgsql')->select($query);

        // return $result;

        $request = new RequestAndBreak();

        $request->truncate();


        foreach($result as $key => $value) {

            $request->create([
                'id_client' => $value->id_client,
                'client_name' => $value->client_name,
                'id_contract' => $value->id_contract,
                'stage_contract' => $value->stage_contract,
                'status_contract' => $value->status_contract,
                'date_created_contract' => $value->date_created_contract,
                'date_approval_contract' => $value->date_approval_contract,
                'connection' => $value->connection,
                'type_assignment' => $value->type_assignment,
                'status_assignment' => $value->status_assignment,
                'protocol' => $value->protocol,
                'team' => $value->team,
                'responsible_name' => $value->responsible_name,
                'description' => $value->description,
                'date_beginning_assignment' => $value->date_beginning_assignment,
                'date_final_assignment' => $value->date_final_assignment,
                'date_beginning_report' => $value->date_beginning_report,
                'date_final_report' => $value->date_final_report,
                'time_report' => $value->time_report,
                'context' => $value->context,
                'problem' => $value->problem,
        ]);

        }

        return response()->json('Tabela atualizada com sucesso!');



    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
