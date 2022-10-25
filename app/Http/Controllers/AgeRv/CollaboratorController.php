<?php

namespace App\Http\Controllers\AgeRv;

use App\Http\Controllers\Controller;
use App\Models\AgeRv\AccessPermission;
use App\Models\AgeRv\Channel;
use App\Models\AgeRv\Collaborator;
use App\Models\User;
use Carbon\Carbon;
use Complex\Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CollaboratorController extends Controller
{

    public function index()
    {
        $c = DB::table('agerv_colaboradores as c')
                ->leftJoin('portal_users as u', 'c.user_id', '=', 'u.id')
                ->leftJoin('portal_colaboradores_funcoes as f', 'c.funcao_id', '=', 'f.id')
                ->leftJoin('agerv_colaboradores_canais as cc', 'c.canal_id', '=', 'cc.id')
                ->leftJoin('agerv_colaboradores_canais as cc2', 'c.tipo_comissao_id', '=', 'cc2.id')
                ->leftJoin('agerv_colaboradores as c2', 'c.supervisor_id', '=', 'c2.id')
                ->leftJoin('portal_users as u2', 'c.gestor_id', '=', 'u2.id')
                ->selectRaw('c.id, c.nome as collaborator, u.name as username, f.funcao as `function`,
                            cc.canal as channel, cc2.canal as type_commission, c2.nome as supervisor,
                            u2.name as management, u.email, u.id as user_id, u.isAD, (SELECT meta FROM agerv_colaboradores_meta
                                                    WHERE colaborador_id = c.id
                                                    AND mes_competencia = '.Carbon::now()->format('m').' limit 1 ) as meta')
                ->get();


        $c->each(function ($item) {
            $item->collaborator = mb_convert_case($item->collaborator, MB_CASE_TITLE, 'UTF-8');
            $item->username = mb_convert_case($item->username, MB_CASE_TITLE, 'UTF-8');
            $item->supervisor = mb_convert_case($item->supervisor, MB_CASE_TITLE, 'UTF-8');
            $item->management = mb_convert_case($item->management, MB_CASE_TITLE, 'UTF-8');
        });

        return response()->json($c, 201);
    }


    public function create()
    {
        //
    }


    public function store(Request $request)
    {
        //
    }


    public function show($id)
    {
        $collaborator = Collaborator::find($id);

        if(! isset($collaborator)) {return response()->json(['error' => 'Não foi encontrado nenhum colaborador.'], 200);}
        else {

            return [
                'nameCollaborator' => $collaborator->nome,
                'userLiked' => $this->getUserLiked($collaborator->user_id),
                'userLiked_id' => $collaborator->user_id,
                'channelLiked' => $collaborator->canal,
                'usersAvaliable' => $this->getUsernames(),
                'channelsAvaliable' => $this->getChannels(),
                'supervisorsAvaliable' => $this->getSupervisors()
            ];

        }
    }

    private function getUserLiked($id)
    {
            $user = User::find($id);

            if(isset($user->name)) {return $user;}
            else {return null;}

    }

    private function getUsernames() {

        try {

            $collaborators = Collaborator::whereNotNull('user_id')->select('user_id')->get();

            if($collaborators->isNotEmpty()) {
                try {
                    $users = User::select('name')->whereNotIn('id', $collaborators)->get();

                    if(! empty($users)) {return $users;}
                    else {throw new \Exception("Nenhum usuário corresponde a consulta.", 301);}

                } catch (\Exception $e) {return $e->getMessage().' - '.$e->getCode();}
            } else {throw new \Exception('Não há vinculo entre colaboradores e usuários.', 301);}

        }   catch (\Exception $e) {return $e->getMessage().' - '.$e->getCode();}


    }

    private function getChannels() {
        try {

            $channels = Channel::select('id','canal')->get();

            if($channels->isNotEmpty()) {return $channels;}
            else {throw new \Exception('Nenhum resultado encontrado no banco.', 301);}

        } catch(\Exception $e) {return $e->getMessage().' - '.$e->getCode();}
    }

    private function getSupervisors() {

        try {

            $supervisors = DB::table('agerv_usuarios_permitidos as up')
                            ->leftJoin('portal_users as u', 'up.user_id', '=', 'u.id')
                            ->whereIn('funcao_id', [3, 4])
                            ->select('u.name', 'u.id')
                            ->get();

           if($supervisors->isNotEmpty()) {

               $supervisors->each(function($item) {
                   return $item->name = mb_convert_case($item->name, MB_CASE_TITLE, 'UTF-8');
              });

               return $supervisors;


           } else {throw new \Exception('Nenhum supervisor ou gerente encontrado!', 301);}

        } catch (\Exception $e) {return $e->getMessage().' - '.$e->getCode();}

    }

    public function edit($id)
    {
        //
    }


    public function update(Request $request, $id)
    {
        //
    }


    public function destroy($id)
    {
        //
    }

    public function showList(Request $request)
    {
        header("Access-Control-Allow-Origin: *");

        $collaborators = Collaborator::where('nome', 'LIKE', '%'.$request->json('name').'%')->limit(5)->get();

        return response()->json($collaborators, 201);
    }
}
