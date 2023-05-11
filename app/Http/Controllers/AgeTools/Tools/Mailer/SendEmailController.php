<?php

namespace App\Http\Controllers\AgeTools\Tools\Mailer;

use App\Http\Controllers\Controller;
use App\Mail\AgeTools\Tools\Mailer\PatternSending;
use App\Models\AgeTools\Tools\Mailer\Mailer;
use App\Models\AgeTools\Tools\Mailer\Template;
use Egulias\EmailValidator\EmailValidator;
use Egulias\EmailValidator\Validation\RFCValidation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;
use function PHPUnit\Framework\isJson;

class SendEmailController extends Controller
{

    private string $email;
    private $data;
    private array $errors;
    private $template;
    private $mailer;

    public function index(Request $request)
    {
        try {


            $this->inputData($request->json('data'));
            $this->isValidEMail();


            if(!empty($this->errors)) {
                throw new \Exception(implode(', ', $this->errors), 400);
            }

            $this->processingSend();


            if(!empty($this->errors)) {
                throw new \Exception(implode(', ', $this->errors), 400);
            }

            return $this->sendingEmail();

        } catch (\Exception $e) {

            // Se ocorrer uma exceção, o bloco catch captura a exceção, obtém a mensagem e o código de status HTTP da exceção e retorna a mensagem como uma resposta JSON com o código de status HTTP correspondente.
            return response()->json($e->getMessage());
        }
    }


    private function isValidEMail()
    {
        $validator = new EmailValidator();

        if(! $validator->isValid($this->email, new RFCValidation())){
            $this->errors[] = 'O e-mail informado é inválido';
        };

    }

    private function inputData($data)
    {
        if(isJson($data)) {
            $this->data = json_decode($data);
            $this->email = $this->data->email;
        } else {
            $this->errors[] = 'Dados enviados em formato inválido';
        }
    }

    private function processingSend()
    {
        $this->mailer = Mailer::whereId($this->data->mailerId)->first(['id', 'mailer', 'configuracao']);

        $this->template = Template::whereMailerId($this->data->mailerId)->whereId($this->data->templateId)
                            ->first(['id', 'nome', 'template']);


        if(! isset($this->mailer->id)) {
            $this->errors[] = "Nenhum mailer vinculado à esse id";
        }

        if(! isset($this->template->id)) {
            $this->errors[] = "Nenhuma template vinculada à esse id";
        }

    }

    private function sendingEmail()
    {


        $to = $this->email;
        $settings = json_decode($this->mailer->configuracao);
        $variables = [];

        foreach($this->data->form as $key => $input) {
            $variables[] = [
              'name' => $input->name,
              'data' => $input->data
            ];
        }

        $variables = $this->data->form;

        $config = [
            'driver' => 'smtp',
            'host' => $settings->host,
            'port' => $settings->port,
            'from' => [
                'address' => $settings->username,
                'name' => $settings->name
            ],
            'encryption' => $settings->encryption,
            'username' => $settings->username,
            'password' => $settings->password,
        ];

        Config::set('mail', $config);

        foreach($variables as $key => $variable) {

            $this->template->template = str_replace("{{".$variable->name."}}", $variable->data, $this->template->template);

        }


        $mailer =  $mailer = Mail::to($to)
                            ->send(new PatternSending($this->template->nome, $this->template->template, $variables));



        return response()->json('E-mail enviado com sucesso', 200);

    }




}
