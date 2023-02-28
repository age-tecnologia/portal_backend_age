<?php

namespace App\Http\Controllers\AgeRv\_aux\sales;

use Carbon\Carbon;

class Sales
{
    private $name;
    private $function_id;
    private $data;
    private $collaboratorData;
    private $calendar;
    private $collaboratorSalesValid;
    private $countSalesLast7Days = 0;

    public function __construct($name, $function_id, $data, $calendar)
    {

        $this->name = mb_convert_case($name, MB_CASE_LOWER, 'UTF-8');
        $this->function_id = $function_id;
        $this->data = $data;
        $this->calendar = $calendar;

        $this->response();
    }

    private function response()
    {

        $this->collaboratorData = $this->data->filter(function ($sale) {

            if($this->function_id === 1 && $sale->vendedor === $this->name) {
                return $sale;
            }

            if($this->function_id === 3 && $sale->supervisor === $this->name) {
                return $sale;
            }
        });


        $this->collaboratorSalesValid = $this->collaboratorData->filter(function ($sale) {
            if($sale->situacao === 'Cancelado') {
                if(Carbon::parse($sale->data_ativacao)->diffInDays(Carbon::parse($sale->data_cancelamento)) >= 7) {
                    return $sale;
                }
            } else {
                return $sale;
            }
        });

    }

    public function getCountData()
    {
        return count($this->collaboratorData);
    }

    public function getExtractData()
    {
        return $this->collaboratorData;
    }

    public function getCountValids()
    {
        return count($this->collaboratorSalesValid);
    }

    public function getExtractValids()
    {
        return $this->collaboratorSalesValid;

    }

    public function getExtractValidsArray()
    {

        $array = [];

        foreach($this->collaboratorSalesValid as $key => $value) {
            $array[] = $value;
        }

        return $array;
    }

    public function getSalesLast7Days()
    {

        $daysName = [];

        foreach($this->calendar->getLast7Days() as $k => $v) {

            $daysName[] = [
                'dayName' => $v['initial'],
                'date' => $v['date'],
                'sales' => 0
            ];
        }

        foreach($daysName as $k => $v) {

                foreach($this->collaboratorData as $key => $value) {

                    if(Carbon::parse($value->data_vigencia) == Carbon::parse($v['date'])) {
                        $daysName[$k]['sales'] = $daysName[$k]['sales'] + 1;
                        $this->countSalesLast7Days += 1;
                    }

                }

        }



        return $daysName;

    }

    public function getSalesLast14Days()
    {

        $daysName = [];
        $countSales = 0;

        foreach($this->calendar->getLast14Days() as $k => $v) {

            $daysName[] = [
                'dayName' => $v['initial'],
                'date' => $v['date'],
                'sales' => 0
            ];
        }

        foreach($daysName as $k => $v) {

            foreach($this->collaboratorData as $key => $value) {

                if(Carbon::parse($value->data_vigencia) == Carbon::parse($v['date'])) {
                    $daysName[$k]['sales'] = $daysName[$k]['sales'] + 1;
                    $countSales += 1;
                }

            }

        }



        return $daysName;
    }

    public function getPercentDiffLast7_14Days()
    {
        $last7Days = $this->getSalesLast7Days();
        $countLast7Days = 0;
        $last14Days = $this->getSalesLast14Days();
        $countLast14Days = 0;


        foreach($last7Days as $key => $value) {
           $countLast7Days += $value['sales'];
        }

        foreach($last14Days as $key => $value) {
            $countLast14Days += $value['sales'];
        }

        if($countLast7Days > 0 && $countLast14Days > 0) {
            $diff = number_format(
                ($countLast7Days - $countLast14Days) / $countLast14Days * 100,
                2, '.', '.');
        } else {
            $diff = 0;
        }

        return [
            'lastWeek' => $countLast7Days,
            'beforeWeek' => $countLast14Days,
            'diff' => $diff
        ];

    }


    public function getSalesForWeek()
    {


        $weeks = $this->calendar->getWeeksDays();


        foreach($weeks as $key => $value) {

            foreach($value['days'] as $key2 => $value2) {

                foreach ($this->collaboratorSalesValid as $key3 => $value3) {


                    if(Carbon::parse($value2)->format('Y-m-d') === Carbon::parse($value3->data_vigencia)->format('Y-m-d')) {

                        $weeks[$key]['sales'] += 1;

                    }

                }


            }
        }

        return $weeks;


    }

}
