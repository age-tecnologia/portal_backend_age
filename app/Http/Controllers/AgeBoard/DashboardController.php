<?php

namespace App\Http\Controllers\AgeBoard;

use App\Http\Controllers\Controller;
use App\Models\AgeBoard\Dashboard;
use Illuminate\Http\Request;

class DashboardController extends Controller
{

    public function index()
    {
        $dashboards = Dashboard::all(['id', 'dashboard']);

        return $dashboards;
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $dashboard = new Dashboard();

        $dashboardExist = Dashboard::whereDashboard($request->input('dashboard'))->first();

        if(isset($dashboardExist->id)) {

            return response()->json(['msg' => 'Já existe um dashboard com esse nome!'], 200);

        } else {

            $dashboard->firstOrCreate([
                'dashboard' => $request->input('dashboard')
            ]);

            return response()->json(['msg' => 'Dashboard criado com sucesso!'], 201);

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
