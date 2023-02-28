<?php

namespace App\Http\Controllers\AgeRv\_aux\sales\analytics;

use App\Http\Controllers\AgeRv\_aux\sales\Calendar;
use App\Http\Controllers\AgeRv\_aux\sales\Cancel;
use App\Http\Controllers\AgeRv\_aux\sales\Commission;
use App\Http\Controllers\AgeRv\_aux\sales\Meta;
use App\Http\Controllers\AgeRv\_aux\sales\MetaPercent;
use App\Http\Controllers\AgeRv\_aux\sales\Sales;
use App\Http\Controllers\AgeRv\_aux\sales\Stars;
use App\Http\Controllers\AgeRv\_aux\sales\ValueStar;
use App\Http\Controllers\Controller;
use App\Models\AgeRv\Collaborator;
use App\Models\AgeRv\VoalleSales;
use Illuminate\Http\Request;

class NewSeller extends Controller
{
    public function __construct($month, $year, $name, $id, $dashboard)
    {
        $this->month = $month;
        $this->year = $year;
        $this->name = $name;
        $this->id = $id;
        $this->dashboard = $dashboard;


        if(! $this->dashboard) {
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
        } else {

            $this->data = \App\Models\AgeRv\Commission::whereMesCompetencia($this->month)
                                                    ->whereAnoCompetencia($this->year)
                                                    ->whereVendedor($this->name)
                                                    ->whereStatus('Aprovado')
                                                    ->get();
        }

        $collab = Collaborator::whereNome($this->name)->first(['tipo_comissao_id', 'data_admissao', 'funcao_id']);

        $this->collabChannelId = $collab->tipo_comissao_id;
        $this->functionId = $collab->funcao_id;
        $this->dateAdmission = $collab->data_admissao;
    }

    public function response()
    {

        $calendar = new Calendar($this->dashboard, $this->month, $this->year);

        $sales = new Sales($this->name, $this->functionId, $this->data, $calendar);
        $cancel = new Cancel($this->data);
        $meta = new Meta($this->id, $this->month, $this->year, $this->dateAdmission);
        $metaPercent = new MetaPercent($sales->getCountValids(), $meta->getMeta());
        $valueStar = new ValueStar($metaPercent->getMetaPercent(), $this->collabChannelId, $this->month, $this->year);
        $stars = new Stars($sales->getExtractValids(), $calendar);
        $commission = new Commission($this->collabChannelId, $valueStar->getValueStar(), $stars->getStars(),
                                        $cancel->getCountCancel(), $this->month, $this->year);

        $data = [
            'name' => $this->name,
            'sales' => [
                'count' => $sales->getCountValids(),
                'salesLast7Days' => $this->dashboard ? '' : $sales->getSalesLast7Days(),
                'salesInfoLast14Days' => $this->dashboard ? '' : $sales->getPercentDiffLast7_14Days(),
                'salesForWeek' => $sales->getSalesForWeek(),
                'extract' => $sales->getExtractValidsArray()
            ],
            'cancel' => [
                'count' => $cancel->getCountCancel(),
                'extract' => $cancel->getExtractCancelArray()
            ],
            'meta' => $meta->getMeta(),
            'metaPercent' => number_format($metaPercent->getMetaPercent(), 2),
            'valueStar' => [
                'value' => number_format($valueStar->getValueStar(), 2, ',', '.'),
                'tracks' => $valueStar->getTracks($this->collabChannelId)
            ],
            'stars' => [
                'totalStars' => $stars->getStars(),
                'starsForWeek' => $stars->getStarsForWeek(),
                'starsLast7Days' => $this->dashboard ? '' : $stars->getStarsLast7Days(),
                'starsInfoLast14Days' => $this->dashboard ? '' : $stars->getPercentDiffLast7_14Days()
            ],
            //'extractStars' => $stars->getPlansAndStars(),
            'mediator' => $cancel->getCountCancel() > 0 ? -10 : 10,
            'commission' => [
                'liquid' => number_format($commission->getCommission(), 2, ',', '.'),
                'brute' => number_format($commission->getCommissionGross(), 2, ',', '.'),
                'diff' => number_format($commission->getCommissionDiff(), 2, ',', '.'),
            ],
        ];

        return $data;
    }
}
