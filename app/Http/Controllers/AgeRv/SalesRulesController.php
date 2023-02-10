<?php

namespace App\Http\Controllers\AgeRv;

use App\Http\Controllers\AgeRv\_aux\sales\analytics\Master;
use App\Http\Controllers\AgeRv\_aux\sales\analytics\NewSeller;
use App\Http\Controllers\AgeRv\_aux\sales\analytics\Seller;
use App\Http\Controllers\AgeRv\_aux\sales\analytics\Supervisor;
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
            ->leftJoin('agerv_colaboradores as c', 'c.user_id', '=', 'u.id')
            ->select('u.name', 'na.nivel', 'cf.funcao', 'c.nome', 'c.id')
            ->where('u.id', auth()->user()->id)
            ->first();


        $this->year = $request->input('year') ? $request->input('year') : Carbon::now()->format('Y');
        $this->month = $request->input('month') ? $request->input('month') : Carbon::now()->format('m');


        // Verifica o nível de acesso, caso se enquadre, permite o acesso máximo ou minificado.
        if($c->nivel === 'Master' ||
            $c->funcao === 'Diretoria' ||
            $c->funcao === 'Gerente geral' ||
            $c->funcao === 'Financeiro') {

            $master = new Master($this->month, $this->year);

            return $master->response();

        } elseif ($c->funcao === 'Supervisor') {

            $supervisor = new Supervisor($this->month, $this->year, $c->nome, $c->id);

            return $supervisor->response();

        }  elseif ($c->funcao === 'Vendedor') {

            $seller = new Seller($this->month, $this->year, $c->nome, $c->id);

            return $seller->response();

        } else {
            return response()->json(["Unauthorized"], 401);
        }
    }

}
