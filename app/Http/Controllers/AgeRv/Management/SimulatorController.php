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
    private $salesCollaboratorData;
    private $starsCollaborator;
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
            ->selectRaw('LOWER(vendedor) as vendedor, id_contrato,status, situacao, data_contrato, data_ativacao, data_vigencia,
                    supervisor, data_cancelamento, plano')
            ->get()
            ->unique('id_contrato');
    }

    public function collaborators($channelId)
    {
        $collaborators = Collaborator::whereTipoComissaoId($channelId)
                                    ->where('nome', '<>', ' ')
                                    ->selectRaw('LOWER(nome) as nome')
                                    ->get('nome');

        $result = [];

        foreach($collaborators as $key => $collab) {
            $result[] = [
                'name' => $collab->nome,
                'sales' => $this->salesCollaborator($collab->nome),
                'stars' => $this->starsCollaborator(),
                'valueStar' => $this->valueStarCollaborator($collab->nome)
            ];
        }

        return $result;
    }

    public function salesCollaborator($name)
    {
        $this->salesCollaborator = 0;
        $this->salesCollaboratorData = null;

        $result = $this->salesData->filter(function ($sale) use($name) {
            if($sale->vendedor === $name) {

                if($sale->situacao === 'Cancelado') {
                    $dateActivation = Carbon::parse($sale->data_ativacao); // Transformando em data.
                    $dateCancel = Carbon::parse($sale->data_cancelamento); // Transformando em data.

                    // Verificando se o cancelamento foi em menos de 7 dias, se sim, não contabiliza.
                    if ($dateActivation->diffInDays($dateCancel) >= 7) {
                        return $sale;
                    }
                } else {
                    return $sale;
                }

            }
        });

        $this->salesCollaboratorData = $result;

        return count($result);
    }

    public function starsCollaborator()
    {
        $this->starsCollaborator = 0;

        $this->salesCollaboratorData->filter(function($item) {

            // Se o mês do cadastro do contrato for MAIO, executa esse bloco.
            if (Carbon::parse($item->data_contrato) < Carbon::parse('2022-06-01') &&
                Carbon::parse($item->data_contrato) >= Carbon::parse('2022-05-01')) {

                // Verifica qual é o plano e atribui a estrela correspondente.
                if (str_contains($item->plano, 'PLANO 120 MEGA PROMOCAO LEVE 360 MEGA')) {
                    $this->starsCollaborator += 5;
                } elseif (str_contains($item->plano, 'PLANO 240 MEGA PROMOCAO LEVE 720 MEGA  + DEEZER')) {
                    $this->starsCollaborator += 9;
                } elseif (str_contains($item->plano, 'PLANO 240 MEGA PROMOCAO LEVE 720 MEGA  + DEEZER PREMIUM')) {
                    $this->starsCollaborator += 9;
                } elseif (str_contains($item->plano, 'PLANO 240 MEGA PROMOCAO LEVE 960 MEGA ')) {
                    $this->starsCollaborator += 9;
                } elseif (str_contains($item->plano, 'PLANO 360 MEGA')) {
                    $this->starsCollaborator += 11;
                } elseif (str_contains($item->plano, 'PLANO 400 MEGA FIDELIZADO')) {
                    $this->starsCollaborator += 15;
                } elseif (str_contains($item->plano, 'PLANO 480 MEGA - FIDELIZADO')) {
                    $this->starsCollaborator += 15;
                } elseif (str_contains($item->plano, 'PLANO 720 MEGA ')) {
                    $this->starsCollaborator += 25;
                } elseif (str_contains($item->plano, 'PLANO 740 MEGA FIDELIZADO')) {
                    $this->starsCollaborator += 25;
                } elseif (str_contains($item->plano, 'PLANO 800 MEGA FIDELIZADO')) {
                    $this->starsCollaborator += 17;
                } elseif (str_contains($item->plano, 'PLANO 960 MEGA')) {
                    $this->starsCollaborator += 35;
                } elseif (str_contains($item->plano, 'PLANO 960 MEGA TURBINADO + AGE TV + DEEZER PREMIUM')) {
                    $this->starsCollaborator += 35;
                } elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 1 GIGA FIDELIZADO')) {
                    $this->starsCollaborator += 35;
                } elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 600 MEGA FIDELIZADO')) {
                    $this->starsCollaborator += 9;
                } elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 600 MEGA FIDELIZADO + IP FIXO')) {
                    $this->starsCollaborator += 12;
                } elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 800 MEGA FIDELIZADO')) {
                    $this->starsCollaborator += 17;
                } elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 800 MEGA FIDELIZADO + IP FIXO')) {
                    $this->starsCollaborator += 20;
                }

                // Se o mês do cadastro do contrato for JUNHO, executa esse bloco.
            } elseif (Carbon::parse($item->data_contrato) < Carbon::parse('2022-07-01') &&
                Carbon::parse($item->data_contrato) >= Carbon::parse('2022-06-01')) {

                // Verifica qual é o plano e atribui a estrela correspondente.
                if (str_contains($item->plano, 'COMBO EMPRESARIAL 600 MEGA + 1 FIXO BRASIL SEM FIDELIDADE')) {
                    $this->starsCollaborator += 10;
                } elseif (str_contains($item->plano, 'COMBO EMPRESARIAL 600 MEGA + 2 FIXOS BRASIL SEM FIDELIDADE')) {
                    $this->starsCollaborator += 13;
                } elseif (str_contains($item->plano, 'PLANO 120 MEGA')) {
                    $this->starsCollaborator += 7;
                } elseif (str_contains($item->plano, 'PLANO 120 MEGA PROMOCAO LEVE 360 MEGA')) {
                    $this->starsCollaborator += 7;
                } elseif (str_contains($item->plano, 'PLANO 240 MEGA PROMOCAO LEVE 720 MEGA  + DEEZER PREMIUM')) {
                    $this->starsCollaborator += 9;
                } elseif (str_contains($item->plano, 'PLANO 240 MEGA PROMOCAO LEVE 960 MEGA ')) {
                    $this->starsCollaborator += 9;
                } elseif (str_contains($item->plano, 'PLANO 240 MEGA SEM FIDELIDADE')) {
                    $this->starsCollaborator += 0;
                } elseif (str_contains($item->plano, 'PLANO 400 MEGA FIDELIZADO')) {
                    $this->starsCollaborator += 15;
                } elseif (str_contains($item->plano, 'PLANO 480 MEGA - FIDELIZADO')) {
                    $this->starsCollaborator += 15;
                } elseif (str_contains($item->plano, 'PLANO 720 MEGA ')) {
                    $this->starsCollaborator += 25;
                } elseif (str_contains($item->plano, 'PLANO 800 MEGA - COLABORADOR')) {
                    $this->starsCollaborator += 17;
                } elseif (str_contains($item->plano, 'PLANO 800 MEGA FIDELIZADO')) {
                    $this->starsCollaborator += 17;
                } elseif (str_contains($item->plano, 'PLANO 960 MEGA')) {
                    $this->starsCollaborator += 35;
                } elseif (str_contains($item->plano, 'PLANO 960 MEGA TURBINADO + AGE TV + DEEZER PREMIUM')) {
                    $this->starsCollaborator += 35;
                } elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 1 GIGA FIDELIZADO')) {
                    $this->starsCollaborator += 35;
                } elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 600 MEGA FIDELIZADO')) {
                    $this->starsCollaborator += 9;
                } elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 800 MEGA FIDELIZADO')) {
                    $this->starsCollaborator += 17;
                }

                // Se o mês do cadastro do contrato for JULHO, executa esse bloco.
            } elseif (Carbon::parse($item->data_contrato) < Carbon::parse('2022-08-01') &&
                Carbon::parse($item->data_contrato) >= Carbon::parse('2022-07-01')) {

                // Verifica qual é o plano e atribui a estrela correspondente.
                if (str_contains($item->plano, 'PLANO 1 GIGA FIDELIZADO + DEEZER + HBO MAX + DR. AGE')) {
                    $this->starsCollaborator += 30;
                } elseif (str_contains($item->plano, 'PLANO 1 GIGA FIDELIZADO + DEEZER PREMIUM')) {
                    $this->starsCollaborator += 15;
                } elseif (str_contains($item->plano, 'PLANO 120 MEGA PROMOCAO LEVE 360 MEGA')) {
                    $this->starsCollaborator += 7;
                } elseif (str_contains($item->plano, 'PLANO 120 MEGA SEM FIDELIDADE')) {
                    $this->starsCollaborator += 0;
                } elseif (str_contains($item->plano, 'PLANO 240 MEGA ')) {
                    $this->starsCollaborator += 9;
                } elseif (str_contains($item->plano, 'PLANO 240 MEGA PROMOCAO LEVE 720 MEGA  + DEEZER PREMIUM')) {
                    $this->starsCollaborator += 9;
                } elseif (str_contains($item->plano, 'PLANO 240 MEGA PROMOCAO LEVE 720 MEGA + DEEZER PREMIUM SEM FIDELIDADE')) {
                    $this->starsCollaborator += 9;
                } elseif (str_contains($item->plano, 'PLANO 400 MEGA - COLABORADOR')) {
                    $this->starsCollaborator += 0;
                } elseif (str_contains($item->plano, 'PLANO 400 MEGA FIDELIZADO')) {
                    $this->starsCollaborator += 7;
                } elseif (str_contains($item->plano, 'PLANO 400 MEGA NÃO FIDELIZADO')) {
                    $this->starsCollaborator += 0;
                } elseif (str_contains($item->plano, 'PLANO 480 MEGA - FIDELIZADO')) {
                    $this->starsCollaborator += 7;
                } elseif (str_contains($item->plano, 'PLANO 480 MEGA FIDELIZADO')) {
                    $this->starsCollaborator += 7;
                } elseif (str_contains($item->plano, 'PLANO 740 MEGA FIDELIZADO')) {
                    $this->starsCollaborator += 9;
                } elseif (str_contains($item->plano, 'PLANO 740 MEGA FIDELIZADO')) {
                    $this->starsCollaborator += 9;
                } elseif (str_contains($item->plano, 'PLANO 800 MEGA - CAMPANHA CONDOMÍNIO FIDELIZADO (AMBOS)')) {
                    $this->starsCollaborator += 0;
                } elseif (str_contains($item->plano, 'PLANO 800 MEGA - COLABORADOR')) {
                    $this->starsCollaborator += 0;
                } elseif (str_contains($item->plano, 'PLANO 800 MEGA FIDELIZADO')) {
                    $this->starsCollaborator += 17;
                } elseif (str_contains($item->plano, 'PLANO 960 MEGA')) {
                    $this->starsCollaborator += 35;
                } elseif (str_contains($item->plano, 'PLANO 960 MEGA (LOJAS)')) {
                    $this->starsCollaborator += 0;
                } elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 1 GIGA FIDELIZADO')) {
                    $this->starsCollaborator += 35;
                } elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 1 GIGA FIDELIZADO')) {
                    $this->starsCollaborator += 35;
                } elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 1 GIGA FIDELIZADO + DEEZER PREMIUM')) {
                    $this->starsCollaborator += 35;
                } elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 1 GIGA FIDELIZADO + IP FIXO')) {
                    $this->starsCollaborator += 38;
                } elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 1 GIGA SEM FIDELIDADE + IP FIXO')) {
                    $this->starsCollaborator += 36;
                } elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 600 MEGA FIDELIZADO')) {
                    $this->starsCollaborator += 9;
                } elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 600 MEGA FIDELIZADO + IP FIXO')) {
                    $this->starsCollaborator += 12;
                } elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 800 MEGA FIDELIZADO')) {
                    $this->starsCollaborator += 15;
                }

                // Se o mês do cadastro do contrato for AGOSTO, executa esse bloco.
            } elseif (Carbon::parse($item->data_contrato) < Carbon::parse('2022-10-01') &&
                Carbon::parse($item->data_contrato) >= Carbon::parse('2022-08-01')) {

                // Verifica qual é o plano e atribui a estrela correspondente.
                if (str_contains($item->plano, 'PLANO 1 GIGA FIDELIZADO + DEEZER + HBO MAX + DR. AGE')) {
                    $this->starsCollaborator += 30;
                } elseif (str_contains($item->plano, 'PLANO 1 GIGA FIDELIZADO + DEEZER PREMIUM')) {
                    $this->starsCollaborator += 15;
                } elseif (str_contains($item->plano, 'PLANO 1 GIGA NÃO FIDELIZADO + DEEZER PREMIUM')) {
                    $this->starsCollaborator += 0;
                } elseif (str_contains($item->plano, 'PLANO 120 MEGA PROMOCAO LEVE 360 MEGA')) {
                    $this->starsCollaborator += 7;
                } elseif (str_contains($item->plano, 'PLANO 240 MEGA PROMOCAO LEVE 720 MEGA  + DEEZER PREMIUM')) {
                    $this->starsCollaborator += 9;
                } elseif (str_contains($item->plano, 'PLANO 240 MEGA PROMOCAO LEVE 720 MEGA  + DEEZER PREMIUM')) {
                    $this->starsCollaborator += 9;
                } elseif (str_contains($item->plano, 'PLANO 400 MEGA FIDELIZADO')) {
                    $this->starsCollaborator += 7;
                } elseif (str_contains($item->plano, 'PLANO 480 MEGA FIDELIZADO')) {
                    $this->starsCollaborator += 7;
                } elseif (str_contains($item->plano, 'PLANO 480 MEGA NÃO FIDELIZADO')) {
                    $this->starsCollaborator += 0;
                } elseif (str_contains($item->plano, 'PLANO 740 MEGA FIDELIZADO')) {
                    $this->starsCollaborator += 9;
                } elseif (str_contains($item->plano, 'PLANO 800 MEGA FIDELIZADO')) {
                    $this->starsCollaborator += 15;
                } elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 1 GIGA FIDELIZADO')) {
                    $this->starsCollaborator += 35;
                } elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 1 GIGA FIDELIZADO + DEEZER PREMIUM')) {
                    $this->starsCollaborator += 35;
                } elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 600 MEGA FIDELIZADO')) {
                    $this->starsCollaborator += 9;
                } elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 600 MEGA FIDELIZADO + IP FIXO')) {
                    $this->starsCollaborator += 12;
                } elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 800 MEGA FIDELIZADO')) {
                    $this->starsCollaborator += 17;
                }
            }
        });

        return $this->starsCollaborator;

    }

    public function valueStarCollaborator($name)
    {

        $data = DB::table('agerv_colaboradores as c')
            ->leftJoin('agerv_colaboradores_meta as cm', 'cm.colaborador_id', '=', 'c.id')
            ->leftJoin('agerv_colaboradores_canais as cc', 'c.tipo_comissao_id', '=', 'cc.id')
            ->where('c.nome', $name)
            ->where('cm.mes_competencia', $this->month)
            ->select('c.id', 'cc.canal', 'cm.meta')
            ->first();

        if (!isset($data->meta)) {
            return "Sem meta";
        } else {

            if ($data->meta === 0) {
                return "Meta zerada";
            } else {

                $metaPercent = (count($this->salesCollaboratorData) / $data->meta) * 100;

                return $metaPercent;



//                if (isset($data->meta)) {
//
//                    // Bloco responsável pela meta mínima e máxima, aplicando valor às estrelas.
//                    if ($data->canal === 'MCV') {
//
//                        $this->minMeta = 70;
//
//                        if ($this->metaPercent >= $this->minMeta && $this->metaPercent < 100) {
//                            $this->valueStars = 0.90;
//                        } elseif ($this->metaPercent >= 100 && $this->metaPercent < 120) {
//                            $this->valueStars = 1.20;
//                        } elseif ($this->metaPercent >= 120 && $this->metaPercent < 141) {
//                            $this->valueStars = 2;
//                        } elseif ($this->metaPercent >= 141) {
//                            $this->valueStars = 4.5;
//                        }
//
//                    } elseif ($data->canal === 'PAP') {
//
//                        if ($this->metaPercent >= 60 && $this->metaPercent < 100) {
//                            $this->valueStars = 1.3;
//                        } elseif ($this->metaPercent >= 100 && $this->metaPercent < 120) {
//                            $this->valueStars = 3;
//                        } elseif ($this->metaPercent >= 120 && $this->metaPercent < 141) {
//                            $this->valueStars = 5;
//                        } elseif ($this->metaPercent >= 141) {
//                            $this->valueStars = 7;
//                        }
//
//                    } elseif ($data->canal === 'LIDER') {
//
//                        if ($this->metaPercent >= 60 && $this->metaPercent < 100) {
//                            $this->valueStars = 0.25;
//                        } elseif ($this->metaPercent >= 100 && $this->metaPercent < 120) {
//                            $this->valueStars = 0.40;
//                        } elseif ($this->metaPercent >= 120 && $this->metaPercent < 141) {
//                            $this->valueStars = 0.80;
//                        } elseif ($this->metaPercent >= 141) {
//                            $this->valueStars = 1.30;
//                        }
//                    }
//
//                    return $this->valueStars;
//
//                } else {
//                    return "Nenhum colaborador ativo";
//                }
            }
        }


    }
}
