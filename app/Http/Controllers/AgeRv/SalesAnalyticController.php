<?php

namespace App\Http\Controllers\AgeRv;

use App\Http\Controllers\AgeRv\Class\SalesSupervisor;
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

    protected $channels;
    private $dataChannels;
    private string $month;
    private string $year;
    private $salesTotals;
    private int $salesTotalsCount = 0;
    private int $salesCancelledsCount = 0;
    private int $salesCancelledsD7Count = 0;
    private int $salesBaseCount = 0;
    private int $starsTotalCount = 0;
    private int $starsSeller = 0;
    private $commissionTotal = 0;
    private $commissionChannel = 0;
    private $metaPercent;
    private $valueStars;
    private $salesSeller;
    private $d7Seller;
    private $deflatorSeller;
    private $salesSup = 0;
    private $salesSupExtract = [];
    private $salesSupCancelleds = 0;
    private $salesSupCancelledsExtract = [];
    private $salesSupCancelledsD7 = 0;
    private $salesSupCancelledsD7Extract = [];
    private $starsSupTotal = 0;
    private $valueStarsSup;
    private $deflatorSup;
    private $metaSup;
    private $metaSeller;



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
            $c->funcao === 'Gerente geral') {

            return $this->master();

        } elseif ($c->funcao === 'Supervisor') {
            return $this->supervisor();
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
            'starsTotal' => $this->starsTotalCount,
            'commissionTotal' => number_format($this->commissionTotal, 2, ',', '.'),
        ];

    }

    private function channels() {

        $this->channels = Channel::select('id','canal')
                                ->where('canal', '<>', 'lider')
                                ->get();

        $this->dataChannels = [];

        $salesSup = new SalesSupervisor();

        foreach ($this->channels as $c => $value) {

            $supervisors = Collaborator::where('funcao_id', 3)
                ->where('canal_id', $value->id)
                ->select('nome')
                ->get();

            $this->commissionChannel = 0;

            $this->dataChannels[] = [
                'channel' => $value->canal,
                'salesTotal' => $this->salesTotalsChannels($supervisors),
                'salesCancelled' => $this->salesCancelleds(),
                'salesBase' => $this->salesBase(),
                'starsTotal' => $this->starsTotal(),
                'supervisors' => $this->supervisors(),
                'commission' => number_format($this->commissionChannel, 2, ',', '.'),
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
            ->where('status', '<>', 'Cancelado')
            ->select('id_contrato', 'nome_cliente', 'status', 'situacao', 'data_contrato', 'data_ativacao', 'data_vigencia',
                    'vendedor', 'supervisor', 'data_cancelamento', 'plano')
            ->get();

        $this->salesTotalsCount += count($this->salesTotals);

        return [
            'extract' => 0,// $this->salesTotals,
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

    private function starsTotal()
    {
        $stars = 0;

        foreach($this->salesTotals as $sale => $item) {
            // Se o mês do cadastro do contrato for MAIO, executa esse bloco.
            if (Carbon::parse($item->data_contrato) < Carbon::parse('2022-06-01') &&
                Carbon::parse($item->data_contrato) >= Carbon::parse('2022-05-01')) {

                // Verifica qual é o plano e atribui a estrela correspondente.
                if (str_contains($item->plano, 'PLANO 120 MEGA PROMOCAO LEVE 360 MEGA')) {
                    $stars += 5;
                } elseif (str_contains($item->plano, 'PLANO 240 MEGA PROMOCAO LEVE 720 MEGA  + DEEZER')) {
                    $stars += 9;
                } elseif (str_contains($item->plano, 'PLANO 240 MEGA PROMOCAO LEVE 720 MEGA  + DEEZER PREMIUM')) {
                    $stars += 9;
                } elseif (str_contains($item->plano, 'PLANO 240 MEGA PROMOCAO LEVE 960 MEGA ')) {
                    $stars += 9;
                } elseif (str_contains($item->plano, 'PLANO 360 MEGA')) {
                    $stars += 11;
                } elseif (str_contains($item->plano, 'PLANO 400 MEGA FIDELIZADO')) {
                    $stars += 15;
                } elseif (str_contains($item->plano, 'PLANO 480 MEGA - FIDELIZADO')) {
                    $stars += 15;
                } elseif (str_contains($item->plano, 'PLANO 720 MEGA ')) {
                    $stars += 25;
                } elseif (str_contains($item->plano, 'PLANO 740 MEGA FIDELIZADO')) {
                    $stars += 25;
                } elseif (str_contains($item->plano, 'PLANO 800 MEGA FIDELIZADO')) {
                    $stars += 17;
                } elseif (str_contains($item->plano, 'PLANO 960 MEGA')) {
                    $stars += 35;
                } elseif (str_contains($item->plano, 'PLANO 960 MEGA TURBINADO + AGE TV + DEEZER PREMIUM')) {
                    $stars += 35;
                } elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 1 GIGA FIDELIZADO')) {
                    $stars += 35;
                } elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 600 MEGA FIDELIZADO')) {
                    $stars += 9;
                } elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 600 MEGA FIDELIZADO + IP FIXO')) {
                    $stars += 12;
                } elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 800 MEGA FIDELIZADO')) {
                    $stars += 17;
                } elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 800 MEGA FIDELIZADO + IP FIXO')) {
                    $stars += 20;
                }

                // Se o mês do cadastro do contrato for JUNHO, executa esse bloco.
            } elseif (Carbon::parse($item->data_contrato) < Carbon::parse('2022-07-01') &&
                Carbon::parse($item->data_contrato) >= Carbon::parse('2022-06-01')) {

                // Verifica qual é o plano e atribui a estrela correspondente.
                if (str_contains($item->plano, 'COMBO EMPRESARIAL 600 MEGA + 1 FIXO BRASIL SEM FIDELIDADE')) {
                    $stars += 10;
                } elseif (str_contains($item->plano, 'COMBO EMPRESARIAL 600 MEGA + 2 FIXOS BRASIL SEM FIDELIDADE')) {
                    $stars += 13;
                } elseif (str_contains($item->plano, 'PLANO 120 MEGA')) {
                    $stars += 7;
                } elseif (str_contains($item->plano, 'PLANO 120 MEGA PROMOCAO LEVE 360 MEGA')) {
                    $stars += 7;
                } elseif (str_contains($item->plano, 'PLANO 240 MEGA PROMOCAO LEVE 720 MEGA  + DEEZER PREMIUM')) {
                    $stars += 9;
                } elseif (str_contains($item->plano, 'PLANO 240 MEGA PROMOCAO LEVE 960 MEGA ')) {
                    $stars += 9;
                } elseif (str_contains($item->plano, 'PLANO 240 MEGA SEM FIDELIDADE')) {
                    $stars += 0;
                } elseif (str_contains($item->plano, 'PLANO 400 MEGA FIDELIZADO')) {
                    $stars += 15;
                } elseif (str_contains($item->plano, 'PLANO 480 MEGA - FIDELIZADO')) {
                    $stars += 15;
                } elseif (str_contains($item->plano, 'PLANO 720 MEGA ')) {
                    $stars += 25;
                } elseif (str_contains($item->plano, 'PLANO 800 MEGA - COLABORADOR')) {
                    $stars += 17;
                } elseif (str_contains($item->plano, 'PLANO 800 MEGA FIDELIZADO')) {
                    $stars += 17;
                } elseif (str_contains($item->plano, 'PLANO 960 MEGA')) {
                    $stars += 35;
                } elseif (str_contains($item->plano, 'PLANO 960 MEGA TURBINADO + AGE TV + DEEZER PREMIUM')) {
                    $stars += 35;
                } elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 1 GIGA FIDELIZADO')) {
                    $stars += 35;
                } elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 600 MEGA FIDELIZADO')) {
                    $stars += 9;
                } elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 800 MEGA FIDELIZADO')) {
                    $stars += 17;
                }

                // Se o mês do cadastro do contrato for JULHO, executa esse bloco.
            } elseif (Carbon::parse($item->data_contrato) < Carbon::parse('2022-08-01') &&
                Carbon::parse($item->data_contrato) >= Carbon::parse('2022-07-01')) {

                // Verifica qual é o plano e atribui a estrela correspondente.
                if (str_contains($item->plano, 'PLANO 1 GIGA FIDELIZADO + DEEZER + HBO MAX + DR. AGE')) {
                    $stars += 30;
                } elseif (str_contains($item->plano, 'PLANO 1 GIGA FIDELIZADO + DEEZER PREMIUM')) {
                    $stars += 15;
                } elseif (str_contains($item->plano, 'PLANO 120 MEGA PROMOCAO LEVE 360 MEGA')) {
                    $stars += 7;
                } elseif (str_contains($item->plano, 'PLANO 120 MEGA SEM FIDELIDADE')) {
                    $stars += 0;
                } elseif (str_contains($item->plano, 'PLANO 240 MEGA ')) {
                    $stars += 9;
                } elseif (str_contains($item->plano, 'PLANO 240 MEGA PROMOCAO LEVE 720 MEGA  + DEEZER PREMIUM')) {
                    $stars += 9;
                } elseif (str_contains($item->plano, 'PLANO 240 MEGA PROMOCAO LEVE 720 MEGA + DEEZER PREMIUM SEM FIDELIDADE')) {
                    $stars += 9;
                } elseif (str_contains($item->plano, 'PLANO 400 MEGA - COLABORADOR')) {
                    $stars += 0;
                } elseif (str_contains($item->plano, 'PLANO 400 MEGA FIDELIZADO')) {
                    $stars += 7;
                } elseif (str_contains($item->plano, 'PLANO 400 MEGA NÃO FIDELIZADO')) {
                    $stars += 0;
                } elseif (str_contains($item->plano, 'PLANO 480 MEGA - FIDELIZADO')) {
                    $stars += 7;
                } elseif (str_contains($item->plano, 'PLANO 480 MEGA FIDELIZADO')) {
                    $stars += 7;
                } elseif (str_contains($item->plano, 'PLANO 740 MEGA FIDELIZADO')) {
                    $stars += 9;
                } elseif (str_contains($item->plano, 'PLANO 740 MEGA FIDELIZADO')) {
                    $stars += 9;
                } elseif (str_contains($item->plano, 'PLANO 800 MEGA - CAMPANHA CONDOMÍNIO FIDELIZADO (AMBOS)')) {
                    $stars += 0;
                } elseif (str_contains($item->plano, 'PLANO 800 MEGA - COLABORADOR')) {
                    $stars += 0;
                } elseif (str_contains($item->plano, 'PLANO 800 MEGA FIDELIZADO')) {
                    $stars += 17;
                } elseif (str_contains($item->plano, 'PLANO 960 MEGA')) {
                    $stars += 35;
                } elseif (str_contains($item->plano, 'PLANO 960 MEGA (LOJAS)')) {
                    $stars += 0;
                } elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 1 GIGA FIDELIZADO')) {
                    $stars += 35;
                } elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 1 GIGA FIDELIZADO')) {
                    $stars += 35;
                } elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 1 GIGA FIDELIZADO + DEEZER PREMIUM')) {
                    $stars += 35;
                } elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 1 GIGA FIDELIZADO + IP FIXO')) {
                    $stars += 38;
                } elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 1 GIGA SEM FIDELIDADE + IP FIXO')) {
                    $stars += 36;
                } elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 600 MEGA FIDELIZADO')) {
                    $stars += 9;
                } elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 600 MEGA FIDELIZADO + IP FIXO')) {
                    $stars += 12;
                } elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 800 MEGA FIDELIZADO')) {
                    $stars += 15;
                }

                // Se o mês do cadastro do contrato for AGOSTO, executa esse bloco.
            } elseif (Carbon::parse($item->data_contrato) < Carbon::parse('2022-09-01') &&
                Carbon::parse($item->data_contrato) >= Carbon::parse('2022-08-01')) {

                // Verifica qual é o plano e atribui a estrela correspondente.
                if (str_contains($item->plano, 'PLANO 1 GIGA FIDELIZADO + DEEZER + HBO MAX + DR. AGE')) {
                    $stars += 30;
                } elseif (str_contains($item->plano, 'PLANO 1 GIGA FIDELIZADO + DEEZER PREMIUM')) {
                    $stars += 15;
                } elseif (str_contains($item->plano, 'PLANO 1 GIGA NÃO FIDELIZADO + DEEZER PREMIUM')) {
                    $stars += 0;
                } elseif (str_contains($item->plano, 'PLANO 120 MEGA PROMOCAO LEVE 360 MEGA')) {
                    $stars += 7;
                } elseif (str_contains($item->plano, 'PLANO 240 MEGA PROMOCAO LEVE 720 MEGA  + DEEZER PREMIUM')) {
                    $stars += 9;
                } elseif (str_contains($item->plano, 'PLANO 240 MEGA PROMOCAO LEVE 720 MEGA  + DEEZER PREMIUM')) {
                    $stars += 9;
                } elseif (str_contains($item->plano, 'PLANO 400 MEGA FIDELIZADO')) {
                    $stars += 7;
                } elseif (str_contains($item->plano, 'PLANO 480 MEGA FIDELIZADO')) {
                    $stars += 7;
                } elseif (str_contains($item->plano, 'PLANO 480 MEGA NÃO FIDELIZADO')) {
                    $stars += 0;
                } elseif (str_contains($item->plano, 'PLANO 740 MEGA FIDELIZADO')) {
                    $stars += 9;
                } elseif (str_contains($item->plano, 'PLANO 800 MEGA FIDELIZADO')) {
                    $stars += 15;
                } elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 1 GIGA FIDELIZADO')) {
                    $stars += 35;
                } elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 1 GIGA FIDELIZADO + DEEZER PREMIUM')) {
                    $stars += 35;
                } elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 600 MEGA FIDELIZADO')) {
                    $stars += 9;
                } elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 600 MEGA FIDELIZADO + IP FIXO')) {
                    $stars += 12;
                } elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 800 MEGA FIDELIZADO')) {
                    $stars += 17;
                }
            }

        }

        $this->starsTotalCount += $stars;

        return $stars;
    }

    private function supervisors() {

        $supervisors = $this->salesTotals->unique(function($item) {
            return $item->supervisor;
        });

        $supervisors = $supervisors->map(function($item) {
            return $item->supervisor;
        });

        $data = [];

        $supervisors->each(function($item) {
            $data[] = [
                $item
            ];
        });

        foreach($supervisors as $item => $value) {

            $this->salesSup = 0;
            $this->salesSupExtract = [];
            $this->salesSupCancelleds = 0;
            $this->salesSupCancelledsExtract = [];
            $this->salesSupCancelledsD7 = 0;
            $this->salesSupCancelledsD7Extract = [];
            $this->starsSupTotal = 0;
            $this->valueStarsSup = 0;
            $this->deflatorSup = 0;
            $this->metaSup = 0;
            $this->metaPercent = 0;

            $data[] = [
                'supervisor' => $value,
                'sellers' => $this->sellers($value),
                'salesTotal' => [
                    'count' => $this->salesSup,
                    'extract' => $this->salesSupExtract,
                ],
                'salesCancelled' => [
                    'count' => $this->salesSupCancelleds,
                    'extract' => $this->salesSupCancelledsExtract
                ],
                'starsTotal' => $this->starsSupTotal,
                'valueStar' => $this->valueStarSup($value),
                'commission' => $this->commissionSup(),
                'deflator' => 0, //$this->deflatorSup,
                'meta' => $this->metaSup,
                'metaPercent' => number_format($this->metaPercent, 2),
            ];
        }


        return $data;

    }

    private function sellers($supervisor) {


        $sellers = $this->salesTotals->filter(function($item) use($supervisor) {
            if($item->supervisor === $supervisor) {
                return $item;
            }
        });

        $sellers = $sellers->unique(function($item) use($supervisor) {
            return $item->vendedor;
        });

        $sellers = $sellers->map(function($item) {
            return $item->vendedor;
        });

        $data = [];

        $sellers = $sellers->each(function($item) {
            $data[] = [
                $item
            ];
        });


        foreach($sellers as $item => $value) {

            $this->metaSeller = 0;
            $this->valueStars = 0;
            $this->metaPercent = 0;
            $this->salesSeller = 0;
            $this->d7Seller = 0;
            $this->deflatorSeller = 0;

            $data[] = [
                'seller' => $value,
                'salesTotal' => $this->salesSeller($value),
                'salesCancelled' => $this->salesCancelledSeller($value),
                'starsTotal' => $this->starsSellers($value),
                'valueStar' => $this->valueStarSeller($value),
                'commission' => $this->commissionSeller(),
                'deflator' => $this->deflatorSeller,
                'meta' => $this->metaSeller,
                'metaPercent' => number_format($this->metaPercent, 2),
            ];
        }

        return $data;

    }

    private function salesSeller($name)
    {

        $sales = $this->salesTotals->filter(function($item) use($name) {
            if($item->vendedor === $name) {
                return $item;
            }
        });

        $sales = $sales->sortBy('nome_cliente');

        $this->salesSeller = count($sales);

        $this->salesSup += count($sales);
        $this->salesSupExtract[] = $sales;

        return [
            'extract' => $sales,
            'count' => count($sales)
        ];
    }

    private function salesCancelledSeller($name) {

        $cancelleds = $this->salesTotals->filter(function ($sale) use($name) {
            if($sale->situacao === 'Cancelado') {
                if($sale->vendedor === $name) {
                    return $sale;
                }
            }
        })->all();

        $d7 = $this->salesTotals->filter(function ($sale) use($name) {
            if($sale->situacao === 'Cancelado') {

                $dateActivation = Carbon::parse($sale->data_ativacao); // Transformando em data.
                $dateCancel = Carbon::parse($sale->data_cancelamento); // Transformando em data.

                // Verificando se o cancelamento foi em menos de 7 dias, se sim, atualiza o banco com inválida.
                if ($dateActivation->diffInDays($dateCancel) < 7) {
                    if($sale->vendedor === $name) {
                        return $sale;
                    }
                }
            }
        });

        $this->d7Seller = count($d7);

        $this->salesSupCancelleds += count($cancelleds);
        $this->salesSupCancelledExtract[] = $cancelleds;
        $this->salesSupCancelledsD7 += count($d7);
        $this->salesSupCancelledD7Extract[] = $d7;




        return [
            'extract' => 0, //$cancelleds,
            'count' => count($cancelleds),
            'd7' => [
                'extract' => 0, //$d7,
                'count' => count($d7)
            ]
        ];
    }

    private function starsSellers($name) {

        $this->starsSeller = 0;

        $result = $this->salesTotals->filter(function($item) use($name) {
           if($item->vendedor === $name) {

               // Se o mês do cadastro do contrato for MAIO, executa esse bloco.
               if (Carbon::parse($item->data_contrato) < Carbon::parse('2022-06-01') &&
                   Carbon::parse($item->data_contrato) >= Carbon::parse('2022-05-01')) {

                   // Verifica qual é o plano e atribui a estrela correspondente.
                   if (str_contains($item->plano, 'PLANO 120 MEGA PROMOCAO LEVE 360 MEGA')) {
                       $this->starsSeller += 5;
                   } elseif (str_contains($item->plano, 'PLANO 240 MEGA PROMOCAO LEVE 720 MEGA  + DEEZER')) {
                       $this->starsSeller += 9;
                   } elseif (str_contains($item->plano, 'PLANO 240 MEGA PROMOCAO LEVE 720 MEGA  + DEEZER PREMIUM')) {
                       $this->starsSeller += 9;
                   } elseif (str_contains($item->plano, 'PLANO 240 MEGA PROMOCAO LEVE 960 MEGA ')) {
                       $this->starsSeller += 9;
                   } elseif (str_contains($item->plano, 'PLANO 360 MEGA')) {
                       $this->starsSeller += 11;
                   } elseif (str_contains($item->plano, 'PLANO 400 MEGA FIDELIZADO')) {
                       $this->starsSeller += 15;
                   } elseif (str_contains($item->plano, 'PLANO 480 MEGA - FIDELIZADO')) {
                       $this->starsSeller += 15;
                   } elseif (str_contains($item->plano, 'PLANO 720 MEGA ')) {
                       $this->starsSeller += 25;
                   } elseif (str_contains($item->plano, 'PLANO 740 MEGA FIDELIZADO')) {
                       $this->starsSeller += 25;
                   } elseif (str_contains($item->plano, 'PLANO 800 MEGA FIDELIZADO')) {
                       $this->starsSeller += 17;
                   } elseif (str_contains($item->plano, 'PLANO 960 MEGA')) {
                       $this->starsSeller += 35;
                   } elseif (str_contains($item->plano, 'PLANO 960 MEGA TURBINADO + AGE TV + DEEZER PREMIUM')) {
                       $this->starsSeller += 35;
                   } elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 1 GIGA FIDELIZADO')) {
                       $this->starsSeller += 35;
                   } elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 600 MEGA FIDELIZADO')) {
                       $this->starsSeller += 9;
                   } elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 600 MEGA FIDELIZADO + IP FIXO')) {
                       $this->starsSeller += 12;
                   } elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 800 MEGA FIDELIZADO')) {
                       $this->starsSeller += 17;
                   } elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 800 MEGA FIDELIZADO + IP FIXO')) {
                       $this->starsSeller += 20;
                   }

                   // Se o mês do cadastro do contrato for JUNHO, executa esse bloco.
               } elseif (Carbon::parse($item->data_contrato) < Carbon::parse('2022-07-01') &&
                   Carbon::parse($item->data_contrato) >= Carbon::parse('2022-06-01')) {

                   // Verifica qual é o plano e atribui a estrela correspondente.
                   if (str_contains($item->plano, 'COMBO EMPRESARIAL 600 MEGA + 1 FIXO BRASIL SEM FIDELIDADE')) {
                       $this->starsSeller += 10;
                   } elseif (str_contains($item->plano, 'COMBO EMPRESARIAL 600 MEGA + 2 FIXOS BRASIL SEM FIDELIDADE')) {
                       $this->starsSeller += 13;
                   } elseif (str_contains($item->plano, 'PLANO 120 MEGA')) {
                       $this->starsSeller += 7;
                   } elseif (str_contains($item->plano, 'PLANO 120 MEGA PROMOCAO LEVE 360 MEGA')) {
                       $this->starsSeller += 7;
                   } elseif (str_contains($item->plano, 'PLANO 240 MEGA PROMOCAO LEVE 720 MEGA  + DEEZER PREMIUM')) {
                       $this->starsSeller += 9;
                   } elseif (str_contains($item->plano, 'PLANO 240 MEGA PROMOCAO LEVE 960 MEGA ')) {
                       $this->starsSeller += 9;
                   } elseif (str_contains($item->plano, 'PLANO 240 MEGA SEM FIDELIDADE')) {
                       $this->starsSeller += 0;
                   } elseif (str_contains($item->plano, 'PLANO 400 MEGA FIDELIZADO')) {
                       $this->starsSeller += 15;
                   } elseif (str_contains($item->plano, 'PLANO 480 MEGA - FIDELIZADO')) {
                       $this->starsSeller += 15;
                   } elseif (str_contains($item->plano, 'PLANO 720 MEGA ')) {
                       $this->starsSeller += 25;
                   } elseif (str_contains($item->plano, 'PLANO 800 MEGA - COLABORADOR')) {
                       $this->starsSeller += 17;
                   } elseif (str_contains($item->plano, 'PLANO 800 MEGA FIDELIZADO')) {
                       $this->starsSeller += 17;
                   } elseif (str_contains($item->plano, 'PLANO 960 MEGA')) {
                       $this->starsSeller += 35;
                   } elseif (str_contains($item->plano, 'PLANO 960 MEGA TURBINADO + AGE TV + DEEZER PREMIUM')) {
                       $this->starsSeller += 35;
                   } elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 1 GIGA FIDELIZADO')) {
                       $this->starsSeller += 35;
                   } elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 600 MEGA FIDELIZADO')) {
                       $this->starsSeller += 9;
                   } elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 800 MEGA FIDELIZADO')) {
                       $this->starsSeller += 17;
                   }

                   // Se o mês do cadastro do contrato for JULHO, executa esse bloco.
               } elseif (Carbon::parse($item->data_contrato) < Carbon::parse('2022-08-01') &&
                   Carbon::parse($item->data_contrato) >= Carbon::parse('2022-07-01')) {

                   // Verifica qual é o plano e atribui a estrela correspondente.
                   if (str_contains($item->plano, 'PLANO 1 GIGA FIDELIZADO + DEEZER + HBO MAX + DR. AGE')) {
                       $this->starsSeller += 30;
                   } elseif (str_contains($item->plano, 'PLANO 1 GIGA FIDELIZADO + DEEZER PREMIUM')) {
                       $this->starsSeller += 15;
                   } elseif (str_contains($item->plano, 'PLANO 120 MEGA PROMOCAO LEVE 360 MEGA')) {
                       $this->starsSeller += 7;
                   } elseif (str_contains($item->plano, 'PLANO 120 MEGA SEM FIDELIDADE')) {
                       $this->starsSeller += 0;
                   } elseif (str_contains($item->plano, 'PLANO 240 MEGA ')) {
                       $this->starsSeller += 9;
                   } elseif (str_contains($item->plano, 'PLANO 240 MEGA PROMOCAO LEVE 720 MEGA  + DEEZER PREMIUM')) {
                       $this->starsSeller += 9;
                   } elseif (str_contains($item->plano, 'PLANO 240 MEGA PROMOCAO LEVE 720 MEGA + DEEZER PREMIUM SEM FIDELIDADE')) {
                       $this->starsSeller += 9;
                   } elseif (str_contains($item->plano, 'PLANO 400 MEGA - COLABORADOR')) {
                       $this->starsSeller += 0;
                   } elseif (str_contains($item->plano, 'PLANO 400 MEGA FIDELIZADO')) {
                       $this->starsSeller += 7;
                   } elseif (str_contains($item->plano, 'PLANO 400 MEGA NÃO FIDELIZADO')) {
                       $this->starsSeller += 0;
                   } elseif (str_contains($item->plano, 'PLANO 480 MEGA - FIDELIZADO')) {
                       $this->starsSeller += 7;
                   } elseif (str_contains($item->plano, 'PLANO 480 MEGA FIDELIZADO')) {
                       $this->starsSeller += 7;
                   } elseif (str_contains($item->plano, 'PLANO 740 MEGA FIDELIZADO')) {
                       $this->starsSeller += 9;
                   } elseif (str_contains($item->plano, 'PLANO 740 MEGA FIDELIZADO')) {
                       $this->starsSeller += 9;
                   } elseif (str_contains($item->plano, 'PLANO 800 MEGA - CAMPANHA CONDOMÍNIO FIDELIZADO (AMBOS)')) {
                       $this->starsSeller += 0;
                   } elseif (str_contains($item->plano, 'PLANO 800 MEGA - COLABORADOR')) {
                       $this->starsSeller += 0;
                   } elseif (str_contains($item->plano, 'PLANO 800 MEGA FIDELIZADO')) {
                       $this->starsSeller += 17;
                   } elseif (str_contains($item->plano, 'PLANO 960 MEGA')) {
                       $this->starsSeller += 35;
                   } elseif (str_contains($item->plano, 'PLANO 960 MEGA (LOJAS)')) {
                       $this->starsSeller += 0;
                   } elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 1 GIGA FIDELIZADO')) {
                       $this->starsSeller += 35;
                   } elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 1 GIGA FIDELIZADO')) {
                       $this->starsSeller += 35;
                   } elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 1 GIGA FIDELIZADO + DEEZER PREMIUM')) {
                       $this->starsSeller += 35;
                   } elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 1 GIGA FIDELIZADO + IP FIXO')) {
                       $this->starsSeller += 38;
                   } elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 1 GIGA SEM FIDELIDADE + IP FIXO')) {
                       $this->starsSeller += 36;
                   } elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 600 MEGA FIDELIZADO')) {
                       $this->starsSeller += 9;
                   } elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 600 MEGA FIDELIZADO + IP FIXO')) {
                       $this->starsSeller += 12;
                   } elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 800 MEGA FIDELIZADO')) {
                       $this->starsSeller += 15;
                   }

                   // Se o mês do cadastro do contrato for AGOSTO, executa esse bloco.
               } elseif (Carbon::parse($item->data_contrato) < Carbon::parse('2022-09-01') &&
                   Carbon::parse($item->data_contrato) >= Carbon::parse('2022-08-01')) {

                   // Verifica qual é o plano e atribui a estrela correspondente.
                   if (str_contains($item->plano, 'PLANO 1 GIGA FIDELIZADO + DEEZER + HBO MAX + DR. AGE')) {
                       $this->starsSeller += 30;
                   } elseif (str_contains($item->plano, 'PLANO 1 GIGA FIDELIZADO + DEEZER PREMIUM')) {
                       $this->starsSeller += 15;
                   } elseif (str_contains($item->plano, 'PLANO 1 GIGA NÃO FIDELIZADO + DEEZER PREMIUM')) {
                       $this->starsSeller += 0;
                   } elseif (str_contains($item->plano, 'PLANO 120 MEGA PROMOCAO LEVE 360 MEGA')) {
                       $this->starsSeller += 7;
                   } elseif (str_contains($item->plano, 'PLANO 240 MEGA PROMOCAO LEVE 720 MEGA  + DEEZER PREMIUM')) {
                       $this->starsSeller += 9;
                   } elseif (str_contains($item->plano, 'PLANO 240 MEGA PROMOCAO LEVE 720 MEGA  + DEEZER PREMIUM')) {
                       $this->starsSeller += 9;
                   } elseif (str_contains($item->plano, 'PLANO 400 MEGA FIDELIZADO')) {
                       $this->starsSeller += 7;
                   } elseif (str_contains($item->plano, 'PLANO 480 MEGA FIDELIZADO')) {
                       $this->starsSeller += 7;
                   } elseif (str_contains($item->plano, 'PLANO 480 MEGA NÃO FIDELIZADO')) {
                       $this->starsSeller += 0;
                   } elseif (str_contains($item->plano, 'PLANO 740 MEGA FIDELIZADO')) {
                       $this->starsSeller += 9;
                   } elseif (str_contains($item->plano, 'PLANO 800 MEGA FIDELIZADO')) {
                       $this->starsSeller += 15;
                   } elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 1 GIGA FIDELIZADO')) {
                       $this->starsSeller += 35;
                   } elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 1 GIGA FIDELIZADO + DEEZER PREMIUM')) {
                       $this->starsSeller += 35;
                   } elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 600 MEGA FIDELIZADO')) {
                       $this->starsSeller += 9;
                   } elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 600 MEGA FIDELIZADO + IP FIXO')) {
                       $this->starsSeller += 12;
                   } elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 800 MEGA FIDELIZADO')) {
                       $this->starsSeller += 17;
                   }
               }

           }
        });

        $this->starsSupTotal += $this->starsSeller;

        return $this->starsSeller;
    }

    private function valueStarSeller($name) {

        $data = DB::table('agerv_colaboradores as c')
                    ->leftJoin('agerv_colaboradores_meta as cm', 'cm.colaborador_id', '=', 'c.id')
                    ->leftJoin('agerv_colaboradores_canais as cc', 'c.tipo_comissao_id', '=', 'cc.id')
                    ->where('c.nome', $name)
                    ->where('cm.mes_competencia', $this->month)
                    ->select('c.id', 'cc.canal', 'cm.meta')
                    ->first();

        if (!isset($data->meta)) {
            $this->valueStars = 0;
            return "Sem meta";
        } else {

            if ($data->meta === 0) {
                $this->valueStars = 0;
                return "Meta zerada";
            } else {
                $this->valueStars = 0;
                $this->metaPercent = ($this->salesSeller / $data->meta) * 100;
                $this->metaSeller = $data->meta;

                if (isset($data->meta)) {

                    // Bloco responsável pela meta mínima e máxima, aplicando valor às estrelas.
                     if ($data->canal === 'MCV') {

                        // Verifica o mês e aplica a diferença na meta mínima
                        if ($this->month <= '07') {
                            $this->minMeta = 70;
                        } elseif ($this->month === '08') {
                            $this->minMeta = 60;
                        }

                        if ($this->metaPercent >= $this->minMeta && $this->metaPercent < 100) {
                            $this->valueStars = 0.90;
                        } elseif ($this->metaPercent >= 100 && $this->metaPercent < 120) {
                            $this->valueStars = 1.20;
                        } elseif ($this->metaPercent >= 120 && $this->metaPercent < 141) {
                            $this->valueStars = 2;
                        } elseif ($this->metaPercent >= 141) {
                            $this->valueStars = 4.5;
                        }

                    } elseif ($data->canal === 'PAP') {

                        if ($this->month === '07') {

                            if ($this->metaPercent >= 60 && $this->metaPercent < 100) {
                                $this->valueStars = 1.3;
                            } elseif ($this->metaPercent >= 100 && $this->metaPercent < 120) {
                                $this->valueStars = 3;
                            } elseif ($this->metaPercent >= 120 && $this->metaPercent < 141) {
                                $this->valueStars = 5;
                            } elseif ($this->metaPercent >= 141) {
                                $this->valueStars = 7;
                            }
                        } elseif ($this->month === '08') {

                            if ($this->metaPercent >= 60 && $this->metaPercent < 100) {
                                $this->valueStars = 2.50;
                            } elseif ($this->metaPercent >= 100 && $this->metaPercent < 120) {
                                $this->valueStars = 5;
                            } elseif ($this->metaPercent >= 120 && $this->metaPercent < 141) {
                                $this->valueStars = 7;
                            } elseif ($this->metaPercent >= 141) {
                                $this->valueStars = 10;
                            }
                        }
                    } elseif ($data->canal === 'LIDER') {

                        if ($this->month === '07') {
                            if ($this->metaPercent >= 60 && $this->metaPercent < 100) {
                                $this->valueStars = 0.25;
                            } elseif ($this->metaPercent >= 100 && $this->metaPercent < 120) {
                                $this->valueStars = 0.40;
                            } elseif ($this->metaPercent >= 120 && $this->metaPercent < 141) {
                                $this->valueStars = 0.80;
                            } elseif ($this->metaPercent >= 141) {
                                $this->valueStars = 1.30;
                            }
                        } elseif ($this->month === '08') {

                            if ($this->metaPercent >= 60 && $this->metaPercent < 100) {
                                $this->valueStars = 0.6;
                            } elseif ($this->metaPercent >= 100 && $this->metaPercent < 120) {
                                $this->valueStars = 0.9;
                            } elseif ($this->metaPercent >= 120 && $this->metaPercent < 141) {
                                $this->valueStars = 1.5;
                            } elseif ($this->metaPercent >= 141) {
                                $this->valueStars = 3;
                            }

                        }
                    }

                    return $this->valueStars;

                } else {
                    return "Nenhum colaborador ativo";
                }
            }
        }
    }

    private function commissionSeller() {

        $commission = $this->starsSeller * $this->valueStars;

        if($this->d7Seller > 0) {
            $commission = $commission * 0.9;
            $this->deflatorSeller = -10;
        } else {
            $commission = $commission * 1.1;
            $this->deflatorSeller = 10;
        }

        $this->commissionTotal += $commission;
        $this->commissionChannel += $commission;

        return number_format($commission, 2, ',', '.');

    }

    private function valueStarSup($name) {

        $data = DB::table('agerv_colaboradores as c')
            ->leftJoin('agerv_colaboradores_meta as cm', 'cm.colaborador_id', '=', 'c.id')
            ->leftJoin('agerv_colaboradores_canais as cc', 'c.tipo_comissao_id', '=', 'cc.id')
            ->where('c.nome', $name)
            ->where('cm.mes_competencia', $this->month)
            ->select('c.id', 'cc.canal', 'cm.meta')
            ->first();

        if (!isset($data->meta)) {
            $this->valueStars = 0;
            $this->metaPercent = 0;
            return "Sem meta";
        } else {

            if ($data->meta === 0) {
                $this->metaPercent = 0;
                return "Meta zerada";
            } else {
                $this->metaPercent = ($this->salesSup / $data->meta) * 100;
                $this->metaSup = $data->meta;

                if (isset($data->meta)) {

                    // Bloco responsável pela meta mínima e máxima, aplicando valor às estrelas.
                     if($data->canal === 'LIDER') {

                        if ($this->month === '07') {
                            if ($this->metaPercent >= 60 && $this->metaPercent < 100) {
                                $this->valueStarsSup = 0.25;
                            } elseif ($this->metaPercent >= 100 && $this->metaPercent < 120) {
                                $this->valueStarsSup = 0.40;
                            } elseif ($this->metaPercent >= 120 && $this->metaPercent < 141) {
                                $this->valueStarsSup = 0.80;
                            } elseif ($this->metaPercent >= 141) {
                                $this->valueStarsSup = 1.30;
                            }
                        } elseif ($this->month === '08') {

                            if ($this->metaPercent >= 60 && $this->metaPercent < 100) {
                                $this->valueStarsSup = 0.6;
                            } elseif ($this->metaPercent >= 100 && $this->metaPercent < 120) {
                                $this->valueStarsSup = 0.9;
                            } elseif ($this->metaPercent >= 120 && $this->metaPercent < 141) {
                                $this->valueStarsSup = 1.5;
                            } elseif ($this->metaPercent >= 141) {
                                $this->valueStarsSup = 3;
                            }

                        }
                    }

                    return $this->valueStarsSup;

                } else {
                    return "Nenhum colaborador ativo";
                }
            }
        }
    }

    private function commissionSup() {

        $commission = $this->starsSupTotal * $this->valueStarsSup;

        // Removido a pedido da Liandra Buck
//        if($this->salesSupCancelledsD7 > 0) {
//            $commission = $commission * 0.9;
//            $this->deflatorSup = -10;
//        } else {
//            $commission = $commission * 1.1;
//            $this->deflatorSup = 10;
//        }

        $this->commissionTotal += $commission;
        $this->commissionChannel += $commission;

        return number_format($commission, 2, ',', '.');
    }


    /*
     *
     *
     * Modelo de visão SUPERVISOR
     *
     *
     */

    private function supervisor() {


        $supervisor = Collaborator::where('user_id', auth()->user()->id)
                                    ->first();

        $collaborator = Collaborator::where('supervisor_id', $supervisor->id)
                        ->select('nome')
                        ->get();

        return [
            'supervisor' => $supervisor->nome,
            'salesTotal' => $this->supervisorSalesTotals($collaborator),
            'sellers' => $this->sellers($supervisor->nome),
            'salesCancelled' => [
                'count' => $this->salesSupCancelleds,
                'extract' => $this->salesSupCancelledsExtract
            ],
            'starsTotal' => $this->starsSupTotal,
            'valueStar' => $this->valueStarSup($supervisor->nome),
            'commission' => $this->commissionSup(),
            'deflator' => 0, //$this->deflatorSup,
            'meta' => $this->metaSup,
            'metaPercent' => number_format($this->metaPercent, 2),
        ];


    }

    private function supervisorSalesTotals ($collaborator) {

        // Trás a contagem de todas as vendas realizadas no mês filtrado.
        $this->salesTotals = VoalleSales::whereMonth('data_vigencia', $this->month)
            ->whereYear('data_vigencia', $this->year)
            ->whereMonth('data_ativacao', $this->month)
            ->whereYear('data_ativacao', $this->year)
            ->whereMonth('data_contrato', '>=', '06')
            ->whereIn('vendedor', $collaborator)
            ->whereYear('data_contrato', $this->year)
            ->where('status', '<>', 'Cancelado')
            ->select('id_contrato', 'nome_cliente', 'status', 'situacao', 'data_contrato', 'data_ativacao', 'data_vigencia',
                'vendedor', 'supervisor', 'data_cancelamento', 'plano')
            ->get();


        $this->salesTotalsCount += count($this->salesTotals);

        return [
            'extract' => 0,// $this->salesTotals,
            'count' => count($this->salesTotals)
        ];

    }
}
