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
    private $metaPercent;


    public function __construct($channelId, $valueStar, $stars, $cancel, $month, $year, $metaPercent = null)
    {
        $this->channelId = $channelId;
        $this->valueStar = $valueStar;
        $this->cancel = $cancel;
        $this->stars = $stars;
        $this->month = $month;
        $this->year = $year;
        $this->metaPercent = $metaPercent;

        $this->response();
    }

    private function response() {

        if($this->channelId === 3 && $this->month >= '04' && $this->year >= '2023') {
            $target = 3000;

            $trackValue = 0;

            $tracks = [
                  0 => [
                      'min' => 0,
                      'max' => 0,
                      'value' => 0
                  ],
                1 => [
                    'min' => 71,
                    'max' => 80,
                    'value' => 0.3
                ],
                2 => [
                    'min' => 81,
                    'max' => 90,
                    'value' => 0.35
                ],
                3 => [
                    'min' => 91,
                    'max' => 99,
                    'value' => 0.4
                ],
                4 => [
                    'min' => 100,
                    'max' => 105,
                    'value' => 0.5
                ],
                5 => [
                    'min' => 106,
                    'max' => 110,
                    'value' => 0.6
                ],
                6 => [
                    'min' => 111,
                    'max' => 120,
                    'value' => 0.7
                ],
//                7 => [
//                    'min' => 121,
//                    'max' => 2000,
//                    'value' => 1
//                ],
            ];

            foreach($tracks as $key => $value) {
                if($this->metaPercent >= $value['min'] && $this->metaPercent <= $value['max']) {
                    $trackValue = $value['value'];
                }
            }

            return $this->commission = $target * $trackValue;
        }

        $this->commission = $this->valueStar * $this->stars;

        if($this->cancel > 0 && $this->channelId !== 3 && $this->channelId !== 6) {
            $this->commission = $this->commission * 0.9;
        } elseif($this->cancel === 0 && $this->channelId !== 3 && $this->channelId !== 6) {
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
