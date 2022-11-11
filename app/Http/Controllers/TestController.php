<?php

namespace App\Http\Controllers;

use App\Exports\ReportExport;
use App\Exports\UsersExport;
use App\Http\Controllers\AgeRv\_aux\sales\Stars;
use App\Ldap\UserLdap;
use App\Models\AgeBoard\AccessPermission;
use App\Models\AgeBoard\DashboardPermitted;
use App\Models\AgeBoard\ItemPermitted;
use App\Models\AgeReport\Report;
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

            $id = $request->input('id');

            $id = [
                94,
                95,
                96,
                97
            ];



            $user = User::find($id);

            $permitted = new AccessPermission();

            $dashPermitted = new DashboardPermitted();

            $itemPermitted = new ItemPermitted();


            foreach($id as $key => $value) {
                $permitted = $permitted->firstOrCreate(
                    ['user_id' => $value],
                    ['funcao_id' => 1, 'setor_id' => 1, 'nivel_acesso_id' => 1]
                );

                $dashPermitted = $dashPermitted->firstOrCreate(
                    ['user_id' => $value],
                    ['dashboard_id' => 3, 'permitido_por' => 1]
                );

                $itemPermitted = $itemPermitted->firstOrCreate(
                    ['user_id' => $value],
                    ['dashboard_id' => 3, 'item_id' => 9, 'criado_por' => 1, 'modificado_por' => 1]
                );

            }

            return 'ok';



//        $users = UserLdap::limit(1)->get(['name']);
//
//        return $users[0]['name'];



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
