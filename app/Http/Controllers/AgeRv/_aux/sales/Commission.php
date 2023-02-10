<?php

namespace App\Http\Controllers\AgeRv\_aux\sales;

class Commission
{
    private $channelId;
    private $valueStar;
    private $stars;
    private $month;
    private $year;
    private $commission;
    private $cancel;


    public function __construct($channelId, $valueStar, $stars, $cancel, $month, $year)
    {
        $this->channelId = $channelId;
        $this->valueStar = $valueStar;
        $this->cancel = $cancel;
        $this->stars = $stars;
        $this->month = $month;
        $this->year = $year;

        $this->response();
    }

    private function response() {
//
//        if($this->channelId === 3 && $this->month >= '09') {
//            $target = 3000;
//
//            return $this->commission = $target * $this->valueStar;
//        }

        $this->commission = $this->valueStar * $this->stars;

        if($this->cancel > 0 && $this->channelId !== 3) {
            $this->commission = $this->commission * 0.9;
        } elseif($this->cancel === 0 && $this->channelId !== 3) {
            $this->commission = $this->commission * 1.1;
        }

    }

    public function getCommission()
    {
        return $this->commission;
    }

    public function getCommissionGross()
    {
        return $this->valueStar * $this->stars;
    }

    public function getCommissionDiff()
    {

        return $this->commission - ($this->valueStar * $this->stars);
    }

}
