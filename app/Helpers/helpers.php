<?php

use Illuminate\Support\Facades\DB;
use App\Models\Setting;

if (!function_exists('t')) {
    function t($key)
    {
        $locale = explode('.', tenant()->domains[0]['domain'])[0] ?? 'en';

        return DB::table('translations')
            ->where('locale', $locale)
            ->where('key', $key)
            ->value('value') ?? $key;
    }
}

if (!function_exists('setting')) {
    function setting($key, $default = null) {
        return Setting::where('key', $key)->value('value') ?? $default;
    }
}
