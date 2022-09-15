<?php

namespace App\Http\Controllers;

use App\Models\AgeRv\Collaborator;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Psy\Util\Str;

class UsersController extends Controller
{

    public function index()
    {
        //
    }


    public function create()
    {
        return "create";
    }


    public function store(Request $request)
    {

    }

    public function newUserAgeRv(Request $request)
    {
        $id = $request->input('id');

        try {
            $collaborator = Collaborator::find($id);

            if(isset($collaborator->nome)) {

                $fullName = explode(' ', $collaborator->nome)[0]." ".explode(' ', $collaborator->nome)[1];
                $email = mb_convert_case(
                    explode(' ', $fullName)[0].".".explode(' ', $fullName)[1]."@agetelecom.com.br", MB_CASE_LOWER, 'UTF-8'
                );

                try {

                    $password = \Illuminate\Support\Str::random(12);

                    $userFind = User::where('email', $email)->first();

                    if(! isset($userFind->email)) {
                        $user = User::create([
                            'email' => $email,
                            'name' => $fullName,
                            'nivel_acesso_id' => 1,
                            'isAD' => 0,
                            'status_id' => 1,
                            'password' => Hash::make($password)
                        ]);

                        $collaborator->update([
                            'user_id' => $user->id
                        ]);


                        if(isset($user->name)) {

                            return response()->json([
                                'email' => $user->email,
                                'password' => $password,
                                'message' => 'Usuário criado e vinculado com sucesso!'
                            ], 201 );

                        }
                        else {throw new \Exception('Usuário não criado, erro desconhecido.', 401);}
                    } else {throw new \Exception('Usuário já existe, não foi possível criar!', 401);}


                } catch (\Exception $e) {return response()->json([$e->getMessage()], 200);}

            } else {throw new \Exception("Nenhum colaborador encontrado.", 301);}

        } catch (\Exception $e) {return response()->json([$e->getMessage()], 200);}
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
