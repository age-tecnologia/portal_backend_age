<?php

namespace App\Http\Controllers\AgeRv;

use App\Http\Controllers\AgeRv\_aux\sales\analytics\Coordenator;
use App\Http\Controllers\AgeRv\_aux\sales\analytics\Master;
use App\Http\Controllers\AgeRv\_aux\sales\analytics\NewSeller;
use App\Http\Controllers\AgeRv\_aux\sales\analytics\NewSupervisor;
use App\Http\Controllers\AgeRv\_aux\sales\analytics\Router;
use App\Http\Controllers\AgeRv\_aux\sales\analytics\Seller;
use App\Http\Controllers\AgeRv\_aux\sales\analytics\Supervisor;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Portal\_aux\PermissionBuilder;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesRulesController extends Controller
{
    public function index(Request $request)
    {

        $router = new PermissionBuilder();

        return $router->build();

        // Trás o nível de permissão do usuário (master, admin) e a função (Diretoria, gerente).
        $c = DB::table('agerv_usuarios_permitidos as up')
            ->leftJoin('portal_colaboradores_funcoes as cf', 'up.funcao_id', '=', 'cf.id')
            ->leftJoin('portal_users as u', 'up.user_id', '=', 'u.id')
            ->leftJoin('portal_nivel_acesso as na', 'u.nivel_acesso_id', '=', 'na.id')
            ->leftJoin('agerv_colaboradores as c', 'c.user_id', '=', 'u.id')
            ->leftJoin('portal_colaboradores_funcoes as cf2', 'c.funcao_id', '=', 'cf2.id')
            ->select('u.name', 'na.nivel', 'cf.funcao', 'cf2.funcao as funcao_collab', 'c.nome', 'c.id')
            ->where('u.id', auth()->user()->id)
            ->first();

            $this->year = $request->input('year') ? $request->input('year') : Carbon::now()->format('Y');
            $this->month = $request->input('month') ? $request->input('month') : Carbon::now()->format('m');
            $this->dashboard = $request->has('dashboard') ? $request->input('dashboard') : false;



        // Verifica o nível de acesso, caso se enquadre, permite o acesso máximo ou minificado.
        if($c->nivel === 'Master' ||
            $c->funcao === 'Diretoria' ||
            $c->funcao === 'Gerente geral' ||
            $c->funcao === 'Financeiro') {

            $master = new Master($this->month, $this->year);

            return $master->response();

        } elseif ($c->funcao === 'Supervisor' || $c->funcao_collab === 'Supervisor') {

            $supervisor = new NewSupervisor($this->month, $this->year, $c->nome, $c->id, $this->dashboard);

            return $supervisor->response();

        }  elseif ($c->funcao === 'Vendedor' || $c->funcao_collab === 'Vendedor') {

            $seller = new NewSeller($this->month, $this->year, $c->nome, $c->id, $this->dashboard);

            return $seller->response();

        }   elseif ($c->funcao === 'Coordenador') {

            $coordenator = new Coordenator($this->month, $this->year, $c->nome, $c->id, $this->dashboard);
            return $coordenator->response();

        }  else {
            return response()->json(["Unauthorized"], 401);
        }
    }

}
