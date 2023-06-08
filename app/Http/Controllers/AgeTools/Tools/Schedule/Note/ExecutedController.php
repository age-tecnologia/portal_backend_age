<?php

namespace App\Http\Controllers\AgeTools\Tools\Schedule\Note;

use App\Http\Controllers\Controller;
use App\Models\AgeTools\Tools\Schedule\Note\Executed;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ExecutedController extends Controller
{

    public function store(Request $request)
    {
        if($request->has('payload')) {

            $executeNote = new Executed();

            $executeNote = $executeNote->create([
                'executada_por' => auth()->user()->id,
                'protocolo' => $request->payload['protocol'],
                'data_inicio_atendimento' => $request->payload['date_start_attendance'],
                'data_fim_atendimento' => $request->payload['date_end_attendance'],
                'data_inicio_agendamento' => $request->payload['date_start_schedule'],
                'data_fim_agendamento' => $request->payload['date_end_schedule'],
            ]);

            if(isset($executeNote->id)) {
                return response()->json('sucess', 200);
            } else {
                return response()->json('error', 400);

            }

        }
    }

}
