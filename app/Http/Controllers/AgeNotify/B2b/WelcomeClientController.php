<?php

namespace App\Http\Controllers\AgeNotify\B2b;

use App\Http\Controllers\Controller;
use App\Mail\AgeNotify\B2b\SendWelcomeClient;
use App\Mail\AgeNotify\Sac\SendAlertPix;
use Carbon\Carbon;
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


//        $template = view('mail.ageNotify.b2b.welcome_client_template',
//            ['client' => 'Carlos Neto', 'contract' => 20391, 'vigence' => '2023-05-01'])->render();
//
//        $dom = new Dompdf();
//
//        $dom->loadHtml($template);
//        $dom->render();
//
//        $pdfOutput = $dom->output();
//
//        $filePath = public_path('image/test2.pdf');
//        file_put_contents($filePath, $pdfOutput);
//
//        return true;
//
//      //  $image = Image::make($template);
//
//      //  return $image->response('png');

        return $this->send();

    }



    public function send(Request $request)
    {

        set_time_limit(20000000);


        $array = \Maatwebsite\Excel\Facades\Excel::toArray(new \stdClass(), $request->file('excel'));

        $error = [];
        $success = [];


        foreach ($array[0] as $key => $value) {
            if(filter_var(trim($value[0]), FILTER_VALIDATE_EMAIL)) {


                $countString = substr_count($value[2], '-');



                for($i = 0; $i < $countString; $i++) {

                    if($i === 0) {
                        $field = $value[2];
                    } else {
                        $field = $fieldFormatted;
                    }

                    $character = "-";
                    $position = strpos($field, $character);
                    $fieldFormatted = substr($field, $position + strlen($character));
                }



//                Mail::mailer('b2b')
//                    ->to($value[0])
//                    ->send(new SendWelcomeClient(trim($fieldFormatted), $value[1], $value[4], $value[3]), 'text-html');


            } else {
                $error[] = [
                    'email' => $value[0]
                ];
            }
        }


        return [
            $error,
            'count_errors' => count($error),
            'emails' => count($array[0]),
            'msg' => 'sucesso.'
        ];


    }


}
