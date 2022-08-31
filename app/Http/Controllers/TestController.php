<?php

namespace App\Http\Controllers;

use App\Exports\ReportExport;
use App\Exports\UsersExport;
use App\Models\AgeRv\VoalleSales;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use LdapRecord\Auth\BindException;
use LdapRecord\Connection;
use Maatwebsite\Excel\Excel;

class TestController extends Controller
{

    public function index(Request $request)
    {

        $dateActual = Carbon::now()->format('d');
        $daysMonth = Carbon::now()->format('t');
        $dayName = Carbon::now()->format('l');
        $year = Carbon::now()->format('Y');
        $month = Carbon::now()->format('m');
        $dayUtils = $daysMonth;
        $dayUtil = 0;

        $datesUtils = [];

        for($i = 1;  ($daysMonth + 1) > $i; $i++) {
            $date = Carbon::parse("$year-$month-$i")->format('d/m/Y');
            $dayName = Carbon::parse("$year-$month-$i")->format('l');


            if($dayName !== 'Sunday') {
                if($dayName === 'Saturday') {
                    $dayUtil = $dayUtil + 0.5;
                } else {
                    $dayUtil += 1;
                }
            }

            $datesUtils[] = [
                $i => [
                    $dayUtil
                ]
            ];
        }

        $dayUtilActual = $datesUtils[($dateActual - 1)];
        $dayUtilPrevius = $datesUtils[($dateActual - 2)];

        foreach($dayUtilActual[$dateActual] as $item => $value) {
            $int = $value;
        }

        return $int;

    }


}
