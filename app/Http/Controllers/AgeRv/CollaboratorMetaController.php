<?php

namespace App\Http\Controllers\AgeRv;

use App\Http\Controllers\Controller;
use App\Models\AgeRv\Collaborator;
use App\Models\AgeRv\CollaboratorMeta;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Mockery\Exception;
use PhpOffice\PhpSpreadsheet\Writer\Ods\Meta;

class CollaboratorMetaController extends Controller
{

    public function index()
    {
        //
    }


    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        try {

            $meta = CollaboratorMeta::where('colaborador_id', $request->input('id'))
                ->where('mes_competencia', Carbon::now()->format('m'))
                ->first();

            if(! isset($meta->meta)) {

                $meta = new CollaboratorMeta();

                $meta->create([
                    'colaborador_id' => $request->input('id'),
                    'mes_competencia' => Carbon::now()->format('m'),
                    'meta' => $request->input('meta'),
                    'modified_by' => auth()->user()->id
                ]);

            } else {throw new Exception('O colaborador já possui meta vinculada.', 403);}

        } catch (Exception $e) {return response()->json([$e->getMessage()], $e->getCode());}


    }


    public function show($id)
    {
        try {

            $meta = CollaboratorMeta::where('colaborador_id', $id)
                ->orderBy('mes_competencia', 'desc')
                ->get(['id', 'mes_competencia', 'meta', 'modified_by']);

            if($meta->isNotEmpty()) {
                return $meta;
            } else { throw new Exception('Nenhuma meta cadastrada para este colaborador.', 404);}

        } catch (Exception $e) {return response()->json([$e->getMessage()], $e->getCode());}
    }


    public function edit($id)
    {

    }


    public function update(Request $request, $id)
    {
        try {

            $meta = CollaboratorMeta::where('colaborador_id', $id)
                ->where('mes_competencia', Carbon::now()->format('m'))
                ->first();

            if(! isset($meta->meta)) {
                throw new Exception('Nenhuma meta atribuída ao mês vigente.', 403);
            } else {
                $meta->update([
                    'meta' => $request->input('meta')
                ]);
            }

        } catch (Exception $e) {return response()->json([$e->getMessage()], $e->getCode());}
    }


    public function destroy($id)
    {
        //
    }

    public function metaAddMass(Request $request)
    {
        $channel = $request->input('channel');
        $meta = $request->input('meta');
        $month = $request->input('month');
        $year = $request->input('year');
        $collab = Collaborator::whereTipoComissaoId($channel)->get('id');

        foreach($collab as $key => $value) {
            $query = CollaboratorMeta::firstOrCreate(
                ['colaborador_id' => $value->id, 'mes_competencia' => $month],
                ['colaborador_id' => $value->id, 'mes_competencia' => $month, 'meta' => $meta, 'modified_by' => auth()->user()->id]
            );
        }

        return response()->json('Meta do time adicionada com sucesso!', 201);

    }
}
