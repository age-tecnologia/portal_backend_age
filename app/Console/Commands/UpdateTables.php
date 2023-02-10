<?php

namespace App\Console\Commands;

use App\Http\Controllers\AgeRv\VoalleSalesController;
use App\Http\Controllers\DataWarehouse\Voalle\AuthenticationContractsController;
use App\Http\Controllers\DataWarehouse\Voalle\ContractAssignmentActivationsController;
use App\Http\Controllers\DataWarehouse\Voalle\ContractsController;
use App\Http\Controllers\DataWarehouse\Voalle\ContractsTypeController;
use App\Http\Controllers\DataWarehouse\Voalle\PeopleAddressController;
use App\Http\Controllers\DataWarehouse\Voalle\PeoplesController;
use App\Http\Controllers\DataWarehouse\Voalle\ServiceProductsController;
use App\Models\DataWarehouse\Voalle\ContractAssignmentActivations;
use App\Models\DataWarehouse\Voalle\Contracts;
use Illuminate\Console\Command;

class UpdateTables extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:tables';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Atualiza todas as tabelas que é necessário atualizações diárias';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {


        $authContracts = new AuthenticationContractsController();
        $authContracts->__invoke();

        $contracts = new ContractsController();
        $contracts->__invoke();

        $contractsType = new ContractsTypeController();
        $contractsType->__invoke();

        $peoples = new PeoplesController();
        $peoples->__invoke();

        $serviceProducts = new ServiceProductsController();
        $serviceProducts->__invoke();

        $voalleSales = new VoalleSalesController();
        $voalleSales->__invoke();

        $contract_assignment_activations = new ContractAssignmentActivationsController();
        $contract_assignment_activations->__invoke();

        $people_address = new PeopleAddressController();
        $people_address->__invoke();

    }
}
