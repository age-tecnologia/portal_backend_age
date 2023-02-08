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

        $today = $this->date->now()->subDays(6);

        return $today;


        for($i = 1; $i >= 6; $i++) {
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

}
