<?php

namespace App\Http\Controllers\AgeTools\Tools\Mailer\_aux;



use App\Models\AgeTools\Tools\Mailer\Batch;
use App\Models\AgeTools\Tools\Mailer\EmailSending;
use App\Models\AgeTools\Tools\Mailer\Mailer;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class VerifyLimitSendings
{

    private array $response = [
        'status' => false,
        'limitAvailable' => 0,
        'errors' => []
    ];

    private $mailer;

    public function __construct(Mailer $mailer, $mailerId)
    {
        $this->mailer = $mailer->find($mailerId);
    }

    public function verify()
    {


        if(! isset($this->mailer->id)) {
            $this->response['errors'][] = 'Nenhum mailer vinculado à esse id:'.$this->mailer->id;
        }

        $dateLimit = Carbon::now()->subHours(24);

        $batchs = Batch::whereMailerId($this->mailer->id)
                        ->where('created_at', '>', $dateLimit)
                        ->get(['id']);

        $emailSendings = EmailSending::whereIn('lote_id', $batchs)->count();



        if($emailSendings < $this->mailer->limite_diario) {
            $this->response['status'] = true;
            $this->response['limitAvailable'] = $this->mailer->limite_diario - $emailSendings;
        } else {
            $this->response['errors'][] = 'Limite de envios atingido, aguarde e tente novamente mais tarde.';
        }

        return $this->response();

    }

    private function response() : array
    {
        return $this->response;
    }

}
