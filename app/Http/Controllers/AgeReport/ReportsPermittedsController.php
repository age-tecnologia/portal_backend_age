<?php

namespace App\Http\Controllers\AgeReport;

use App\Http\Controllers\Controller;
use App\Models\AgeReport\Report;
use App\Models\AgeReport\ReportPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportsPermittedsController extends Controller
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
        //
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

    public function getReportsPermitteds($id)
    {
        $idPermitteds = ReportPermission::whereUserId($id)->get('id');

        $permittedReports = DB::table('agereport_relatorios_permissoes as rp')
                                ->leftJoin('agereport_relatorios as r', 'r.id', '=', 'rp.relatorio_id')
                                ->whereUserId($id)
                                ->select('r.nome', 'r.id')
                                ->get();

        $allReports = Report::whereNotIn('id', $idPermitteds)->get(['id', 'nome']);

        $result = [];

        foreach($permittedReports as $key => $value) {
            $result[] = [
                'id' => $value->id,
                'report' => $value->nome,
                'access' => true
            ];
        }

        foreach($allReports as $key => $value) {
            $result[] = [
                'id' => $value->id,
                'report' => $value->nome,
                'access' => false
            ];
        }

        return response()->json($result, 200);
    }
}
