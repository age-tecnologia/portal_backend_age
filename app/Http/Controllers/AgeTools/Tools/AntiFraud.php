<?php

namespace App\Http\Controllers\AgeTools\Tools;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AntiFraud extends Controller
{

    public function index(Request $request)
    {




            $query = 'select p."name", p.cell_phone_1, p.email, p.street, p."number", p.neighborhood, p.postal_code, c.v_stage, c.v_status, ac."user"
                        from erp.contracts c
                        left join erp.people p on c.client_id = p.id
                        left join erp.authentication_contracts ac on c.id = ac.contract_id
                         where
                        ';

            $lastCondition = 1;

            $conditions = array(); // Array para armazenar as condições da consulta

            $fieldsMap = array(
                'name' => 'LOWER(p."name")',
                'email' => 'LOWER(p.email)',
                'number' => 'LOWER(p."number")',
                'cep' => 'LOWER(p.postal_code)',
                'address' => 'LOWER(p.street)',
                'neighborhood' => 'LOWER(p.neighborhood)',
                'document' => 'LOWER(p.document)',
                'tel' => 'LOWER(p.tel)',
                'cel' => 'LOWER(p.cel)'
            );

            // Verifica cada campo no objeto de requisição
            foreach ($fieldsMap as $field => $fieldSQL) {
                if (isset($request->$field)) {
                    $conditions[] = $fieldSQL . ' LIKE LOWER(\'%' . $request->$field . '%\')';
                }
            }

            // Concatena as condições à consulta SQL
            if (!empty($conditions)) {
                $query .= implode(' AND ', $conditions);
            } else {
                $query .= '1 = 1'; // Caso nenhuma condição seja encontrada, adiciona uma condição que sempre seja verdadeira para retornar todos os resultados
            }

            $query = $query.' limit 20';

            $result = DB::connection('pgsql')->select($query);

            return response()->json([$result], 200);


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
