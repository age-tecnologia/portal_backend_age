<?php

namespace App\Http\Controllers\DataWarehouse\Voalle;

use App\Http\Controllers\Controller;
use App\Models\DataWarehouse\Voalle\ContractType;
use App\Models\DataWarehouse\Voalle\People;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PeoplesController extends Controller
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

        $query = 'select id, name, type_tx_id, tx_id, email, cell_phone_1, street,neighborhood, city, postal_code from erp.people';

        $result = DB::connection('pgsql')->select($query);

        // return $result;

        $people = new People();

        $people->truncate();


        foreach($result as $key => $value) {
            $people->create([
                'id_people' => $value->id,
                'type_tx_id' => $value->type_tx_id,
                'email' => $value->email,
                'tel' => $value->cell_phone_1,
                'tx_id' => $value->tx_id,
                'name' => $value->name,
                'street' => $value->street,
                'neighborhood' => $value->neighborhood,
                'city' => $value->city,
                'postal_code' => $value->postal_code
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
