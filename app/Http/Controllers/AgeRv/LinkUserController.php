<?php

namespace App\Http\Controllers\AgeRv;

use App\Http\Controllers\Controller;
use App\Models\AgeRv\AccessPermission;
use App\Models\AgeRv\Collaborator;
use App\Models\User;
use Illuminate\Http\Request;

class LinkUserController extends Controller
{

    public function index()
    {
        //
    }

    public function getUsersNotLinkAgeRv(Request $request)
    {
        $collab = Collaborator::where('nome', $request->input('name'))->whereNull('user_id')->first();

        if(isset($collab->id)) {

            $usersVinc = Collaborator::whereNotNull('user_id')->get(['user_id']);

            $users = User::whereNotIn('id', $usersVinc)->whereNivelAcessoId(1)->get(['id', 'name', 'email']);

            $name = explode(' ', mb_convert_case($request->input('name'), MB_CASE_LOWER, 'utf-8'));

            $users = $users->filter(function($item, $key) use($request, $name) {
                if((str_contains($item->name, $name[0]) || (str_contains($item->name, $name[1]) ))){
                    return $item;
                }
            });

            return response()->json($users, 201);
        }

        return response()->json('Usuário já possui vínculo.', 200);

    }

    public function linkUserAndReleaseAccess(Request $request)
    {

        $collab = Collaborator::whereId($request->input('idCollab'))->whereNull('user_id')->first();

        if(isset($collab->id)) {

            $collab->update([
                'user_id' => $request->input('idUser')
            ]);

            if($collab) {

                $permited = AccessPermission::create([
                    'user_id' => $collab->user_id,
                    'funcao_id' => $collab->funcao_id,
                    'setor_id' => 1,
                    'nivel_acesso_id' => 1
                ]);


                return response()->json('Usuário vinculado com sucesso!', 201);
            } else {
                return response()->json('Usuário não foi vinculado!', 200);
            }
        }


        return response()->json('Nenhum usuário encontrado com esse ID.', 200);
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
        //
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
}
