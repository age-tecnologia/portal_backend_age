<?php

namespace App\Http\Controllers\AgeRv\_aux\sales;

class ValueStar
{
    private $metaPercent;
    private $channelId;
    private $month;
    private $year;
    private $minMeta;
    private $valueStar = 0;

    public function __construct($metaPercent, $channelId, $month, $year)
    {
        $this->metaPercent = $metaPercent;
        $this->channelId = $channelId;
        $this->month = $month;
        $this->year = $year;

        $this->response();
    }

    private function response() {

        if ($this->channelId === 1) {

            // Verifica o mês e aplica a diferença na meta mínima
            if ($this->month <= '07' && $this->year === '2022') {
                $this->minMeta = 70;
            } elseif (($this->month === '08') && $this->year === '2022') {
                $this->minMeta = 60;
            } elseif ($this->month >= '09' && $this->year === '2022') {
                $this->minMeta = 70;
            } elseif ($this->month >= '01' && $this->year === '2023') {
                $this->minMeta = 70;
            }

            // Regra e valores vigentes até o momento -- 09/10/2022
            if ($this->metaPercent >= $this->minMeta && $this->metaPercent < 100) {
                $this->valueStar = 0.90;
            } elseif ($this->metaPercent >= 100 && $this->metaPercent < 120) {
                $this->valueStar = 1.20;
            } elseif ($this->metaPercent >= 120 && $this->metaPercent < 141) {
                $this->valueStar = 2;
            } elseif ($this->metaPercent >= 141) {
                $this->valueStar = 4.5;
            }

        } elseif ($this->channelId === 2) {

            if ($this->month <= '07' && $this->year === '2022') {

                if ($this->metaPercent >= 60 && $this->metaPercent < 100) {
                    $this->valueStar = 1.3;
                } elseif ($this->metaPercent >= 100 && $this->metaPercent < 120) {
                    $this->valueStar = 3;
                } elseif ($this->metaPercent >= 120 && $this->metaPercent < 141) {
                    $this->valueStar = 5;
                } elseif ($this->metaPercent >= 141) {
                    $this->valueStar = 7;
                }
            } elseif (($this->month === '08') && $this->year === '2022') {

                if ($this->metaPercent >= 60 && $this->metaPercent < 100) {
                    $this->valueStar = 2.50;
                } elseif ($this->metaPercent >= 100 && $this->metaPercent < 120) {
                    $this->valueStar = 5;
                } elseif ($this->metaPercent >= 120 && $this->metaPercent < 141) {
                    $this->valueStar = 7;
                } elseif ($this->metaPercent >= 141) {
                    $this->valueStar = 10;
                }
            } elseif (($this->month >= '09') && $this->year === '2022') {

                if ($this->metaPercent >= 70 && $this->metaPercent < 100) {
                    $this->valueStar = 2.50;
                } elseif ($this->metaPercent >= 100 && $this->metaPercent < 120) {
                    $this->valueStar = 5;
                } elseif ($this->metaPercent >= 120 && $this->metaPercent < 141) {
                    $this->valueStar = 7;
                } elseif ($this->metaPercent >= 141) {
                    $this->valueStar = 8;
                }
            } elseif (($this->month >= '01') && $this->year === '2023') {

                if ($this->metaPercent >= 70 && $this->metaPercent < 100) {
                    $this->valueStar = 2.50;
                } elseif ($this->metaPercent >= 100 && $this->metaPercent < 120) {
                    $this->valueStar = 5;
                } elseif ($this->metaPercent >= 120 && $this->metaPercent < 141) {
                    $this->valueStar = 7;
                } elseif ($this->metaPercent >= 141) {
                    $this->valueStar = 8;
                }
            }


        } elseif ($this->channelId === 3) {

            if ($this->month <= '07' && $this->year === '2022') {
                if ($this->metaPercent >= 60 && $this->metaPercent < 100) {
                    $this->valueStar = 0.25;
                } elseif ($this->metaPercent >= 100 && $this->metaPercent < 120) {
                    $this->valueStar = 0.40;
                } elseif ($this->metaPercent >= 120 && $this->metaPercent < 141) {
                    $this->valueStar = 0.80;
                } elseif ($this->metaPercent >= 141) {
                    $this->valueStar = 1.30;
                }
            } elseif (($this->month === '08') && $this->year === '2022') {

                if ($this->metaPercent >= 60 && $this->metaPercent < 100) {
                    $this->valueStar = 0.6;
                } elseif ($this->metaPercent >= 100 && $this->metaPercent < 120) {
                    $this->valueStar = 0.9;
                } elseif ($this->metaPercent >= 120 && $this->metaPercent < 141) {
                    $this->valueStar = 1.5;
                } elseif ($this->metaPercent >= 141) {
                    $this->valueStar = 3;
                }

            } elseif ($this->month >= '09' && $this->year === '2022') {

//                if ($this->metaPercent >= 70 && $this->metaPercent < 80) {
//                    $this->valueStar = .5;
//                } elseif ($this->metaPercent >= 80 && $this->metaPercent < 90) {
//                    $this->valueStar = .6;
//                } elseif ($this->metaPercent >= 90 && $this->metaPercent < 100) {
//                    $this->valueStar = .8;
//                } elseif ($this->metaPercent >= 100) {
//                    $this->valueStar = ($this->metaPercent / 100);
//                }

                if ($this->metaPercent >= 70 && $this->metaPercent < 100) {
                    $this->valueStar = 0.6;
                } elseif ($this->metaPercent >= 100 && $this->metaPercent < 120) {
                    $this->valueStar = 0.9;
                } elseif ($this->metaPercent >= 120 && $this->metaPercent < 141) {
                    $this->valueStar = 1.5;
                } elseif ($this->metaPercent >= 141) {
                    $this->valueStar = 3;
                }

            } elseif ($this->month >= '09' && $this->year === '2022') {

                if ($this->metaPercent >= 70 && $this->metaPercent < 100) {
                    $this->valueStar = 0.6;
                } elseif ($this->metaPercent >= 100 && $this->metaPercent < 120) {
                    $this->valueStar = 0.9;
                } elseif ($this->metaPercent >= 120 && $this->metaPercent < 141) {
                    $this->valueStar = 1.5;
                } elseif ($this->metaPercent >= 141) {
                    $this->valueStar = 3;
                }

            } elseif ($this->month >= '01' && $this->year === '2023') {

                if ($this->metaPercent >= 70 && $this->metaPercent < 100) {
                    $this->valueStar = 0.6;
                } elseif ($this->metaPercent >= 100 && $this->metaPercent < 120) {
                    $this->valueStar = 0.9;
                } elseif ($this->metaPercent >= 120 && $this->metaPercent < 141) {
                    $this->valueStar = 1.5;
                } elseif ($this->metaPercent >= 141) {
                    $this->valueStar = 3;
                }

            }
        }

    }

