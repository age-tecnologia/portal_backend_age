<?php

namespace App\Http\Controllers\AgeBoard;

use App\Http\Controllers\Controller;
use App\Models\AgeBoard\Dashboard;
use App\Models\AgeBoard\DashboardPermitted;
use Illuminate\Http\Request;

class PermmittedsDashboardController extends Controller
{
    public function index()
    {
        $dashboard = Dashboard::all();
        $dashboardPermitted = DashboardPermitted::all();

        return [
            $dashboard,
            $dashboardPermitted
        ];
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
