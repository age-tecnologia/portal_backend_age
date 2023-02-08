<?php

namespace App\Http\Controllers\Mail;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TestController extends Controller
{

    public function index(Request $request)
    {

        $request->validate();

        if($request->has('valor1')) {
            return "existe 1";
        }



        return "Oi";

        //return view('mail.portal.management.new_user');
    }
}
