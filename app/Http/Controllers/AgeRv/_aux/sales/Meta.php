<?php

namespace App\Http\Controllers\AgeRv\_aux\sales;

use App\Models\AgeRv\CollaboratorMeta;
use Carbon\Carbon;

class Meta
{

    private $collaboratorId;
    private $month;
    private $year;
    private $collaboratorMeta;
    private $dateAdmission;
    private $dateCompetence;


    public function __construct($id, $month, $year, $dateAdmission)
    {
        $this->collaboratorId = $id;
        $this->month = $month;
        $this->year = $year;
        $this->dateAdmission = Carbon::parse($dateAdmission);

        $this->response();
    }

    private function response() {



        $this->collaboratorMeta = CollaboratorMeta::whereColaboradorId($this->collaboratorId)->whereMesCompetencia($this->month)->first(['meta']);




        if(isset($this->collaboratorMeta->meta)) {



            $this->collaboratorMeta = $this->collaboratorMeta->meta;

            return $this->collaboratorMeta;

        } else {

            $this->collaboratorMeta = 0;

            return 'Colaborador sem meta definida.';
        }
    }

    public function getMeta()
    {

//        if($this->dateAdmission) {
//
//            $this->dateCompetence = Carbon::parse("$this->year-$this->month-01");
//
//            $diffMonth = $this->dateCompetence->format('m') - $this->dateAdmission->format('m');
//
//
//            if($diffMonth === 0) {
//                return "Meta * dias úteis trabalhados";
//            } elseif ($diffMonth === 1) {
//                return "50%";
//            } elseif ($diffMonth === 2) {
//                return "75%";
//            } else {
//                return '100%';
//
//                return $this->collaboratorMeta;
//
//            }
//
//        }
        return $this->collaboratorMeta;

    }

}
