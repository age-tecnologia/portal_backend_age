<?php

namespace App\Http\Controllers\DataWarehouse\Voalle;

use App\Http\Controllers\Controller;
use App\Models\DataWarehouse\Voalle\ContractAssignmentActivations;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContractAssignmentActivationsController extends Controller
{

    public function __invoke()
    {
        $this->create();
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
        set_time_limit(2000);
        ini_set('memory_limit', '2048M');

        $query = 'select * from erp.contract_assignment_activations';

        $result = DB::connection('pgsql')->select($query);

        $contract_assignments = new ContractAssignmentActivations();

        $contract_assignments->truncate();


        foreach($result as $key => $value) {
            $contract_assignments->create([
                'contract_id' => $value->contract_id,
                'assignment_id' => $value->assignment_id,
                'person_id' => $value->person_id,
                'activation_date' => $value->activation_date,
                'invoice_note_id' => $value->invoice_note_id,
                'created' => $value->created,
                'modified' => $value->modified,
                'created_by' => $value->created_by,
                'modified_by' => $value->modified_by
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
