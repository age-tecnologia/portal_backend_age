<?php

namespace App\Http\Controllers\AgeRv;

use App\Http\Controllers\Controller;
use App\Models\AgeRv\Collaborator;
use App\Models\AgeRv\CollaboratorMeta;
use App\Models\AgeRv\VoalleSales;
use Carbon\Carbon;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RvSellerController extends Controller
{
    /*
     * Esse bloco é responsável por apresentar o dashboard de vendas levando em consideração
     * todas as regras dadas pela diretoria pro sistema de comissionamento, abaixo está descrito as mesmas.
     *
     * 1 - Vendas instaladas e aprovadas.
     * 2 - Deflator tem como valor padrão 10%, caso aja cancelamento nas vendas antes dos 7 dias, o valor é -10%.
     * 3 - Estrelas são acumulativas, baseada no plano e quantidade de vendas daquele plano em específico,
     * exemplo: 120Mbps equivalem a X Estrelas.
     * 4 - Valor da estrela, é baseado na meta. Exemplo: Acima de 60% e abaixo de 99%, o valor é X,
     * acima de 99%, o valor é Y.
     * 5 - Comissão é a multiplicação das estrelas pelo valor da estrela + deflator.
     * Cálculo: (estrela x valor_estrela) + deflator
     */


    private string $username; // Dados do usuário para extração do relatório.
    private int $id; // Dados do usuário para extração do relatório.
    private string $channel; // Dados do canal que o usuário pertence.
    private string $month; // Mês para filtro das vendas.
    private string $year; // Ano para filtro das vendas.
    private int $cancelD7 = 0;  // Vendas canceladas antes dos 7 dias.
    private int $deflator = 0; // Deflator de adição ou subtração.
    private int $sales = 0; // Vendas validas.
    private int $salesTotals; // Vendas totais.
    private int $salesAprovation; // Vendas em aprovação.
    private int $stars = 0; // Estrelas acumuladas.
    private int $cancelTotals = 0; // Cancelamentos totais, antes ou depois dos 7 dias.
    private int $meta = 0; // Meta do colaborador.
    private int $minMeta = 0; // Mínimo a ser atingido.
    private float $metaPercent = 0.0; // Percentual da meta atingida.
    private float $valueStars = 0.0; // Valor das estrelas, baseado no percentual da meta atingida.
    private $commission; // Comissão, sendo o cálculo (estrela x valor_estrela) + deflator.
    private int $valueStar; // Valor da estrela do plano individualmente.

    /*
     * Bloco responsável pela chamada nos outros métodos e envio do json contendo as informações pro dashboard.
     */
    public function seller(Request $request)
    {

        $validated = $request->validate([
           'year' => 'required',
           'month' => 'required'
        ]);

        $collaborator = Collaborator::where('user_id', auth()->user()->id)->select('user_id','nome')->first();

        if(! isset($collaborator->nome)) {
            return response()->json(['Usuário sem colaborador vinculado!'], 406);
        }

        $this->username = $collaborator->nome;
        $this->id = $collaborator->user_id;

        $this->year = $request->input('year'); // Recupera o ano filtrado.
        $this->month = $request->input('month'); // Recupera o mês filtrado.


        return response()->json([
            'cancelD7' => $this->cancelD7(),
            'deflator' => $this->deflator(),
            'sales' => $this->sales(),
            'salesTotals' => $this->salesTotals(),
            'salesAprovation' => $this->salesAprovation(),
            'stars' => $this->stars(),
            'cancelTotals' => $this->cancelTotals(),
            'metaPercent' => $this->metaPercent(),
            'valueStars' => $this->valueStars(),
            'commission' => $this->commission(),
            'meta' => $this->meta,
            'minMeta' => $this->minMeta,
            'extractStars' => $this->extractStars(),
            'extractSalesTotals' => $this->extractSalesTotals(),
            'extractSalesAprovation' => $this->extractSalesAprovation()
        ], 201);
    }

    /*
     * Retorna todas as vendas canceladas com menos de 7 dias no período.
     */
    public function cancelD7() : int
    {
        // Trás todas as vendas com situação cancelada e com a vigência do mês/ano requisitado
        $sales = VoalleSales::select('id',
                                     'id_contrato',
                                     'nome_cliente',
                                     'status',
                                     'situacao',
                                     'data_contrato',
                                     'data_ativacao',
                                     'data_vigencia',
                                     'data_cancelamento',
                                     'plano')
                            ->whereYear('data_vigencia', $this->year)
                            ->whereMonth('data_vigencia', $this->month)
                            ->where('situacao', 'Cancelado')
                            ->where('vendedor', $this->username)
                            ->get();


        // Percorre todas as vendas onde a situação é cancelada, verifica se o cancelamento ocorreu em
        // menos de 7 dias, se sim, adiciona "inválida" ao status, fazendo com que não seja comissionado.
        $sales->each(function($item, $key) {
            if($item->situacao === 'Cancelado') {
                $dateActivation = Carbon::parse($item->data_ativacao); // Transformando em data.
                $dateCancel = Carbon::parse($item->data_cancelamento); // Transformando em data.

                // Verificando se o cancelamento foi em menos de 7 dias, se sim, atualiza o banco com inválida.
                if($dateActivation->diffInDays($dateCancel) < 7) {
                    $update = VoalleSales::findOrFail($item->id);
                    $update->update([
                       'status' => 'Inválida'
                    ]);
                }
            }
        });

        // Busca as vendas inválidas, para apresentar no dashboard.
        $this->cancelD7 = VoalleSales::where('status', 'Inválida')
                                        ->where('vendedor', $this->username)
                                        ->whereMonth('data_vigencia', $this->month)
                                        ->count();

        return $this->cancelD7;
    }

    /*
     * Retorna o valor do deflator, baseado na regra de negócio.
     */
    public function deflator() : int
    {
        // Valida se existe cancelamento antes dos 7 dias, caso aja, o deflator passa a penalizar em -10%
        if($this->cancelD7 > 0) {
            $this->deflator = -10;
        } else {
            $this->deflator = 10;
        }

        return $this->deflator;
    }

    /*
     * Retorna a contagem de vendas válidas no período.
     */
    public function sales() : int
    {
        // Trás a contagem de todas as vendas realizadas no mês filtrado.
        $this->sales = VoalleSales::where('vendedor', $this->username)
                                    ->whereMonth('data_vigencia', $this->month)
                                    ->whereYear('data_vigencia', $this->year)
                                    ->whereMonth('data_ativacao', $this->month)
                                    ->whereYear('data_ativacao', $this->year)
                                    ->whereMonth('data_contrato','>=', '05')
                                    ->whereYear('data_contrato', $this->year)
                                    ->where('status', 'Aprovado')
                                    ->count();
        return $this->sales;
    }

    /*
     * Retorna a contagem de vendas totais no período.
     */
    public function salesTotals() : int
    {
        // Trás a contagem de todas as vendas realizadas no mês filtrado.
        $this->salesTotals = VoalleSales::where('vendedor', $this->username)
            ->whereMonth('data_vigencia', $this->month)
            ->whereYear('data_vigencia', $this->year)
            ->whereMonth('data_ativacao', $this->month)
            ->whereYear('data_ativacao', $this->year)
            ->whereMonth('data_contrato','>=', '05')
            ->whereYear('data_contrato', $this->year)
            ->count();
        return $this->salesTotals;
    }

    /*
     * Retorna a contagem de vendas em aprovação no período.
     */
    public function salesAprovation() : int
    {
        // Trás a contagem de todas as vendas realizadas no mês filtrado.
        $this->salesAprovation = VoalleSales::where('vendedor', $this->username)
            ->whereMonth('data_contrato', $this->month)
            ->whereYear('data_contrato', $this->year)
            ->where('status', 'Em Aprovação')
            ->count();

        return $this->salesAprovation;
    }

    /*
     * Retorna a contagem de estrelas baseada no plano vendido.
     */
    public function stars() : int
    {
        // Trás os planos vendidos, onde o status não for inválido
        $plans = VoalleSales::select('plano', 'data_contrato')
                            ->whereMonth('data_vigencia', $this->month)
                            ->whereYear('data_vigencia', $this->year)
                            ->where('status', 'Aprovado')
                            ->where('vendedor', $this->username)
                            ->whereMonth('data_ativacao', $this->month)
                            ->whereYear('data_ativacao', $this->year)
                            ->whereMonth('data_contrato','>=', '05')
                            ->whereYear('data_contrato', $this->year)
                            ->get();

        // Percorre todos os dados, verificando qual o plano vendido e atribui as estrelas devidas.
        $plans->each(function($item) {

            // Se o mês do cadastro do contrato for MAIO, executa esse bloco.
            if( Carbon::parse($item->data_contrato) < Carbon::parse('2022-06-01') &&
                Carbon::parse($item->data_contrato) >= Carbon::parse('2022-05-01')) {

                // Verifica qual é o plano e atribui a estrela correspondente.
                if(str_contains($item->plano,'PLANO 120 MEGA PROMOCAO LEVE 360 MEGA')) {
                    $this->stars += 5;
                } elseif(str_contains($item->plano,'PLANO 240 MEGA PROMOCAO LEVE 720 MEGA  + DEEZER')) {
                    $this->stars += 9;
                } elseif(str_contains($item->plano,'PLANO 240 MEGA PROMOCAO LEVE 720 MEGA  + DEEZER PREMIUM')) {
                    $this->stars += 9;
                } elseif(str_contains($item->plano,'PLANO 240 MEGA PROMOCAO LEVE 960 MEGA ')) {
                    $this->stars += 9;
                } elseif(str_contains($item->plano,'PLANO 360 MEGA')) {
                    $this->stars += 11;
                } elseif(str_contains($item->plano,'PLANO 400 MEGA FIDELIZADO')) {
                    $this->stars += 15;
                } elseif(str_contains($item->plano,'PLANO 480 MEGA - FIDELIZADO')) {
                    $this->stars += 15;
                } elseif(str_contains($item->plano,'PLANO 720 MEGA ')) {
                    $this->stars += 25;
                } elseif(str_contains($item->plano,'PLANO 740 MEGA FIDELIZADO')) {
                    $this->stars += 25;
                } elseif(str_contains($item->plano,'PLANO 800 MEGA FIDELIZADO')) {
                    $this->stars += 17;
                } elseif(str_contains($item->plano,'PLANO 960 MEGA')) {
                    $this->stars += 35;
                } elseif(str_contains($item->plano,'PLANO 960 MEGA TURBINADO + AGE TV + DEEZER PREMIUM')) {
                    $this->stars += 35;
                } elseif(str_contains($item->plano,'PLANO EMPRESARIAL 1 GIGA FIDELIZADO')) {
                    $this->stars += 35;
                } elseif(str_contains($item->plano,'PLANO EMPRESARIAL 600 MEGA FIDELIZADO')) {
                    $this->stars += 9;
                } elseif(str_contains($item->plano,'PLANO EMPRESARIAL 600 MEGA FIDELIZADO + IP FIXO')) {
                    $this->stars += 12;
                } elseif(str_contains($item->plano,'PLANO EMPRESARIAL 800 MEGA FIDELIZADO')) {
                    $this->stars += 17;
                } elseif(str_contains($item->plano,'PLANO EMPRESARIAL 800 MEGA FIDELIZADO + IP FIXO')) {
                    $this->stars += 20;
                }

            // Se o mês do cadastro do contrato for JUNHO, executa esse bloco.
            } elseif(Carbon::parse($item->data_contrato) < Carbon::parse('2022-07-01') &&
                     Carbon::parse($item->data_contrato) >= Carbon::parse('2022-06-01')) {

                // Verifica qual é o plano e atribui a estrela correspondente.
                if(str_contains($item->plano,'COMBO EMPRESARIAL 600 MEGA + 1 FIXO BRASIL SEM FIDELIDADE')) {
                    $this->stars += 10;
                } elseif(str_contains($item->plano,'COMBO EMPRESARIAL 600 MEGA + 2 FIXOS BRASIL SEM FIDELIDADE')) {
                    $this->stars += 13;
                } elseif(str_contains($item->plano,'PLANO 120 MEGA')) {
                    $this->stars += 7;
                } elseif(str_contains($item->plano,'PLANO 120 MEGA PROMOCAO LEVE 360 MEGA')) {
                    $this->stars += 7;
                } elseif(str_contains($item->plano,'PLANO 240 MEGA PROMOCAO LEVE 720 MEGA  + DEEZER PREMIUM')) {
                    $this->stars += 9;
                } elseif(str_contains($item->plano,'PLANO 240 MEGA PROMOCAO LEVE 960 MEGA ')) {
                    $this->stars += 9;
                } elseif(str_contains($item->plano,'PLANO 240 MEGA SEM FIDELIDADE')) {
                    $this->stars += 0;
                } elseif(str_contains($item->plano,'PLANO 400 MEGA FIDELIZADO')) {
                    $this->stars += 15;
                } elseif(str_contains($item->plano,'PLANO 480 MEGA - FIDELIZADO')) {
                    $this->stars += 15;
                } elseif(str_contains($item->plano,'PLANO 720 MEGA ')) {
                    $this->stars += 25;
                } elseif(str_contains($item->plano,'PLANO 800 MEGA - COLABORADOR')) {
                    $this->stars += 17;
                } elseif(str_contains($item->plano,'PLANO 800 MEGA FIDELIZADO')) {
                    $this->stars += 17;
                } elseif(str_contains($item->plano,'PLANO 960 MEGA')) {
                    $this->stars += 35;
                } elseif(str_contains($item->plano,'PLANO 960 MEGA TURBINADO + AGE TV + DEEZER PREMIUM')) {
                    $this->stars += 35;
                } elseif(str_contains($item->plano,'PLANO EMPRESARIAL 1 GIGA FIDELIZADO')) {
                    $this->stars += 35;
                } elseif(str_contains($item->plano,'PLANO EMPRESARIAL 600 MEGA FIDELIZADO')) {
                    $this->stars += 9;
                } elseif(str_contains($item->plano,'PLANO EMPRESARIAL 800 MEGA FIDELIZADO')) {
                    $this->stars += 17;
                }

              // Se o mês do cadastro do contrato for JULHO, executa esse bloco.
            } elseif(Carbon::parse($item->data_contrato) < Carbon::parse('2022-08-01') &&
                     Carbon::parse($item->data_contrato) >= Carbon::parse('2022-07-01')) {

                // Verifica qual é o plano e atribui a estrela correspondente.
                if(str_contains($item->plano,'PLANO 1 GIGA FIDELIZADO + DEEZER + HBO MAX + DR. AGE')) {
                    $this->stars += 30;
                } elseif(str_contains($item->plano,'PLANO 1 GIGA FIDELIZADO + DEEZER PREMIUM')) {
                    $this->stars += 15;
                } elseif(str_contains($item->plano,'PLANO 120 MEGA PROMOCAO LEVE 360 MEGA')) {
                    $this->stars += 7;
                } elseif(str_contains($item->plano,'PLANO 120 MEGA SEM FIDELIDADE')) {
                    $this->stars += 0;
                } elseif(str_contains($item->plano,'PLANO 240 MEGA ')) {
                    $this->stars += 9;
                } elseif(str_contains($item->plano,'PLANO 240 MEGA PROMOCAO LEVE 720 MEGA  + DEEZER PREMIUM')) {
                    $this->stars += 9;
                } elseif(str_contains($item->plano,'PLANO 240 MEGA PROMOCAO LEVE 720 MEGA + DEEZER PREMIUM SEM FIDELIDADE')) {
                    $this->stars += 9;
                } elseif(str_contains($item->plano,'PLANO 400 MEGA - COLABORADOR')) {
                    $this->stars += 0;
                } elseif(str_contains($item->plano,'PLANO 400 MEGA FIDELIZADO')) {
                    $this->stars += 7;
                } elseif(str_contains($item->plano,'PLANO 400 MEGA NÃO FIDELIZADO')) {
                    $this->stars += 0;
                } elseif(str_contains($item->plano,'PLANO 480 MEGA - FIDELIZADO')) {
                    $this->stars += 7;
                } elseif(str_contains($item->plano,'PLANO 480 MEGA FIDELIZADO')) {
                    $this->stars += 7;
                } elseif(str_contains($item->plano,'PLANO 740 MEGA FIDELIZADO')) {
                    $this->stars += 9;
                } elseif(str_contains($item->plano,'PLANO 740 MEGA FIDELIZADO')) {
                    $this->stars += 9;
                } elseif(str_contains($item->plano,'PLANO 800 MEGA - CAMPANHA CONDOMÍNIO FIDELIZADO (AMBOS)')) {
                    $this->stars += 0;
                } elseif(str_contains($item->plano,'PLANO 800 MEGA - COLABORADOR')) {
                    $this->stars += 0;
                } elseif(str_contains($item->plano,'PLANO 800 MEGA FIDELIZADO')) {
                    $this->stars += 17;
                } elseif(str_contains($item->plano,'PLANO 960 MEGA')) {
                    $this->stars += 35;
                } elseif(str_contains($item->plano,'PLANO 960 MEGA (LOJAS)')) {
                    $this->stars += 0;
                } elseif(str_contains($item->plano,'PLANO EMPRESARIAL 1 GIGA FIDELIZADO')) {
                    $this->stars += 35;
                } elseif(str_contains($item->plano,'PLANO EMPRESARIAL 1 GIGA FIDELIZADO')) {
                    $this->stars += 35;
                } elseif(str_contains($item->plano,'PLANO EMPRESARIAL 1 GIGA FIDELIZADO + DEEZER PREMIUM')) {
                    $this->stars += 35;
                } elseif(str_contains($item->plano,'PLANO EMPRESARIAL 1 GIGA FIDELIZADO + IP FIXO')) {
                    $this->stars += 38;
                } elseif(str_contains($item->plano,'PLANO EMPRESARIAL 1 GIGA SEM FIDELIDADE + IP FIXO')) {
                    $this->stars += 36;
                } elseif(str_contains($item->plano,'PLANO EMPRESARIAL 600 MEGA FIDELIZADO')) {
                    $this->stars += 9;
                } elseif(str_contains($item->plano,'PLANO EMPRESARIAL 600 MEGA FIDELIZADO + IP FIXO')) {
                    $this->stars += 12;
                } elseif(str_contains($item->plano,'PLANO EMPRESARIAL 800 MEGA FIDELIZADO')) {
                    $this->stars += 15;
                }

              // Se o mês do cadastro do contrato for AGOSTO, executa esse bloco.
            } elseif(Carbon::parse($item->data_contrato) < Carbon::parse('2022-09-01') &&
                     Carbon::parse($item->data_contrato) >= Carbon::parse('2022-08-01')) {

                // Verifica qual é o plano e atribui a estrela correspondente.
                if(str_contains($item->plano,'PLANO 1 GIGA FIDELIZADO + DEEZER + HBO MAX + DR. AGE')) {
                    $this->stars += 30;
                } elseif(str_contains($item->plano,'PLANO 1 GIGA FIDELIZADO + DEEZER PREMIUM')) {
                    $this->stars += 15;
                } elseif(str_contains($item->plano,'PLANO 1 GIGA NÃO FIDELIZADO + DEEZER PREMIUM')) {
                    $this->stars += 0;
                } elseif(str_contains($item->plano,'PLANO 120 MEGA PROMOCAO LEVE 360 MEGA')) {
                    $this->stars += 7;
                } elseif(str_contains($item->plano,'PLANO 240 MEGA PROMOCAO LEVE 720 MEGA  + DEEZER PREMIUM')) {
                    $this->stars += 9;
                } elseif(str_contains($item->plano,'PLANO 240 MEGA PROMOCAO LEVE 720 MEGA  + DEEZER PREMIUM')) {
                    $this->stars += 9;
                } elseif(str_contains($item->plano,'PLANO 400 MEGA FIDELIZADO')) {
                    $this->stars += 7;
                } elseif(str_contains($item->plano,'PLANO 480 MEGA FIDELIZADO')) {
                    $this->stars += 7;
                } elseif(str_contains($item->plano,'PLANO 480 MEGA NÃO FIDELIZADO')) {
                    $this->stars += 0;
                } elseif(str_contains($item->plano,'PLANO 740 MEGA FIDELIZADO')) {
                    $this->stars += 9;
                } elseif(str_contains($item->plano,'PLANO 800 MEGA FIDELIZADO')) {
                    $this->stars += 15;
                } elseif(str_contains($item->plano,'PLANO EMPRESARIAL 1 GIGA FIDELIZADO')) {
                    $this->stars += 35;
                } elseif(str_contains($item->plano,'PLANO EMPRESARIAL 1 GIGA FIDELIZADO + DEEZER PREMIUM')) {
                    $this->stars += 35;
                } elseif(str_contains($item->plano,'PLANO EMPRESARIAL 600 MEGA FIDELIZADO')) {
                    $this->stars += 9;
                } elseif(str_contains($item->plano,'PLANO EMPRESARIAL 600 MEGA FIDELIZADO + IP FIXO')) {
                    $this->stars += 12;
                } elseif(str_contains($item->plano,'PLANO EMPRESARIAL 800 MEGA FIDELIZADO')) {
                    $this->stars += 17;
                }
            }
        });

        return $this->stars;

    }

    /*
     * Retorna a contagem de cancelamento total no período.
     */
    public function cancelTotals() : int
    {
        $this->cancelTotals = VoalleSales::select('plano', 'data_contrato')
                                        ->whereMonth('data_vigencia', $this->month)
                                        ->whereYear('data_vigencia', $this->year)
                                        ->where('status', '<>', 'Aprovado')
                                        ->where('status', '<>', 'Em Aprovação')
                                        ->where('vendedor', $this->username)
                                        ->count();

        return $this->cancelTotals;
    }

    /*
     * Retorna a porcentagem da meta atingida no período..
     */
    public function metaPercent() : float
    {

        $data = CollaboratorMeta::where('colaborador_id', $this->id)
                                ->where('mes_competencia', $this->month)
                                ->first();

        if(! isset($data->meta)) {
            return 0;
        }

        $this->meta = $data->meta;


        $this->metaPercent = number_format(($this->sales / $this->meta) * 100, 2);

        return $this->metaPercent;

    }

    /*
     * Retorna a contagem de estrelas no período.
     */
    public function valueStars() : float
    {

        $data = DB::table('agerv_colaboradores as c')
                    ->leftJoin('agerv_colaboradores_canais as cc', 'c.canal_id', '=', 'cc.id')
                    ->select('c.nome','cc.canal')
                    ->first();

        $this->channel = $data->canal;

            // Bloco responsável pela meta mínima e máxima, aplicando valor às estrelas.
            if($this->channel === 'PJ') {
                if($this->metaPercent >= 60 && $this->metaPercent < 100) {
                    $this->valueStars = 1.30;
                } elseif($this->metaPercent >= 100 && $this->metaPercent < 120) {
                    $this->valueStars = 3;
                } elseif($this->metaPercent >= 120 && $this->metaPercent < 141) {
                    $this->valueStars = 5;
                } elseif($this->metaPercent >= 141) {
                    $this->valueStars = 7;
                }
            } elseif ($this->channel === 'MCV') {

                // Verifica o mês e aplica a diferença na meta mínima
                if($this->month <= '07') {
                    $this->minMeta = 70;
                } elseif($this->month === '08') {
                    $this->minMeta = 60;
                }

                if($this->metaPercent >= $this->minMeta && $this->metaPercent < 100) {
                    $this->valueStars = 0.90;
                } elseif($this->metaPercent >= 100 && $this->metaPercent < 120) {
                    $this->valueStars = 1.20;
                } elseif($this->metaPercent >= 120 && $this->metaPercent < 141) {
                    $this->valueStars = 2;
                } elseif($this->metaPercent >= 141) {
                    $this->valueStars = 4.5;
                }

            } elseif ($this->channel === 'PAP') {

                if($this->metaPercent >= 60 && $this->metaPercent < 100) {
                    $this->valueStars = 1.30;
                } elseif($this->metaPercent >= 100 && $this->metaPercent < 120) {
                    $this->valueStars = 3;
                } elseif($this->metaPercent >= 120 && $this->metaPercent < 141) {
                    $this->valueStars = 5;
                } elseif($this->metaPercent >= 141) {
                    $this->valueStars = 7;
                }
            } elseif($this->channel === 'LIDER') {
                if($this->metaPercent >= 60 && $this->metaPercent < 100) {
                    $this->valueStars = 0.25;
                } elseif($this->metaPercent >= 100 && $this->metaPercent < 120) {
                    $this->valueStars = 0.40;
                } elseif($this->metaPercent >= 120 && $this->metaPercent < 141) {
                    $this->valueStars = 0.80;
                } elseif($this->metaPercent >= 141) {
                    $this->valueStars = 1.30;
                }
            }

        return $this->valueStars;
    }

    /*
     * Retorna o valor da comissão no período.
     */
    public function commission()
    {
        $this->commission = $this->valueStars * $this->stars;

        if($this->commission > 0) {
            if($this->deflator > 0) {
                $this->commission = $this->commission * 1.1;
            } elseif($this->deflator < 0) {
                $this->commission = $this->commission * 0.9;
            }
        }

        return number_format($this->commission, 2, ',', '.');
    }

    /*
     * Retorna o extrato das estrelas de acordo com o plano
     */
    public function extractStars()
    {
        // Trás os planos vendidos, onde o status não for inválido
        $plans = VoalleSales::selectRaw('plano, count(*) as "qntd" ')
            ->whereMonth('data_vigencia', $this->month)
            ->whereYear('data_vigencia', $this->year)
            ->where('status', 'Aprovado')
            ->where('vendedor', $this->username)
            ->whereMonth('data_ativacao', $this->month)
            ->whereYear('data_ativacao', $this->year)
            ->whereMonth('data_contrato','>=', '05')
            ->whereYear('data_contrato', $this->year)
            ->distinct()
            ->groupBy('plano')
            ->get();

        $data = [];

        foreach($plans as $plan => $value) {
            $data[] = [
                "plan" => $value->plano,
                'qntd' => $value->qntd,
                'valueStar' => $this->valueStar($value->plano),
                'totals' => $this->valueStar * $value->qntd
            ];
        }

        return $data;
    }

    /*
     * Retorna o valor estrelas de acordo com o plano AUXILIANDO a função @extractStars()
     */
    public function valueStar($plan)
    {

        $plans = VoalleSales::select('plano', 'data_contrato')
            ->where('plano', $plan)
            ->where('vendedor', $this->username)
            ->whereMonth('data_vigencia', $this->month)
            ->whereYear('data_vigencia', $this->year)
            ->where('status', 'Aprovado')
            ->where('vendedor', $this->username)
            ->whereMonth('data_ativacao', $this->month)
            ->whereYear('data_ativacao', $this->year)
            ->whereMonth('data_contrato','>=', '05')
            ->whereYear('data_contrato', $this->year)
            ->get();

        $plans->each(function($item) {

            // Se o mês do cadastro do contrato for MAIO, executa esse bloco.
            if( Carbon::parse($item->data_contrato) < Carbon::parse('2022-06-01') &&
                Carbon::parse($item->data_contrato) >= Carbon::parse('2022-05-01')) {

                // Verifica qual é o plano e atribui a estrela correspondente.
                if(str_contains($item->plano,'PLANO 120 MEGA PROMOCAO LEVE 360 MEGA')) {
                    $this->valueStar = 5;
                } elseif(str_contains($item->plano,'PLANO 240 MEGA PROMOCAO LEVE 720 MEGA  + DEEZER')) {
                    $this->valueStar = 9;
                } elseif(str_contains($item->plano,'PLANO 240 MEGA PROMOCAO LEVE 720 MEGA  + DEEZER PREMIUM')) {
                    $this->valueStar = 9;
                } elseif(str_contains($item->plano,'PLANO 240 MEGA PROMOCAO LEVE 960 MEGA ')) {
                    $this->valueStar = 9;
                } elseif(str_contains($item->plano,'PLANO 360 MEGA')) {
                    $this->valueStar = 11;
                } elseif(str_contains($item->plano,'PLANO 400 MEGA FIDELIZADO')) {
                    $this->valueStar = 15;
                } elseif(str_contains($item->plano,'PLANO 480 MEGA - FIDELIZADO')) {
                    $this->valueStar = 15;
                } elseif(str_contains($item->plano,'PLANO 720 MEGA ')) {
                    $this->valueStar = 25;
                } elseif(str_contains($item->plano,'PLANO 740 MEGA FIDELIZADO')) {
                    $this->valueStar = 25;
                } elseif(str_contains($item->plano,'PLANO 800 MEGA FIDELIZADO')) {
                    $this->valueStar = 17;
                } elseif(str_contains($item->plano,'PLANO 960 MEGA')) {
                    $this->valueStar = 35;
                } elseif(str_contains($item->plano,'PLANO 960 MEGA TURBINADO + AGE TV + DEEZER PREMIUM')) {
                    $this->valueStar = 35;
                } elseif(str_contains($item->plano,'PLANO EMPRESARIAL 1 GIGA FIDELIZADO')) {
                    $this->valueStar = 35;
                } elseif(str_contains($item->plano,'PLANO EMPRESARIAL 600 MEGA FIDELIZADO')) {
                    $this->valueStar = 9;
                } elseif(str_contains($item->plano,'PLANO EMPRESARIAL 600 MEGA FIDELIZADO + IP FIXO')) {
                    $this->valueStar = 12;
                } elseif(str_contains($item->plano,'PLANO EMPRESARIAL 800 MEGA FIDELIZADO')) {
                    $this->valueStar = 17;
                } elseif(str_contains($item->plano,'PLANO EMPRESARIAL 800 MEGA FIDELIZADO + IP FIXO')) {
                    $this->valueStar = 20;
                }

                // Se o mês do cadastro do contrato for JUNHO, executa esse bloco.
            } elseif(Carbon::parse($item->data_contrato) < Carbon::parse('2022-07-01') &&
                Carbon::parse($item->data_contrato) >= Carbon::parse('2022-06-01')) {

                // Verifica qual é o plano e atribui a estrela correspondente.
                if(str_contains($item->plano,'COMBO EMPRESARIAL 600 MEGA + 1 FIXO BRASIL SEM FIDELIDADE')) {
                    $this->valueStar = 10;
                } elseif(str_contains($item->plano,'COMBO EMPRESARIAL 600 MEGA + 2 FIXOS BRASIL SEM FIDELIDADE')) {
                    $this->valueStar = 13;
                } elseif(str_contains($item->plano,'PLANO 120 MEGA')) {
                    $this->valueStar = 7;
                } elseif(str_contains($item->plano,'PLANO 120 MEGA PROMOCAO LEVE 360 MEGA')) {
                    $this->valueStar = 7;
                } elseif(str_contains($item->plano,'PLANO 240 MEGA PROMOCAO LEVE 720 MEGA  + DEEZER PREMIUM')) {
                    $this->valueStar = 9;
                } elseif(str_contains($item->plano,'PLANO 240 MEGA PROMOCAO LEVE 960 MEGA ')) {
                    $this->valueStar = 9;
                } elseif(str_contains($item->plano,'PLANO 240 MEGA SEM FIDELIDADE')) {
                    $this->valueStar = 0;
                } elseif(str_contains($item->plano,'PLANO 400 MEGA FIDELIZADO')) {
                    $this->valueStar = 15;
                } elseif(str_contains($item->plano,'PLANO 480 MEGA - FIDELIZADO')) {
                    $this->valueStar = 15;
                } elseif(str_contains($item->plano,'PLANO 720 MEGA ')) {
                    $this->valueStar = 25;
                } elseif(str_contains($item->plano,'PLANO 800 MEGA - COLABORADOR')) {
                    $this->valueStar = 17;
                } elseif(str_contains($item->plano,'PLANO 800 MEGA FIDELIZADO')) {
                    $this->valueStar = 17;
                } elseif(str_contains($item->plano,'PLANO 960 MEGA')) {
                    $this->valueStar = 35;
                } elseif(str_contains($item->plano,'PLANO 960 MEGA TURBINADO + AGE TV + DEEZER PREMIUM')) {
                    $this->valueStar = 35;
                } elseif(str_contains($item->plano,'PLANO EMPRESARIAL 1 GIGA FIDELIZADO')) {
                    $this->valueStar = 35;
                } elseif(str_contains($item->plano,'PLANO EMPRESARIAL 600 MEGA FIDELIZADO')) {
                    $this->valueStar = 9;
                } elseif(str_contains($item->plano,'PLANO EMPRESARIAL 800 MEGA FIDELIZADO')) {
                    $this->valueStar = 17;
                }

                // Se o mês do cadastro do contrato for JULHO, executa esse bloco.
            } elseif(Carbon::parse($item->data_contrato) < Carbon::parse('2022-08-01') &&
                Carbon::parse($item->data_contrato) >= Carbon::parse('2022-07-01')) {

                // Verifica qual é o plano e atribui a estrela correspondente.
                if(str_contains($item->plano,'PLANO 1 GIGA FIDELIZADO + DEEZER + HBO MAX + DR. AGE')) {
                    $this->valueStar = 30;
                } elseif(str_contains($item->plano,'PLANO 1 GIGA FIDELIZADO + DEEZER PREMIUM')) {
                    $this->valueStar = 15;
                } elseif(str_contains($item->plano,'PLANO 120 MEGA PROMOCAO LEVE 360 MEGA')) {
                    $this->valueStar = 7;
                } elseif(str_contains($item->plano,'PLANO 120 MEGA SEM FIDELIDADE')) {
                    $this->valueStar = 0;
                } elseif(str_contains($item->plano,'PLANO 240 MEGA ')) {
                    $this->valueStar = 9;
                } elseif(str_contains($item->plano,'PLANO 240 MEGA PROMOCAO LEVE 720 MEGA  + DEEZER PREMIUM')) {
                    $this->valueStar = 9;
                } elseif(str_contains($item->plano,'PLANO 240 MEGA PROMOCAO LEVE 720 MEGA + DEEZER PREMIUM SEM FIDELIDADE')) {
                    $this->valueStar = 9;
                } elseif(str_contains($item->plano,'PLANO 400 MEGA - COLABORADOR')) {
                    $this->valueStar = 0;
                } elseif(str_contains($item->plano,'PLANO 400 MEGA FIDELIZADO')) {
                    $this->valueStar = 7;
                } elseif(str_contains($item->plano,'PLANO 400 MEGA NÃO FIDELIZADO')) {
                    $this->valueStar = 0;
                } elseif(str_contains($item->plano,'PLANO 480 MEGA - FIDELIZADO')) {
                    $this->valueStar = 7;
                } elseif(str_contains($item->plano,'PLANO 480 MEGA FIDELIZADO')) {
                    $this->valueStar = 7;
                } elseif(str_contains($item->plano,'PLANO 740 MEGA FIDELIZADO')) {
                    $this->valueStar = 9;
                } elseif(str_contains($item->plano,'PLANO 740 MEGA FIDELIZADO')) {
                    $this->valueStar = 9;
                } elseif(str_contains($item->plano,'PLANO 800 MEGA - CAMPANHA CONDOMÍNIO FIDELIZADO (AMBOS)')) {
                    $this->valueStar = 0;
                } elseif(str_contains($item->plano,'PLANO 800 MEGA - COLABORADOR')) {
                    $this->valueStar = 0;
                } elseif(str_contains($item->plano,'PLANO 800 MEGA FIDELIZADO')) {
                    $this->valueStar = 17;
                } elseif(str_contains($item->plano,'PLANO 960 MEGA')) {
                    $this->valueStar = 35;
                } elseif(str_contains($item->plano,'PLANO 960 MEGA (LOJAS)')) {
                    $this->valueStar = 0;
                } elseif(str_contains($item->plano,'PLANO EMPRESARIAL 1 GIGA FIDELIZADO')) {
                    $this->valueStar = 35;
                } elseif(str_contains($item->plano,'PLANO EMPRESARIAL 1 GIGA FIDELIZADO')) {
                    $this->valueStar = 35;
                } elseif(str_contains($item->plano,'PLANO EMPRESARIAL 1 GIGA FIDELIZADO + DEEZER PREMIUM')) {
                    $this->valueStar = 35;
                } elseif(str_contains($item->plano,'PLANO EMPRESARIAL 1 GIGA FIDELIZADO + IP FIXO')) {
                    $this->valueStar = 38;
                } elseif(str_contains($item->plano,'PLANO EMPRESARIAL 1 GIGA SEM FIDELIDADE + IP FIXO')) {
                    $this->valueStar = 36;
                } elseif(str_contains($item->plano,'PLANO EMPRESARIAL 600 MEGA FIDELIZADO')) {
                    $this->valueStar = 9;
                } elseif(str_contains($item->plano,'PLANO EMPRESARIAL 600 MEGA FIDELIZADO + IP FIXO')) {
                    $this->valueStar = 12;
                } elseif(str_contains($item->plano,'PLANO EMPRESARIAL 800 MEGA FIDELIZADO')) {
                    $this->valueStar = 15;
                }

                // Se o mês do cadastro do contrato for AGOSTO, executa esse bloco.
            } elseif(Carbon::parse($item->data_contrato) < Carbon::parse('2022-09-01') &&
                Carbon::parse($item->data_contrato) >= Carbon::parse('2022-08-01')) {

                // Verifica qual é o plano e atribui a estrela correspondente.
                if(str_contains($item->plano,'PLANO 1 GIGA FIDELIZADO + DEEZER + HBO MAX + DR. AGE')) {
                    $this->valueStar = 30;
                } elseif(str_contains($item->plano,'PLANO 1 GIGA FIDELIZADO + DEEZER PREMIUM')) {
                    $this->valueStar = 15;
                } elseif(str_contains($item->plano,'PLANO 1 GIGA NÃO FIDELIZADO + DEEZER PREMIUM')) {
                    $this->valueStar = 0;
                } elseif(str_contains($item->plano,'PLANO 120 MEGA PROMOCAO LEVE 360 MEGA')) {
                    $this->valueStar = 7;
                } elseif(str_contains($item->plano,'PLANO 240 MEGA PROMOCAO LEVE 720 MEGA  + DEEZER PREMIUM')) {
                    $this->valueStar = 9;
                } elseif(str_contains($item->plano,'PLANO 240 MEGA PROMOCAO LEVE 720 MEGA  + DEEZER PREMIUM')) {
                    $this->valueStar = 9;
                } elseif(str_contains($item->plano,'PLANO 400 MEGA FIDELIZADO')) {
                    $this->valueStar = 7;
                } elseif(str_contains($item->plano,'PLANO 480 MEGA FIDELIZADO')) {
                    $this->valueStar = 7;
                } elseif(str_contains($item->plano,'PLANO 480 MEGA NÃO FIDELIZADO')) {
                    $this->valueStar = 0;
                } elseif(str_contains($item->plano,'PLANO 740 MEGA FIDELIZADO')) {
                    $this->valueStar = 9;
                } elseif(str_contains($item->plano,'PLANO 800 MEGA FIDELIZADO')) {
                    $this->valueStar = 15;
                } elseif(str_contains($item->plano,'PLANO EMPRESARIAL 1 GIGA FIDELIZADO')) {
                    $this->valueStar = 35;
                } elseif(str_contains($item->plano,'PLANO EMPRESARIAL 1 GIGA FIDELIZADO + DEEZER PREMIUM')) {
                    $this->valueStar = 35;
                } elseif(str_contains($item->plano,'PLANO EMPRESARIAL 600 MEGA FIDELIZADO')) {
                    $this->valueStar = 9;
                } elseif(str_contains($item->plano,'PLANO EMPRESARIAL 600 MEGA FIDELIZADO + IP FIXO')) {
                    $this->valueStar = 12;
                } elseif(str_contains($item->plano,'PLANO EMPRESARIAL 800 MEGA FIDELIZADO')) {
                    $this->valueStar = 17;
                }
            }
        });

        return $this->valueStar;
    }

    public function extractSalesTotals()
    {
        // Trás a contagem de todas as vendas realizadas no mês filtrado.
        $data = VoalleSales::where('vendedor', $this->username)
            ->whereMonth('data_vigencia', $this->month)
            ->whereYear('data_vigencia', $this->year)
            ->whereMonth('data_ativacao', $this->month)
            ->whereYear('data_ativacao', $this->year)
            ->whereMonth('data_contrato','>=', '05')
            ->whereYear('data_contrato', $this->year)
            ->select('id_contrato', 'nome_cliente', 'status', 'situacao', 'data_contrato', 'data_ativacao',
                     'data_vigencia', 'data_cancelamento', 'plano')
            ->get();

        $data->each(function($value) {
            $value->data_contrato = Carbon::parse($value->data_contrato)->format('d/m/Y');
            $value->data_ativacao = Carbon::parse($value->data_ativacao)->format('d/m/Y');
            $value->data_vigencia = Carbon::parse($value->data_vigencia)->format('d/m/Y');
            if(! is_null($value->data_cancelamento)) {
                $value->data_cancelamento = Carbon::parse($value->data_cancelamento)->format('d/m/Y');
            }

            $this->sanitizePlan($value);
        });

        return $data;
    }

    public function extractSalesAprovation()
    {
        $data = VoalleSales::where('vendedor', $this->username)
            ->whereMonth('data_contrato',$this->month)
            ->whereYear('data_contrato', $this->year)
            ->where('status', 'Em Aprovação')
            ->select('id_contrato', 'nome_cliente', 'status', 'situacao', 'data_contrato', 'plano')
            ->get();

        return $data;
    }

    public function extractSalesValids()
    {
        // Trás a contagem de todas as vendas realizadas no mês filtrado.
        $data = VoalleSales::where('vendedor', $this->username)
            ->whereMonth('data_vigencia', $this->month)
            ->whereYear('data_vigencia', $this->year)
            ->whereMonth('data_ativacao', $this->month)
            ->whereYear('data_ativacao', $this->year)
            ->whereMonth('data_contrato','>=', '05')
            ->whereYear('data_contrato', $this->year)
            ->where('status', 'Aprovado')
            ->get();

        return $data;

    }

    public function sanitizePlan($valor)
    {

        for($i = 0; $i < 6; $i++) {
            if(str_contains($valor->plano, 'FIDELIZADO')) {
                $valor->plano = explode('FIDELIZADO', $valor->plano)[0];
            } elseif (str_contains($valor->plano, 'TURBINADO')) {
                $valor->plano = explode('TURBINADO', $valor->plano)[0];
            } elseif (str_contains($valor->plano, '+')) {
                $valor->plano = explode('+', $valor->plano)[0];
            } elseif (str_contains($valor->plano, 'PROMOCAO')) {
                $valor->plano = explode('PROMOCAO', $valor->plano)[0];
            } elseif (str_contains($valor->plano, 'NÃO')) {
                $valor->plano = explode('NÃO', $valor->plano)[0];
            }
        }
    }

}

