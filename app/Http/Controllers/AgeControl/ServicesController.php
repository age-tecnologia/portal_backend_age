<?php

namespace App\Http\Controllers\AgeControl;

use App\Http\Controllers\Controller;
use App\Models\AgeControl\Service;
use App\Models\AgeControl\TypeService;
use Illuminate\Http\Request;

class ServicesController extends Controller
{

    public function index()
    {
        $service = Service::all(['id', 'servico']);

        return response()->json($service, 200);
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
