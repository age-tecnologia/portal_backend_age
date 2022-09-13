<?php

namespace App\Http\Controllers\AgeRv;

use App\Http\Controllers\Controller;
use App\Models\AgeRv\Collaborator;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CollaboratorController extends Controller
{

    public function index()
    {
        $c = DB::table('agerv_colaboradores as c')
                ->leftJoin('portal_users as u', 'c.user_id', '=', 'u.id')
                ->leftJoin('portal_colaboradores_funcoes as f', 'c.funcao_id', '=', 'f.id')
                ->leftJoin('agerv_colaboradores_canais as cc', 'c.canal_id', '=', 'cc.id')
                ->leftJoin('agerv_colaboradores_canais as cc2', 'c.tipo_comissao_id', '=', 'cc2.id')
                ->leftJoin('agerv_colaboradores as c2', 'c.supervisor_id', '=', 'c2.id')
                ->leftJoin('portal_users as u2', 'c.gestor_id', '=', 'u2.id')
                ->selectRaw('c.id, c.nome as collaborator, u.name as username, f.funcao as `function`,
                            cc.canal as channel, cc2.canal as type_commission, c2.nome as supervisor,
                            u2.name as management, (SELECT meta FROM agerv_colaboradores_meta
                                                    WHERE colaborador_id = c.id
                                                    AND mes_competencia = '.Carbon::now()->format('m').') as meta')
                ->get();


        $c->each(function ($item) {
            $item->collaborator = mb_convert_case($item->collaborator, MB_CASE_TITLE, 'UTF-8');
            $item->username = mb_convert_case($item->username, MB_CASE_TITLE, 'UTF-8');
            $item->supervisor = mb_convert_case($item->supervisor, MB_CASE_TITLE, 'UTF-8');
            $item->management = mb_convert_case($item->management, MB_CASE_TITLE, 'UTF-8');
        });

        return response()->json($c, 201);
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
