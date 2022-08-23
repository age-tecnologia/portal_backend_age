<?php

namespace App\Http\Controllers;

use App\Models\SystemPermission;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SystemPermissionController extends Controller
{

    public function index(Request $request)
    {
        // Recupera o usuário autenticado.
        $user = auth()->user();

        // Recupera o nome do sistema solicitado pela request.
        $sysName = $request->only('sysName');

        // Busca a permissão no banco pra validar se o usuário pode ter acesso.
        $permission = DB::table('portal_sistema_permissoes as sp')
                            ->leftJoin('portal_sistemas as s', 'sp.sistema_id', '=', 's.id')
                            ->select('s.sistema')
                            ->where('user_id', $user->id)
                            ->where('s.sistema', $sysName)
                            ->get();

        if(count($permission) > 0) {
            // Se o resultado for maior que zero, ele permite o acesso a página
            // Os dados retornados são com o nome do sistema, o tratamento de qual página pode ser acessada,
            // está no front-end em middlewares.
            return response()->json(['Authorized'], 201);
        } else {
            // Se não houver nenhum registro em banco, o usuário não está autorizado a continuar.
            return response()->json(['Unauthorized'], 401);
        }
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
