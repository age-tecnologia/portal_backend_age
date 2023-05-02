<?php

namespace App\Http\Controllers\AgeRv\_aux\sales\analytics;

use App\Http\Controllers\Controller;
use App\Models\AgeRv\Channel;
use App\Models\AgeRv\Collaborator;
use App\Models\AgeRv\Commission;
use App\Models\AgeRv\CommissionConsolidated;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class Coordenator extends Controller
{

    private string $month;
    private string $year;
    private int $userId;
    private Collection $supervisors;
    private Collection $sales;
    private Collection $salesFiltereds;
    private Collection $data;
    private Collection $channels;
    private string $competence;


    public function __construct($month, $year)
    {

        $this->month = $month;
        $this->year = $year;
        $this->userId = auth()->user()->id;
        $this->data = new Collection();
        $this->competence = "$this->year-$this->month-01";
        $this->competence = Carbon::parse($this->competence);






    }

    public function response()
    {
        return $this->getAllData();



    }


    private function getAllData()
    {
        $this->channels = Channel::all(['id', 'canal']);

        try {
            $this->supervisors = Collaborator::whereGestorId($this->userId)->get(['id', 'tipo_comissao_id', 'nome']);


            if($this->supervisors->isEmpty()) {
                throw new \Exception('Nenhum supervisor vínculado ao coordenador.', 400);
            }


        } catch (\Exception $e) {

            return response()->json(['msg' => $e->getMessage()], $e->getCode());

        }

        foreach($this->channels as $key => $channel) {
            $this->data->push([
              'channel' => $channel->canal,
              'supervisors' => $this->linkChannelAndSupervisors($channel->id)
            ]);
        }



        return $this->data;
    }

    private function linkChannelAndSupervisors($channelId)
    {


        $supervisors = $this->supervisors->where('tipo_comissao_id', $channelId)->values();

        $result = new Collection();

        foreach($supervisors as $key => $supervisor) {
            $result->push([
                'supervisorData' => $this->getDataSupervisors($supervisor->id),
                'sellersData' => $this->linkSupervisorsAndSellers($supervisor->nome)
            ]);
        }

        return $result;

    }


    private function linkSupervisorsAndSellers($supervisorName)
    {
        $sellers = Commission::leftJoin('agerv_colaboradores as c','c.nome', '=', 'cv.vendedor')
                                ->from('agerv_comissao_vendas as cv')
                                ->whereSupervisor($supervisorName)
                                ->whereMesCompetencia($this->month)
                                ->whereAnoCompetencia($this->year)
                                ->select('c.id', 'c.nome')
                                ->distinct('vendedor')
                                ->get();

        $result = new Collection();


        foreach($sellers as $k => $seller) {
            $result->push([
                'data' => $this->getDataSellers($seller->id)
            ]);
        }

        return $result;





    }

    private function getDataSupervisors($supervisorId)
    {

        $commissionConsolidated = CommissionConsolidated::whereColaboradorId($supervisorId)
            ->whereCompetencia($this->competence)
            ->get();


        return $commissionConsolidated;



    }

    private function getDataSellers($sellerId)
    {

        $commissionConsolidated = CommissionConsolidated::whereColaboradorId($sellerId)
                                                        ->whereCompetencia($this->competence)
                                                        ->get();


        return $commissionConsolidated;



    }





}
