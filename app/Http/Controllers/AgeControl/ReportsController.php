<?php

namespace App\Http\Controllers\AgeControl;

use App\Http\Controllers\Controller;
use App\Models\AgeControl\Report;
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

    public function viewReportComplete()
    {
        $reports = DB::table('agecontrol_relatos as r')
                        ->leftJoin('agecontrol_condutores as c', 'c.id', '=', 'r.condutor_id')
                        ->leftJoin('agecontrol_veiculos as v', 'c.id', '=', 'v.condutor_id')
                        ->leftJoin('agecontrol_veiculo_tipo as vt', 'vt.id', '=', 'v.tipo_veiculo_id')
                        ->leftJoin('agecontrol_relato_periodos as rp', 'rp.id', '=', 'r.periodo_id')
                        ->leftJoin('portal_colaboradores_grupos as cg', 'cg.id', '=', 'c.grupo_id')
                        ->orderBy('r.id', 'desc')
                        ->get([
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
                            'rp.periodo'
                        ]);

        return response()->json($reports->toArray(), 200);
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
            'periodo_id' => $request->input('period'),
            'aprovador_id' => null,
            'nome_foto' => $this->uploadImage($request->input('image')),
        ]);

        return response()->json([
            'msg' => 'Relato adicionado com sucesso!',
            'status' => 'success'
        ], 201);

    }

    protected function uploadImage($image_64)
    {

        $extension = explode('/', explode(':', substr($image_64, 0, strpos($image_64, ';')))[1])[1];   // .jpg .png .pdf

        $replace = substr($image_64, 0, strpos($image_64, ',')+1);


        $image = str_replace($replace, '', $image_64);

        $image = str_replace(' ', '+', $image);

        $imageName = \Illuminate\Support\Str::random(10).'.'.$extension;

        Storage::disk('ageControlReports')->put($imageName, base64_decode($image));

        return $imageName;
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
