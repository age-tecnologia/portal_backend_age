<?php

namespace App\Http\Controllers\AgeNotify\Sac;

use App\Http\Controllers\Controller;
use App\Mail\AgeNotify\Sac\SendBillingError;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class BillingErrorController extends Controller
{


    public function sendEmail(Request $request)
    {
        set_time_limit(20000000);


        $array = \Maatwebsite\Excel\Facades\Excel::toArray(new \stdClass(), $request->file('excel'));

        $error = [];


        foreach ($array[0] as $key => $value) {
            if(filter_var($value[0], FILTER_VALIDATE_EMAIL)) {


                Mail::mailer('sac')
                    ->to($value[0])
                    ->send(new SendBillingError());

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
