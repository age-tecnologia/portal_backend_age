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
    private $salesChannelCount;
    private $salesCollaborator;
    private $salesCollaboratorData;
    private $salesD7;
    private $metaPercentCollaborator;
    private $starsCollaborator;
    private $valueStarCollaborator;
    private $salesChannel;
    private $commissionChannel;
    private $rulesRange;
    private $metaPercentRuleSupervisor;
    private $metaPercentSupervisor;
    private $teste = [];


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


        if(! $request->has('rulesRange')) {
            return response()->json(['Nenhuma regra de negócio enviada.'], 404);
        }

        $this->rulesRange = $request->input('rulesRange');


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
                $this->salesChannelCount = 0;
                $this->commissionChannel = 0;
                $this->teste = [];

            $result[] = [
                    'channel' => $this->channel,
                    'collaborators' => $this->collaborators($channel->id),
                    'sales' => $this->salesChannelCount,
                    'commission' => number_format($this->commissionChannel, 2, ',', '.'),
                ];

        }

        return $result;
    }

    public function sales() : void
    {
        // Trás a contagem de todas as vendas realizadas no mês filtrado.
        $this->salesData = VoalleSales::
            whereMonth('data_vigencia', $this->month)
            ->whereYear('data_vigencia', $this->year)
            ->whereMonth('data_contrato', '>', '04')
            ->whereYear('data_contrato', $this->year)
            ->whereYear('data_ativacao', $this->year)
            ->whereMonth('data_ativacao', '>=', '06')
            ->where('status', 'Aprovado')
            ->selectRaw('LOWER(vendedor) as vendedor, id_contrato,status, situacao, data_contrato, data_ativacao, data_vigencia,
                    LOWER(supervisor) as supervisor, data_cancelamento, plano')
            ->get()
            ->unique('id_contrato');

    }

    public function collaborators($channelId)
    {

        $collaborators = DB::table('agerv_colaboradores as c')
                            ->leftJoin('agerv_colaboradores_canais as cc', 'c.tipo_comissao_id', '=', 'cc.id')
                            ->where('c.tipo_comissao_id', $channelId)
                            ->selectRaw('LOWER(c.nome) as nome, cc.canal as canal')
                            ->distinct()
                            ->get();

        $result = [];

        foreach($collaborators as $key => $collab) {


            if($this->channel === 'LIDER') {

                $result[] = [
                    'name' => $collab->nome,
                    'sales' => $this->salesCollaborator($collab->nome),
                    'commission' => $this->commissionSupervisor($collab->nome),
                    'metaPercent' => number_format($this->metaPercentSupervisor, 2, ','),
                    'metaRule' => number_format($this->metaPercentRuleSupervisor, 2, ',')
                ];

            } elseif ($this->channel === $collab->canal) {

                $result[] = [
                    'name' => $collab->nome,
                    'sales' => $this->salesCollaborator($collab->nome),
                    'stars' => $this->starsCollaborator(),
                    'valueStar' => $this->valueStarCollaborator($collab->nome),
                    'commission' => $this->commissionCollaborator(),
                    'metaPercent' => number_format($this->metaPercentCollaborator, 2, ',')
                ];

            }

        }

        return $result;
    }

    public function salesCollaborator($name)
    {
        $this->salesCollaborator = 0;
        $this->salesCollaboratorData = 0;
        $this->salesD7 = 0;

        $result = $this->salesData->filter(function ($sale) use($name) {

            if($sale->vendedor === $name || $sale->supervisor === $name) {

                if($sale->situacao === 'Cancelado') {
                    $dateActivation = Carbon::parse($sale->data_ativacao); // Transformando em data.
                    $dateCancel = Carbon::parse($sale->data_cancelamento); // Transformando em data.

                    // Verificando se o cancelamento foi em menos de 7 dias, se sim, não contabiliza.
                    if ($dateActivation->diffInDays($dateCancel) >= 7) {
                        return $sale;
                    } else {
                        $this->salesD7 += 1;
                    }
                } else {
                    return $sale;
                }

            }
        });

        $this->salesCollaboratorData = $result;
        $this->salesChannelCount += count($result);



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

        $this->valueStarCollaborator = 0;
        $this->metaPercentCollaborator = 0;

        if (!isset($data->meta)) {
            return "Sem meta";
        } else {

            if ($data->meta === 0) {
                return "Meta zerada";
            } else {

                $this->metaPercentCollaborator = (count($this->salesCollaboratorData) / $data->meta) * 100;
                $this->valueStarCollaborator = 0;


                foreach($this->rulesRange as $field => $value)  {
                    if($this->channel === $field) {
                        foreach($value as $key => $value) {
                            if($value['last'] === null) {
                                if ($this->metaPercentCollaborator >= $value['first']) {
                                    $this->valueStarCollaborator = $value['value'];
                                }
                            } else {
                                if ($this->metaPercentCollaborator >= $value['first'] && $this->metaPercentCollaborator < $value['last']) {
                                    $this->valueStarCollaborator = $value['value'];
                                }
                            }
                        }
                    }
                }

                return $this->valueStarCollaborator;
            }
        }


    }

    public function commissionCollaborator()
    {
        $commission = ($this->starsCollaborator * $this->valueStarCollaborator);

        if($this->salesD7 > 0) {
            $commission = $commission * 0.9;
        } else {
            $commission = $commission * 1.1;
        }

        $this->commissionChannel += $commission;

        return number_format($commission, 2, ',', '.');
    }

    public function commissionSupervisor($name)
    {
        $target = 3000;

        $data = DB::table('agerv_colaboradores as c')
            ->leftJoin('agerv_colaboradores_meta as cm', 'cm.colaborador_id', '=', 'c.id')
            ->leftJoin('agerv_colaboradores_canais as cc', 'c.tipo_comissao_id', '=', 'cc.id')
            ->where('c.nome', $name)
            ->where('cm.mes_competencia', $this->month)
            ->select('c.id', 'cc.canal', 'cm.meta')
            ->first();

        $this->metaPercentRuleSupervisor = 0;
        $this->metaPercentSupervisor = 0;

        if (!isset($data->meta)) {
            return "Sem meta";
        } else {

            if ($data->meta === 0) {
                return "Meta zerada";
            } else {

                $this->metaPercentSupervisor = (count($this->salesCollaboratorData) / $data->meta) * 100;

                foreach($this->rulesRange as $field => $value)  {
                    if($this->channel === $field) {
                        foreach($value as $key => $value) {
                            if($value['last'] === null) {
                                if ($this->metaPercentSupervisor >= $value['first']) {
                                    $this->metaPercentRuleSupervisor = $this->metaPercentSupervisor;
                                }
                            } else {
                                if ($this->metaPercentSupervisor >= $value['first'] && $this->metaPercentSupervisor < $value['last']) {
                                    $this->metaPercentRuleSupervisor = $value['value'];
                                }
                            }
                        }
                    }
                }
            }
        }



        $commission = ($target * $this->metaPercentRuleSupervisor) / 100;

        $this->commissionChannel += $commission;

        return number_format($commission, 2, ',', '.');


    }
}
