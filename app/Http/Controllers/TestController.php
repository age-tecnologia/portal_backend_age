<?php

namespace App\Http\Controllers;

use App\Exports\ReportExport;
use App\Exports\UsersExport;
use App\Http\Controllers\AgeRv\_aux\sales\Stars;
use App\Models\AgeRv\Channel;
use App\Models\AgeRv\Collaborator;
use App\Models\AgeRv\CollaboratorMeta;
use App\Models\AgeRv\VoalleSales;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use LdapRecord\Auth\BindException;
use LdapRecord\Connection;
use Maatwebsite\Excel\Excel;

class TestController extends Controller
{
    protected $year;
    protected $month;
    private $salesTotals;


    public function index(Request $request)
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

        foreach($data as $item => $value) {
            return $value->vendedor;
        }


//        $query = $request->input('query');
//
//        $query = Str::replaceFirst('#', $request->input('first'), $query);
//        $query = Str::replaceLast('#', $request->input('last'), $query);
//
//        return $query;
//
//        $result = DB::connection('mysql')->select($query);
//
//        return $result;

    }


}
