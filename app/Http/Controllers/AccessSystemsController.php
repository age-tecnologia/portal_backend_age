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
                case 'ageboard':
                    return $this->usersAgeBoard();
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

    private function usersAgeBoard() {


        $idUsersBoard = \App\Models\AgeBoard\AccessPermission::all(['user_id']);

        $usersBoard = DB::table('ageboard_usuarios_permitidos as up')
            ->leftJoin('portal_users as u', 'u.id', '=', 'up.user_id')
            ->get(['u.id', 'u.name', 'u.email']);

        $users = User::where('nivel_acesso_id', 1)->whereNotIn('id', $idUsersBoard)->get(['id', 'name', 'email']);

        $result = [];


        foreach($usersBoard as $key => $value) {
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

    public function alternateAccess(Request $request, $id)
    {
        if($this->system !== null) {

            switch ($this->system) {
                case 'agereport':
                    return $this->accessAgeReport($id);
                    break;
                case 'ageboard':
                    return $this->accessAgeBoard($id);
                    break;
            }

        }
    }

    private function accessAgeReport($id)
    {
        $user = AccessPermission::whereUserId($id)->withTrashed()->first();

        if(! isset($user->id)) {
            $user = AccessPermission::create([
               'user_id' => $id,
                'funcao_id' => 2,
                'setor_id' => 9,
                'nivel_acesso_id' => 1
            ]);

            return response()->json(['msg' => 'Usuário liberado com sucesso.', 'access' => true], 201);

        } else {

            if(isset($user->deleted_at)) {

                $user = $user->restore();

                return response()->json(['msg' => 'Usuário liberado com sucesso.', 'access' => true], 201);

            } else {
                $user = $user->delete();

                return response()->json(['msg' => 'Usuário inativado com sucesso.', 'access' => false], 201);
            }

        }
    }

    private function accessAgeBoard($id)
    {
        $user = \App\Models\AgeBoard\AccessPermission::whereUserId($id)->withTrashed()->first();

        if(! isset($user->id)) {
            $user = \App\Models\AgeBoard\AccessPermission::create([
               'user_id' => $id,
                'funcao_id' => 2,
                'setor_id' => 9,
                'nivel_acesso_id' => 1
            ]);

            return response()->json(['msg' => 'Usuário liberado com sucesso.', 'access' => true], 201);

        } else {

            if(isset($user->deleted_at)) {

                $user = $user->restore();

                return response()->json(['msg' => 'Usuário liberado com sucesso.', 'access' => true], 201);

            } else {
                $user = $user->delete();

                return response()->json(['msg' => 'Usuário inativado com sucesso.', 'access' => false], 201);
            }

        }
    }
}
