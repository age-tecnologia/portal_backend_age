<?php

namespace App\Http\Controllers\AgeRv;

use App\Http\Controllers\AgeRv\_aux\sales\analytics\Master;
use App\Http\Controllers\Controller;
use App\Models\AgeRv\Commission;
use App\Models\AgeRv\VoalleSales;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommissionController extends Controller
{

    public function index()
    {
        //
    }


    public function create()
    {
        //
    }


    public function store(Request $request)
    {

        $this->month = $request->input('month');
        $this->year = $request->input('year');

        $data = VoalleSales::where(function ($query) {
            $query->whereMonth('data_ativacao','>=', ($this->month - 1))->whereMonth('data_vigencia', $this->month)->whereYear('data_ativacao', $this->year);
        })
            ->whereStatus('Aprovado')
            ->selectRaw('LOWER(supervisor) as supervisor, LOWER(vendedor) as vendedor,
                                                            id_contrato,
                                                            status, situacao,
                                                            data_contrato, data_ativacao, data_vigencia, data_cancelamento,
                                                            plano,
                                                            nome_cliente')
            ->get()->unique(['id_contrato']);


        $commission = new Commission();

        foreach($data as $item => $value) {
            $commission->create([
                'mes_competencia' => $this->month,
                'ano_competencia' => $this->year,
                'id_contrato' => $value->id_contrato,
                'nome_cliente' => $value->nome_cliente,
                'supervisor' =>$value->supervisor,
                'vendedor' => $value->vendedor,
                'status' => $value->status,
                'situacao' => $value->situacao,
                'data_contrato' => $value->data_contrato,
                'data_ativacao' => $value->data_ativacao,
                'data_vigencia' => $value->data_vigencia,
                'data_cancelamento' => $value->data_cancelamento,
                'plano' => $value->plano
            ]);
        }

        return 'Ok';
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
