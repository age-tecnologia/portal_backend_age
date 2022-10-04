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
            ->get(['r.nome', 'r.nome_arquivo', 'r.url', 'r.isPeriodo']);

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

        set_time_limit(2000);
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
                where EXTRACT (MONTH FROM dfd.created) = \'07\'';

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

        $query = 'select
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

    public function takeBlip(Request $request)
    {

        set_time_limit(1000);
        ini_set('memory_limit', '2048M');

        if ($request->input('firstPeriod') !== null && $request->input('lastPeriod') === null) {
            $query = 'select * from relatorio_personalizado_Age_Telecon_1
                        where data_de_atendimento >= \'' . $request->input('firstPeriod') . '\'';
        } elseif ($request->input('firstPeriod') === null && $request->input('lastPeriod') !== null) {
            $query = 'select * from relatorio_personalizado_Age_Telecon_1
                        where data_de_atendimento <= \'' . $request->input('lastPeriod') . '\'';
        } elseif ($request->input('firstPeriod') !== null && $request->input('lastPeriod') !== null) {
            $query = 'select * from relatorio_personalizado_Age_Telecon_1
                        where data_de_atendimento >= \'' . $request->input('firstPeriod') . '\' and data_de_atendimento <= \'' . $request->input('lastPeriod') . '\'';
        } else {
            $query = 'select * from relatorio_personalizado_Age_Telecon_1';
        }


        $result = DB::connection('mysql_take')->select($query);


        $headers = [
            'id_ticket_humano',
            'id_atendimento',
            'data_de_entrada_no_bot',
            'data_de_atendimento',
            'data_inicial_do_atendimento_humano',
            'Fila_Destino',
            'Tempo_de_Espera_Fila',
            'Tempo_de_Primeira_resposta',
            'Tempo_de_Conversacao',
            'Tempo_Total',
            'Telefone',
            'Tags',
            'Status',
            'bot',
            'Numero_de_contrato_ou_CPF',
            'Protocolo',
            'transferido_Y_N',
            'Canal'
        ];

        return \Maatwebsite\Excel\Facades\Excel::download(new ReportExport($result, $headers), 'dici.xlsx');


    }

    public function base_clients()
    {
        set_time_limit(2000);
        ini_set('memory_limit', '2048M');

        $query = 'select
                    ac.contract_id as "Contrato",
                    p.name as "Nome cliente",
                    sp.title as "Plano",
                    ac.neighborhood as "Região",
                    c.amount as "Valor do plano",
                    c.beginning_date as "vigência",
                    c.v_stage as "Estágio",
                    c.v_status as "Status",
                    ac."user" as "Conexao"
                    from erp.authentication_contracts ac
                    left join erp.contracts c on c.id = ac.contract_id
                    left join erp.people p on p.id = c.client_id
                    left join erp.service_products sp on sp.id = ac.service_product_id
                    where ac.user != \'\'';

        $result = DB::connection('pgsql')->select($query);

        $headers = [
            'Contrato',
            'Nome cliente',
            'Plano',
            'Região',
            'Valor do plano',
            'Vigência',
            'Estágio',
            'Status',
            'Conexão'
        ];

        return \Maatwebsite\Excel\Facades\Excel::download(new ReportExport($result, $headers), 'clientes_base.xlsx');


    }

    public function sales()
    {

        set_time_limit(1000);
        ini_set('memory_limit', '2048M');


        $query = 'select
                    c.id  as "Contrato",
                    p.name as "Nome cliente",
                    sp.title as "Plano",
                    ac.neighborhood as "Região",
                    c.amount as "Valor do plano",
                    c.date as "Data venda",
                    c.beginning_date as "vigência/instalação",
                    c.v_stage as "Estágio",
                    c.v_status as "Status",
                    ac."user" as "Conexão"
                    from erp.contracts c
                    left join erp.authentication_contracts ac on ac.contract_id = c.id
                    left join erp.people p on p.id = c.client_id
                    left join erp.service_products sp on sp.id = ac.service_product_id
                    where c.v_stage = \'Aprovado\'';

        $result = DB::connection('pgsql')->select($query);

        $headers = [
            'Contrato',
            'Nome cliente',
            'Plano',
            'Região',
            'Valor do plano',
            'Data venda',
            'vigência/instalação',
            'Estágio',
            'Status',
            'Conexão'
        ];

        return \Maatwebsite\Excel\Facades\Excel::download(new ReportExport($result, $headers), 'vendas.xlsx');

    }

    public function contracts_assigments()
    {

        set_time_limit(1000);
        ini_set('memory_limit', '2048M');

        $query = 'select
                 c2.id as "Contrato",
                 c2.v_status as "Status_contrato",
                 c2.v_stage as "Situação",
                 p.name as "Cliente",      -- es.person-id
                 es.description as "Descrição",
                CASE
                    WHEN es.status = 3 THEN \'Assinado\'
                    WHEN es.status = 2 THEN \'Aguardando Assinatura\'
                    WHEN es.status = 1 THEN \'Em construção\'
                    WHEN es.status = 5 THEN \'Cancelado\'
                    WHEN es.status = 6 THEN \'Expirado\'
                    WHEN es.status = 7 THEN \'Concluído com Pendência\'
                 END as "Status",
                 es.created as "Data criação",
                 p2.name as "Enviado por",  -- es.created
                 es.deleted as "Deletado"
                from erp.electronic_signatures es
                left join erp.contracts c on c.id = es.contract_id
                left join erp.people p on p.id = es.person_id
                left join erp.people p2 on p2.id = es.created_by
                left join erp.contracts c2 on c2.client_id = p.id';

        $result = DB::connection('pgsql')->select($query);

        $headers = [
            'Contrato',
            'Status Contrato',
            'Situação',
            'Cliente',
            'Descrição',
            'Status',
            'Data criação',
            'Enviado por',
            'Deletado'
        ];

        return \Maatwebsite\Excel\Facades\Excel::download(new ReportExport($result, $headers), 'contratos_assinados.xlsx');

    }

    // TakeBlip
    public function totals_calls(Request $request)
    {

        if ($request->input('firstPeriod') !== null && $request->input('lastPeriod') === null) {
            $query = 'select e.EXTRAS_VALUE as "Canal",
                    count(*) as "Quantidade"
                    from Eventos e
                    where EXTRAS_key = \'canal\' group by EXTRAS_VALUE ORDER BY "Quantidade" desc
                    and e.DATA_HORA >= \''.$request->input('firstPeriod').'\'';

        } elseif ($request->input('firstPeriod') === null && $request->input('lastPeriod') !== null) {

            $query = 'select e.EXTRAS_VALUE as "Canal",
                    count(*) as "Quantidade"
                    from Eventos e
                    where EXTRAS_key = \'canal\' group by EXTRAS_VALUE ORDER BY "Quantidade" desc
                    and e.DATA_HORA <= \''.$request->input('lastPeriod').'\'';

        } elseif ($request->input('firstPeriod') !== null && $request->input('lastPeriod') !== null) {

            $query = 'select e.EXTRAS_VALUE as "Canal",
                    count(*) as "Quantidade"
                    from Eventos e
                    where EXTRAS_key = \'canal\'
                    and e.DATA_HORA BETWEEN \''.$request->input('firstPeriod').'\' and \''.$request->input('lastPeriod').'\'
                    group by EXTRAS_VALUE ORDER BY "Quantidade" desc';

        } else {
            $query = 'select e.EXTRAS_VALUE as "Canal",
                    count(*) as "Quantidade"
                    from Eventos e
                    where EXTRAS_key = \'canal\' group by EXTRAS_VALUE ORDER BY "Quantidade" desc';
        }

        $result = DB::connection('mysql_take')->select($query);

        $headers = [
            'Canal',
            'Quantidade'
        ];

        return \Maatwebsite\Excel\Facades\Excel::download(new ReportExport($result, $headers), 'totals_calls.xlsx');

    }

    public function contratcs_so_open()
    {
        set_time_limit(2000);
        ini_set('memory_limit', '2048M');

        $query = 'select
                    c.contract_id as "Contrato",
                    c.assignment_id as "Protocolo",
                    c.activation_date as "Data ativação",
                    c.created as "Data criação",
                    p."name"  as "Colaborador",
                    c2.v_stage as "Estágio",
                    c2.v_status as "status"
                    from erp.contract_assignment_activations c
                    left join erp.people p on p.id = c.person_id
                    left join erp.contracts c2 on c2.id = c.contract_id
                    where c2.invoice_type = 1';

        $result = DB::connection('pgsql')->select($query);

        $headers = [
            'Contrato',
            'Protocolo',
            'Data ativação',
            'Data criação',
            'Colaborador',
            'Estágio',
            'Status'
        ];

        return \Maatwebsite\Excel\Facades\Excel::download(new ReportExport($result, $headers), 'contracts_so_opens.xlsx');


    }

    public function teams_voalle()
    {
        $query = 'select
                vu.name as " name",
                t.title as "tilte",
                vu.active as "Ativo",
                vu.deleted as "Delete"
                from v_users vu
                left join teams t on t.id = vu.team_id
                where t.title notnull';

        $result = DB::connection('pgsql')->select($query);

        $headers = [
            'Nome',
            'Equipe',
            'Ativo',
            'Deletado'
        ];

        return \Maatwebsite\Excel\Facades\Excel::download(new ReportExport($result, $headers), 'teams.xlsx');

    }

}
