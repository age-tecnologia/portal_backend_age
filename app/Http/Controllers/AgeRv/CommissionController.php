<?php

namespace App\Http\Controllers\AgeRv;

use App\Http\Controllers\AgeRv\_aux\sales\analytics\Master;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CommissionController extends Controller
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
        $data = new Master('08', '2022');

        $data = $data->response();



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
