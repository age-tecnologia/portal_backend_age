<?php

namespace App\Http\Controllers\AgeRv\Management;

use App\Http\Controllers\Controller;
use App\Models\AgeRv\Channel;
use App\Models\AgeRv\Collaborator;
use App\Models\AgeRv\VoalleSales;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SimulatorController extends Controller
{

    private $channels;
    private $channel;
    private $salesData;
    private $salesCollaborator;
    private $salesChannel;


    public function index(Request $request)
    {
        // Trás o nível de permissão do usuário (master, admin) e a função (Diretoria, gerente).
        $c = DB::table('agerv_usuarios_permitidos as up')
            ->leftJoin('portal_colaboradores_funcoes as cf', 'up.funcao_id', '=', 'cf.id')
            ->leftJoin('portal_users as u', 'up.user_id', '=', 'u.id')
            ->leftJoin('portal_nivel_acesso as na', 'u.nivel_acesso_id', '=', 'na.id')
            ->select('u.name', 'na.nivel', 'cf.funcao')
            ->where('u.id', auth()->user()->id)
            ->first();

        $this->month = $request->has('month') ? $request->input('month') : Carbon::now()->format('m');
        $this->year = $request->has('year') ? $request->input('year') : Carbon::now()->format('Y');

        // Verifica o nível de acesso, caso se enquadre, permite o acesso máximo ou minificado.
        if($c->nivel === 'Master' ||
            $c->funcao === 'Diretoria' ||
            $c->funcao === 'Gerente geral') {

            return $this->master();

        }
    }

    public function master()
    {
        return [
          'channels' => $this->channels()
        ];
    }

    public function channels()
    {
        $this->channels = Channel::get(['id', 'canal']);
        $this->sales();

        $result = [];

        foreach($this->channels as $key => $channel) {

            $this->channel = $channel->canal;

            $result[] = [
                'channel' => $this->channel,
                'sales' => count($this->salesData),
                'collaborators' => $this->collaborators($channel->id)
            ];

        }

        return $result;
    }

    public function sales() : void
    {
        // Trás a contagem de todas as vendas realizadas no mês filtrado.
        $this->salesData = VoalleSales::whereMonth('data_vigencia', $this->month)
            ->whereYear('data_vigencia', $this->year)
            ->whereMonth('data_contrato', '>=', '04')
            ->whereYear('data_contrato', $this->year)
            ->where('status', '<>', 'Cancelado')
            ->get(['id_contrato', 'status', 'situacao', 'data_contrato', 'data_ativacao', 'data_vigencia',
                    'vendedor', 'supervisor', 'data_cancelamento', 'plano'])
            ->unique('id_contrato');
    }

    public function collaborators($channelId)
    {
        $collaborators = Collaborator::whereTipoComissaoId($channelId)
                                    ->where('nome', '<>', ' ')
                                    ->get('nome');

        $result = [];

        foreach($collaborators as $key => $collab) {
            $result[] = [
                'name' => $collab->nome,
                'sales' => $this->salesCollaborator($collab->nome)
            ];
        }

        return $result;
    }

    public function salesCollaborator($name)
    {
        $this->salesCollaborator = 0;

        $result = $this->salesData->filter(function ($sale) use($name) {
            if($sale->vendedor === $name) {
                return $sale->vendedor;
            }
        });


        return count($result);
    }
}
