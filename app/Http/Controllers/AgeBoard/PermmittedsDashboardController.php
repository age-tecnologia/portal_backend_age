<?php

namespace App\Http\Controllers\AgeBoard;

use App\Http\Controllers\Controller;
use App\Models\AgeBoard\Dashboard;
use App\Models\AgeBoard\DashboardPermitted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PermmittedsDashboardController extends Controller
{
    public function index()
    {
//
        $dashPermitted = DB::table('ageboard_dashboards as d')
                            ->leftJoin('ageboard_dashboard_permissoes as dp', 'd.id', '=', 'dp.dashboard_id')
                            ->where('dp.user_id', auth()->user()->id)
                            ->get(['d.id', 'd.dashboard']);

        $result = [];

        foreach($dashPermitted as $item => $value) {
            $result[] = [
                'id' => $value->id,
                'dashboard' => $value->dashboard,
                'itens' => $this->itensPermmitteds($value->id)
            ];
        }


        return $result;
    }

    public function itensPermmitteds($id)
    {
        $itemPermitted = DB::table('ageboard_dashboards_itens as di')
            ->leftJoin('ageboard_dashboards_itens_permissoes as dip', 'di.id', '=', 'dip.item_id')
            ->where('dip.dashboard_id', $id)
            ->where('dip.user_id', auth()->user()->id)
            ->get(['di.id', 'di.item', 'di.iframe']);


        return $itemPermitted;
    }

    public function create()
    {

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
