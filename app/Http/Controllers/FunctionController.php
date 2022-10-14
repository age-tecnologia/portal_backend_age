<?php

namespace App\Http\Controllers;

use App\Models\FunctionUser;
use Illuminate\Http\Request;

class FunctionController extends Controller
{

    public function index()
    {
        $functions = FunctionUser::all(['id', 'funcao']);

        return response()->json($functions, 201);
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
