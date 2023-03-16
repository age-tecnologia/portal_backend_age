<?php

namespace App\Http\Controllers\AgeNotify\Sac;

use App\Http\Controllers\Controller;
use App\Mail\AgeNotify\Sac\SendAlertPix;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AlertPixController extends Controller
{
    public function sendEmail()
    {

        $array = \Maatwebsite\Excel\Facades\Excel::toArray(new \stdClass(), $request->file('excel'));

        foreach($list as $k => $v) {
            Mail::mailer('notification')
                ->to($v[0])
                ->send(new SendAlertPix());
        }


        return 'ok';
    }
}
