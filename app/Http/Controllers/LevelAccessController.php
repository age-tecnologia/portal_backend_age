<?php

namespace App\Http\Controllers;

use App\Models\LevelAccess;
use Illuminate\Http\Request;

class LevelAccessController extends Controller
{

    public function index()
    {
        $levels = LevelAccess::all(['id', 'nivel']);

        return response()->json($levels, 201);
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
