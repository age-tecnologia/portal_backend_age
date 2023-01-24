<?php

namespace App\Http\Controllers\AgeControl;

use App\Http\Controllers\Controller;
use App\Http\Requests\AgeControl\ConductorStoreRequest;
use App\Models\AgeControl\Conductor;
use App\Models\AgeControl\Vehicle;
use http\Client\Curl\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ConductorController extends Controller
{

    private $fullName;

    public function index(Conductor $conductor)
    {
        return response()->json($conductor->all(), 200);
    }

    public function viewConductorComplete(Conductor $conductor)
    {
        $conductors = DB::table('agecontrol_condutores as c')
                        ->leftJoin('agecontrol_veiculos as v', 'c.id', '=', 'v.condutor_id')
                        ->leftJoin('agecontrol_veiculo_tipo as vt', 'vt.id', '=', 'v.tipo_veiculo_id')
                        ->leftJoin('portal_colaboradores_grupos as g', 'g.id', '=', 'c.grupo_id')
                        ->leftJoin('portal_cidades as pc', 'pc.id', '=', 'c.cidade_id')
                        ->leftJoin('agecontrol_servicos as s', 's.id', '=', 'c.servico_id')
                        ->get(['c.id', 'c.primeiro_nome', 'c.segundo_nome', 'g.grupo', 'vt.tipo', 'v.fabricante', 'v.modelo', 'c.created_at']);

        return response()->json($conductors, 200);

    }


    public function create()
    {
        //
    }


    public function store(ConductorStoreRequest $request, Conductor $conductor)
    {

        $validated = $request->validated();

        try {

            $this->fullName = mb_convert_case($request->input('firstName')." ".$request->input('lastName'), MB_CASE_TITLE, 'UTF-8');

            $user = \App\Models\User::where($request->only('email'))->first();

            if(isset($user->email)) {
                return response()->json([
                    'error' => 'Esse e-mail já está cadastrado na base de dados.'
                ], 422);
            }

            $user = \App\Models\User::create([
                'name' => $this->fullName,
                'email' => mb_convert_case($request->input('email'), MB_CASE_LOWER, 'UTF-8'),
                'isAD' => 0,
                'nivel_acesso_id' => 1,
                'status_id' => 1,
                'password' => Hash::make('0@hnRB6R00qyRH&LHFg$zgWh3MHmOVo&$IliWjCr')
            ]);

            if(! isset($user->email)) {
                return response()->json([
                    'error' => 'Erro ao criar usuário, contate a equipe de desenvolvimento.'
                ], 422);
            }


            if(isset($user->email)) {

                $conductor = $conductor->create([
                   'primeiro_nome' => mb_convert_case($request->input('firstName'), MB_CASE_TITLE, 'UTF-8'),
                   'segundo_nome' => mb_convert_case($request->input('lastName'), MB_CASE_TITLE, 'UTF-8'),
                   'endereco' => $request->input('address'),
                   'cidade_id' => $request->input('city'),
                   'grupo_id' => $request->input('group'),
                   'servico_id' => $request->input('typeService'),
                   'user_id' => $user->id,
                ]);

                if(! isset($conductor->id)) {
                    return response()->json([
                        'error' => 'Erro ao criar um condutor, contate a equipe de desenvolvimento.'
                    ], 422);
                }

                $vehicle = Vehicle::create([
                    'condutor_id' => $conductor->id,
                    'tipo_veiculo_id' => $request->input('typeVehicle'),
                    'fabricante' => $request->input('manufacturer'),
                    'modelo' => $request->input('model'),
                    'capacidade_tanque' => $request->input('tankCapacity'),
                    'media_km_litro' => $request->input('averageKmL'),
                    'quilometragem_inicial' => $request->input('initialKm'),
                    'distancia_sede_casa' => $request->input('distanceBaseHouse'),
                    'modalidade_id' => $request->input('modality'),
                ]);

                if(! isset($vehicle->id)) {
                    return response()->json([
                        'error' => 'Erro ao criar um condutor, contate a equipe de desenvolvimento.'
                    ], 422);
                }

                return response()->json([
                    'msg' => 'Condutor criado com sucesso!',
                    'status' => 'success'
                ], 201);


            }


        } catch (\Exception $e) {

            return $e->getMessage();

        }

    }


    public function show(Conductor $conductor)
    {
        //
    }


    public function edit(Conductor $conductor)
    {
        //
    }


    public function update(Request $request, Conductor $conductor)
    {
        //
    }


    public function destroy(Conductor $conductor)
    {
        //
    }


}
