<?php

namespace App\Http\Controllers\TakeBlip;

use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class SendingMessageActiveController extends Controller
{

    private $log = [];

    public function index()
    {




        $cellphones = [
            '61984700440'
        ];

        foreach($cellphones as $key => $cellphone) {

            $cellphoneFormated = $this->removeCharacterSpecials($cellphone);

            $this->sendingMessage($cellphoneFormated);
            $this->intermediary($cellphoneFormated);
            $this->moveBlock($cellphoneFormated);

        }


        return $this->log;




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

        $this->log[] = ['sending' => 'success'];

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

        $this->log[] = ['intermediary' => 'success'];


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

        $this->log[] = ['move' => 'success'];


    }

    private function removeCharacterSpecials($cellphone) {
        $cellphone = preg_replace('/[^0-9]/', '', $cellphone);
        return $cellphone;
    }
}
