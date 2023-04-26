<?php

namespace App\Http\Controllers\AgeNotify\B2b;

use App\Http\Controllers\Controller;
use App\Mail\AgeNotify\B2b\SendWelcomeClient;
use App\Mail\AgeNotify\Sac\SendAlertPix;
use Dompdf\Dompdf;
use http\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Mail\Markdown;
use Illuminate\Support\HtmlString;
use Intervention\Image\Facades\Image;


class WelcomeClientController extends Controller
{

    public function index()
    {


        $template = view('mail.ageNotify.b2b.welcome_client_template',
            ['client' => 'Carlos Neto', 'contract' => 20391, 'vigence' => '2023-05-01'])->render();

        $dom = new Dompdf();

        $dom->loadHtml($template);
        $dom->render();

        $pdfOutput = $dom->output();

        $filePath = public_path('image/test.pdf');
        file_put_contents($filePath, $pdfOutput);

        return true;

      //  $image = Image::make($template);

      //  return $image->response('png');


    }



    private function send()
    {
        Mail::mailer('notification')
            ->to('carlos.neto@agetelecom.com.br')
            ->send(new SendWelcomeClient(), 'text-html');

        return true;
    }


}
