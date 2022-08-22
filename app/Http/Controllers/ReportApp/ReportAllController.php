<?php

namespace App\Http\Controllers\ReportApp;

use App\Exports\ReportExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportAllController extends Controller
{
    public function list_connections()
    {
        $query = 'select p.name as "Usuário Cliente",
                   p.tx_id as "Documento",
                   a.user as "S/N",
                   sp.title as "Serviço Address List",
                   ac.title as "Ponto de Acesso Equipamento"
                    from erp.authentication_contracts a
                    Left join erp.authentication_access_points ac on ac.id = a.authentication_access_point_id
                    Left join erp.service_products sp on sp.id = a.service_product_id
                    Left join erp.contracts c on c.id = a.contract_id
                    Left join erp.people p on p.id = c.client_id limit 10';

        $result = DB::connection('pgsql')->select($query);
        $headers = [
            'Nome',
            'Documento',
            'S/N',
            'Serviço Address List',
            'Ponto de Acesso Equipamento'
        ];


        return \Maatwebsite\Excel\Facades\Excel::download(new ReportExport($result, $headers), 'lista_conexoes.xlsx');

    }
}
