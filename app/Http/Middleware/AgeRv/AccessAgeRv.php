<?php

namespace App\Http\Middleware\AgeRv;

use App\Models\AgeRv\AccessPermission;
use Closure;
use Illuminate\Http\Request;

class AccessAgeRv
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $collaborator = AccessPermission::where('user_id', auth()->user()->id)->first();

        if((isset($collaborator->id)) ||
            auth()->user()->isAdmin === 1 ||
            auth()->user()->isMaster === 1 ||
            auth()->user()->isCommittee === 1 ) {
            return $next($request);
        } else {
            return response()->json(['Usuário não tem permissão para acessar o sistema!'], 403);
        }

    }
}
