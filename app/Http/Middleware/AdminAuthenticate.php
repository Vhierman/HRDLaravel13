<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class AdminAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // return $next($request);
        $user = Auth::user();
        // if ($user) {
        //     return $next($request);
        // }
        //     return redirect()->route('admin.login');

        // return $next($request);


        if (!$user) {
            return redirect()->route('admin.login');
        }
        $allowedRoles = ['admin', 'hrd', 'accounting', 'leader'];
        if (in_array($user->roles, $allowedRoles)) {
            return $next($request);
        }
        return redirect()->route('user.dashboard')->withErrors([
            'error' => 'Anda tidak memiliki hak akses ke halaman Admin.'
        ]);



    }
}
