<?php

namespace App\Http\Controllers\DataWarehouse\Voalle;

use App\Http\Controllers\Controller;
use App\Models\DataWarehouse\Voalle\AuthenticationContracts;
use App\Models\DataWarehouse\Voalle\ContractType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuthenticationContractsController extends Controller
{

    public function __invoke()
    {
        $this->create();
    }

    public function index()
    {
        //
    }


    public function create()
    {
        set_time_limit(2000);
        ini_set('memory_limit', '2048M');

        $query = 'select id, contract_id, service_product_id, "user" from erp.authentication_contracts';

        $result = DB::connection('pgsql')->select($query);

        // return $result;

        $auth_contracts = new AuthenticationContracts();

        $auth_contracts->truncate();


        foreach($result as $key => $value) {
            $auth_contracts->create([
                'id_authentication_contract' => $value->id,
                'contract_id' => $value->contract_id,
                'service_product_id' => $value->service_product_id,
                'user' => $value->user
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
