<?php

namespace App\Http\Controllers\AgeRv\_aux\sales;

use Carbon\Carbon;

class Sales
{
    private $name;
    private $data;
    private $collaboratorData;
    private $collaboratorSalesValid;
    private $countSalesLast7Days = 0;

    public function __construct($name, $data)
    {

        $this->name = mb_convert_case($name, MB_CASE_LOWER, 'UTF-8');
        $this->data = $data;

        $this->response();
    }

    private function response()
    {
        $this->collaboratorData = $this->data->filter(function ($sale) {
            if($sale->vendedor === $this->name || $sale->supervisor === $this->name) {
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
        $calendar = new Calendar();


        $daysName = [];

        foreach($calendar->getLast7Days() as $k => $v) {

            $daysName[] = [
                'dayName' => $v['initial'],
                'date' => $v['date'],
                'sales' => 0
            ];
        }

        foreach($daysName as $k => $v) {

                foreach($this->collaboratorData as $key => $value) {

                    if(Carbon::parse($value->data_contrato) == Carbon::parse($v['date'])) {
                        $daysName[$k]['sales'] = $daysName[$k]['sales'] + 1;
                        $this->countSalesLast7Days += 1;
                    }

                }

        }



        return $daysName;

    }

    public function getCountSalesLast7Days()
    {
        return $this->countSalesLast7Days;
    }

}
