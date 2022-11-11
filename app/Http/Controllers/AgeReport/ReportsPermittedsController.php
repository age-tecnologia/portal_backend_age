<?php

namespace App\Http\Controllers\AgeReport;

use App\Http\Controllers\Controller;
use App\Models\AgeReport\AccessPermission;
use App\Models\AgeReport\Report;
use App\Models\AgeReport\ReportPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportsPermittedsController extends Controller
{



    public function getReportsPermitteds($id)
    {
        $idPermitteds = ReportPermission::whereUserId($id)->whereNull('deleted_at')->withTrashed()->get('relatorio_id');

        $permittedReports = DB::table('agereport_relatorios_permissoes as rp')
                                ->leftJoin('agereport_relatorios as r', 'r.id', '=', 'rp.relatorio_id')
                                ->whereUserId($id)
                                ->whereNull('rp.deleted_at')
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

    public function alternateReportsPermitteds($idUser, $idReport)
    {

        $report = ReportPermission::whereUserId($idUser)->whereRelatorioId($idReport)->withTrashed()->first();

        if(! isset($report->id)) {
            $user = ReportPermission::create([
                'user_id' => $idUser,
                'relatorio_id' => $idReport,
                'permitido_por' => auth()->user()->id
            ]);

            return response()->json(['msg' => 'Relatório liberado com sucesso.', 'access' => true], 201);

        } else {

            if(isset($report->deleted_at)) {

                $report = $report->restore();

                return response()->json(['msg' => 'Relatório liberado com sucesso.', 'access' => true], 201);

            } else {
                $report = $report->delete();

                return response()->json(['msg' => 'Relatório inativado com sucesso.', 'access' => false], 201);
            }

        }
    }
}
