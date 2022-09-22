<?php

namespace App\Http\Controllers\ReportApp;

use App\Exports\ReportExport;
use App\Http\Controllers\Controller;
use App\Models\AgeReport\ReportPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Excel;

class ReportAllController extends Controller
{

    public function getAll()
    {
        $reports = DB::table('agereport_relatorios as r')
                    ->leftJoin('agereport_relatorios_permissoes as rp', 'r.id', 'rp.relatorio_id')
                    ->where('rp.user_id', auth()->user()->id)
                    ->get(['r.nome', 'r.nome_arquivo', 'r.url']);

        return $reports;
    }

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
                    Left join erp.people p on p.id = c.client_id';

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

    public function dici()
    {

        set_time_limit(1000);
        ini_set('memory_limit', '2048M');

        $query = 'select
                dfd.client_id as "ID do cliente",
                dfd.contract_id as "ID do contrato",
                (select c.contract_number  as "Nº do contrato" from erp.contracts c where c.id = dfd.contract_id ),
                (select title as "Título" from erp.service_products sp where sp.id = dfd.service_product_id),
                dfd.created as "Criado em" ,
                dfd.created_by as "ID do criador",
                dfd.dici_file_id as "ID do dici file",
                dfd.modified as "Modificado",
                (select dfh.competence as "Competência" from erp.dici_file_headers dfh where dfh.id = df.dici_file_header_id),
                dfd.modified_by as "Modificado por",
                dfd.service_product_id as "ID do serviço_produto"
                from erp.dici_file_details dfd
                left join erp.dici_files df on dfd.dici_file_id  = df.id
                where EXTRACT (MONTH FROM dfd.created) = \'07\' limit 10';

        $result = DB::connection('pgsql')->select($query);

        $headers = [
            'ID do cliente',
            'ID do contrato',
            'Nº do contrato',
            'Título',
            'Criado em',
            'ID do criador',
            'ID do dici file',
            'Modificado',
            'Competência',
            'Modificado por',
            'ID do serviço_produto',
        ];

        return \Maatwebsite\Excel\Facades\Excel::download(new ReportExport($result, $headers), 'dici.xlsx');

    }

    public function default()
    {

    }

    public function condominiums()
    {
        $query =    'select
                    aap.title as "condomínio",
                    count(*) as "Clientes"
                    from erp.authentication_contracts ac
                    left join erp.authentication_access_points aap on aap.id  = ac.condominium_id
                    group by aap.title';

        $result = DB::connection('pgsql')->select($query);

        $headers = [
          'Condomínio',
          'Clientes'
        ];

        return \Maatwebsite\Excel\Facades\Excel::download(new ReportExport($result, $headers), 'condominios.xlsx');


    }

    public function technical_control()
    {
        set_time_limit(1000);
        ini_set('memory_limit', '2048M');

        $query = 'select
                    a.title as "Protocolo des",
                    it.title as "Protocolo tipo",
                    p1.name as "Técnico",
                    is2.title as "Status",
                    a.beginning_date as "data abertura",
                    p2.name as "Cliente",
                    p2.neighborhood  as "região"
                    from erp.assignments a
                    left join erp.assignment_incidents ai on ai.assignment_id = a.id
                    left join erp.incident_types it on it.id = ai.incident_type_id
                    left join erp.incident_status is2 on is2.id = ai.incident_status_id
                    left join erp.solicitation_problems sp on sp.id = ai.solicitation_problem_id
                    left join erp.solicitation_classifications sc on sc.id = ai.solicitation_classification_id
                    left join erp.people p2 on p2.id = a.requestor_id
                    left join erp.people p1 on p1.id = a.responsible_id
                    where it.id in (1030, 1058, 1020, 1011, 1067, 1061)';

        $result = DB::connection('pgsql')->select($query);

        $headers = [
            'Protocolo_des',
            'Protocolo_tipo',
            'Técnico',
            'Status',
            'Data_abertura',
            'Cliente',
            'Região'
        ];

        return \Maatwebsite\Excel\Facades\Excel::download(new ReportExport($result, $headers), 'controle_tecnico.xlsx');

    }
}
