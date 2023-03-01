<?php

namespace App\Http\Controllers\AgeRv\_aux\sales\analytics;

use App\Http\Controllers\AgeRv\_aux\sales\Calendar;
use App\Http\Controllers\AgeRv\_aux\sales\Cancel;
use App\Http\Controllers\AgeRv\_aux\sales\CollaboratorFilter;
use App\Http\Controllers\AgeRv\_aux\sales\Commission;
use App\Http\Controllers\AgeRv\_aux\sales\Meta;
use App\Http\Controllers\AgeRv\_aux\sales\MetaPercent;
use App\Http\Controllers\AgeRv\_aux\sales\Sales;
use App\Http\Controllers\AgeRv\_aux\sales\Stars;
use App\Http\Controllers\AgeRv\_aux\sales\ValueStar;
use App\Models\AgeRv\Channel;
use App\Models\AgeRv\Collaborator;
use App\Models\AgeRv\CollaboratorMeta;
use App\Models\AgeRv\VoalleSales;
use Carbon\Carbon;

class Master
{
    private $data;
    private $channels;
    private $collaborators;
    private $collaboratorData;
    private $month;
    private $year;

    public function __construct($month, $year)
    {
        $this->month = $month;
        $this->year = $year;


//        $this->data = VoalleSales::where(function ($query) {
//                                    $query->whereMonth('data_ativacao','>=', ($this->month - 1))->whereMonth('data_vigencia', $this->month)->whereYear('data_ativacao', $this->year);
//                                })
//                                ->whereStatus('Aprovado')
//                                ->selectRaw('LOWER(supervisor) as supervisor, LOWER(vendedor) as vendedor,
//                                            id_contrato,
//                                            status, situacao,
//                                            data_contrato, data_ativacao, data_vigencia, data_cancelamento,
//                                            plano,
//                                            nome_cliente')
//                                ->get()->unique(['id_contrato']);



        $this->data = \App\Models\AgeRv\Commission::where('mes_competencia', $this->month)
                                                    ->where('ano_competencia', $this->year)
                                                    ->whereStatus('Aprovado')
                                                    ->selectRaw('LOWER(supervisor) as supervisor, LOWER(vendedor) as vendedor,
                                                                                    id_contrato,
                                                                                    status, situacao,
                                                                                    data_contrato, data_ativacao, data_vigencia, data_cancelamento,
                                                                                    plano,
                                                                                    nome_cliente')
                                                    ->get()->unique(['id_contrato']);

        $this->channels = Channel::get(['id', 'canal']);
    }

    public function response()
    {
        if($this->data->isNotEmpty()) {

            return [
                'channels' => $this->channelsData(),
            ];

        } else {return response()->json('Nenhum dado retornado.', 406);}
    }

    public function channelsData()
    {

        $data = [];

        foreach($this->channels as $key => $value) {

            $this->collaborators = Collaborator::where('tipo_comissao_id', $value->id)
                                        ->distinct('nome')
                                        ->selectRaw('LOWER(nome) as nome, id, data_admissao, funcao_id')
                                        ->get();

            $data[] = [
                'name' => $value->canal,
                'collaborators' => $this->collaboratorData($value->id)
            ];
        }

        return $data;
    }

    public function collaboratorData($channelId)
    {

        $data = [];

        $filterCollab = new CollaboratorFilter($this->collaborators, $this->data);

        $this->collaborators = $filterCollab->response();


        foreach($this->collaborators as $key => $value) {

            $this->collaboratorData = null;
            $calendar = new Calendar(false, $this->month, $this->year);


            $sales = new Sales($value->nome, $value->funcao_id, $this->data, $calendar);
            $cancel = new Cancel($sales->getExtractData());
            $meta = new Meta($value->id, $this->month, $this->year, $value->data_admissao);
            $metaPercent = new MetaPercent($sales->getCountValids(), $meta->getMeta());
            $valueStar = new ValueStar($metaPercent->getMetaPercent(), $channelId, $this->month, $this->year);
            $stars = new Stars($sales->getExtractValids(), $calendar);
            $commission = new Commission($channelId, $valueStar->getValueStar(), $stars->getStars(), $cancel->getCountCancel(), $this->month, $this->year);

            $data[] = [
                'name' => $value->nome,
                'sales' => [
                    'count' => $sales->getCountValids(),
                    'extract' => $sales->getExtractValidsArray()
                ],
                'cancel' => [
                    'count' => $cancel->getCountCancel(),
                    'extract' => $cancel->getExtractCancel()
                ],
                'meta' => $meta->getMeta(),
                'metaPercent' => number_format($metaPercent->getMetaPercent(), 2),
                'valueStar' => number_format($valueStar->getValueStar(), 2),
                'stars' => $stars->getStars(),
                'mediator' => $channelId !== 3 ? $cancel->getCountCancel() > 0 ? -10 : 10 : 0,
                'commission' => number_format($commission->getCommission(), 2, ',', '.')
            ];
        }



        return $data;

    }


}
