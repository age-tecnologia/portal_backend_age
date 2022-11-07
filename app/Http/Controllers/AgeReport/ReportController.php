<?php

namespace App\Http\Controllers\AgeReport;

use App\Exports\ReportExport;
use App\Http\Controllers\Controller;
use App\Models\AgeReport\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{

    public function index()
    {
        return "index";
    }


    public function create()
    {
        //
    }


    public function store(Request $request)
    {
        //
    }


    public function show($id)
    {
        $report = Report::find($id);

        if(isset($report->id)) {
            return response()->json(['status' => true, 'data' => $report], 200);
        } else {return response()->json(['status' => false, 'msg' => 'Nenhum relatório encontrado id:'.$id], 404);}
    }


    public function edit($id, Request $request)
    {
        $report = Report::find($id);

        $report->update([
           'nome' => $request->input('name'),
           'nome_arquivo' => $request->input('name_archive'),
           'query' => $request->input('query'),
           'cabecalhos' => $request->input('headers'),
           'banco_solicitado' => $request->input('database'),
           'isPeriodo' => $request->input('isPeriod'),
           'isPeriodoHora' => $request->input('isPeriodHour'),
        ]);

        $report = Report::find($id);

        return response()->json(['data' => $report, 'msg' => 'Relatório atualizado com sucesso!'], 201);

    }


    public function update(Request $request, $id)
    {
        //
    }


    public function destroy($id)
    {
        //
    }

    public function download($id)
    {
        $report = Report::find($id);

        set_time_limit(2000);
        ini_set('memory_limit', '2048M');

        if($report->isPeriodo === 0 && $report->isPeriodoHora === 0) {

            $query = $report->query;
            $i = substr_count($report->cabecalhos, ';');
            $headers = explode(';', $report->cabecalhos);
            $arrHeaders = [];

            for($x = 0; $i > $x; $x++) {
                $arrHeaders[] = $headers[$x];
            }

            $result = DB::connection($report->banco_solicitado)->select($query);

            return \Maatwebsite\Excel\Facades\Excel::download(new ReportExport($result, $arrHeaders), $report->nome_arquivo);

        }

    }
}
