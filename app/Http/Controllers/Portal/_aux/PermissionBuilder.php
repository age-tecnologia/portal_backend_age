<?php

namespace App\Http\Controllers\Portal\_aux;


use App\Models\LevelAccess;
use App\Models\Portal\Modules;
use App\Models\Portal\ModulesGroupsSections;
use App\Models\Portal\ModulesGroupUsers;
use Illuminate\Database\Eloquent\Collection;

class PermissionBuilder extends Collection
{
    /**
     * Constrói a estrutura de permissões.
     *
     * @return $this
     */
    public function build()
    {
        $this->identifyUser(new LevelAccess());
        $this->getModulesAndSections();

        return $this;
    }

    /**
     * Identifica o usuário e adiciona as informações do usuário à estrutura de permissões.
     *
     * @param LevelAccess $levelAccess
     * @return void
     */
    private function identifyUser(LevelAccess $levelAccess) : void
    {
        // Recupera o usuário autenticado atualmente
        $user = auth()->user();

        // Cria o array com as informações do usuário
        $userInfo = [
            'id' => $user->id,
            'level' => [
                'id' => $user->nivel_acesso_id,
                'name' => $levelAccess->whereId($user->nivel_acesso_id)->first('nivel')->nivel
            ]
        ];

        // Adiciona as informações do usuário à estrutura de permissões
        $this->put('userInfo', $userInfo);
    }

    /**
     * Obtém os módulos e suas seções e adiciona-os à estrutura de permissões.
     *
     * @return void
     */
    private function getModulesAndSections()
    {
        // Consulta os módulos e suas seções no banco de dados
        $query = Modules::from('portal_modulos as m')
            ->whereAtivo(1)
            ->with('sections')
            ->get(['id', 'modulo', 'icone', 'descricao', 'rota']);

        // Adiciona os módulos e seções à estrutura de permissões
        $this->put('modules', $query);

        // Adiciona o campo 'liberado' a cada seção de cada módulo
        $this->modules = collect($this['modules'])->map(function ($module) {
            $module['sections'] = collect($module['sections'])->map(function ($section) {
                $section['liberado'] = true;
                return $section;
            })->all();
            return $module;
        })->all();
    }
}
