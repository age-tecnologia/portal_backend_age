<?php

namespace App\Http\Controllers\AgeRv\_aux\sales;

use Carbon\Carbon;

class Stars
{
    private $salesTotals;

//    public function __construct($salesTotals)
//    {return $this->salesTotals = $salesTotals;}

    public function starsValues($item)
    {
        $stars = 0;

            // Se o mês do cadastro do contrato for MAIO, executa esse bloco.
            if (Carbon::parse($item->data_contrato) < Carbon::parse('2022-06-01') &&
                Carbon::parse($item->data_contrato) <= Carbon::parse('2022-05-01')) {

                // Verifica qual é o plano e atribui a estrela correspondente.
                if (str_contains($item->plano, 'PLANO 120 MEGA PROMOCAO LEVE 360 MEGA')) {
                    $stars = 5;
                } elseif (str_contains($item->plano, 'PLANO 240 MEGA PROMOCAO LEVE 720 MEGA  + DEEZER')) {
                    $stars = 9;
                } elseif (str_contains($item->plano, 'PLANO 240 MEGA PROMOCAO LEVE 720 MEGA  + DEEZER PREMIUM')) {
                    $stars = 9;
                } elseif (str_contains($item->plano, 'PLANO 240 MEGA PROMOCAO LEVE 960 MEGA ')) {
                    $stars = 9;
                } elseif (str_contains($item->plano, 'PLANO 360 MEGA')) {
                    $stars = 11;
                } elseif (str_contains($item->plano, 'PLANO 400 MEGA FIDELIZADO')) {
                    $stars = 15;
                } elseif (str_contains($item->plano, 'PLANO 480 MEGA - FIDELIZADO')) {
                    $stars = 15;
                } elseif (str_contains($item->plano, 'PLANO 720 MEGA ')) {
                    $stars = 25;
                } elseif (str_contains($item->plano, 'PLANO 740 MEGA FIDELIZADO')) {
                    $stars = 25;
                } elseif (str_contains($item->plano, 'PLANO 800 MEGA FIDELIZADO')) {
                    $stars = 17;
                } elseif (str_contains($item->plano, 'PLANO 960 MEGA')) {
                    $stars = 35;
                } elseif (str_contains($item->plano, 'PLANO 960 MEGA TURBINADO + AGE TV + DEEZER PREMIUM')) {
                    $stars = 35;
                } elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 1 GIGA FIDELIZADO')) {
                    $stars = 35;
                } elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 600 MEGA FIDELIZADO')) {
                    $stars = 9;
                } elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 600 MEGA FIDELIZADO + IP FIXO')) {
                    $stars = 12;
                } elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 800 MEGA FIDELIZADO')) {
                    $stars = 17;
                } elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 800 MEGA FIDELIZADO + IP FIXO')) {
                    $stars = 20;
                }

                // Se o mês do cadastro do contrato for JUNHO, executa esse bloco.
            } elseif (Carbon::parse($item->data_contrato) < Carbon::parse('2022-07-01') &&
                Carbon::parse($item->data_contrato) >= Carbon::parse('2022-06-01')) {

                // Verifica qual é o plano e atribui a estrela correspondente.
                if (str_contains($item->plano, 'COMBO EMPRESARIAL 600 MEGA + 1 FIXO BRASIL SEM FIDELIDADE')) {
                    $stars = 10;
                } elseif (str_contains($item->plano, 'COMBO EMPRESARIAL 600 MEGA + 2 FIXOS BRASIL SEM FIDELIDADE')) {
                    $stars = 13;
                } elseif (str_contains($item->plano, 'PLANO 120 MEGA')) {
                    $stars = 7;
                } elseif (str_contains($item->plano, 'PLANO 120 MEGA PROMOCAO LEVE 360 MEGA')) {
                    $stars = 7;
                } elseif (str_contains($item->plano, 'PLANO 240 MEGA PROMOCAO LEVE 720 MEGA  + DEEZER PREMIUM')) {
                    $stars = 9;
                } elseif (str_contains($item->plano, 'PLANO 240 MEGA PROMOCAO LEVE 960 MEGA ')) {
                    $stars = 9;
                } elseif (str_contains($item->plano, 'PLANO 240 MEGA SEM FIDELIDADE')) {
                    $stars = 0;
                } elseif (str_contains($item->plano, 'PLANO 400 MEGA FIDELIZADO')) {
                    $stars = 15;
                } elseif (str_contains($item->plano, 'PLANO 480 MEGA - FIDELIZADO')) {
                    $stars = 15;
                } elseif (str_contains($item->plano, 'PLANO 720 MEGA ')) {
                    $stars = 25;
                } elseif (str_contains($item->plano, 'PLANO 800 MEGA - COLABORADOR')) {
                    $stars = 17;
                } elseif (str_contains($item->plano, 'PLANO 800 MEGA FIDELIZADO')) {
                    $stars = 17;
                } elseif (str_contains($item->plano, 'PLANO 960 MEGA')) {
                    $stars = 35;
                } elseif (str_contains($item->plano, 'PLANO 960 MEGA TURBINADO + AGE TV + DEEZER PREMIUM')) {
                    $stars = 35;
                } elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 1 GIGA FIDELIZADO')) {
                    $stars = 35;
                } elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 600 MEGA FIDELIZADO')) {
                    $stars = 9;
                } elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 800 MEGA FIDELIZADO')) {
                    $stars = 17;
                }

                // Se o mês do cadastro do contrato for JULHO, executa esse bloco.
            } elseif (Carbon::parse($item->data_contrato) < Carbon::parse('2022-08-01') &&
                Carbon::parse($item->data_contrato) >= Carbon::parse('2022-07-01')) {

                // Verifica qual é o plano e atribui a estrela correspondente.
                if (str_contains($item->plano, 'PLANO 1 GIGA FIDELIZADO + DEEZER + HBO MAX + DR. AGE')) {
                    $stars = 30;
                } elseif (str_contains($item->plano, 'PLANO 1 GIGA FIDELIZADO + DEEZER PREMIUM')) {
                    $stars = 15;
                } elseif (str_contains($item->plano, 'PLANO 120 MEGA PROMOCAO LEVE 360 MEGA')) {
                    $stars = 7;
                } elseif (str_contains($item->plano, 'PLANO 120 MEGA SEM FIDELIDADE')) {
                    $stars = 0;
                } elseif (str_contains($item->plano, 'PLANO 240 MEGA ')) {
                    $stars = 9;
                } elseif (str_contains($item->plano, 'PLANO 240 MEGA PROMOCAO LEVE 720 MEGA  + DEEZER PREMIUM')) {
                    $stars = 9;
                } elseif (str_contains($item->plano, 'PLANO 240 MEGA PROMOCAO LEVE 720 MEGA + DEEZER PREMIUM SEM FIDELIDADE')) {
                    $stars = 9;
                } elseif (str_contains($item->plano, 'PLANO 400 MEGA - COLABORADOR')) {
                    $stars = 0;
                } elseif (str_contains($item->plano, 'PLANO 400 MEGA FIDELIZADO')) {
                    $stars = 7;
                } elseif (str_contains($item->plano, 'PLANO 400 MEGA NÃO FIDELIZADO')) {
                    $stars = 0;
                } elseif (str_contains($item->plano, 'PLANO 480 MEGA - FIDELIZADO')) {
                    $stars = 7;
                } elseif (str_contains($item->plano, 'PLANO 480 MEGA FIDELIZADO')) {
                    $stars = 7;
                } elseif (str_contains($item->plano, 'PLANO 740 MEGA FIDELIZADO')) {
                    $stars = 9;
                } elseif (str_contains($item->plano, 'PLANO 740 MEGA FIDELIZADO')) {
                    $stars = 9;
                } elseif (str_contains($item->plano, 'PLANO 800 MEGA - CAMPANHA CONDOMÍNIO FIDELIZADO (AMBOS)')) {
                    $stars = 0;
                } elseif (str_contains($item->plano, 'PLANO 800 MEGA - COLABORADOR')) {
                    $stars = 0;
                } elseif (str_contains($item->plano, 'PLANO 800 MEGA FIDELIZADO')) {
                    $stars = 17;
                } elseif (str_contains($item->plano, 'PLANO 960 MEGA')) {
                    $stars = 35;
                } elseif (str_contains($item->plano, 'PLANO 960 MEGA (LOJAS)')) {
                    $stars = 0;
                } elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 1 GIGA FIDELIZADO')) {
                    $stars = 35;
                } elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 1 GIGA FIDELIZADO')) {
                    $stars = 35;
                } elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 1 GIGA FIDELIZADO + DEEZER PREMIUM')) {
                    $stars = 35;
                } elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 1 GIGA FIDELIZADO + IP FIXO')) {
                    $stars = 38;
                } elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 1 GIGA SEM FIDELIDADE + IP FIXO')) {
                    $stars = 36;
                } elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 600 MEGA FIDELIZADO')) {
                    $stars = 9;
                } elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 600 MEGA FIDELIZADO + IP FIXO')) {
                    $stars = 12;
                } elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 800 MEGA FIDELIZADO')) {
                    $stars = 15;
                }

                // Se o mês do cadastro do contrato for AGOSTO, executa esse bloco.
            } elseif (Carbon::parse($item->data_contrato) < Carbon::parse('2022-10-01') &&
                Carbon::parse($item->data_contrato) >= Carbon::parse('2022-08-01')) {

                // Verifica qual é o plano e atribui a estrela correspondente.
                if (str_contains($item->plano, 'PLANO 1 GIGA FIDELIZADO + DEEZER + HBO MAX + DR. AGE')) {
                    $stars = 30;
                } elseif (str_contains($item->plano, 'PLANO 1 GIGA FIDELIZADO + DEEZER PREMIUM')) {
                    $stars = 15;
                } elseif (str_contains($item->plano, 'PLANO 1 GIGA NÃO FIDELIZADO + DEEZER PREMIUM')) {
                    $stars = 0;
                } elseif (str_contains($item->plano, 'PLANO 120 MEGA PROMOCAO LEVE 360 MEGA')) {
                    $stars = 7;
                } elseif (str_contains($item->plano, 'PLANO 240 MEGA PROMOCAO LEVE 720 MEGA  + DEEZER PREMIUM')) {
                    $stars = 9;
                } elseif (str_contains($item->plano, 'PLANO 240 MEGA PROMOCAO LEVE 720 MEGA  + DEEZER PREMIUM')) {
                    $stars = 9;
                } elseif (str_contains($item->plano, 'PLANO 400 MEGA FIDELIZADO')) {
                    $stars = 7;
                } elseif (str_contains($item->plano, 'PLANO 480 MEGA FIDELIZADO')) {
                    $stars = 7;
                } elseif (str_contains($item->plano, 'PLANO 480 MEGA NÃO FIDELIZADO')) {
                    $stars = 0;
                } elseif (str_contains($item->plano, 'PLANO 740 MEGA FIDELIZADO')) {
                    $stars = 9;
                } elseif (str_contains($item->plano, 'PLANO 800 MEGA FIDELIZADO')) {
                    $stars = 15;
                } elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 1 GIGA FIDELIZADO')) {
                    $stars = 35;
                } elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 1 GIGA FIDELIZADO + DEEZER PREMIUM')) {
                    $stars = 35;
                } elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 600 MEGA FIDELIZADO')) {
                    $stars = 9;
                } elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 600 MEGA FIDELIZADO + IP FIXO')) {
                    $stars = 12;
                } elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 800 MEGA FIDELIZADO')) {
                    $stars = 17;
                }
            }

            return $stars;

    }


}
