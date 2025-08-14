<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {           
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not authenticated'
                ], 401);
            }

            return redirect()->route('login');
        }

        $user = Auth::user();

        if (!$user->isAdmin()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not authorized to access this resource. Only admin can access.'
                ], 403);
            }

            return redirect()->route('home')->with('error', 'Bạn không có quyền truy cập vào tài nguyên này. Chỉ quản trị viên mới có quyền truy cập.');
        }

        return $next($request);
    }
}