<?php

namespace App\Http\Controllers\AgeRv;

use App\Http\Controllers\Controller;
use App\Models\AgeRv\AccessPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesAnalyticController extends Controller
{
    public function index()
    {

        // Trás o nível de permissão do usuário (master, admin) e a função (Diretoria, gerente).
        $c = DB::table('agerv_usuarios_permitidos as up')
                            ->leftJoin('portal_colaboradores_funcoes as cf', 'up.funcao_id', '=', 'cf.id')
                            ->leftJoin('portal_users as u', 'up.user_id', '=', 'u.id')
                            ->leftJoin('portal_nivel_acesso as na', 'u.nivel_acesso_id', '=', 'na.id')
                            ->select('u.name', 'na.nivel', 'cf.funcao')
                            ->where('u.id', auth()->user()->id)
                            ->first();

        // Verifica o nível de acesso, caso se enquadre, permite o acesso máximo ou minificado.
        if($c->nivel === 'Master' ||
            $c->funcao === 'Diretoria' ||
            $c->funcao === 'Gerente Geral') {

            return $this->master();

        } else {
            return response()->json(["Unauthorized"], 401);
        }

    }

    /*
     * Retorna todos os dados de vendas disponíveis.
     */
    private function master() {





        return [
            'channels' => [
                0 => [
                    'channel' => 'MCV',
                    'salesTotal' => 0,
                    'salesCancelled' => 0,
                    'salesCancelledD7' => 0,
                    'salesValid' => 0,
                    'starsTotal' => 0,
                    'commissionTotal' => 0,
                ]
            ],
            'salesTotal' => 0,
            'salesCancelled' => 0,
            'salesCancelledD7' => 0,
            'salesValid' => 0,
            'starsTotal' => 0,
            'commissionTotal' => 0
        ];

    }
}
