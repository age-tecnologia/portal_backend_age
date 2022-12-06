<?php

namespace App\Http\Controllers\AgeRv\_aux\sales;

class CollaboratorFilter
{

    private $collaborators;
    private $data;

    public function __construct($collab, $data)
    {
        $this->collaborators = $collab;
        $this->data = $data;
    }

    private function extractCollab()
    {
        $sellers = $this->data->unique(function($item) {
           return $item->vendedor;
        });

        $sellers = $sellers->map(function($item) {
           return $item->vendedor;
        });

        $supervisors = $this->data->unique(function($item) {
            return $item->supervisor;
        });

        $supervisors = $supervisors->map(function($item) {
            return $item->supervisor;
        });

        $data = [];

        $data = $sellers->merge($supervisors);

        return $data;
    }

    public function response()
    {
        $result = $this->extractCollab();

        $filtered = $this->collaborators->filter(function ($item) use($result) {

            foreach($result as $key => $value) {

                if($value === $item->nome) {
                    return $item->nome;
                }

            }

        });

        return $filtered;
    }


}
