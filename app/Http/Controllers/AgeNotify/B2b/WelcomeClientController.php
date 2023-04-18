<?php

namespace App\Http\Controllers\AgeNotify\B2b;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WelcomeClientController extends Controller
{

    public function index()
    {
        return view('mail.ageNotify.b2b.welcome_client')
                ->with(['client' => 'Multimidia Samsung', 'contract' => '245481', 'vigence' => '10/12/2026',
                        'lorem' => 'Primeira, gostariámos de lhe dar as boas-vindas pela']);
    }


}
