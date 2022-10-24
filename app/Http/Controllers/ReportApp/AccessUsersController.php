<?php

namespace App\Http\Controllers\ReportApp;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccessUsersController extends Controller
{

    public function index()
    {
        $users = DB::table('agereport_usuarios_permitidos as up')
                ->leftJoin('portal_users as u', 'u.id', '=', 'up.user_id')
                ->leftJoin('portal_nivel_acesso as na', 'na.id', '=', 'up.nivel_acesso_id')
                ->leftJoin('portal_colaboradores_funcoes as f', 'up.funcao_id', '=', 'f.id')
                ->leftJoin('portal_colaboradores_setores as s', 'up.setor_id', '=', 's.id')
                ->get(['u.id','u.name', 'u.email', 'na.nivel', 'f.funcao', 's.setor']);

        return $users;
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
