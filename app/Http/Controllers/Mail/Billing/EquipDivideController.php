<?php

namespace App\Http\Controllers\Mail\Billing;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class EquipDivideController extends Controller
{
    public function index()
    {
        return view('mail');
    }

    public function createPDF()
    {

        $pdf = Pdf::loadView('mail.pdf.billing_equip_divide');

        return $pdf->download();

    }
}
