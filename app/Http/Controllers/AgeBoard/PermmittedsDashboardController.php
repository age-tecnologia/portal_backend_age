<?php

namespace App\Http\Controllers\AgeBoard;

use App\Http\Controllers\AccessSystemsController;
use App\Http\Controllers\Controller;
use App\Models\AgeBoard\AccessPermission;
use App\Models\AgeBoard\Dashboard;
use App\Models\AgeBoard\DashboardPermitted;
use App\Models\AgeBoard\Item;
use App\Models\AgeBoard\ItemPermitted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PermmittedsDashboardController extends Controller
{

    private $level;

    public function index()
    {
//
        $this->level = auth()->user()->nivel_acesso_id;


        if($this->level === 2 || $this->level === 3) {
            $dashPermitted = Dashboard::all(['id', 'dashboard']);

            foreach($dashPermitted as $item => $value) {
                $result[] = [
                    'id' => $value->id,
                    'dashboard' => $value->dashboard,
                    'itens' => $this->itemsPermmitteds($value->id)
                ];
            }

        } else {
            $dashPermitted = DB::table('ageboard_dashboards as d')
                ->leftJoin('ageboard_dashboard_permissoes as dp', 'd.id', '=', 'dp.dashboard_id')
                ->where('dp.user_id', auth()->user()->id)
                ->get(['d.id', 'd.dashboard']);
        }

        $result = [];

        foreach($dashPermitted as $item => $value) {
            $result[] = [
                'id' => $value->id,
                'dashboard' => $value->dashboard,
                'itens' => $this->itemsPermmitteds($value->id)
            ];
        }


        return $result;
    }

    public function itemsPermmitteds($id)
    {

        if($this->level === 2 || $this->level === 3) {
            $itemPermitted = Item::where('dashboard_id', $id)->get(['id', 'item', 'iframe']);

        } else {
            $itemPermitted = DB::table('ageboard_dashboards_itens as di')
                ->leftJoin('ageboard_dashboards_itens_permissoes as dip', 'di.id', '=', 'dip.item_id')
                ->where('dip.dashboard_id', $id)
                ->where('dip.user_id', auth()->user()->id)
                ->get(['di.id', 'di.item', 'di.iframe']);
        }


        return $itemPermitted;
    }

    public function itemsPermittedsAndNot(Request $request)
    {

        $itemsPermitteds = ItemPermitted::whereUserId($request->input('userId'))->get(['item_id']);

        $data = [];


        $itemsNotPermitteds = Item::whereDashboardId($request->input('dashboardId'))
                                ->whereNotIn('id', $itemsPermitteds)
                                ->get(['id', 'item', 'iframe']);

        $itemsPermitteds = Item::whereIn('id', $itemsPermitteds)->get(['id', 'item', 'iframe']);

        foreach($itemsPermitteds as $key => $value) {
            $data[] = [
                'id' => $value->id,
                'item' => $value->item,
                'iframe' => $value->iframe,
                'status' => true
            ];
        }

        foreach($itemsNotPermitteds as $key => $value) {
            $data[] = [
                'id' => $value->id,
                'item' => $value->item,
                'iframe' => $value->iframe,
                'status' => false
            ];
        }

        return $data;

    }

    public function itemsAlternateAccess(Request $request)
    {
        $user = AccessPermission::whereUserId($request->input('idUser'))->withTrashed()->first();

        // Criar acesso ao AgeBoard
        if(! isset($user->id)) {
            $user = \App\Models\AgeBoard\AccessPermission::create([
                'user_id' => $request->input('idUser'),
                'funcao_id' => 2,
                'setor_id' => 9,
                'nivel_acesso_id' => 1
            ]);

        } else {

            if(isset($user->deleted_at)) {

                $user = $user->restore();

            }
        }

        $item = Item::whereId($request->input('idItem'))->first(['dashboard_id']);

        $dashboardAccess = DashboardPermitted::whereUserId($request->input('idUser'))->first();

        // Liberar Dashboard
        if(! isset($dashboardAccess->id)) {
            $dashboardAccess = \App\Models\AgeBoard\DashboardPermitted::create([
                'user_id' => $request->input('idUser'),
                'dashboard_id' => $item->dashboard_id,
                'permitido_por' => auth()->user()->id,
            ]);

        }


        $itemAccess = ItemPermitted::whereUserId($request->input('idUser'))->whereItemId($request->input('idItem'))->first();

        // Liberar item do dashboard
        if(! isset($itemAccess->id)) {
            $itemAccess = \App\Models\AgeBoard\ItemPermitted::create([
                'user_id' => $request->input('idUser'),
                'dashboard_id' => $item->dashboard_id,
                'item_id' => $request->input('idItem'),
                'criado_por' => auth()->user()->id,
                'modificado_por' => auth()->user()->id,
            ]);

            return response()->json(['msg' => 'Dashboard liberado com sucesso.', 'access' => true], 201);

        } else {

            $itemAccess = ItemPermitted::whereUserId($request->input('idUser'))->whereItemId($request->input('idItem'))->delete();

            return response()->json(['msg' => 'Dashboard bloqueado com sucesso.', 'access' => false], 201);

        }


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
