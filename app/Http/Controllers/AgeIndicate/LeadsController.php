<?php

namespace App\Http\Controllers\AgeIndicate;

use App\Http\Controllers\Controller;
use App\Models\AgeIndicate\Lead;
use Illuminate\Http\Request;

class LeadsController extends Controller
{

    public function index()
    {
        //
    }


    public function create()
    {
        //
    }


    public function store(Request $request)
    {
        $name = $request->input('name');
        $tel = $request->input('tel');
        $address = $request->input('address');

        $lead = new Lead();

        $lead = $lead->create([
            'nome_cliente' => $name,
            'telefone_cliente' => $tel,
            'endereco_cliente' => $address
        ]);

        if(isset($lead->id)) {
            return true;
        } else {
            return false;
        }

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
