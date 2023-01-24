<?php

namespace App\Http\Controllers\AgeControl;

use App\Http\Controllers\Controller;
use App\Models\AgeControl\VehicleModality;
use Illuminate\Http\Request;

class VehicleModalityController extends Controller
{

    public function index(VehicleModality $vehicleModality)
    {
        return response()->json($vehicleModality->all(['id', 'modalidade']), 200);
    }


    public function create()
    {
        //
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
