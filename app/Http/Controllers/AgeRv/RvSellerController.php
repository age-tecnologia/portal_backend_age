<?php

namespace App\Http\Controllers\AgeRv;

use App\Http\Controllers\Controller;
use App\Models\AgeRv\VoalleSales;
use Carbon\Carbon;
use http\Env\Response;
use Illuminate\Http\Request;

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
    private string $channel; // Dados do canal que o usuário pertence.
    private string $month; // Mês para filtro das vendas.
    private string $year; // Ano para filtro das vendas.
    private int $cancelD7 = 0;  // Vendas canceladas antes dos 7 dias.
    private int $deflator = 0; // Deflator de adição ou subtração.
    private int $sales = 0; // Vendas totais.
    private int $stars = 0; // Estrelas acumuladas.
    private int $cancelTotals = 0; // Cancelamentos totais, antes ou depois dos 7 dias.
    private int $meta = 0; // Meta do colaborador.
    private float $metaPercent = 0.0; // Percentual da meta atingida.
    private float $valueStars = 0.0; // Valor das estrelas, baseado no percentual da meta atingida.
    private float $commission = 0.0; // Comissão, sendo o cálculo (estrela x valor_estrela) + deflator.

    /*
     * Bloco responsável pela chamada nos outros métodos e envio do json contendo as informações pro dashboard.
     */
    public function seller(Request $request)
    {

        $validated = $request->validate([
           'year' => 'required',
           'month' => 'required'
        ]);


        $this->username = $request->input('username'); // Recupera o valor do usuário pela requisição,
        $this->year = $request->input('year'); // Recupera o ano filtrado.
        $this->month = $request->input('month'); // Recupera o mês filtrado.


        // Dados tratados para exibição na dashboard do vendedor.
        $data = [
            'cancel_d7' => $this->cancelD7(),
            'deflator' => $this->deflator(),
            'sales' => $this->sales(),
            'stars' => $this->stars(),
            'cancel_total' => $this->cancelTotals(),
            'meta_percent' => $this->metaPercent(),
            'value_stars' => $this->valueStars(),
            'commission' => $this->commission(),

        ];

        return response()->json([$data], 201);
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
     * Retorna a contagem de vendas realizadas no período.
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
                    $this->stars += 0;
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
                    $this->stars += 0;
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
        $this->meta = 60;

        $this->metaPercent = number_format(($this->sales / $this->meta) * 100, 2);

        return $this->metaPercent;

    }

    /*
     * Retorna a contagem de estrelas no período.
     */
    public function valueStars() : float
    {

        $this->channel = 'MCV';

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
                    $minPercent = 70;
                } elseif($this->month === '08') {
                    $minPercent = 60;
                }

                if($this->metaPercent >= $minPercent && $this->metaPercent < 100) {
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
    public function commission() : float
    {
        $this->commission = $this->valueStars * $this->stars;

        return $this->commission;
    }
}

