<?php

namespace App\Http\Controllers\AgeRv\_aux\sales;

use App\Models\AgeRv\Plan;
use Carbon\Carbon;

class StarsRefatored
{
    private $data;
    private $month;
    private $year;
    private $stars = 0;
    private $plansStarsPeriod;
    private $totalStarsLast7Days = 0;


    public function __construct($data, $calendar, $month = null, $year = null)
    {
        $this->data = $data;
        $this->month = $month;
        $this->year = $year;
        $this->calendar = $calendar;

        $this->response();
    }

    public function response()
    {

        $rules = $this->rules();

        foreach($this->data as $k => $sale) {

            foreach($rules as $month => $plans) {

                if(Carbon::parse($sale->data_contrato)->month() == $month) {
                    dd($month);
                }

            }

        }

    }

    public function getStars()
    {
        return $this->stars;
    }

    public function getPlansAndStars()
    {

        $plans = Plan::where('mes_competencia', $this->month)
                        ->where('ano_competencia', $this->year)
                        ->get(['plano', 'valor_estrela']);


        $this->plansStarsPeriod = collect($this->plansStarsPeriod);

        foreach($this->data as $key => $value) {
            $this->plansStarsPeriod->push([
                                        'plan' => $value->plano,
                                        'count' => 0,
                                        'valueStar' => 0,
                                        'stars' => 0
                                    ]);
        }

        $this->plansStarsPeriod = $this->plansStarsPeriod->unique('plan');

        $data = [];

        foreach($this->plansStarsPeriod as $k => $v) {
            $data[] = $v;
        }


        $this->plansStarsPeriod = $data;


        // Contagem de quantas vezes o plano apareceu
        foreach($this->data as $key => $value) {
            foreach($this->plansStarsPeriod as $k => $v) {
                if($v['plan'] === $value->plano) {
                    $this->plansStarsPeriod[$k]['count'] += 1;
                }

            }
        }


        foreach($this->plansStarsPeriod as $key => $value) {

            foreach($plans as $k => $v) {


                if($value['plan'] === $v['plano']) {
                    $this->plansStarsPeriod[$key]['valueStar'] = $v['valor_estrela'];
                    $this->plansStarsPeriod[$key]['stars'] = $value['count'] * $v['valor_estrela'];
                }

            }

        }

        return $this->plansStarsPeriod;



    }

    public function getStarsLast7Days()
    {


        $stars = $this->convertStars();



        $daysName = [
            'totalStarsLast7Days' => 0,
            'plans' => []
        ];

        foreach($this->calendar->getLast7Days() as $k => $v) {

            $daysName['plans'][] = [
                'dayName' => $v['initial'],
                'date' => $v['date'],
                'stars' => 0
            ];
        }

        foreach($daysName['plans'] as $key => $value) {

            foreach($stars['plans'] as $k => $v) {

                if(Carbon::parse($value['date']) == Carbon::parse($v['dateSale'])) {

                    $daysName['plans'][$key]['stars'] = $daysName['plans'][$key]['stars'] + $v['star'];

                    $daysName['totalStarsLast7Days'] += $v['star'];
                }

            }


        }

       return $daysName;


    }

    public function getStarsLast14Days()
    {


        $stars = $this->convertStars();



        $daysName = [
            'totalStarsLast7Days' => 0,
            'plans' => []
        ];

        foreach($this->calendar->getLast14Days() as $k => $v) {

            $daysName['plans'][] = [
                'dayName' => $v['initial'],
                'date' => $v['date'],
                'stars' => 0
            ];
        }

        foreach($daysName['plans'] as $key => $value) {

            foreach($stars['plans'] as $k => $v) {

                if(Carbon::parse($value['date']) == Carbon::parse($v['dateSale'])) {

                    $daysName['plans'][$key]['stars'] = $daysName['plans'][$key]['stars'] + $v['star'];

                    $daysName['totalStarsLast7Days'] += $v['star'];
                }

            }


        }

        return $daysName;


    }

