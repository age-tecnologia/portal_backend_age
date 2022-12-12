<?php

namespace App\Http\Controllers\DataWarehouse\Voalle;

use App\Http\Controllers\Controller;
use App\Models\DataWarehouse\Voalle\Contracts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContractsController extends Controller
{

    public function index()
    {
        //
    }


    public function create()
    {
        set_time_limit(2000);
        ini_set('memory_limit', '2048M');

        $query = 'select
                     id,
                     client_id,
                     contract_number,
                     description,
                     contract_type_id,
                     date,
                     beginning_date,
                     final_date,
                     billing_beginning_date,
                     billing_final_date,
                     collection_day,
                     cut_day,
                     seller_1_id,
                     seller_2_id,
                     amount,
                     status,
                     stage,
                     cancellation_date,
                     cancellation_motive,
                     approval_submission_date,
                     approval_date,
                     v_stage,
                     v_status,
                     v_invoice_type
                    from
                        erp.contracts';

        $result = DB::connection('pgsql')->select($query);

       // return $result;

        $contracts = new Contracts();

        $contracts->truncate();


        foreach($result as $key => $value) {
            $contracts->create([
                 'id_contract' => $value->id,
                 'client_id' => $value->client_id,
                 'contract_number' => $value->contract_number,
                 'description' => $value->description,
                 'contract_type_id' => $value->contract_type_id,
                 'date' => $value->date,
                 'beginning_date' => $value->beginning_date,
                 'final_date' => $value->final_date,
                 'billing_beginning_date' => $value->billing_beginning_date,
                 'billing_final_date' => $value->billing_final_date,
                 'collection_day' => $value->collection_day,
                 'cut_day' => $value->cut_day,
                 'seller_1_id' => $value->seller_1_id,
                 'seller_2_id' => $value->seller_2_id,
                 'amount' => $value->amount,
                 'status' =>$value->status,
                 'stage' => $value->stage,
                 'cancellation_date' => $value->cancellation_date,
                 'cancellation_motive' => $value->cancellation_motive,
                 'approval_submission_date' => $value->approval_submission_date,
                 'approval_date' => $value->approval_date,
                 'v_stage' => $value->v_stage,
                 'v_status' => $value->v_status,
                 'v_invoice_type' => $value->v_invoice_type
            ]);
        }

        return $contracts;

    }


    public function store(Request $request)
    {
        //
    }


    public function show($id)
    {
        //
    }


    public function edit($id)
    {
        //
    }


    public function update(Request $request, $id)
    {
        //
    }


    public function destroy($id)
    {
        //
    }
}
