<?php

namespace App\Http\Middleware\AgeBoard;

use App\Models\AgeReport\AccessPermission;
use Closure;
use Illuminate\Http\Request;

class AccessAgeBoard
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
        $access = \App\Models\AgeBoard\AccessPermission::whereUserId(auth()->user()->id)->first();
        $level = auth()->user()->nivel_acesso_id;

        if(isset($access->id) || $level === 2 || $level === 3 ) {
            return $next($request);
        } else {
            return response()->json(['Usuário não tem permissão para acessar o sistema!'], 403);
        }
    }
}
