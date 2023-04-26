<?php

namespace App\Http\Controllers\AgePortal\_Aux;

use App\Http\Controllers\Controller;
use App\Models\Portal\Modules;
use Illuminate\Http\Request;

class AccessModulesController extends Controller
{

    public function getModules()
    {
        $modules = Modules::all();


        return $modules;
    }




}
