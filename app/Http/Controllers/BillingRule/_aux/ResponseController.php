<?php

namespace App\Http\Controllers\BillingRule\_aux;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ResponseController extends Controller
{
    private array $response = [
      'status' => 0,
      'msg' => '',
      'data' => [],
      'expections' => []
    ];

    public function constructResponse($status, $msg, $data, $exceptions)
    {
        $this->response['status'] = $status;
        $this->response['msg'] = $msg;
        $this->response['data'] = $data;
        $this->response['exceptions'] = $exceptions;

        return $this->response;
    }

    private function response()
    {
        return response()->json($this->response, $this->response['status']);
    }
}
