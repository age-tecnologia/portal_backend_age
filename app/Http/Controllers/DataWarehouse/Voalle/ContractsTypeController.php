<?php

namespace App\Http\Controllers\DataWarehouse\Voalle;

use App\Http\Controllers\Controller;
use App\Models\DataWarehouse\Voalle\Contracts;
use App\Models\DataWarehouse\Voalle\ContractType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContractsTypeController extends Controller
{

    public function index()
    {
        //
    }


    public function create()
    {
        set_time_limit(2000);
        ini_set('memory_limit', '2048M');

        $query = 'select id, title from erp.contract_types';

        $result = DB::connection('pgsql')->select($query);

        // return $result;

        $contract_type = new ContractType();

        $contract_type->truncate();


        foreach($result as $key => $value) {
            $contract_type->create([
                'id_contract_type' => $value->id,
                'title' => $value->title
            ]);
        }

        return response()->json('Tabela atualizada com sucesso!');
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
