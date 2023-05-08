<?php

namespace App\Http\Controllers\AgeTools\Tools\Mailer;

use App\Http\Controllers\Controller;
use App\Models\AgeTools\Tools\Mailer\Mailer;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;


class MailersController extends Controller
{

    private Collection $data;

    public function index(Mailer $mailer)
    {
        $level = auth()->user()->nivel_acesso_id;

        if($level === 2 || $level === 3) {
            return response()->json($this->getData(), 200);
        }
    }

    private function getData()
    {
        $this->data = new Collection();


        $mailers = Mailer::all(['id', 'mailer', 'limite_diario']);

        $batch = new BatchController();
        $template = new TemplatesController();

        foreach($mailers as $key => $mailer) {
            $this->data->push([
                'id' => $mailer->id,
                'mailer' => $mailer->mailer,
                'limit_daily' => $mailer->limite_diario,
                'limit_reached' => $batch->getCountEmailReached($mailer->id),
                'info' => $batch->getBatchAndEmailsSendings($mailer->id),
                'templates' => $template->getCountTemplates($mailer->id)
            ]);
        }

        return $this->data;
    }


    public function create()
    {
        //
    }


    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        //
    }


    public function edit($id)
    {
        //
    }


    public function update(Request $request, $id)
    {
        //
    }


    public function destroy($id)
    {
        //
    }
}
