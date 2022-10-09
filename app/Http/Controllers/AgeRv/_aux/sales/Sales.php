<?php

namespace App\Http\Controllers\AgeRv\_aux\sales;

use Carbon\Carbon;

class Sales
{
    private $name;
    private $data;
    private $collaboratorData;
    private $collaboratorSalesValid;

    public function __construct($name, $data)
    {

        $this->name = $name;
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
            if(Carbon::parse($sale->data_ativacao)->diffInDays($sale->data_cancelamento) >= 7) {
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

}
