<?php

namespace App\Http\Controllers\BillingRule;

use App\Http\Controllers\BillingRule\_aux\ResponseController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BuilderController extends Controller
{
    private $response;


    public function __construct()
    {
        $this->response = new ResponseController();
    }


    public function build()
    {
        $query = $this->getQuery();



        return $this->response->constructResponse(200, 'sucesso', [], []);
    }

    private function getQuery()
    {
        $query = '';

    }
}
