<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // If user is not authenticated, deny access
        if (!$request->user()) {
            return redirect('login');
        }

        // If user doesn't have a role, deny access
        if (!$request->user()->role) {
            abort(403, 'المستخدم غير مخول للدخول إلى هذه الصفحة');
        }

        // Check if user's role is in the allowed roles
        foreach ($roles as $role) {
            if ($request->user()->role->name === $role) {
                return $next($request);
            }
        }

        // User doesn't have required role
        abort(403, 'لا توجد لديك الصلاحيات المطلوبة للوصول إلى هذه الصفحة');
    }
}
