<?php

namespace App\Http\Controllers\AgeRv\Collaborators;

use App\Http\Controllers\Controller;
use App\Models\AgeRv\Collaborator\Collaborator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class CollaboratorsController extends Controller
{

    // Consulta SQL para obter colaboradores do Voalle
    private string $query;

    // Array de resposta da requisição
    private array $response = [
        'status' => 200,
        'message' => 'success',
        'exceptions' => [],
        'data' => []
    ];

    // Instância do modelo "Collaborator"
    private $collaborator;



    /**
     * Construtor da classe.
     *
     * @return void
     */
    public function __construct()
    {
        // Define a consulta SQL para obter colaboradores do Voalle
        $this->query = 'SELECT id, name, vendor, supervisor, technical FROM erp.people
                        WHERE (vendor is true or supervisor is true or technical is true) and collaborator is true';
        $this->collaborator = new Collaborator();

        // Chama o método "checkNeedUpdate" para verificar se é necessário importar colaboradores do Voalle
        $this->checkNeedUpdate();
    }

    /**
     * Resposta da requisição.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    private function response() : \Illuminate\Http\JsonResponse
    {
        //
        return response()->json($this->response, $this->response['status']);
    }

    /**
     * Obter a lista de colaboradores.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getList()
    {
        $this->response['data'] = $this->collaborator
            ->leftJoin('portal_colaboradores_funcoes as f', 'f.id', '=', 'agerv_colaborador.funcao_id')
            ->leftJoin('agerv_colaboradores_canais as c', 'c.id', '=', 'agerv_colaborador.canal_id')
            ->leftJoin('agerv_colaboradores_canais as c2', 'c2.id', '=', 'agerv_colaborador.tipo_comissao_id')
            ->leftJoin('portal_users as u', 'u.id', '=', 'agerv_colaborador.coordenador_id')
            ->leftJoin('portal_users as u2', 'u2.id', '=', 'agerv_colaborador.gerente_id')
            ->select('agerv_colaborador.id', 'agerv_colaborador.nome', 'f.funcao as funcao', 'c.canal as canal',
                    'c2.canal as tipo_comissao', 'u.name as coordenador', 'u2.name as gerente')
            ->get();

        return $this->response();

    }


    /**
     * Importar colaboradores do Voalle e criá-los no banco de dados.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    private function importVoalle()
    {
        // Aumentar o limite de tempo para a execução desta função.
        set_time_limit(2000);

        // Obter a lista existente de IDs de colaboradores do banco de dados.
        $existingList = $this->collaborator->pluck('voalle_id')->toArray();

        // Converter a lista de IDs para uma string separada por vírgulas ou definir como 0 se estiver vazia.
        $existingList = !empty($existingList) ? implode(',', $existingList) : 0;

        // Construir a consulta SQL para buscar novos colaboradores do Voalle.
        $query = $this->query." and id not in ($existingList)";

        // Executar a consulta e ob ter os resultados.
        $data = \DB::connection('pgsql')->select($query);

        // Se novos colaboradores forem encontrados, criá-los no banco de dados.
        if (!empty($data)) {
            $this->create($data);
        }
    }

    /**
     * Criar colaboradores no banco de dados.
     *
     * @param  array  $import
     * @return \Illuminate\Http\JsonResponse
     */
    private function create($import)
    {
        $collaborators = [];

        foreach ($import as $item) {

            $function = 0;

            if($item->supervisor){
                $function = 3;
            } else if($item->vendor){
                $function = 1;
            } else if($item->technical){
                $function = 9;
            }

            $collaborators[] = [
                'voalle_id' => $item->id,
                'nome' => mb_convert_case(trim($item->name), MB_CASE_TITLE, 'UTF-8'),
                'funcao_id' => $function,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
        }

        $this->collaborator->insert($collaborators);


        $this->response['exceptions']['import'] = '['.count($collaborators).'] Colaboradores importados com sucesso.';
    }


    /**
     * Verificar se é necessário importar colaboradores do Voalle.
     *
     * @return void
     */
    private function checkNeedUpdate() : void
    {
        // Conta o número de colaboradores usando o modelo "collaborator"
        $countCollaborators = $this->collaborator->count();

        // Executa uma consulta no banco de dados PostgreSQL usando a conexão "pgsql" e armazena o resultado na variável $countCollaboratorsVoalle
        $countCollaboratorsVoalle = \DB::connection('pgsql')->select($this->query);

        // Verifica se o número de colaboradores obtidos do modelo "collaborator" é diferente do número de colaboradores obtidos da consulta PostgreSQL
        if ($countCollaborators != count($countCollaboratorsVoalle)) {
            // Chama o método "importVoalle" para importar dados do Voalle
            $this->importVoalle();
        } else {
            // Define uma mensagem de exceção informando que não há dados para importar
            $this->response['exceptions']['import'] = 'Não há dados para importar.';
        }
    }


}
