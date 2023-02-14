<?php

// Verifica qual é o plano e atribui a estrela correspondente.
if (str_contains($item->plano, 'PLANO 1 GIGA FIDELIZADO + DEEZER + HBO MAX + DR. AGE')) {
    $this->stars += 30;
} elseif (str_contains($item->plano, 'PLANO 1 GIGA FIDELIZADO + DEEZER PREMIUM')) {
    $this->stars += 15;
} elseif (str_contains($item->plano, 'PLANO 1 GIGA NÃO FIDELIZADO + DEEZER PREMIUM')) {
    $this->stars += 0;
} elseif (str_contains($item->plano, 'PLANO 120 MEGA PROMOCAO LEVE 360 MEGA')) {
    $this->stars += 7;
} elseif (str_contains($item->plano, 'PLANO 240 MEGA PROMOCAO LEVE 720 MEGA  + DEEZER PREMIUM')) {
    $this->stars += 9;
} elseif (str_contains($item->plano, 'PLANO 240 MEGA PROMOCAO LEVE 720 MEGA  + DEEZER PREMIUM')) {
    $this->stars += 9;
} elseif (str_contains($item->plano, 'PLANO 400 MEGA FIDELIZADO')) {
    $this->stars += 7;
} elseif (str_contains($item->plano, 'PLANO 480 MEGA FIDELIZADO')) {
    $this->stars += 7;
} elseif (str_contains($item->plano, 'PLANO 480 MEGA NÃO FIDELIZADO')) {
    $this->stars += 0;
} elseif (str_contains($item->plano, 'PLANO 740 MEGA FIDELIZADO')) {
    $this->stars += 9;
} elseif (str_contains($item->plano, 'PLANO 800 MEGA FIDELIZADO')) {
    $this->stars += 15;
} elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 1 GIGA FIDELIZADO')) {
    $this->stars += 35;
} elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 1 GIGA FIDELIZADO + DEEZER PREMIUM')) {
    $this->stars += 35;
} elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 600 MEGA FIDELIZADO')) {
    $this->stars += 9;
} elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 600 MEGA FIDELIZADO + IP FIXO')) {
    $this->stars += 12;
} elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 800 MEGA FIDELIZADO')) {
    $this->stars += 17;
} elseif (str_contains($item->plano, 'PLANO 1 GIGA HOTEL LAKE SIDE')) {
    $this->stars += 0;
} elseif (str_contains($item->plano, 'PLANO 480 MEGA FIDELIZADO + DIRECTV GO')) {
    $this->stars += 0;
} elseif (str_contains($item->plano, 'PLANO 1 GIGA FIDELIZADO + DEEZER + HBO MAX + DR. AGE + DIRECTV GO')) {
    $this->stars += 0;
} elseif (str_contains($item->plano, 'PLANO 740 MEGA FIDELIZADO + DIRECTV GO')) {
    $this->stars += 0;
} elseif (str_contains($item->plano, 'PLANO 1 GIGA  FIDELIZADO + DEEZER PREMIUM + DIRECTV GO')) {
    $this->stars += 0;
} elseif (str_contains($item->plano, 'PLANO 1 GIGA  FIDELIZADO + DIRECTV GO')) {
    $this->stars += 0;
} elseif (str_contains($item->plano, 'PLANO COLABORADOR 1 GIGA + DEEZER + HBO MAX + DR. AGE')) {
    $this->stars += 0;
} elseif (str_contains($item->plano, 'PLANO EMPRESARIAL 600 MEGA NÃO FIDELIZADO')) {
    $this->stars += 0;
} elseif (str_contains($item->plano, 'PLANO COLABORADOR 1 GIGA + DEEZER')) {
    $this->stars += 0;
} elseif (str_contains($item->plano, 'PLANO 1 GIGA NÃO FIDELIZADO + DEEZER PREMIUM')) {
    $this->stars += 0;
} elseif (str_contains($item->plano, 'PLANO 800 MEGA NÃO FIDELIZADO')) {
    $this->stars += 0;
}
