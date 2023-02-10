<?php

namespace App\Http\Controllers\AgeRv\_aux\sales;

use Carbon\Carbon;

class Calendar
{
    private $daysMonth;
    private $month;
    private $year;
    private $date;

    public function __construct()
    {
        date_default_timezone_set('America/Sao_Paulo');
        setlocale(LC_ALL, 'pt_BR.utf-8', 'ptb', 'pt_BR', 'portuguese-brazil', 'portuguese-brazilian', 'bra', 'brazil', 'br');
        setlocale(LC_TIME, 'pt_BR.utf-8', 'ptb', 'pt_BR', 'portuguese-brazil', 'portuguese-brazilian', 'bra', 'brazil', 'br');

        $this->date = new Carbon();
        $this->daysMonth = $this->date->now()->format('t');
        $this->month = $this->date->now()->format('m');
        $this->year = $this->date->now()->format('Y');


    }

    public function response()
    {
        $calendar = [];


        for($i = 1; $i <= $this->daysMonth; $i++) {
            $dateFormatted = Carbon::parse("$this->year-$this->month-$i");
            $dayName = Carbon::parse("$this->year-$this->month-$i")->format('l');

            $calendar[] = [
                'date' => $dateFormatted->format('Y-m-d'),
                'name' => $dayName,
                'initial' => $dayName[0],
                'week' => $dateFormatted->weekOfMonth
            ];
        }

        return $calendar;
    }

    public function getLast7Days()
    {
        $calendar = [];

        $newDate = $this->date->now()->subDays(7);

        for($i = 1; $i <= 7; $i++){
            $newDate = $newDate->addDays(1);
            $dayName = $newDate->formatLocalized('%A');


            $calendar[] = [
                'date' => $newDate->format('Y-m-d'),
                'dateFormatted' => $newDate->format('d/m/Y'),
                'initial' => mb_convert_case($dayName[0], MB_CASE_UPPER, 'utf8'),
                'week' => $newDate->weekOfMonth
            ];
        }

        return $calendar;


    }

}
