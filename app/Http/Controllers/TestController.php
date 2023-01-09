<?php

namespace App\Http\Controllers;

use App\Exports\ReportExport;
use App\Exports\UsersExport;
use App\Http\Controllers\AgeRv\_aux\sales\Stars;
use App\Http\Controllers\DataWarehouse\Voalle\PeoplesController;
use App\Ldap\UserLdap;
use App\Mail\SendBlackFiber;
use App\Mail\SendInvoice;
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
    protected $monthCompetence;
    protected $dateCompetence;
    private $salesTotals;
    private $dateAdmission;
    private $meta;




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


        $sellers = VoalleSales::distinct('vendedor')->get(['vendedor']);

        return $sellers;



       // return view('mail.invoice_error');
//
     //   $array = \Maatwebsite\Excel\Facades\Excel::toArray(new \stdClass(), $request->file('excel'));


//        $connection = new Connection([
//            'hosts' => ['10.25.0.1'],
//            'base_dn' => 'dc=tote, dc=local',
//            'username' => 'ldap',
//            'password' => 'iAcWMMqC@',
//
//            // Optional Configuration Options
//            'port' => 389,
//            'use_ssl' => false,
//            'use_tls' => false,
//            'version' => 3,
//            'timeout' => 5,
//            'follow_referrals' => false,
//
//        ]);
//
//
//        try {
//            $connection->connect();
//
//            $username = $request->input('email') . '@tote.local';
//            $password = $request->input('password');
//
//            if ($connection->auth()->attempt($username, $password)) {
//                // Separa o nome e o sobrenome
//
//                return response()->json('Authentic', 201);
//
//            } else {
//
//                return response()->json('Unauthentic', 200);
//
//            }
//        } catch (\Exception $e) {
//
//        }

//        foreach($array as $key => $value) {
//
//            foreach($value as $k => $v) {
//
//                if (! preg_match('/^[a-zA-Z0-9]+/', $v[1])) {
//                    return $v[1];
//                }
//            }
//        }

//
//        foreach($array as $key => $value) {
//
//            foreach($value as $k => $v) {
//                Mail::to($v[1])
//                    ->send(new SendInvoice());
//
//            }
//        }




        return "ok";
//
////
//        Mail::to('carlos.neto@agetelecom.com.br')
//            ->send(new SendInvoice());
//

//
//        $people = new PeoplesController();
//
//        return $people->create();

//        $array = [
//          'channels' => [
//              0 => [
//                  'name' => 'MCV',
//                  'collaborators' => [
//                      0 => [
//                          'name' => 'Carlos Neto',
//                          'commission' => 100
//                      ],
//                      1 => [
//                          'name' => 'Neto Carlos',
//                          'commission' => 300
//                      ],
//                      2 => [
//                          'name' => 'Joao da Silva',
//                          'commission' => 200
//                      ],
//                  ]
//              ],
//              1 => [
//                  'name' => 'PAP',
//                  'collaborators' => [
//                      0 => [
//                          'name' => 'Carlos Neto',
//                          'commission' => 400
//                      ],
//                      1 => [
//                          'name' => 'Neto Carlos',
//                          'commission' => 500
//                      ],
//                      2 => [
//                          'name' => 'Joao da Silva',
//                          'commission' => 300
//                      ],
//                  ]
//              ],
//              2 => [
//                  'name' => 'LIDER',
//                  'collaborators' => [
//                      0 => [
//                          'name' => 'Carlos Neto',
//                          'commission' => 500
//                      ],
//                      1 => [
//                          'name' => 'Neto Carlos',
//                          'commission' => 300
//                      ],
//                      2 => [
//                          'name' => 'Joao da Silva',
//                          'commission' => 400
//                      ],
//                  ]
//              ]
//          ]
//        ];
//
//
//        $array = collect($array);
//
//
//        $array = $array->sortByDesc("channels.collaborators.commission");
//
//        dd($array);


