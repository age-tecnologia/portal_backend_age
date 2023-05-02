<?php

namespace App\Http\Controllers\AgeRv;

use App\Http\Controllers\AgeRv\_aux\sales\analytics\Master;
use App\Http\Controllers\Controller;
use App\Models\AgeRv\Commission;
use App\Models\AgeRv\CommissionConsolidated;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CommissionConsolidatedController extends Controller
{

    public function index()
    {
        //
    }

    public function create()
    {

    }


    public function store(Request $request)
    {
        set_time_limit(20000);

        if((! isset($request->month)) || (! isset($request->year))) {
            return response()->json('Mês ou ano não informado.', 400);
        }


        $salesConsolidated = Commission::whereMesCompetencia($request->month)
                            ->whereAnoCompetencia($request->year)->count();

        if($salesConsolidated === 0) {
            return response()->json('Não há vendas no banco para o mês solicitado.', 400);
        }

        $result = new Master($request->month, $request->year);

        $result = $result->response();


        $commissionConsolidated = new CommissionConsolidated();
        $date = "$request->year-$request->month-01";
        $competence = Carbon::parse($date);


        foreach($result as $key => $value) {

            foreach($value as $k => $v) {

                foreach($v['collaborators'] as $index => $collab) {

                    $commissionConsolidated->create([
                        'auditada' => 0,
                        'canal' => $collab['channel'],
                        'colaborador_id' => $collab['id'],
                        'colaborador' => $collab['name'],
                        'vendas' => $collab['sales']['count'],
                        'meta' => $collab['meta'],
                        'meta_atingida' => $collab['metaPercent'],
                        'vendas_canceladas' => $collab['cancel']['count'],
                        'estrelas' => $collab['stars'],
                        'valor_estrela' => $collab['valueStar'],
                        'acelerador_deflator' => $collab['mediator'],
                        'comissao' => $collab['commission'],
                        'competencia' => $competence
                    ]);


                }

            }

        }
    }


    public function show($id)
    {
        //
    }


    public function edit($id)
    {
        //
    }


    public function update(Request $request, $id)
    {
        //
    }


    public function destroy($id)
    {
        //
    }
}
