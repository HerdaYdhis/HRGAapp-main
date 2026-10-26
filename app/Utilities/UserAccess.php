<?php

namespace App\Utilities;

use Throwable;

class UserAccess
{
    public static function can($key, $menu)
    {
        try {
            $user = auth()->user();
            $roles = $user->role->menu_access;
            $key_roles = $roles[$key][$menu];

            return $key_roles;
        } catch (Throwable $th) {
            return false;
        }
    }

    public static function parent($key)
    {
        try {
            $user = auth()->user();
            $roles = $user->role->menu_access;

            return array_key_exists($key, $roles);
        } catch (Throwable $th) {
            return false;
        }
    }
}
