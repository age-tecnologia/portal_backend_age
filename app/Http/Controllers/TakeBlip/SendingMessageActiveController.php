<?php

namespace App\Http\Controllers\TakeBlip;

use App\Http\Controllers\Controller;
use App\Models\Integrator\Takeblip\MessageActive;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SendingMessageActiveController extends Controller
{

    private $log = [];

    public function index(Request $request)
    {


        $array = \Maatwebsite\Excel\Facades\Excel::toArray(new \stdClass(), $request->file('excel'));

        set_time_limit(200000);

        $contracts = [];


        $i = 0;

        foreach($array[0] as $k => $v) {

            $contracts[] = $v[0];

        }

        $cellphonesList = implode(",", $contracts);



        $query = "SELECT
                          c.id,
                          p.v_name,
                          c.collection_day,
                          CASE
                              WHEN p.cell_phone_1 IS NOT NULL THEN p.cell_phone_1
                              ELSE p.cell_phone_2
                          END AS \"cellphone\"
                      FROM
                          erp.contracts c
                      LEFT JOIN
                          erp.people p ON p.id = c.client_id
                      WHERE
                          c.id in ($cellphonesList)  and c.collection_day = 5 order by c.id asc";




        $cellphoneContracts = DB::connection("pgsql")->select($query);



        try {
            foreach($cellphoneContracts as $key => $cellphone) {

                try {
                    $cellphoneFormated = $this->removeCharacterSpecials($cellphone->cellphone);

                    $this->sendingMessage($cellphoneFormated);
                    $this->intermediary($cellphoneFormated);
                    $this->moveBlock($cellphoneFormated);
                    $this->saveData($cellphone, $cellphoneFormated);
                } catch (\Exception $e) {
                    throw $e;
                }

            }
        } catch (\Exception $e) {
            $e;
        }


        return [
            'cellphones' => $cellphoneContracts
        ];




    }

    private function sendingMessage($cellphone)
    {
        $client = new Client();

        // Cria o array com os dados a serem enviados
        $data = [
            "id" => uniqid(),
            "to" => "55$cellphone@wa.gw.msging.net",
            "type" => "application/json",
            "content" => [
                "type" => "template",
                "template" => [
                    "namespace" => "0c731938_5304_4f41_9ccf_f0942721dd48",
                    "name" => "envio_fatura_requisicao",
                    "language" => [
                        "code" => "PT_BR",
                        "policy" => "deterministic"
                    ],
                    "components" => [
                        // Adicione os componentes necessários aqui
                    ]
                ]
            ]
        ];

        // Faz a requisição POST usando o cliente Guzzle HTTP
        $response = $client->post('https://agetelecom.http.msging.net/messages', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Key YWdldGVsZWNvbXJvdXRlcjpYdlBjZWNRaUs0VTdKT2RNT2VmdQ=='
            ],
            'json' => $data
        ]);

        // Obtém o corpo da resposta
        $body = $response->getBody();


    }

    private function intermediary($cellphone) {

        // Cria uma instância do cliente Guzzle HTTP
        $client = new Client();

        // Cria o array com os dados a serem enviados
        $data = [
            "id" => uniqid(),
            "to" => "postmaster@msging.net",
            "method" => "set",
            "uri" => "/contexts/55$cellphone@wa.gw.msging.net/Master-State",
            "type" => "text/plain",
            "resource" => "contatoativoenviodefatura"
        ];

        // Faz a requisição POST usando o cliente Guzzle HTTP
        $response = $client->post('https://agetelecom.http.msging.net/commands', [
            'json' => $data,
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Key YWdldGVsZWNvbXJvdXRlcjpYdlBjZWNRaUs0VTdKT2RNT2VmdQ=='
            ]
        ]);

        // Obtém o corpo da resposta
        $body = $response->getBody();



    }

    private function moveBlock($cellphone)
    {
        // Cria uma instância do cliente Guzzle HTTP
        $client = new Client();

        // Cria o array com os dados a serem enviados
        $data = [
            "id" => uniqid(),
            "to" => "postmaster@msging.net",
            "method" => "set",
            "uri" => "/contexts/55$cellphone@wa.gw.msging.net/stateid@684abf3b-a37b-4c29-bb28-4600739efde0",
            "type" => "text/plain",
            "resource" => "b1814ddb-4d3b-4857-904f-cd5a0a6a9c5e"
        ];

        // Faz a requisição POST usando o cliente Guzzle HTTP
        $response = $client->post('https://agetelecom.http.msging.net/commands', [
            'json' => $data,
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Key YWdldGVsZWNvbXJvdXRlcjpYdlBjZWNRaUs0VTdKT2RNT2VmdQ=='
            ]
        ]);

        // Obtém o corpo da resposta
        $body = $response->getBody();



    }

    private function removeCharacterSpecials($cellphone) {
        $cellphone = preg_replace('/[^0-9]/', '', $cellphone);
        return $cellphone;
    }

    private function saveData($data, $cellFormatted)
    {
        $takeMsgActive = new MessageActive();


        $takeMsgActive->create([
           'cliente' => $data->v_name,
           'numero_original' => $data->cellphone,
           'numero_enviado' => $cellFormatted,
           'lote' => 1,
           'vencimento' => $data->collection_day,
           'data_envio_whatsapp' => Carbon::now(),
           'sucesso' => 1,
        ]);

    }

}
