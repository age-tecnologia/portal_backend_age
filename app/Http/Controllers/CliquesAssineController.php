<?php

namespace App\Http\Controllers;

use App\Models\CliqueAssine;
use Illuminate\Http\Request;

class CliquesAssineController extends Controller
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
        $ip = $_SERVER['REMOTE_ADDR'];

        $click = CliqueAssine::whereIp($ip)->get()->count();

        if($click < 5) {
            $click = new CliqueAssine();

            $click = $click->create([
                'ip' => $ip,
                'clique_em' => $request->input('plan')
            ]);

            if(isset($click->ip)) {
                return response()->json('sucess', 200);
            }
        }

        return response()->json('failed', 403);
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
