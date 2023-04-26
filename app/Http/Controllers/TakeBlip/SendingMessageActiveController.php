<?php

namespace App\Http\Controllers\TakeBlip;

use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class SendingMessageActiveController extends Controller
{
    public function index()
    {



        $cellphones = [
          '5191211218'
        ];

        foreach($cellphones as $key => $cellphone) {
            $this->sendingMessage($cellphone);
            $this->moveBlock($cellphone);

        }
        return true;




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
                    "name" => "ativocobranca",
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
        $response = $client->post('https://telek.http.msging.net/messages', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Key YWdldGVsZWNvbXJvdXRlcjpYdlBjZWNRaUs0VTdKT2RNT2VmdQ=='
            ],
            'json' => $data
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
            "uri" => "/contexts/55$cellphone@wa.gw.msging.net/stateid@ca3df1c5-8183-47e0-9529-5368f44c4951",
            "type" => "text/plain",
            "resource" => "53e35c35-0c06-44c2-b302-74803bf51304"
        ];

        // Faz a requisição POST usando o cliente Guzzle HTTP
        $response = $client->post('https://telek.http.msging.net/commands', [
            'json' => $data,
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Key YWdldGVsZWNvbXJvdXRlcjpYdlBjZWNRaUs0VTdKT2RNT2VmdQ=='
            ]
        ]);

        // Obtém o corpo da resposta
        $body = $response->getBody();

    }
}
