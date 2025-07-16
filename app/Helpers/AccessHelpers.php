<?php

use App\Models\Setting\Menu;
use App\Models\Setting\RolesA;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

if (!function_exists('getmenu')) {
    $blade = '';

    // function to render parent menu
    function getmenu()
    {
        $blade = '';
        // if (Cache::has('key')) {
        //     // ambil data dari cache
        //     $query = Cache::get('key');
        // } else {
            // $query = Cache::rememberForever('key', function () {
                $menu = Menu::with('rolesA')->whereHas('rolesA', function ($q) {
                    $q->where('code', Auth::user()->roles->code)
                        ->where('deleted_status', 0);
                })
                    ->where('deleted_status', 0)
                    ->orderBy('xlevel')
                    ->get();

        //         return $menu;
        //     });
        // }

        foreach ($menu as $val) {
            $parent_id = $val->parent_id;
            if (!$parent_id) {
                $blade .= "<li class='sidebar-title' id='$val->xlevel'>$val->name</li>" . getMenuChild($menu, $val->id);
            }
        }

        return $blade;
    }
}

    // function to render child menu (invinite child)
    function getMenuChild($menu, $parent_id)
    {
        $child = '';
        foreach ($menu as $val) {
            $mn = $val;
            if ($mn->parent_id != null) {
                if ($mn->parent_id == $parent_id && Route::has($mn->xurl)) {
                    $child .= "
                    <li class='sidebar-item'>
                        <a href='" . route($mn->xurl) . "' class='sidebar-link spa_route' id='$mn->xurl' data-idmenu='$mn->id'>
                            <i class='$mn->xicon'></i>
                            <span>$mn->name</span>
                        </a>
                        <ul class='submenu'>
                            " . getMenuChild($menu, $mn->id, true) . "
                        </ul>
                    </li>
                    ";
                }
            }
        }

        return $child;
    }

    // Function to get all permission
    function getPermission($currentRoute)
    {
        $menu_id = Menu::where('xurl', $currentRoute)->first();
        if ($menu_id != null){
            $rolesA = RolesA::where('id_menu', $menu_id->id)->where('code', Auth::user()->roles->code)->first();
            return $rolesA;
        } else {
            return false;
        }
    }

    function getUrlMenu()
    {
        $currentUrl = request()->url(); // Mendapatkan URL lengkap
        $path = parse_url($currentUrl, PHP_URL_PATH); // Mendapatkan path dari URL
        $segments = explode('/', $path); // Memecah path menjadi segmen

        if (count($segments) > 1) {
            // Menghapus segmen terakhir dari array
            $segments = array_slice($segments, 0, -1);

            // Menggabungkan segmen yang tersisa
            $result = implode('.', $segments);
            $result = ltrim($result, '.');

            return $result;
        }
    }

    function getUrlMenuMethod()
    {
        $currentUrl = request()->url(); // Mendapatkan URL lengkap
        $path = parse_url($currentUrl, PHP_URL_PATH); // Mendapatkan path dari URL
        $segments = explode('/', $path); // Memecah path menjadi segmen
    
        if (count($segments) > 2) {
            // Menghapus dua segmen terakhir dari array
            $segments = array_slice($segments, 0, -2);
    
            // Menggabungkan segmen yang tersisa
            $result = implode('.', $segments);
            $result = ltrim($result, '.');
    
            return $result;
        }
    }

