<?php

namespace App\Http\Controllers\AgeRv\_aux\sales;

use Carbon\Carbon;

class Cancel
{

    private $data;
    private $cancel;

    public function __construct($data)
    {
        $this->data = $data;

        $this->response();
    }

    private function response()
    {
        $this->cancel = $this->data->filter(function ($sale) {
            if(Carbon::parse($sale->data_ativacao)->diffInDays($sale->data_cancelamento) < 7) {
                return $sale;
            }
        });
    }

    public function getExtractCancel()
    {
        return $this->cancel;
    }

    public function getCountCancel()
    {
        return count($this->cancel);
    }

}
