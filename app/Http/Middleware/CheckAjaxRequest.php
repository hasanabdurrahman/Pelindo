<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAjaxRequest
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->ajax()) {
            if(str_contains($request->url(), 'print') || str_contains($request->url(), 'download-file') || str_contains($request->url(), 'import-timeline')){
                return $next($request);
            }
            return redirect()->route('redirect')->with(['Redirect' => $request->path()]);
        }

        return $next($request);
    }
}
