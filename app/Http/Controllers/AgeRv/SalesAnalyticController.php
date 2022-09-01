<?php

namespace App\Http\Controllers\AgeRv;

use App\Http\Controllers\Controller;
use App\Models\AgeRv\AccessPermission;
use App\Models\AgeRv\Channel;
use App\Models\AgeRv\Collaborator;
use App\Models\AgeRv\VoalleSales;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesAnalyticController extends Controller
{

    private $channels;
    private $dataChannels;
    private string $month;
    private string $year;
    private $salesTotals;
    private int $salesTotalsCount = 0;
    private int $salesCancelledsCount = 0;
    private int $salesCancelledsD7Count = 0;
    private int $salesBaseCount = 0;


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

        $this->year = '2022';
        $this->month = '08';

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
            'channels' => $this->channels(),
            'salesTotal' => $this->salesTotalsCount,
            'salesCancelled' => $this->salesCancelledsCount,
            'salesCancelledD7' => $this->salesCancelledsD7Count,
            'salesBase' => $this->salesBaseCount,
            'starsTotal' => 0,
            'commissionTotal' => 0
        ];

    }

    private function channels() {

        $this->channels = Channel::select('id','canal')
                                ->where('canal', '<>', 'lider')
                                ->get();

        $this->dataChannels = [];

        foreach ($this->channels as $c => $value) {

            $supervisors = Collaborator::where('funcao_id', 3)
                ->where('canal_id', $value->id)
                ->select('nome')
                ->get();

            $this->dataChannels[] = [
                'canal' => $value->canal,
                'salesTotal' => $this->salesTotalsChannels($supervisors),
                'salesCancelled' => $this->salesCancelleds(),
                'salesBase' => $this->salesBase()
            ];
        }

        return $this->dataChannels;
    }

    private function salesTotalsChannels($supervisors)
    {

        // Trás a contagem de todas as vendas realizadas no mês filtrado.
        $this->salesTotals = VoalleSales::whereMonth('data_vigencia', $this->month)
            ->whereYear('data_vigencia', $this->year)
            ->whereMonth('data_ativacao', $this->month)
            ->whereYear('data_ativacao', $this->year)
            ->whereMonth('data_contrato', '>=', '06')
            ->whereYear('data_contrato', $this->year)
            ->whereIn('supervisor', $supervisors)
            ->get();

        $this->salesTotalsCount += count($this->salesTotals);

        return [
            'extract' => 0, //$salesTotals,
            'count' => count($this->salesTotals)
        ];
    }

    private function salesCancelleds() {

        $cancelleds = $this->salesTotals->filter(function ($sale) {
            if($sale->situacao === 'Cancelado') {
                return $sale;
            }
        })->all();

        $d7 = $this->salesTotals->filter(function ($sale) {
            if($sale->situacao === 'Cancelado') {

                $dateActivation = Carbon::parse($sale->data_ativacao); // Transformando em data.
                $dateCancel = Carbon::parse($sale->data_cancelamento); // Transformando em data.

                // Verificando se o cancelamento foi em menos de 7 dias, se sim, atualiza o banco com inválida.
                if ($dateActivation->diffInDays($dateCancel) < 7) {
                    return $sale;
                }
            }
        });

        $this->salesCancelledsCount += count($cancelleds);
        $this->salesCancelledsD7Count += count($d7);

        return [
            'count' => count($cancelleds),
            'D7' => [
                'count' => count($d7),
                'extract' => 0 // $d7
            ]
        ];

    }

    private function salesBase() {

        $salesValids = $this->salesTotals->filter(function ($sale) {

            if($sale->situacao !== 'Cancelado') {

              return $sale;

            }

        });

        $this->salesBaseCount += count($salesValids);

        return count($salesValids);

    }
}
