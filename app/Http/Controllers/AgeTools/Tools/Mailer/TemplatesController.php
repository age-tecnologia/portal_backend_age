<?php

namespace App\Http\Controllers\AgeTools\Tools\Mailer;

use App\Http\Controllers\Controller;
use App\Models\AgeTools\Tools\Mailer\Batch;
use App\Models\AgeTools\Tools\Mailer\Template;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class TemplatesController extends Controller
{

    public function index(Request $request)
    {
        $templates = Template::whereMailerId($request->mailerId)->get(['id', 'nome', 'template', 'formulario']);

        $data = new Collection();
        $batch = new BatchController();

        foreach ($templates as $key => $template) {
            $data->push([
                'id' => $template->id,
               'name' => $template->nome,
               'template' => $template->template,
               'form' => $template->formulario,
               'info' =>  $batch->getBatchAndEmailsSendingsTemplate($template->id)
            ]);
        }

        return $data;
    }

    public function getCountTemplates($mailerId)
    {
        $templates = Template::whereMailerId($mailerId)->count();


        return $templates;
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
