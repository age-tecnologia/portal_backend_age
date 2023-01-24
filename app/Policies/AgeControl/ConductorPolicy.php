<?php

namespace App\Policies\AgeControl;

use App\Models\AgeControl\Conductor;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class ConductorPolicy
{
    use HandlesAuthorization;

    public function store(User $user, Conductor $conductor)
    {
        return $user->nivel_acesso_id === 3
                ? Response::allow()
                : Response::deny('Você não tem privilégios para inserir dados.');
    }
}
