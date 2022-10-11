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

        $array = [
            'Ana Karina De Sousa Lisboa',
            'Carolina Lima Correa',
            'Daiane Ribeiro Silva',
            'Daniel Moreira Silva',
            'Derick Richarlington Santos De Jesus',
            'Elem Aparecida De Sousa Laura',
            'Fabio Diego Coelho Branco',
            'Fernanda Cristynna Da Silva Machado',
            'Francisco Paz De Andrade Junior',
            'Heldionara Marques Nogueira',
            'Helen Beatriz Pereira Costa',
            'Juliano Leno Borges Da Silva',
            'Kariny Nobrega Oliveira',
            'Luane Lira Da Silva',
            'Maria Natividade Brandao Neta',
            'Pamela Vieira Vogado',
            'Patricia Quirino Gomes Ferreira',
            'Rebeca Souza Costa',
            'Taynara Tolentino De Sousa',
            'Thalia Isabella Santos',
            'Witoria Silva Frota',
        ];

        $c = Collaborator::whereIn('nome', $array)->get('id');

        $m = CollaboratorMeta::whereIn('colaborador_id', $c)->where('mes_competencia', '08')->get();

        return $m;


        foreach($c as $key => $value) {
            $m = CollaboratorMeta::where('colaborador_id', $value->id)->where('mes_competencia', '08')->first();

            $m->update([
                'meta' => 11
            ]);
        }

        return $m;



//        $metaPercent = 39;
//        $valueStar = 0;
//        $channel = 'MCV';
//
//
//        return $valueStar;
//

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
