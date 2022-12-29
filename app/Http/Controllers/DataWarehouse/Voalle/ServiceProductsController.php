<?php

namespace App\Http\Controllers\DataWarehouse\Voalle;

use App\Http\Controllers\Controller;
use App\Models\DataWarehouse\Voalle\ContractType;
use App\Models\DataWarehouse\Voalle\ServiceProducts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ServiceProductsController extends Controller
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

        $query = 'select id, title from erp.service_products';

        $result = DB::connection('pgsql')->select($query);

        // return $result;

        $service_product = new ServiceProducts();

        $service_product->truncate();


        foreach($result as $key => $value) {
            $service_product->create([
                'id_service_product' => $value->id,
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
