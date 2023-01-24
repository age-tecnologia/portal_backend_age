<?php

namespace App\Models\AgeControl;

use App\Models\City;
use App\Models\CollaboratorGroup;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Conductor extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'agecontrol_condutores';
    protected $fillable = [
                            'primeiro_nome',
                            'segundo_nome',
                            'endereco',
                            'cidade_id',
                            'grupo_id',
                            'servico_id',
                            'user_id'
                        ];


    public function city()
    {
        return $this->hasOne(City::class, 'id', 'cidade_id')->select('id','cidade');
    }

    public function group()
    {
        return $this->hasOne(CollaboratorGroup::class, 'id', 'grupo_id')->select('id','grupo');
    }

    public function service()
    {
        return $this->hasOne(Service::class, 'id', 'servico_id')->select('id','servico');
    }
}
