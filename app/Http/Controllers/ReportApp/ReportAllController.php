<?php

namespace App\Http\Controllers\ReportApp;

use App\Exports\ReportExport;
use App\Http\Controllers\Controller;
use App\Models\AgeReport\Report;
use App\Models\AgeReport\ReportPermission;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Excel;

class ReportAllController extends Controller
{

    public function getAll()
    {
        $level = auth()->user()->nivel_acesso_id;

        if($level === 2 || $level === 3) {
            $reports = Report::all(['nome', 'nome_arquivo', 'url', 'isPeriodo', 'id']);

            return $reports;
        }

        $reports = DB::table('agereport_relatorios as r')
            ->leftJoin('agereport_relatorios_permissoes as rp', 'r.id', 'rp.relatorio_id')
            ->where('rp.user_id', auth()->user()->id)
            ->get(['r.nome', 'r.nome_arquivo', 'r.url', 'r.isPeriodo', 'r.id']);

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
            'Agente',
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

        return \Maatwebsite\Excel\Facades\Excel::download(new ReportExport($result, $headers), 'takeblip.xlsx');


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

        $firstPeriod = Carbon::parse($request->input('firstPeriod'))->format('Y-m-d H');
        $lastPeriod = Carbon::parse($request->input('lastPeriod'))->format('Y-m-d H');

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

        }  elseif ($request->input('firstPeriod') !== null && $request->input('lastPeriod') !== null) {

            $query = 'select e.EXTRAS_VALUE as "Canal",
                    count(*) as "Quantidade"
                    from Eventos e
                    where EXTRAS_key = \'canal\'
                    and e.DATA_HORA BETWEEN \''.$firstPeriod.'\' and \''.$lastPeriod.'\'
                    group by EXTRAS_VALUE ORDER BY "Quantidade" desc';

        }  else {
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
                from erp.v_users vu
                left join erp.teams t on t.id = vu.team_id
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

    public function contracts_address()
    {
        set_time_limit(2000);
        ini_set('memory_limit', '2048M');


        $query = 'select
                    c.id as  "contracts",
                    p.name as "Nome",
                    pa.neighborhood as "Região",
                    pa.street as "Endereço",
                    pa.number as "Número",
                    pa.address_complement as "Complemento",
                    c.v_status as "Status",
                    c.v_stage as "Estágio"
                    from erp.contracts c
                    left join erp.people p on p.id = c.client_id
                    left join erp.people_addresses pa on pa.id = c.people_address_id';

        $result = DB::connection('pgsql')->select($query);

        $headers = [
          'Contratos',
          'Nome',
          'Região',
          'Endereço',
          'Número',
          'Complemento',
          'Status',
          'Estágio'
        ];

        return \Maatwebsite\Excel\Facades\Excel::download(new ReportExport($result, $headers), 'contracts_address.xlsx');


    }

    public function human_care()
    {
        set_time_limit(2000);
        ini_set('memory_limit', '2048M');


        $query = 'select `relatorio_personalizado_Age_Telecon_1`.`id_ticket_humano`
                    AS `id_ticket_humano`,`relatorio_personalizado_Age_Telecon_1`.`id_atendimento`
                    AS `id_atendimento`,`relatorio_personalizado_Age_Telecon_1`.`data_de_entrada_no_bot`
                    AS `data_de_entrada_no_bot`,`relatorio_personalizado_Age_Telecon_1`.`data_de_atendimento`
                    AS `data_de_atendimento`,`relatorio_personalizado_Age_Telecon_1`.`data_inicial_do_atendimento_humano`
                    AS `data_inicial_do_atendimento_humano`,`relatorio_personalizado_Age_Telecon_1`.`Fila_Destino`
                    AS `Fila_Destino`,`relatorio_personalizado_Age_Telecon_1`.`Agente`
                    AS `Agente`,`relatorio_personalizado_Age_Telecon_1`.`Tempo_de_Espera_Fila`
                    AS `Tempo_de_Espera_Fila`,`relatorio_personalizado_Age_Telecon_1`.`Tempo_de_Primeira_resposta`
                    AS `Tempo_de_Primeira_resposta`,`relatorio_personalizado_Age_Telecon_1`.`Tempo_de_Conversacao`
                    AS `Tempo_de_Conversacao`,`relatorio_personalizado_Age_Telecon_1`.`Tempo_Total`
                    AS `Tempo_Total`,`relatorio_personalizado_Age_Telecon_1`.`Telefone`
                    AS `Telefone`,`relatorio_personalizado_Age_Telecon_1`.`Tags`
                    AS `Tags`,`relatorio_personalizado_Age_Telecon_1`.`Status`
                    AS `Status`,`relatorio_personalizado_Age_Telecon_1`.`bot`
                    AS `bot`,`relatorio_personalizado_Age_Telecon_1`.`Numero_de_contrato_ou_CPF`
                    AS `Numero_de_contrato_ou_CPF`,`relatorio_personalizado_Age_Telecon_1`.`Protocolo`
                    AS `Protocolo`,`relatorio_personalizado_Age_Telecon_1`.`transferido_Y_N`
                    AS `transferido_Y_N`,`relatorio_personalizado_Age_Telecon_1`.`canal`
                    AS `canal` from `relatorio_personalizado_Age_Telecon_1`
                   where (`relatorio_personalizado_Age_Telecon_1`.`bot` <> \'AtendimentoNovaAssinatura\')';

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
            'canal',
        ];

        return \Maatwebsite\Excel\Facades\Excel::download(new ReportExport($result, $headers), 'human_care.xlsx');

    }

    public function new_assigments()
    {
        $query = 'select
                cast(`Eventos`.`DATA_HORA` as date) AS `DATA`,
                `Eventos`.`EXTRAS_VALUE` AS
                `canal`,count(0) AS `nova_assinatura`
                from `Eventos`
                where ((`Eventos`.`EXTRAS_KEY` = \'CANAL\') and (not(`Eventos`.`TICKET` in (select `Eventos`.`TICKET` from `Eventos`
                where (`Eventos`.`EXTRAS_KEY` = \'CPF\') group by `Eventos`.`TICKET`))))
                group by `Eventos`.`EXTRAS_VALUE`,cast(`Eventos`.`DATA_HORA` as date)';

        $result = DB::connection('mysql_take')->select($query);

        $headers = [
            'Data',
            'Canal',
            'Nova_assinatura'
        ];

        return \Maatwebsite\Excel\Facades\Excel::download(new ReportExport($result, $headers), 'new_assigments.xlsx');



    }

    public function base_clients_active()
    {

        set_time_limit(2000);
        ini_set('memory_limit', '2048M');


        $query = 'select
                    ac.contract_id as "Contrato",
                    p.name as "Nome cliente",
                    lower(p.email)  as "E-mail",
                    p.street as "Endereço",
                    p.number as "Número",
                    p.neighborhood as "Cidade",
                    c.v_stage,
                    c.v_status
                    from erp.authentication_contracts ac
                    left join erp.contracts c on c.id = ac.contract_id
                    left join erp.people p on p.id = c.client_id
                    left join erp.service_products sp on sp.id = ac.service_product_id
                    where ac.user != \'\'
                    and ac.user like \'ALCL%\'
                    and c.v_status = \'Normal\'
                    order by p.name';

        $result = DB::connection('pgsql')->select($query);

        $headers = [
          'Contrato',
          'Nome cliente',
          'E-mail',
            'Endereço',
            'Número',
            'Cidade',
          'Status',
          'Situacao',
        ];

        return \Maatwebsite\Excel\Facades\Excel::download(new ReportExport($result, $headers), 'base_clients_active.xlsx');
    }

    public function productive_retenction()
    {
        set_time_limit(2000);
        ini_set('memory_limit', '2048M');

        $query = 'select
                a.title as "Protocolo des",
                ai.protocol as "Nº protocolo",
                vu.name as "Atendente Origem",
                cs.title as "Catalago de Servido" ,
                csi.title as "itens de serviço",
                csc.title  as "Sub item",
                sp.title as "Problema",
                sc.title as "Contexto",
                a.beginning_date as "data abertura",
                p2.name as "Cliente",
                p2.id as "ID cliente"
                from erp.assignments a
                left join erp.assignment_incidents ai on ai.assignment_id = a.id
                left join erp.incident_types it on it.id = ai.incident_type_id
                left join erp.catalog_services cs on cs.id = ai.catalog_service_id
                left join erp.catalog_services_items csi on csi.id = ai.catalog_service_item_id
                left join erp.catalog_service_item_classes csc on csc.id = ai.catalog_service_item_class_id
                left join erp.solicitation_problems sp on sp.id = ai.solicitation_problem_id
                left join erp.solicitation_classifications sc on sc.id = ai.solicitation_classification_id
                left join erp.people p on p.id = a.responsible_id
                left join erp.people p2 on p2.id = a.requestor_id
                left join erp.v_users vu on vu.id = a.created_by
                where it.id = 1068';

        $result = DB::connection('pgsql')->select($query);

        $headers = [
          'Descrição do protocolo',
          'Atendente origem',
          'Catálogo de serviço',
          'Sub item',
          'Problema',
          'Contexto',
          'Data da abertura',
          'Cliente'
        ];

        return \Maatwebsite\Excel\Facades\Excel::download(new ReportExport($result, $headers), 'productive_retenction.xlsx');



    }


    public function contracts_seller()
    {
        set_time_limit(2000);
        ini_set('memory_limit', '2048M');

        $query = 'select
                    vc.client_name as "Cliente",
                    vc.id as "N° Contrato",
                    vc.contract_type_title as "Tipo",
                    vc.date as "Dt. Cadastro",
                    c.beginning_date as "Vigência de",
                    c.final_date as "Dt. Término",
                    c.suspension_days as "SuspensÃ£o em",
                    c.collection_day as "Dia Cobrança",
                    vc.financial_collection_type_title as "Tipo de Cobrança",
                    vc.seller1_name as "Vendedor 1",
                    vc.seller2_name as "Vendedor 2",
                    vc.amount as "Valor",
                    vc.v_status as "Situação",
                    vc.v_stage as "Status",
                    c.cancellation_date as "Dt. Cancelamento",
                    c.cancellation_motive as "Motivo do Cancelamento",
                    vu.name as "Criado por",
                    aap.title as "PE Contabilizado"
                    from erp.v_contracts vc
                    left join erp.contracts c on c.id = vc.id
                    left join erp.authentication_access_points aap on aap.id = c.authentication_access_point_id
                    left join erp.v_users vu on vu.id = c.created_by';

        $result = DB::connection('pgsql')->select($query);

        $headers = [
            'Cliente',
            'Nº contrato',
            'Data do cadastro',
            'Vigência de',
            'Dt. Término',
            'Suspenção em',
            'Dia cobrança',
            'Tipo de cobrança',
            'Vendedor 1',
            'Vendedor 2',
            'Valor',
            'Situação',
            'Status',
            'Dt. Cancelamento',
            'Motivo do Cancelamento',
            'Criado por',
            'PE Contabilizado'
        ];

        return \Maatwebsite\Excel\Facades\Excel::download(new ReportExport($result, $headers), 'contracts_seller.xlsx');

    }

    public function renove()
    {
        $query = 'select
                  cp.name_2  as "Local",
                  f.client_id as "Código cliente",
                  p.name as "Nome",
                  p.tx_id as "Documento",
                  CASE WHEN P.type_tx_id = 1 then \'CNPJ\'
                       WHEN P.type_tx_id = 2 then \'CPF\'
                   END as "Tipo de documento",
                  p.phone as "contato 1",
                  p.cell_phone_1 as "contato 2",
                  p.email as "E-mail",
                  f.title as "N° Título",
                  f.parcel as "Parcela",
                  f.bank_title_number as "Nosso N°",
                  f.title_amount as "Valor título",
                  f.balance as "Saldo",
                  f.interest_amount as "Acréscimo",
                  f.fine_amount as "Multa",
                  f.entry_date as "Emissão",
                  f.expiration_date as "Vcto",
                  f.original_expiration_date as "Vcto Original",
                  fo.title as "Operação",
                  fn.title as "Nat.Financeira",
                  fct.title as "Tipo Cobrança",
                  f.payment_condition_id as "Condições Pagamento",
                  f.complement as "Complemento",
                  f.contract_id as "N° Contrato",
                  f.competence as "Competência",
                  f.internal_billet_printed as "Baixado",
                  f.typeful_line as "linha editável",
                  f.deleted as "Excluído",
                  f.p_is_receivable "Em Aberto",
                  f.p_has_before_update_info as "Título Reagendado",
                  f.issue_date as "Vencimento (filtro)",
                  f.renegotiated as "Título Renegociado",
                  c.v_status as "Situação",
                  c.v_stage as "Status",
                  cbd.number_of_days as "Dias Bloqueado",
                  frt.client_paid_date as "Dt Último Pagamento"
                from erp.financial_receivable_titles f
                Left join erp.people p on p.id = f.client_id
                Left join erp.companies_places cp on  cp.id = f.company_place_id
                Left Join erp.financial_operations fo on fo.id = f.financial_operation_id
                Left join erp.financers_natures fn on fn.id = f.financer_nature_id
                Left join erp.financial_collection_types fct on  fct.id = f.financial_collection_type_id
                Left join erp.contracts c on c.id = f.contract_id
                left join erp.financial_receipt_titles frt on frt.financial_receivable_title_id = f.id
                left join erp.contract_blocked_days cbd on cbd.contract_id = f.contract_id
                where f.title like \'FAT%\'
                and f.deleted is false
                and f.p_is_receivable is true
                and (f.original_expiration_date - current_date) >= 30';

        $result = DB::connection('pgsql')->select($query);

    }

    public function against_evidence()
    {
        set_time_limit(2000);
        ini_set('memory_limit', '2048M');

        $query = 'WITH contratos_bloqueados AS (
                SELECT
                    contract_id,
                    begin_date
                FROM  erp.contract_changed_status ccs
                WHERE
                    status IN (5,6,7) AND end_date IS NULL
                )
                SELECT
                    c.description AS "contrato",
                    c.invoice_type as "Tipo de faturamento",
                    p."name",
                    c.created AS "data_criacao_contrato",
                    c.billing_beginning_date AS "Vigência",
                    c.v_stage AS stage,
                    c.v_status AS status,
                    cb.begin_date AS "dt_ultimo_bloqueio",
                    c.billing_day AS "dia_de_faturar",
                    cdc.title AS "ciclo_faturamento",
                    string_agg(frt.title, \',\') AS "faturas",
                    string_agg(date(frt.created)::TEXT, \',\') AS "data_criacao_fatura",
                    string_agg(date(frt.competence)::TEXT, \',\') AS "data_competencia",
                    string_agg(date(frt.expiration_date)::TEXT, \',\') AS "data_vencimento"
                FROM  erp.contracts c
                JOIN erp.people p ON
                    p.id = c.client_id
                LEFT JOIN erp.financial_receivable_titles frt ON
                    frt.contract_id = c.id
                    AND frt.competence = \'2022-10-01\'
                    AND frt.bill_title_id IS NULL
                    AND frt.deleted = FALSE
                    AND frt.renegotiated = FALSE
                LEFT JOIN contratos_bloqueados cb ON
                    cb.contract_id = c.id
                LEFT JOIN erp.contract_date_configurations cdc ON
                    c.contract_date_configuration_id = cdc.id
                WHERE
                    c.stage = 3
                    AND c.status NOT IN (4,9)
                    AND c.created <= \'2022-11-01\'
                    AND frt.id IS NULL
                    AND c.status in (1,6)
                    AND c.grouping_contracts = FALSE
                  AND c.billing_beginning_date >= \'2021-01-01\'
                GROUP BY
                    c.id,
                    p.id,
                    cb.begin_date,
                    cdc.id';

        $result = DB::connection('pgsql')->select($query);

        $headers = [
            'contrato',
            'Tipo de fatura',
            'Nome',
            'data_criacao_contrato',
            'Vigencia',
            'Status',
            'Situacao',
            'data_ultimo_bloqueio',
            'dia_de_faturar',
            'ciclo_faturamento',
            'faturas',
            'data_criacao_fatura',
            'data_competencia',
            'data_vencimento',
        ];

        return \Maatwebsite\Excel\Facades\Excel::download(new ReportExport($result, $headers), 'against_evidence.xlsx');

    }
}
