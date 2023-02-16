<?php

namespace App\Http\Controllers\DataWarehouse\Voalle;

use App\Http\Controllers\Controller;
use App\Models\DataWarehouse\Voalle\ContractServiceTag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContractsServiceTagsController extends Controller
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

        $query = 'select contract_id, service_tag, title, description, client_id, status, active from erp.contract_service_tags';

        $result = DB::connection('pgsql')->select($query);

        // return $result;

        $contract_type = new ContractServiceTag();

        $contract_type->truncate();


        foreach($result as $key => $value) {
            $contract_type->create([
                'contract_id' => $value->contract_id,
                'service_tag' => $value->service_tag,
                'title' => $value->title,
                'description' => $value->description,
                'client_id' => $value->client_id,
                'status' => $value->status,
                'active' => $value->active
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
