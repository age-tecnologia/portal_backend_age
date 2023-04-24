<?php

namespace App\Http\Controllers\AgeNotify\B2b;

use App\Http\Controllers\Controller;
use App\Mail\AgeNotify\B2b\SendWelcomeClient;
use App\Mail\AgeNotify\Sac\SendAlertPix;
use http\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Mail\Markdown;
use Illuminate\Support\HtmlString;


class WelcomeClientController extends Controller
{

    public function index()
    {


        Mail::mailer('notification')
            ->to('carlos.neto@agetelecom.com.br')
            ->send(new SendWelcomeClient(), 'text-html');



        return view('mail.ageNotify.b2b.welcome_client')
                    ->with(['client' => 'Multimidia Samsung', 'contract' => '245481', 'vigence' => '10/12/2026',
                        'lorem' => 'Primeira, gostariámos de lhe dar as boas-vindas pela']);
                }


}
