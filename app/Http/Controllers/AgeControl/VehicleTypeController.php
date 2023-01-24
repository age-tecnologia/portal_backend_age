<?php

namespace App\Http\Controllers\AgeControl;

use App\Http\Controllers\Controller;
use App\Models\AgeControl\VehicleType;
use Illuminate\Http\Request;

class VehicleTypeController extends Controller
{

    public function index(VehicleType $vehicleType)
    {
        return response()->json($vehicleType->all(['id', 'tipo']), 200);
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
