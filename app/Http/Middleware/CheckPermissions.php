<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

class CheckPermissions
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $permission): Response
    {
        if ($permission == 'read') {
            $rolesA = getPermission(Route::currentRouteName());
        } else if ($permission == 'add' || $permission == 'datatable') {
            $url = getUrlMenu();
            $rolesA = getPermission($url);
        } else if ($permission == 'update') {
            $url = getUrlMenu();
            $rolesA = getPermission($url);
        } else {
            $url = getUrlMenuMethod();
            if($request->method() == 'GET'){
                $url = explode('.', $url)[0].'.'.explode('.', $url)[1];
            }
            $rolesA = getPermission($url);
        }
        $status = false;
        if ($permission == 'read' && $rolesA) {
            $status = true;
        } else if ($permission == 'datatable' && $rolesA) {
            $status = true;
        } else if ($permission == 'update') {
            if ($rolesA->xupdate) {
                $status = true;
            }
        } else if ($permission == 'add') {
            if ($rolesA->xadd) {
                $status = true;
            }
        } else if ($permission == 'approve') {
            if ($rolesA->xapprove) {
                $status = true;
            }
        } else if ($permission == 'delete') {
            if ($rolesA->xdelete) {
                $status = true;
            }
        } else if ($permission == 'print') {
            if ($rolesA->xprint) {
                $status = true;
            }
        }

        if (!$status) {
            $customMessage = 'Your custom message';
            return response(view('errors.forbidden'));
        }

        return $next($request);

    }
}
