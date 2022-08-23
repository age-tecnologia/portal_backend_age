<?php

namespace App\Http\Controllers\AgeRv;

use App\Http\Controllers\Controller;
use App\Models\AgeRv\VoalleSales;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VoalleSalesController extends Controller
{

    public function index()
    {
        //
    }


    public function create()
    {
        $query = 'SELECT DISTINCT
                    c.id as "id_contrato",
                    p.name AS "nome_cliente",
                    c.v_stage as "status",
                    c.v_status as "situacao",
                    c.date as "data_contrato",
                    caa.activation_date as "data_ativacao",
                    c.beginning_date  as "data_vigencia",
                    ac.user as "conexao",
                    c.amount AS "valor",
                    (SELECT name AS "vendedor" FROM erp.people p WHERE c.seller_1_id = p.id),
                    (SELECT name AS "supervisor" FROM erp.people p WHERE c.seller_2_id = p.id),
                    c.cancellation_date AS "data_cancelamento",
                    CASE
                        WHEN sp.title <> \'\' THEN sp.title
                        WHEN c.v_status = \'Cancelado\' THEN
                            CASE
                                WHEN cst.title is null THEN cst2.title
                                ELSE
                                    cst.title
                            END
                    END AS "plano"
                FROM
                    erp.contracts c
                LEFT JOIN
                    erp.contract_assignment_activations caa ON caa.contract_id = c.id
                LEFT JOIN
                    erp.authentication_contracts ac ON ac.contract_id = c.id
                LEFT JOIN
                    erp.people p ON p.id = c.client_id
                LEFT JOIN
                    erp.service_products sp ON ac.service_product_id = sp.id
                LEFT JOIN
                    erp.contract_service_tags cst ON cst.contract_id = c.id AND cst.title LIKE \'PLANO COMBO%\'
                LEFT JOIN
                    erp.contract_service_tags cst2 ON cst2.contract_id = c.id AND cst2.title LIKE \'PLANO%\' AND cst2.title NOT LIKE \'%COMBO%\'
                WHERE
                    caa.contract_id = c.id
                    OR
                    ac.user LIKE \'ALCL%\'';

                $salesVoalle = DB::connection('pgsql')->select($query);

                // Instanciando o banco de dados local
                $dataVoalle = new VoalleSales();

                set_time_limit(1000);

                $dataVoalle->truncate();


                foreach($salesVoalle as $sale => $value) {
                    $dataVoalle->firstOrCreate([
                        'id_contrato' => $value->id_contrato,
                    ], [
                        'nome_cliente' => $value->nome_cliente,
                        'status' => $value->status,
                        'situacao' => $value->situacao,
                        'data_contrato' => $value->data_contrato,
                        'data_ativacao' => $value->data_ativacao,
                        'data_vigencia' => $value->data_vigencia,
                        'conexao' => $value->conexao,
                        'valor' => $value->valor,
                        'vendedor' => $value->vendedor,
                        'supervisor' => $value->supervisor,
                        'data_cancelamento' => $value->data_cancelamento,
                        'plano' => $value->plano,
                    ]);
                }

//                $collaborator = new CollaboratorController();
//                $collaborator->create();

                return "Operação realizada";
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
