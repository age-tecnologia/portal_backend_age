<?php

namespace App\Http\Controllers;

use App\Exports\ReportExport;
use App\Exports\UsersExport;
use App\Models\AgeRv\Collaborator;
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

    public function index(Request $request)
    {
        $s = Collaborator::where('funcao_id', 3)
                        ->where('nome', '<>', 'Multi Canal de Vendas')
                        ->select('nome', 'id')
                        ->distinct()
                        ->get();

        $c = new Collaborator();

        foreach ($s as $item => $value) {

            $ss = VoalleSales::whereMonth('data_contrato', '>', '06')
                ->whereYear('data_contrato', '=', '2022')
                ->where('supervisor', $value->nome)
                ->where('vendedor', '<>', ' ')
                ->select('vendedor')
                ->distinct()
                ->get();

            foreach($ss as $item2 => $value2) {

                $c->create([
                    'nome' => $value2->vendedor,
                    'funcao_id' => 1,
                    'canal_id' => 2,
                    'supervisor_id' => $value->id
                ]);

            }

        }

//        foreach ($s as $item => $value) {
//
//        }
    }


}
