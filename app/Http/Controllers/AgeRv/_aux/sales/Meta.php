<?php

namespace App\Http\Controllers\AgeRv\_aux\sales;

use App\Models\AgeRv\CollaboratorMeta;

class Meta
{

    private $collaboratorId;
    private $month;
    private $year;
    private $collaboratorMeta;


    public function __construct($id, $month, $year)
    {
        $this->collaboratorId = $id;
        $this->month = $month;
        $this->year = $year;

        $this->response();
    }

    private function response() {

        $this->collaboratorMeta = CollaboratorMeta::whereColaboradorId($this->collaboratorId)->whereMesCompetencia($this->month)->first('meta');

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
        return $this->collaboratorMeta;
    }

}
