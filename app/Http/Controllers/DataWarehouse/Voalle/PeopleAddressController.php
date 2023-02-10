<?php

namespace App\Http\Controllers\DataWarehouse\Voalle;

use App\Http\Controllers\Controller;
use App\Models\DataWarehouse\Voalle\PeopleAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PeopleAddressController extends Controller
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

        $query = 'select * from erp.people_addresses';

        $result = DB::connection('pgsql')->select($query);

        // return $result;

        $people = new PeopleAddress();

        $people->truncate();


        foreach($result as $key => $value) {
            $people->create([
                'people_address_id' => $value->id,
                'type' => $value->type,
                'street_type' => $value->street_type,
                'postal_code' => $value->postal_code,
                'street' => $value->street,
                'number' => $value->number,
                'address_complement' => $value->address_complement,
                'neighborhood' => $value->neighborhood,
                'city' => $value->city,
                'state' => $value->state,
                'country' => $value->country,
                'code_country' => $value->code_country,
                'address_reference' => $value->address_reference,
                'latitude' => $value->latitude,
                'longitude' => $value->longitude,
                'property_type' => $value->property_type,
                'created' => $value->created,
                'modified' => $value->modified,
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