    protected function convertStars ()
    {
        $result = [
            'plans' => [],
            'starsTotal' => 0,
        ];


        foreach ($this->data as $key => $item) {

             if (Carbon::parse($item->data_contrato) < Carbon::parse('2022-08-01') &&
                Carbon::parse($item->data_contrato) >= Carbon::parse('2022-07-01')) {

                // Verifica qual é o plano e atribui a estrela correspondente.
                if (str_contains($item->plano, 'PLANO 1 GIGA FIDELIZADO + DEEZER + HBO MAX + DR. AGE')) {

                    $result['plans'][] = [
                        'plan' => $item->plano,
                        'star' => 30,
                        'dateSale' => $item->data_vigencia
                    ];
                } elseif (str_contains($item->plano, 'PLANO 1 GIGA FIDELIZADO + DEEZER PREMIUM')) {

                    $result['plans'][] = [
                        'plan' => $item->plano,
                        'star' => 15,
                        'dateSale' => $item->data_vigencia
                    ];
                } elseif (str_contains($item->plano, 'PLANO 120 MEGA PROMOCAO LEVE 360 MEGA')) {
                    $this->stars += 7;

                    $result['plans'][] = [
                        'plan' => $item->plano,
                        'star' => 7,
                        'dateSale' => $item->data_vigencia
                    ];
                } elseif (str_contains($item->plano, 'PLANO 120 MEGA SEM FIDELIDADE')) {

                    $result['plans'][] = [
                        'plan' => $item->plano,
                        'star' => 0,
                        'dateSale' => $item->data_vigencia
                    ];
                } elseif (str_contains($item->plano, 'PLANO 240 MEGA ')) {

                    $result['plans'][] = [
                        'plan' => $item->plano,
                        'star' => 9,
                        'dateSale' => $item->data_vigencia
                    ];
                } elseif (str_contains($item->plano, 'PLANO 240 MEGA PROMOCAO LEVE 720 MEGA  + DEEZER PREMIUM')) {

                    $result['plans'][] = [
                        'plan' => $item->plano,
                        'star' => 9,
                        'dateSale' => $item->data_vigencia
                    ];
                } elseif (str_contains($item->plano, 'PLANO 240 MEGA PROMOCAO LEVE 720 MEGA + DEEZER PREMIUM SEM FIDELIDADE')) {

                    $result['plans'][] = [
                        'plan' => $item->plano,
                        'star' => 9,
                        'dateSale' => $item->data_vigencia
                    ];
                } elseif (str_contains($item->plano, 'PLANO 400 MEGA - COLABORADOR')) {

                    $result['plans'][] = [
                        'plan' => $item->plano,
                        'star' => 0,
                        'dateSale' => $item->data_vigencia
                    ];
                } elseif (str_contains($item->plano, 'PLANO 400 MEGA FIDELIZADO')) {

                    $result['plans'][] = [
                        'plan' => $item->plano,
                        'star' => 7,
                        'dateSale' => $item->data_vigencia
                    ];
                } elseif (str_contains($item->plano, 'PLANO 400 MEGA NÃO FIDELIZADO')) {

                    $result['plans'][] = [
                        'plan' => $item->plano,
                        'star' => 0,
                        'dateSale' => $item->data_vigencia
                    ];
                } elseif (str_contains($item->plano, 'PLANO 480 MEGA - FIDELIZADO')) {

                    $result['plans'][] = [
                        'plan' => $item->plano,
                        'star' => 7,
                        'dateSale' => $item->data_vigencia
                    ];
                } elseif (str_contains($item->plano, 'PLANO 480 MEGA FIDELIZADO')) {

                    $result['plans'][] = [
                        'plan' => $item->plano,
                        'star' => 7,
                        'dateSale' => $item->data_vigencia
                    ];
                } elseif (str_contains($item->plano, 'PLANO 740 MEGA FIDELIZADO')) {

                    $result['plans'][] = [
                        'plan' => $item->plano,
                        'star' => 9,
                        'dateSale' => $item->data_vigencia
                    ];
                } elseif (str_contains($item->plano, 'PLANO 740 MEGA FIDELIZADO')) {

                    $result['plans'][] = [
                        'plan' => $item->plano,
                        'star' => 9,
                        'dateSale' => $item->data_vigencia
                    ];
                } elseif (str_contains($item->plano, 'PLANO 800 MEGA - CAMPANHA CONDOMÍNIO FIDELIZADO (AMBOS)')) {

                    $result['plans'][] = [
                        'plan' => $item->plano,
                        'star' => 0,
                        'dateSale' => $item->data_vigencia
                    ];
                } elseif (str_contains($item->plano, 'PLANO 800 MEGA - COLABORADOR')) {

                    $result['plans'][] = [
                        'plan' => $item->plano,
                        'star' => 0,
                        'dateSale' => $item->data_vigencia
                    ];
                } elseif (str_contains($item->plano, 'PLANO 800 MEGA FIDELIZADO')) {

                    $result['plans'][] = [
                        'plan' => $item->plano,
                        'star' => 17,
                        'dateSale' => $item->data_vigencia
                    ];
                } elseif (str_contains($item->plano, 'PLANO 960 MEGA')) {

                    $result['plans'][] = [
                        'plan' => $item->plano,
                        'star' => 35,
                        'dateSale' => $item->data_vigencia
                    ];
                } elseif (str_contains($item->plano, 'PLANO 960 MEGA (LOJAS)')) {

                    $result['plans'][] = [
                        'plan' => $item->plano,
                        'star' => 0,
                        'dateSale' => $item->data_vigencia
                    ];
                } elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 1 GIGA FIDELIZADO')) {

                    $result['plans'][] = [
                        'plan' => $item->plano,
                        'star' => 35,
                        'dateSale' => $item->data_vigencia
                    ];
                } elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 1 GIGA FIDELIZADO')) {

                    $result['plans'][] = [
                        'plan' => $item->plano,
                        'star' => 35,
                        'dateSale' => $item->data_vigencia
                    ];
                } elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 1 GIGA FIDELIZADO + DEEZER PREMIUM')) {

                    $result['plans'][] = [
                        'plan' => $item->plano,
                        'star' => 35,
                        'dateSale' => $item->data_vigencia
                    ];
                } elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 1 GIGA FIDELIZADO + IP FIXO')) {

