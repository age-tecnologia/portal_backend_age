<?php

namespace App\Http\Controllers;

use App\Exports\ReportExport;
use App\Exports\UsersExport;
use App\Http\Controllers\AgeRv\_aux\sales\Stars;
use App\Ldap\UserLdap;
use App\Mail\SendBlackFiber;
use App\Mail\SendMainUser;
use App\Models\AgeBoard\AccessPermission;
use App\Models\AgeBoard\DashboardPermitted;
use App\Models\AgeBoard\ItemPermitted;
use App\Models\AgeReport\Report;
use App\Models\AgeRv\Channel;
use App\Models\AgeRv\Collaborator;
use App\Models\AgeRv\CollaboratorMeta;
use App\Models\AgeRv\VoalleSales;
use App\Models\Test;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use LdapRecord\Auth\BindException;
use LdapRecord\Connection;
use Maatwebsite\Excel\Excel;
use Nette\Utils\Random;
use Barryvdh\DomPDF\PDF;

class TestController extends Controller
{
    protected $year;
    protected $month;
    private $salesTotals;



    public function __invoke()
    {
        $test = new Test();

        $test->truncate();

    }

    public function index(Request $request)
    {

//        $id = $request->input('id');
//        $idCollab = $request->input('idCollab');
//
//        $user = User::find($id);
//
//        $collab = User::find($idCollab);

        set_time_limit(200000);

//
//        $array = \Maatwebsite\Excel\Facades\Excel::toArray(new \stdClass(), $request->file('excel'));
//
//        foreach($array as $key => $value) {
//
//            foreach($value as $k => $v) {
//                Mail::to($v[1])
//                    ->send(new SendMainUser($v[0]));
//
//            }
//        }
//
//        return "ok";

//        $array = \Maatwebsite\Excel\Facades\Excel::toArray(new \stdClass(), $request->file('excel'));
//
//        foreach($array as $key => $value) {
//
//            foreach($value as $k => $v) {
//                Mail::to($v[1])
//                    ->send(new SendBlackFiber());
//
//            }
//        }
//
//        return "ok";

//        Mail::to('carlos.neto@agetelecom.com.br')
//                    ->send(new SendBlackFiber());
//
//
//            return "Ok";



//        $array = [
//          'Ancelmo De Sales',
//            'Angelica Mires',
//            'Carla Julia',
//            'Erivan de Souza',
//            'Geovana Souza',
//            'Itanael',
//            'Jose Loiola',
//            'Karen de Almeida',
//            'Lucas de Brito',
//            'Marcus Vinicius',
//            'Maria Dilma',
//            'Thalia Isabella'
//        ];
//
//
//        foreach($array as $key => $value) {
//
//            $collab = Collaborator::where('nome', 'like', $value.'%')->first('id');
//
//            $meta = CollaboratorMeta::whereColaboradorId($collab->id)->whereMesCompetencia('10')->first();
//
//            $meta = $meta->update(['meta' => 30]);
//
//
//
//        }

//
//


//
//        for($i = 9502; $i < 99999; $i++) {
//            $data = Http::withHeaders(['Content-Type' => 'Application/json'])
//                ->post('https://plataforma.astenassinatura.com.br/api/downloadPDFEnvelopeDocs/', [
//                    'token' => '3koqaYFIRSx5QypP2huHk4gpIsVT0WZ3bIEbLfbfJWwKzgu0WP+jI13IISftJl+6x5yKrknzeGyvNuqYcVVky4-S8HNSIjlCU90x8GWDthturJN+Nue40K9PPLxRCvo5mqdQ28eqVfA',
//                    'params' => [
//                        'idEnvelope' => $i,
//                        'incluirDocs' => 'S',
//                        'versaoSemCertificado' => null
//                    ]
//                ])
//                ->json();
//
//            if(isset($data['response'])) {
//                if($data['response']['nomeArquivo'] !== '6763 - KLEBSON SANTOS SILVA.zip') {
//
//                    if(! Storage::disk('public2')->exists($data['response']['nomeArquivo'])) {
//                        Storage::disk('public2')->put($data['response']['nomeArquivo'], base64_decode($data['response']['envelopeContent']));
//                    }
//
//                }
//
//
//            }
//        }


//        $array = [
//            'Ana Paula Andrade',
//            'Eduardo Alves de Lima',
//            'Elenilda Pereira',
//            'Filipe de Carvalho',
//            'Geony de Sousa',
//            'Jaqueline Ferreira',
//            'Joao Victor Alves',
//            'Jordelino Rodrigues',
//            'Luiza de Oliveira',
//            'Mateus Lisboa'
//        ];
//
//        $collaborator = Collaborator::whereIn('nome', $array)->whereTipoComissaoId(2)->get(['id']);
//        $metas = new CollaboratorMeta();
//
//        foreach($array as $k => $v) {
//
//
//            $collaborator = Collaborator::where('nome', 'like', '%'.$v.'%')->whereTipoComissaoId(2)->first('id');
//
//            $metas = CollaboratorMeta::where('colaborador_id', $collaborator->id)->whereMesCompetencia('10')->delete();
//            $metas = new CollaboratorMeta();
//
//            $metas->create([
//                'colaborador_id' => $collaborator->id,
//                'mes_competencia' => '10',
//                'meta' => 16.5,
//                'modified_by' => 1
//            ]);
//
//
//        }
//
//
//
//
//        $metas = new CollaboratorMeta();
//
//        foreach($collaborator as $k => $v) {
//
//        }



//        $users = UserLdap::limit(10)->get(['name']);
//
//        return $users;
//
//        $result = [];
//
//        foreach($users as $key => $val) {
//            $result[] = $val->name;
//        }
//
//        return $result;



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
