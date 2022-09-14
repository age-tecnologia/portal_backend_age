<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AccessMaster
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
        if( auth()->user()->nivel_acesso_id === 3) {
            return $next($request);
        } else {
            return response()->json(['Usuário não tem permissão para acessar o sistema!'], 403);
        }
    }
}
