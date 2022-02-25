<?php

namespace App\Helpers;

class ManageSessionHelper
{
    public static function createSession(object $request, array $data): void
    {
        foreach ($data as $key => $value) {
            $request->session()->put($key, $value);
        }
    }

    public static function destroySession(object $request, array $data): void
    {
        $request->session()->forget($data);
    }
}