                    $result['plans'][] = [
                        'plan' => $item->plano,
                        'star' => 38,
                        'dateSale' => $item->data_vigencia
                    ];
                } elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 1 GIGA SEM FIDELIDADE + IP FIXO')) {

                    $result['plans'][] = [
                        'plan' => $item->plano,
                        'star' => 36,
                        'dateSale' => $item->data_vigencia
                    ];
                } elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 600 MEGA FIDELIZADO')) {

                    $result['plans'][] = [
                        'plan' => $item->plano,
                        'star' => 9,
                        'dateSale' => $item->data_vigencia
                    ];
                } elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 600 MEGA FIDELIZADO + IP FIXO')) {

                    $result['plans'][] = [
                        'plan' => $item->plano,
                        'star' => 12,
                        'dateSale' => $item->data_vigencia
                    ];
                } elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 800 MEGA FIDELIZADO')) {
                    $result['plans'][] = [
                        'plan' => $item->plano,
                        'star' => 15,
                        'dateSale' => $item->data_vigencia
                    ];
                }

                // Se o mês do cadastro do contrato for AGOSTO, executa esse bloco.
            } elseif (Carbon::parse($item->data_contrato) >= Carbon::parse('2022-08-01')) {
                // Verifica qual é o plano e atribui a estrela correspondente.
                if (str_contains($item->plano, 'PLANO 1 GIGA FIDELIZADO + DEEZER + HBO MAX + DR. AGE')) {

                    $result['plans'][] = [
                        'plan' => $item->plano,
                        'star' => 30,
                        'dateSale' => $item->data_vigencia
                    ];

                    $result['starsTotal'] += 30;

                } elseif (str_contains($item->plano, 'PLANO 1 GIGA FIDELIZADO + DEEZER PREMIUM')) {
                    $result['plans'][] = [
                        'plan' => $item->plano,
                        'star' => 15,
                        'dateSale' => $item->data_vigencia
                    ];

                    $result['starsTotal'] += 15;

                } elseif (str_contains($item->plano, 'PLANO 1 GIGA NÃO FIDELIZADO + DEEZER PREMIUM')) {
                    $result['plans'][] = [
                        'plan' => $item->plano,
                        'star' => 0,
                        'dateSale' => $item->data_vigencia
                    ];

                    $result['starsTotal'] += 0;

                } elseif (str_contains($item->plano, 'PLANO 120 MEGA PROMOCAO LEVE 360 MEGA')) {
                    $result['plans'][] = [
                        'plan' => $item->plano,
                        'star' => 7,
                        'dateSale' => $item->data_vigencia
                    ];

                    $result['starsTotal'] += 7;

                } elseif (str_contains($item->plano, 'PLANO 240 MEGA PROMOCAO LEVE 720 MEGA  + DEEZER PREMIUM')) {
                    $result['plans'][] = [
                        'plan' => $item->plano,
                        'star' => 9,
                        'dateSale' => $item->data_vigencia
                    ];

                    $result['starsTotal'] += 9;

                } elseif (str_contains($item->plano, 'PLANO 240 MEGA PROMOCAO LEVE 720 MEGA  + DEEZER PREMIUM')) {
                    $result['plans'][] = [
                        'plan' => $item->plano,
                        'star' => 9,
                        'dateSale' => $item->data_vigencia
                    ];

                    $result['starsTotal'] += 9;

                } elseif (str_contains($item->plano, 'PLANO 400 MEGA FIDELIZADO')) {
                    $result['plans'][] = [
                        'plan' => $item->plano,
                        'star' => 7,
                        'dateSale' => $item->data_vigencia
                    ];

                    $result['starsTotal'] += 7;

                } elseif (str_contains($item->plano, 'PLANO 480 MEGA FIDELIZADO')) {
                    $result['plans'][] = [
                        'plan' => $item->plano,
                        'star' => 7,
                        'dateSale' => $item->data_vigencia
                    ];

                    $result['starsTotal'] += 7;

                } elseif (str_contains($item->plano, 'PLANO 480 MEGA NÃO FIDELIZADO')) {
                    $result['plans'][] = [
                        'plan' => $item->plano,
                        'star' => 0,
                        'dateSale' => $item->data_vigencia
                    ];

                    $result['starsTotal'] += 0;

                } elseif (str_contains($item->plano, 'PLANO 740 MEGA FIDELIZADO')) {
                    $result['plans'][] = [
                        'plan' => $item->plano,
                        'star' => 9,
                        'dateSale' => $item->data_vigencia
                    ];

                    $result['starsTotal'] += 9;

                } elseif (str_contains($item->plano, 'PLANO 800 MEGA FIDELIZADO')) {
                    $result['plans'][] = [
                        'plan' => $item->plano,
                        'star' => 15,
                        'dateSale' => $item->data_vigencia
                    ];

                    $result['starsTotal'] += 15;

                } elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 1 GIGA FIDELIZADO')) {
                    $result['plans'][] = [
                        'plan' => $item->plano,
                        'star' => 35,
                        'dateSale' => $item->data_vigencia
                    ];

                    $result['starsTotal'] += 35;

                } elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 1 GIGA FIDELIZADO + DEEZER PREMIUM')) {
                    $result['plans'][] = [
                        'plan' => $item->plano,
                        'star' => 35,
                        'dateSale' => $item->data_vigencia
                    ];

                    $result['starsTotal'] += 35;

                } elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 600 MEGA FIDELIZADO')) {
                    $result['plans'][] = [
                        'plan' => $item->plano,
                        'star' => 9,
                        'dateSale' => $item->data_vigencia
                    ];

                    $result['starsTotal'] += 9;

                } elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 600 MEGA FIDELIZADO + IP FIXO')) {
                    $result['plans'][] = [
                        'plan' => $item->plano,
                        'star' => 12,
                        'dateSale' => $item->data_vigencia
                    ];

                    $result['starsTotal'] += 12;

                } elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 800 MEGA FIDELIZADO')) {
                    $result['plans'][] = [
                        'plan' => $item->plano,
                        'star' => 17,
                        'dateSale' => $item->data_vigencia
                    ];

                    $result['starsTotal'] += 17;

                } elseif (str_contains($item->plano, 'PLANO 1 GIGA HOTEL LAKE SIDE')) {

                    $result['plans'][] = [
                        'plan' => $item->plano,
                        'star' => 0,
                        'dateSale' => $item->data_vigencia
                    ];

                    $result['starsTotal'] += 0;

                } elseif (str_contains($item->plano, 'PLANO 480 MEGA FIDELIZADO + DIRECTV GO')) {

                    $result['plans'][] = [
                        'plan' => $item->plano,
                        'star' => 17,
                        'dateSale' => $item->data_vigencia
                    ];

                    $result['starsTotal'] += 17;

                } elseif (str_contains($item->plano, 'PLANO 1 GIGA FIDELIZADO + DEEZER + HBO MAX + DR. AGE + DIRECTV GO')) {

                    $result['plans'][] = [
                        'plan' => $item->plano,
                        'star' => 22,
                        'dateSale' => $item->data_vigencia
                    ];

                    $result['starsTotal'] += 22;

                } elseif (str_contains($item->plano, 'PLANO 740 MEGA FIDELIZADO + DIRECTV GO')) {

                    $result['plans'][] = [
                        'plan' => $item->plano,
                        'star' => 18,
                        'dateSale' => $item->data_vigencia
                    ];

                    $result['starsTotal'] += 18;

                } elseif (str_contains($item->plano, 'PLANO 1 GIGA  FIDELIZADO + DEEZER PREMIUM + DIRECTV GO')) {

                    $result['plans'][] = [
                        'plan' => $item->plano,
                        'star' => 20,
                        'dateSale' => $item->data_vigencia
                    ];

                    $result['starsTotal'] += 20;

                } elseif (str_contains($item->plano, 'PLANO 1 GIGA  FIDELIZADO + DIRECTV GO')) {

                    $result['plans'][] = [
                        'plan' => $item->plano,
                        'star' => 20,
                        'dateSale' => $item->data_vigencia
                    ];

                    $result['starsTotal'] += 20;

                } elseif (str_contains($item->plano, 'PLANO COLABORADOR 1 GIGA + DEEZER + HBO MAX + DR. AGE')) {

                    $result['plans'][] = [
                        'plan' => $item->plano,
                        'star' => 0,
                        'dateSale' => $item->data_vigencia
                    ];

                    $result['starsTotal'] += 0;

                } elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 600 MEGA NÃO FIDELIZADO')) {

                    $result['plans'][] = [
                        'plan' => $item->plano,
                        'star' => 0,
                        'dateSale' => $item->data_vigencia
                    ];

                    $result['starsTotal'] += 0;

                } elseif (str_contains($item->plano, 'PLANO COLABORADOR 1 GIGA + DEEZER')) {

                    $result['plans'][] = [
                        'plan' => $item->plano,
                        'star' => 0,
                        'dateSale' => $item->data_vigencia
                    ];

                    $result['starsTotal'] += 0;

                } elseif (str_contains($item->plano, 'PLANO 1 GIGA NÃO FIDELIZADO + DEEZER PREMIUM')) {

                    $result['plans'][] = [
                        'plan' => $item->plano,
                        'star' => 0,
                        'dateSale' => $item->data_vigencia
                    ];

                    $result['starsTotal'] += 0;

                } elseif (str_contains($item->plano, 'PLANO 800 MEGA NÃO FIDELIZADO')) {

                    $result['plans'][] = [
                        'plan' => $item->plano,
                        'star' => 0,
                        'dateSale' => $item->data_vigencia
                    ];

                    $result['starsTotal'] += 0;

                } else {
                    $result['plans'][] = [
                        'plan' => $item->plano,
                        'star' => 0,
                        'dateSale' => $item->data_vigencia,
                        'errors' => [
                            'msg' => 'Plano não encontrado ou sem estrela vinculada.'
                        ]
                    ];
                }
            }
             else {
                 $result['plans'][] = [
                     'plan' => $item->plano,
                     'star' => 0,
                     'dateSale' => $item->data_vigencia,
                     'errors' => [
                         'msg' => 'Plano não está dentro da data filtrada'
                     ],
                 ];
             }

        }


        return $result;

    }

    public function getPercentDiffLast7_14Days()
    {
        $last7Days = $this->getStarsLast7Days();
        $countLast7Days = 0;
        $last14Days = $this->getStarsLast14Days();
        $countLast14Days = 0;


        foreach($last7Days['plans'] as $key => $value) {
            $countLast7Days += $value['stars'];

        }

        foreach($last14Days['plans'] as $key => $value) {
            $countLast14Days += $value['stars'];
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

    public function getStarsForWeek()
    {
        $weeks = $this->calendar->getWeeksDays();
        $stars = $this->convertStars();


        foreach($weeks as $key => $value) {

            foreach($value['days'] as $key2 => $value2) {

                foreach ($stars['plans'] as $key3 => $value3) {


                    if(Carbon::parse($value2)->format('Y-m-d') === Carbon::parse($value3['dateSale'])->format('Y-m-d')) {

                        $weeks[$key]['sales'] += $value3['star'];

                    }

                }


            }
        }

        return $weeks;
    }

    private function rules () : array
    {
            $rules = [
                '2022-05' => [
                    'PLANO 120 MEGA PROMOCAO LEVE 360 MEGA' => 5,
                    'PLANO 240 MEGA PROMOCAO LEVE 720 MEGA  + DEEZER' => 9,
                    'PLANO 240 MEGA PROMOCAO LEVE 720 MEGA  + DEEZER PREMIUM' => 9,
                    'PLANO 240 MEGA PROMOCAO LEVE 960 MEGA' => 9,
                    'PLANO 360 MEGA' => 11,
                    'PLANO 400 MEGA FIDELIZADO' => 15,
                    'PLANO 480 MEGA - FIDELIZADO' => 15,
                    'PLANO 720 MEGA' => 25,
                    'PLANO 740 MEGA FIDELIZADO' => 25,
                    'PLANO 800 MEGA FIDELIZADO' => 17,
                    'PLANO 960 MEGA' => 35,
                    'PLANO 960 MEGA TURBINADO + AGE TV + DEEZER PREMIUM' => 35,
                    'PLANO EMPRESARIAL 1 GIGA FIDELIZADO' => 35,
                    'PLANO EMPRESARIAL 600 MEGA FIDELIZADO' => 9,
                    'PLANO EMPRESARIAL 600 MEGA FIDELIZADO + IP FIXO' => 12,
                    'PLANO EMPRESARIAL 800 MEGA FIDELIZADO' => 17,
                    'PLANO EMPRESARIAL 800 MEGA FIDELIZADO + IP FIXO' => 20,
                ],
                '2022-06' => [
                    'COMBO EMPRESARIAL 600 MEGA + 1 FIXO BRASIL SEM FIDELIDADE' => 10,
                    'COMBO EMPRESARIAL 600 MEGA + 2 FIXOS BRASIL SEM FIDELIDADE' => 13,
                    'PLANO 120 MEGA' => 7,
                    'PLANO 120 MEGA PROMOCAO LEVE 360 MEGA' => 7,
                    'PLANO 240 MEGA PROMOCAO LEVE 720 MEGA  + DEEZER PREMIUM' => 9,
                    'PLANO 240 MEGA PROMOCAO LEVE 960 MEGA' => 9,
                    'PLANO 240 MEGA SEM FIDELIDADE' => 0,
                    'PLANO 400 MEGA FIDELIZADO' => 15,
                    'PLANO 480 MEGA - FIDELIZADO' => 15,
                    'PLANO 720 MEGA' => 25,
                    'PLANO 800 MEGA - COLABORADOR' => 17,
                    'PLANO 800 MEGA FIDELIZADO' => 17,
                    'PLANO 960 MEGA' => 35,
                    'PLANO 960 MEGA TURBINADO + AGE TV + DEEZER PREMIUM' => 35,
                    'PLANO EMPRESARIAL 1 GIGA FIDELIZADO' => 35,
                    'PLANO EMPRESARIAL 600 MEGA FIDELIZADO' => 9,
                    'PLANO EMPRESARIAL 800 MEGA FIDELIZADO' => 17
                ],
                '2022-07' => [
                    'PLANO 1 GIGA FIDELIZADO + DEEZER + HBO MAX + DR. AGE' => 30,
                    'PLANO 1 GIGA FIDELIZADO + DEEZER PREMIUM' => 15,
                    'PLANO 120 MEGA PROMOCAO LEVE 360 MEGA' => 7,
                    'PLANO 120 MEGA SEM FIDELIDADE' => 0,
                    'PLANO 240 MEGA ' => 9,
                    'PLANO 240 MEGA PROMOCAO LEVE 720 MEGA  + DEEZER PREMIUM' => 9,
                    'PLANO 240 MEGA PROMOCAO LEVE 720 MEGA + DEEZER PREMIUM SEM FIDELIDADE' => 9,
                    'PLANO 400 MEGA - COLABORADOR' => 0,
                    'PLANO 400 MEGA FIDELIZADO' => 7,
                    'PLANO 400 MEGA NÃO FIDELIZADO' => 0,
                    'PLANO 480 MEGA - FIDELIZADO' => 7,
                    'PLANO 480 MEGA FIDELIZADO' => 7,
                    'PLANO 740 MEGA FIDELIZADO' => 9,
                    'PLANO 740 MEGA FIDELIZADO' => 9,
                    'PLANO 800 MEGA - CAMPANHA CONDOMÍNIO FIDELIZADO (AMBOS)' => 0,
                    'PLANO 800 MEGA - COLABORADOR' => 0,
                    'PLANO 800 MEGA FIDELIZADO' => 17,
                    'PLANO 960 MEGA' => 35,
                    'PLANO 960 MEGA (LOJAS)' => 0,
                    'PLANO EMPRESARIAL 1 GIGA FIDELIZADO' => 35,
                    'PLANO EMPRESARIAL 1 GIGA FIDELIZADO' => 35,
                    'PLANO EMPRESARIAL 1 GIGA FIDELIZADO + DEEZER PREMIUM' => 35,
                    'PLANO EMPRESARIAL 1 GIGA FIDELIZADO + IP FIXO' => 38,
                    'PLANO EMPRESARIAL 1 GIGA SEM FIDELIDADE + IP FIXO' => 36,
                    'PLANO EMPRESARIAL 600 MEGA FIDELIZADO' => 9,
                    'PLANO EMPRESARIAL 600 MEGA FIDELIZADO + IP FIXO' => 12,
                    'PLANO EMPRESARIAL 800 MEGA FIDELIZADO' => 15
                ],
                '2022-08' => [
                    'PLANO 1 GIGA FIDELIZADO + DEEZER + HBO MAX + DR. AGE' => 30,
                    'PLANO 1 GIGA FIDELIZADO + DEEZER PREMIUM' => 15,
                    'PLANO 1 GIGA NÃO FIDELIZADO + DEEZER PREMIUM' => 0,
                    'PLANO 120 MEGA PROMOCAO LEVE 360 MEGA' => 7,
                    'PLANO 240 MEGA PROMOCAO LEVE 720 MEGA  + DEEZER PREMIUM' => 9,
                    'PLANO 240 MEGA PROMOCAO LEVE 720 MEGA  + DEEZER PREMIUM' => 9,
                    'PLANO 400 MEGA FIDELIZADO' => 7,
                    'PLANO 480 MEGA FIDELIZADO' => 7,
                    'PLANO 480 MEGA NÃO FIDELIZADO' => 0,
                    'PLANO 740 MEGA FIDELIZADO' => 9,
                    'PLANO 800 MEGA FIDELIZADO' => 15,
                    'PLANO EMPRESARIAL 1 GIGA FIDELIZADO' => 35,
                    'PLANO EMPRESARIAL 1 GIGA FIDELIZADO + DEEZER PREMIUM' => 35,
                    'PLANO EMPRESARIAL 600 MEGA FIDELIZADO' => 9,
                    'PLANO EMPRESARIAL 600 MEGA FIDELIZADO + IP FIXO' => 12,
                    'PLANO EMPRESARIAL 800 MEGA FIDELIZADO' => 17,
                    'PLANO 1 GIGA HOTEL LAKE SIDE' => 0,
                    'PLANO 480 MEGA FIDELIZADO + DIRECTV GO' => 17,
                    'PLANO 1 GIGA FIDELIZADO + DEEZER + HBO MAX + DR. AGE + DIRECTV GO' => 22,
                    'PLANO 740 MEGA FIDELIZADO + DIRECTV GO' => 18,
                    'PLANO 1 GIGA  FIDELIZADO + DEEZER PREMIUM + DIRECTV GO' => 20,
                    'PLANO 1 GIGA  FIDELIZADO + DIRECTV GO' => 20,
                    'PLANO COLABORADOR 1 GIGA + DEEZER + HBO MAX + DR. AGE' => 0,
                    'PLANO EMPRESARIAL 600 MEGA NÃO FIDELIZADO' => 0,
                    'PLANO COLABORADOR 1 GIGA + DEEZER' => 0,
                    'PLANO 1 GIGA NÃO FIDELIZADO + DEEZER PREMIUM' => 0,
                    'PLANO 800 MEGA NÃO FIDELIZADO' => 0
                ]
            ];

        return $rules;
    }



}
