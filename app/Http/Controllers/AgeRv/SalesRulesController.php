<?php

namespace App\Http\Controllers\AgeRv;

use App\Http\Controllers\AgeRv\_aux\sales\analytics\Master;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesRulesController extends Controller
{
    public function index(Request $request)
    {
        // Trás o nível de permissão do usuário (master, admin) e a função (Diretoria, gerente).
        $c = DB::table('agerv_usuarios_permitidos as up')
            ->leftJoin('portal_colaboradores_funcoes as cf', 'up.funcao_id', '=', 'cf.id')
            ->leftJoin('portal_users as u', 'up.user_id', '=', 'u.id')
            ->leftJoin('portal_nivel_acesso as na', 'u.nivel_acesso_id', '=', 'na.id')
            ->select('u.name', 'na.nivel', 'cf.funcao')
            ->where('u.id', 1)
            ->first();


        $this->year = $request->input('year') ? $request->input('year') : Carbon::now()->format('Y');
        $this->month = $request->input('month') ? $request->input('month') : Carbon::now()->format('m');


        // Verifica o nível de acesso, caso se enquadre, permite o acesso máximo ou minificado.
        if($c->nivel === 'Master' ||
            $c->funcao === 'Diretoria' ||
            $c->funcao === 'Gerente geral') {

            $master = new Master($this->month, $this->year);

            return $master->response();

        } elseif ($c->funcao === 'Supervisor') {
            return $this->supervisor();
        } elseif($c->funcao === 'Gerente') {
            return $this->management();
        } else {
            return response()->json(["Unauthorized"], 401);
        }
    }


}
