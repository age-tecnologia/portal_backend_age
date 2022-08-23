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
    private string $month; // Mês para filtro das vendas.
    private string $year; // Ano para filtro das vendas.
    private int $cancelD7 = 0;  // Vendas canceladas antes dos 7 dias.
    private int $deflator = 0; // Deflator de adição ou subtração.
    private int $sales = 0; // Vendas totais.
    private int $stars = 0; // Estrelas acumuladas.
    private int $cancel_totals = 0; // Cancelamentos totais, antes ou depois dos 7 dias.
    private int $meta = 0; // Meta do colaborador.
    private float $meta_percent = 0.0; // Percentual da meta atingida.
    private float $value_stars = 0.0 // Valor das estrelas, baseado no percentual da meta atingida.
    private float $commission = 0.0; // Comissão, sendo o cálculo (estrela x valor_estrela) + deflator.

    public function index()
    {
        //$this->username = 'SABRINA LAIS MOREIRA MOTA';
        $this->username = 'Camila Meirelles Gonçalvez'; // Recupera o valor do usuário pela requisição,
        $this->year = Carbon::now()->format('Y'); // Trás o ano atual.
        $this->month = Carbon::now()->format('m'); // Trás o mês atual.


        $data = [
            'cancel_d7' => $this->cancelD7(),
            'deflator' => $this->deflator(),
            'sales' => $this->sales(),
            'stars' => $this->stars(),
            'cancel_total' => '',
            'meta_percent' => '',
            'value_stars' => '',
            'commission' => '',

        ];

        return response()->json([$data], 201);
    }

    public function cancelD7()
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

    public function deflator()
    {
        // Valida se existe cancelamento antes dos 7 dias, caso aja, o deflator passa a penalizar em -10%
        if($this->cancelD7 > 0) {
            $this->deflator = -10;
        } else {
            $this->deflator = 10;
        }

        return $this->deflator;
    }

    public function sales()
    {
        $this->sales = VoalleSales::where('vendedor', $this->username)
                                    ->whereMonth('data_vigencia', $this->month)
                                    ->whereYear('data_vigencia', $this->year)
                                    ->count();
        return $this->sales;
    }

    public function stars()
    {
        $plans = VoalleSales::select('plano')
                            ->whereMonth('data_vigencia', $this->month)
                            ->whereYear('data_vigencia', $this->year)
                            ->where('status', '<>', 'Inválida')
                            ->where('vendedor', $this->username)
                            ->get();

        $plans->each(function ($item, $key) {
            if(str_contains($item->plano,'PLANO 480 MEGA FIDELIZADO')) {
                $this->stars += 9;
            } elseif(str_contains($item->plano,'PLANO 480 MEGA FIDELIZADO'))
        });

        return $this->stars;

    }


}