    public function getValueStar()
    {
        return $this->valueStar;
    }

    public function getTracks($channelId)
    {
        if($channelId === 1) {

            $tracks = [
                0 => [
                    'initial' => 0,
                    'final' => 69,
                    'value' => number_format(0, 2, ',', '.')
                ],
                1 => [
                    'initial' => 70,
                    'final' => 99,
                    'value' => number_format(.9, 2, ',', '.')
                ],
                2 => [
                    'initial' => 100,
                    'final' => 119,
                    'value' => number_format(1.2, 2, ',', '.')
                ],
                3 => [
                    'initial' => 120,
                    'final' => 140,
                    'value' => number_format(2, 2, ',', '.')
                ],
                4 => [
                    'initial' => 141,
                    'final' => null,
                    'value' => number_format(4.5, 2, ',', '.')
                ],
            ];

        }
        if($channelId === 3) {

            $tracks = [
                0 => [
                    'initial' => 0,
                    'final' => 69,
                    'value' => number_format(0, 2, ',', '.')
                ],
                1 => [
                    'initial' => 70,
                    'final' => 99,
                    'value' => number_format(.6, 2, ',', '.')
                ],
                2 => [
                    'initial' => 100,
                    'final' => 119,
                    'value' => number_format(.9, 2, ',', '.')
                ],
                3 => [
                    'initial' => 120,
                    'final' => 140,
                    'value' => number_format(1.5, 2, ',', '.')
                ],
                4 => [
                    'initial' => 141,
                    'final' => null,
                    'value' => number_format(3, 2, ',', '.')
                ],
            ];

        }
        if($channelId === 2) {

            $tracks = [
                0 => [
                    'initial' => 0,
                    'final' => 69,
                    'value' => number_format(0, 2, ',', '.')
                ],
                1 => [
                    'initial' => 70,
                    'final' => 99,
                    'value' => number_format(2.5, 2, ',', '.')
                ],
                2 => [
                    'initial' => 100,
                    'final' => 119,
                    'value' => number_format(5, 2, ',', '.')
                ],
                3 => [
                    'initial' => 120,
                    'final' => 140,
                    'value' => number_format(7, 2, ',', '.')
                ],
                4 => [
                    'initial' => 141,
                    'final' => null,
                    'value' => number_format(8, 2, ',', '.')
                ],
            ];

        }

        return $tracks;

    }

}
