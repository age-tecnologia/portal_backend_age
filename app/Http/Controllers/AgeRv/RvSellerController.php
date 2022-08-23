<?php

namespace App\Http\Controllers\AgeRv;

use App\Http\Controllers\Controller;
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
    private int $cancelD7;  // Vendas canceladas antes dos 7 dias.
    private int $deflator; // Deflator de adição ou subtração.
    private int $sales; // Vendas totais.
    private int $stars; // Estrelas acumuladas.
    private int $cancel_totals; // Cancelamentos totais, antes ou depois dos 7 dias.
    private int $meta; // Meta do colaborador.
    private float $meta_percent; // Percentual da meta atingida.
    private float $value_stars; // Valor das estrelas, baseado no percentual da meta atingida.
    private float $commission; // Comissão, sendo o cálculo (estrela x valor_estrela) + deflator

    public function index()
    {

        return auth()->user()->email;

        $data = [
            'cancel_d7' => 2,
            'deflator' => 10,
            'sales' => 20,
            'stars' => 10,
            'cancel_total' => 3,
            'meta_percent' => 10,
            'value_stars' => 0.20,
            'commission' => 10.20,

        ];

        return response()->json([$data], 201);
    }


}
