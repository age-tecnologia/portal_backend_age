<?php

namespace App\Http\Controllers;

use App\Exports\ReportExport;
use App\Exports\UsersExport;
use App\Models\AgeRv\Channel;
use App\Models\AgeRv\Collaborator;
use App\Models\AgeRv\CollaboratorMeta;
use App\Models\AgeRv\VoalleSales;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use LdapRecord\Auth\BindException;
use LdapRecord\Connection;
use Maatwebsite\Excel\Excel;

class TestController extends Controller
{

    public function index(Request $request)
    {
        $co = Collaborator::where('tipo_comissao_id', 2)->get();

        $m = new CollaboratorMeta();

        foreach($co as $item => $value) {
            $m->create([
                'colaborador_id' => $value->id,
                'mes_competencia' => '07',
                'meta' => 22,
                'modified_by' => 1
            ]);
        }
    }


}
