<?php

namespace App\Http\Controllers\AgeRv\_aux\sales\analytics;

use App\Http\Controllers\AgeRv\_aux\sales\Cancel;
use App\Http\Controllers\AgeRv\_aux\sales\Commission;
use App\Http\Controllers\AgeRv\_aux\sales\Meta;
use App\Http\Controllers\AgeRv\_aux\sales\MetaPercent;
use App\Http\Controllers\AgeRv\_aux\sales\Sales;
use App\Http\Controllers\AgeRv\_aux\sales\Stars;
use App\Http\Controllers\AgeRv\_aux\sales\ValueStar;
use App\Models\AgeRv\Channel;
use App\Models\AgeRv\Collaborator;
use App\Models\AgeRv\VoalleSales;

class Supervisor
{
    private $month;
    private $year;
    private $name;
    private $data;
    private $id;

    public function __construct($month, $year, $name, $id)
    {
        $this->month = $month;
        $this->year = $year;
        $this->name = $name;
        $this->id = $id;


        $this->data = VoalleSales::where(function ($query) {
            $query->whereMonth('data_ativacao','>=', $this->month)->whereMonth('data_vigencia', $this->month)->whereYear('data_ativacao', $this->year);
        })
            ->whereStatus('Aprovado')
            ->whereSupervisor($this->name)
            ->selectRaw('LOWER(supervisor) as supervisor, LOWER(vendedor) as vendedor,
                                            id_contrato,
                                            status, situacao,
                                            data_contrato, data_ativacao, data_vigencia, data_cancelamento,
                                            plano,
                                            nome_cliente')
            ->get()->unique(['id_contrato']);

    }

    public function response()
    {

        $sales = new Sales($this->name, $this->data);
        $cancel = new Cancel($this->data);
        $meta = new Meta($this->id, $this->month, $this->year);
        $metaPercent = new MetaPercent($sales->getCountValids(), $meta->getMeta());
        $valueStar = new ValueStar($metaPercent->getMetaPercent(), 3, $this->month, $this->year);
        $stars = new Stars($sales->getExtractValids());
        $commission = new Commission(3, $valueStar->getValueStar(), $stars->getStars(),
                                    $cancel->getCountCancel(), $this->month, $this->year);

        $data[] = [
            'name' => $this->name,
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
            'valueStar' => $valueStar->getValueStar(),
            'stars' => $stars->getStars(),
            'mediator' => 0,
            'commission' => number_format($commission->getCommission(), 2, ',', '.'),
            'sellers' => $this->sellers()
        ];

        return $data;
    }

    private function sellers() {

        $sellers = $this->data->unique(['vendedor']);

        $sellers = $sellers->map(function ($item) {
           return $item->vendedor;
        });

        $data = [];

        $sellers = Collaborator::whereIn('nome', $sellers)->get()->unique(['nome']);

        foreach($sellers as $key => $value) {

            $sales = new Sales($value->nome, $this->data);
            $cancel = new Cancel($sales->getExtractData());
            $meta = new Meta($value->id, $this->month, $this->year);
            $metaPercent = new MetaPercent($sales->getCountValids(), $meta->getMeta());
            $valueStar = new ValueStar($metaPercent->getMetaPercent(), $value->canal_id, $this->month, $this->year);
            $stars = new Stars($sales->getExtractValids());
            $commission = new Commission($value->canal_id, $valueStar->getValueStar(), $stars->getStars(),
            $cancel->getCountCancel(), $this->month, $this->year);

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
                'valueStar' => $valueStar->getValueStar(),
                'stars' => $stars->getStars(),
                'mediator' => 0,
                'commission' => number_format($commission->getCommission(), 2, ',', '.'),
            ];
        }

        return $data;
    }


}
