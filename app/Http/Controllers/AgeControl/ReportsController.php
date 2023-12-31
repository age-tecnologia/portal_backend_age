<?php

namespace App\Http\Controllers\AgeControl;

use App\Exports\ReportExport;
use App\Http\Controllers\Controller;
use App\Models\AgeControl\Report;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Psy\Util\Str;

class ReportsController extends Controller
{

    public function index()
    {
        //
    }

    public function viewReportComplete(Request $request)
    {
        $reports = DB::table('agecontrol_relatos as r')
                        ->leftJoin('agecontrol_condutores as c', 'c.id', '=', 'r.condutor_id')
                        ->leftJoin('agecontrol_veiculos as v', 'c.id', '=', 'v.condutor_id')
                        ->leftJoin('agecontrol_veiculo_tipo as vt', 'vt.id', '=', 'v.tipo_veiculo_id')
                        ->leftJoin('agecontrol_relato_periodos as rp', 'rp.id', '=', 'r.periodo_id')
                        ->leftJoin('portal_colaboradores_grupos as cg', 'cg.id', '=', 'c.grupo_id')
                        ->orderBy('r.id', 'desc');




        if($request->has('filters')) {

           $this->filterRepors($reports, $request->filters);
        }

        if($request->has('idReports')) {
            return $this->downloadReports($reports, $request->input('idReports'));
        }


        return response()->json($reports->get([
                                        'r.id',
                                        'c.primeiro_nome',
                                        'c.segundo_nome',
                                        'cg.grupo',
                                        'vt.tipo',
                                        'v.fabricante',
                                        'v.modelo',
                                        'r.created_at',
                                        'r.quilometragem_aprovada',
                                        'aprovador_id',
                                        'rp.periodo',
                                        'r.data_referencia'
                                    ])->toArray(), 200);
    }


    public function create()
    {
        //
    }


    public function store(Request $request, Report $report)
    {

        $report = $report->create([
            'condutor_id' => $request->input('conductor'),
            'quilometragem_relatada' => $request->input('kmReport'),
            'quilometragem_aprovada' => $request->input('kmReport'),
            'data_referencia' => $request->input('date'),
            'periodo_id' => $request->input('period'),
            'aprovador_id' => auth()->user()->id,
            'nome_foto' => $this->uploadImage($request->input('image')),
        ]);

        return response()->json([
            'msg' => 'Relato adicionado com sucesso!',
            'status' => 'success'
        ], 201);

    }

    protected function uploadImage($image_64)
    {
        if($image_64 !== null) {
            $extension = explode('/', explode(':', substr($image_64, 0, strpos($image_64, ';')))[1])[1];   // .jpg .png .pdf

            $replace = substr($image_64, 0, strpos($image_64, ',')+1);


            $image = str_replace($replace, '', $image_64);

            $image = str_replace(' ', '+', $image);

            $imageName = \Illuminate\Support\Str::random(10).'.'.$extension;

            Storage::disk('ageControlReports')->put($imageName, base64_decode($image));

            return $imageName;
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


    protected function filterRepors($reports, $filters)
    {
        $filters = json_decode($filters);

        $data = [];

        if($filters->conductor !== '') {
            $reports = $reports->where('r.condutor_id', $filters->conductor);
        }

        if($filters->period !== '') {
            $reports = $reports->where('r.periodo_id', $filters->period);
        }

        if($filters->firstPeriod !== '') {
            $reports = $reports->where('r.data_referencia','>=',$filters->firstPeriod);
        }

        if($filters->lastPeriod !== '') {
            $reports = $reports->where('r.data_referencia','<=',$filters->lastPeriod);
        }

        return $reports;
    }

    protected function downloadReports($reports, $idReports)
    {
        set_time_limit(2000);
        ini_set('memory_limit', '2048M');


        $result = $reports->whereIn('r.id', $idReports)->get([
            'r.id',
            'r.data_referencia',
            'rp.periodo',
            'r.quilometragem_aprovada',
            'c.primeiro_nome',
            'c.segundo_nome',
            'cg.grupo',
            'vt.tipo',
            'v.fabricante',
            'v.modelo'
        ])->toArray();


        $headers = [
            'relato_id',
            'relato_data_referente',
            'relato_periodo_referente',
            'relato_quilometragem',
            'condutor_primeiro_nome',
            'condutor_segundo_nome',
            'condutor_grupo',
            'veiculo_tipo',
            'veiculo_fabricante',
            'veiculo_modelo',
        ];

        return \Maatwebsite\Excel\Facades\Excel::download(new ReportExport($result, $headers), 'relatorio_relatos.xlsx');

    }
}

