<?php

namespace App\Http\Controllers;

use App\Models\SystemPermission;
use Illuminate\Http\Request;

class SystemPermissionController extends Controller
{

    public function index()
    {
        $user = auth()->user();

        $permission = SystemPermission::where('user_id', 13)->get();

        return $permission;

        if(! empty($permission)) {
            return response()->json([$permission], 201);
        } else {
            return response()->json(['Unauthorized'], 401);
        }
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