//
//
//        $collab = [
//            0 => [
//                'name' => 'Aegiton',
//                'meta' => 231
//            ],
//            1 => [
//                'name' => 'Cesar',
//                'meta' => 396
//            ],
//            2 => [
//                'name' => 'Clebersom',
//                'meta' => 297
//            ],
//            3 => [
//                'name' => 'DAIANE',
//                'meta' => 264
//            ],
//            4 => [
//                'name' => 'EMANUEL',
//                'meta' => 297
//            ],
//            5 => [
//                'name' => 'HEBERTY',
//                'meta' => 231
//            ],
//            6 => [
//                'name' => 'JESSICA',
//                'meta' => 198
//            ],
//            7 => [
//                'name' => 'KEILA',
//                'meta' => 264
//            ],
//            8 => [
//                'name' => 'LAIANE',
//                'meta' => 264
//            ],
//            9 => [
//                'name' => 'NILMAR',
//                'meta' => 297
//            ],
//            10 => [
//                'name' => 'PEDRO',
//                'meta' => 231
//            ],
//            11 => [
//                'name' => 'TARCISIANE',
//                'meta' => 264
//            ],
//            12 => [
//                'name' => 'ALISSON',
//                'meta' => 1.977
//            ]
//        ];
//
//        foreach($collab as $k => $v) {
//
//            $collaborator = Collaborator::where('nome', 'like', $v['name'].'%')->whereTipoComissaoId(3)->first();
//
//            $meta = new CollaboratorMeta();
//
//            $meta->create([
//               'colaborador_id' => $collaborator->id,
//                'mes_competencia' => '11',
//                'meta' => $v['meta'],
//                'modified_by' => 1
//            ]);
//        }
//
//
//        return "break";
//
//        $this->dateAdmission = $request->input('dateAdmission') ? Carbon::parse($request->input('dateAdmission'))->format('Y-m-d') : null;
//
//        $this->dateCompetence = $this->monthCompetence ? $this->monthCompetence : Carbon::now()->subMonth(2)->format('Y-m-d');
//
//        if(! $this->dateAdmission) {
//            return $this->response($this->meta);
//        }
//
//
//
//        return $this->dateCompetence;
//
//
//        $dateAdmission = Carbon::parse('2023-01-09');
//
//
//
//        $calendar = [];
//
//        return $dateAdmission->format('Y');
//
//
//        for($i = 1; $daysMonth >= $i; $i++) {
//            $calendar[] = [
//                'date' => Carbon::parse("$year-$month-$i")->format('Y-m-d'),
//                'name' => Carbon::parse("$year-$month-$i")->format('l')
//            ];
//        }
//
//        $calendar = collect($calendar);
//
//        $countDaysUtils = 0;
//        $countDaysCollab = 0;
//
//
//
//        foreach($calendar as $k => $v) {
//            if($dateAdmission == $v['date'] || $dateAdmission <= $v['date']) {
//                echo $v['date'];
//                echo '<br>';
//            }
//
//            if($v['name'] !== 'Sunday') {
//                if($v['name'] === 'Saturday') {
//                    $countDaysUtils = $countDaysUtils + 0.5;
//                } else {
//                    $countDaysUtils = $countDaysUtils + 1;
//                }
//            }
//
//        }
//
//        $meta = 90;
//
//
//        return $countDaysCollab;
//
//
//        $dateActual = Carbon::now()->format('d');
//        $daysMonth = Carbon::now()->format('t');
//        $dayName = Carbon::now()->format('l');
//        $year = Carbon::now()->format('Y');
//        $month = Carbon::now()->format('m');
//        $dayUtils = $daysMonth;
//        $dayUtil = 0;
//        $datesUtils = [];
//
//
//        for ($i = 1; ($daysMonth + 1) > $i; $i++) {
//            $date = Carbon::parse("$year-$month-$i")->format('d/m/Y');
//            $dayName = Carbon::parse("$year-$month-$i")->format('l');
//
//            if($date != '07/09/2022') {
//                if ($dayName !== 'Sunday') {
//                    if ($dayName === 'Saturday') {
//                        $dayUtil = $dayUtil + 0.5;
//                    } else {
//                        $dayUtil += 1;
//                    }
//                }
//            }
//
//            $datesUtils[] = [
//                $i => [
//                    $dayUtil
//                ]
//            ];
//        }




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


//
//
//            return "Ok";
//


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

    public function response()
    {
        return true;
    }



}
