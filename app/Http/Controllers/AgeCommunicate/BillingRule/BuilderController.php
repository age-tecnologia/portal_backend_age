<?php

namespace App\Http\Controllers\AgeCommunicate\BillingRule;

use App\Http\Controllers\AgeCommunicate\BillingRule\_aux\Response;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BuilderController extends Controller
{
    private $response;


    public function __construct()
    {
        $this->response = new Response();
    }


    public function build()
    {
        $query = $this->getQuery();



        return $this->response->constructResponse(200, 'sucesso', [], []);
    }

    private function getQuery()
    {
        $query = '';

        return $query;

    }
}
