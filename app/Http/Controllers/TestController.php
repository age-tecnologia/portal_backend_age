<?php

namespace App\Http\Controllers;

use App\Exports\ReportExport;
use App\Exports\UsersExport;
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

    public function index(Request $request)
    {

        $metaPercent = 39;
        $valueStar = 0;
        $channel = 'MCV';


        return $valueStar;


//        $meta = new CollaboratorMeta();
//        $colaborador = Collaborator::where('tipo_comissao_id', 3)
//                        ->get('id');
//
//        return $colaborador;

//        $colaborador->each(function($item) use($meta) {
//            $meta->create([
//                'colaborador_id' => $item->id,
//                'mes_competencia' => '06',
//                'meta' => 200,
//                'modified_by' => 1
//            ]);
//        });




//        $u = new User();
//
//        $u->create([
//           'name' => 'Daniela',
//           'email' => 'financeiro@agetelecom.com.br',
//           'nivel_acesso_id' => 1,
//           'status_id' => 1,
//           'password' => Hash::make('94OGg06TQBjakr6')
//        ]);
//
//        return 'ok';

    }


}
