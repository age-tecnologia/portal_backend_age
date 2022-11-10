<?php

namespace App\Http\Controllers;

use App\Models\AgeReport\AccessPermission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccessSystemsController extends Controller
{
   private $system;
   private $user_id;

   public function __construct(Request $request)
   {
       $this->system = $request->has('system') ? $request->input('system')  : null;
       $this->user_id = $request->has('userid') ? $request->input('userid')  : 0;
   }

    public function getUsers(Request $request)
    {

        if($this->system !== null) {

            switch ($this->system) {
                case 'agereport':
                    return $this->usersAgeReport();
                        break;
            }

        }
    }

    private function usersAgeReport() {


       $idUsersReport = AccessPermission::all(['user_id']);

       $usersReport = DB::table('agereport_usuarios_permitidos as up')
                        ->leftJoin('portal_users as u', 'u.id', '=', 'up.user_id')
                        ->get(['u.id', 'u.name', 'u.email']);

       $users = User::where('nivel_acesso_id', 1)->whereNotIn('id', $idUsersReport)->get(['id', 'name', 'email']);

       $result = [];


       foreach($usersReport as $key => $value) {
           $result[] = [
             'id' => $value->id,
             'name' => $value->name,
             'email' => $value->email,
             'access' => true
           ];
       }

        foreach($users as $key => $value) {
            $result[] = [
                'id' => $value->id,
                'name' => $value->name,
                'email' => $value->email,
                'access' => false
            ];
        }



       return $result;

    }
}
