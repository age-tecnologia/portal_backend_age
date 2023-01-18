<?php

namespace App\Http\Controllers\AgeRv\_aux\sales\analytics;

use App\Http\Controllers\AgeRv\_aux\sales\Cancel;
use App\Http\Controllers\AgeRv\_aux\sales\Commission;
use App\Http\Controllers\AgeRv\_aux\sales\Meta;
use App\Http\Controllers\AgeRv\_aux\sales\MetaPercent;
use App\Http\Controllers\AgeRv\_aux\sales\Sales;
use App\Http\Controllers\AgeRv\_aux\sales\Stars;
use App\Http\Controllers\AgeRv\_aux\sales\ValueStar;
use App\Models\AgeRv\Collaborator;
use App\Models\AgeRv\VoalleSales;
use Carbon\Carbon;

class Seller
{

    private $month;
    private $year;
    private $name;
    private $data;
    private $id;
    private $collabChannelId;
    private $dateAdmission;

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
            ->whereVendedor($this->name)
            ->selectRaw('LOWER(supervisor) as supervisor, LOWER(vendedor) as vendedor,
                                            id_contrato,
                                            status, situacao,
                                            data_contrato, data_ativacao, data_vigencia, data_cancelamento,
                                            plano,
                                            nome_cliente')
            ->get()->unique(['id_contrato']);

        $collab = Collaborator::whereNome($this->name)->first(['tipo_comissao_id', 'data_admissao']);

        $this->collabChannelId = $collab->tipo_comissao_id;
        $this->dateAdmission = $collab->data_admissao;
    }

    public function response()
    {
        $sales = new Sales($this->name, $this->data);
        $cancel = new Cancel($this->data);
        $meta = new Meta($this->id, $this->month, $this->year, $this->dateAdmission);
        $metaPercent = new MetaPercent($sales->getCountValids(), $meta->getMeta());
        $valueStar = new ValueStar($metaPercent->getMetaPercent(), $this->collabChannelId, $this->month, $this->year);
        $stars = new Stars($sales->getExtractValids());
        $commission = new Commission($this->collabChannelId, $valueStar->getValueStar(), $stars->getStars(),
        $cancel->getCountCancel(), $this->month, $this->year);

        $data = [
            'name' => $this->name,
            'sales' => [
                'count' => $sales->getCountValids(),
                'extract' => $sales->getExtractValidsArray()
            ],
            'cancel' => [
                'count' => $cancel->getCountCancel(),
                'extract' => $cancel->getExtractCancelArray()
            ],
            'meta' => $meta->getMeta(),
            'metaPercent' => number_format($metaPercent->getMetaPercent(), 2),
            'valueStar' => $valueStar->getValueStar(),
            'stars' => $stars->getStars(),
            'mediator' => $cancel->getCountCancel() > 0 ? -10 : 10,
            'commission' => number_format($commission->getCommission(), 2, ',', '.'),
            'projection' => $this->projection($metaPercent->getMetaPercent(), $sales->getExtractValids(), $cancel->getCountCancel())
        ];

        return $data;
    }

    public function projection($metaPercent, $salesValids, $cancel)
    {

        $dateActual = Carbon::now()->format('d');
        $daysMonth = Carbon::now()->format('t');
        $dayName = Carbon::now()->format('l');
        $year = Carbon::now()->format('Y');
        $month = Carbon::now()->format('m');
        $dayUtils = $daysMonth;
        $dayUtil = 0;
        $datesUtils = [];

        if($this->month != $month) {
            return "Sem projeção para o mês anterior";
        }


        for ($i = 1; ($daysMonth + 1) > $i; $i++) {
            $date = Carbon::parse("$year-$month-$i")->format('d/m/Y');
            $dayName = Carbon::parse("$year-$month-$i")->format('l');

            if($date != '07/09/2022') {
                if ($dayName !== 'Sunday') {
                    if ($dayName === 'Saturday') {
                        $dayUtil = $dayUtil + 0.5;
                    } else {
                        $dayUtil += 1;
                    }
                }
            }

            $datesUtils[] = [
                $i => [
                    $dayUtil
                ]
            ];
        }


        $dayUtilActual = $datesUtils[$dateActual - 1];


        if (array_key_exists(($dateActual - 2), $datesUtils)) {
            $dayUtilPrevius = $datesUtils[($dateActual - 2)];
        } else {
            return "Projeção indisponível, pois é o primeiro dia do mês!";
        }


        foreach ($dayUtilActual as $item => $value) {
            $dateUtilActual = $value[0];
        }


        if($metaPercent === 0) {
            return 0;
        }


        $metaPercent = $metaPercent / ($dateUtilActual - 1) * $dayUtil;
        $daysMissing = $dayUtil - $dateUtilActual;

        $valueStar = new ValueStar($metaPercent, $this->collabChannelId, $this->month, $this->year);

        $stars = new Stars($salesValids);

        $commission = new Commission($this->collabChannelId, $valueStar->getValueStar(), $stars->getStars(),
            $cancel, $this->month, $this->year);


        $stars = ($stars->getStars() / ($dateUtilActual - 1)) * $dayUtil;


        return [
            'stars' => number_format($stars, 0),
            'sales' => number_format((count($salesValids) / ($dateUtilActual - 1)) * $dayUtil, 0),
            'metaPercent' => number_format($metaPercent, 2),
            'commission' => number_format($commission->getCommission(), 2, ',', '.'),
            'dateActual' => $dateUtilActual,
            'daysMissing' => $daysMissing,
        ];

    }

}
