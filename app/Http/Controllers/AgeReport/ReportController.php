<?php

namespace App\Http\Controllers\AgeReport;

use App\Exports\ReportExport;
use App\Http\Controllers\Controller;
use App\Models\AgeReport\Report;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Psy\Util\Str;
use Symfony\Component\Console\Input\Input;

class ReportController extends Controller
{

    private $report;

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

        $report = new Report();

        $report = $report->create([
           'nome' => $request->input('name'),
           'nome_arquivo' => $request->input('namearchive'),
           'query' => $request->input('query'),
           'cabecalhos' => $request->input('headers'),
           'banco_solicitado' => $request->input('database'),
           'isPeriodo' => $request->input('isPeriod'),
           'isPeriodoHora' => $request->input('isPeriodHour'),
           'url' => 'sem_url'
        ]);

        if(isset($report->id)) {
            return response()->json(['status' => true, 'msg' => 'Relatório criado com sucesso!'], 201);
        }
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
           'nome_arquivo' => $request->input('namearchive'),
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

    public function download(Request $request, $id)
    {
        $this->report = Report::find($id);

        set_time_limit(2000);
        ini_set('memory_limit', '2048M');


        if($this->report->isPeriodo === 1) {
            return $this->reportPeriod($request, 1);

        } elseif($this->report->isPeriodoHora === 1) {

            return $this->reportPeriod($request, 2);

        } else {

            return $this->report($this->report->query);

        }

    }

    private function reportPeriod($request, $type) {

        if($type === 1) {
            $firstPeriod = $request->has('firstPeriod') ? Carbon::parse($request->input('firstPeriod'))->format('Y-m-d') : null;
            $lastPeriod = $request->has('lastPeriod') ? Carbon::parse($request->input('lastPeriod'))->format('Y-m-d') : null;
        } elseif($type === 2) {
            $firstPeriod = $request->has('firstPeriod') ? Carbon::parse($request->input('firstPeriod'))->format('Y-m-d H:i:s') : null;
            $lastPeriod = $request->has('lastPeriod') ? Carbon::parse($request->input('lastPeriod'))->format('Y-m-d H:i:s') : null;
        }

        $query = \Illuminate\Support\Str::replaceFirst('#', $firstPeriod, $this->report->query);
        $query = \Illuminate\Support\Str::replaceFirst('#', $lastPeriod, $query);


        $result = DB::connection($this->report->banco_solicitado)->select($query);

        return $this->report($query);

    }

    private function report($query) {


        $i = substr_count($this->report->cabecalhos, ';');
        $headers = explode(';', $this->report->cabecalhos);
        $arrHeaders = [];

        for($x = 0; $i > $x; $x++) {
            $arrHeaders[] = $headers[$x];
        }

        $result = DB::connection($this->report->banco_solicitado)->select($query);

        return \Maatwebsite\Excel\Facades\Excel::download(new ReportExport($result, $arrHeaders), $this->report->nome_arquivo);

    }
}
