<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Role;

class isAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        $roleAdmin =  Role::where('name', 'admin')->value('id');

        if ($user->role_id != $roleAdmin) {
            return response([
                'message' => 'Role anda tidak diizinkan untuk mengakses rute ini'
            ], 403);
        }
        return $next($request);
    }
}
