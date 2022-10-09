<?php

namespace App\Http\Controllers\AgeRv\_aux\sales;

class MetaPercent
{
    private $salesValid;
    private $meta;
    private $metaPercent;

    public function __construct($sales, $meta)
    {
        $this->salesValid = $sales;
        $this->meta = $meta;

        $this->response();
    }

    private function response() {

        if($this->meta > 0) {
            $this->metaPercent = ($this->salesValid / $this->meta) * 100;
        } else {
            $this->metaPercent = 0;
        }

    }

    public function getMetaPercent()
    {
        return $this->metaPercent;
    }

}
