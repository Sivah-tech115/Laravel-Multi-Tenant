<?php

use Illuminate\Support\Facades\DB;
use App\Models\Setting;

if (!function_exists('t')) {
    function t($key)
    {
        $locale = tenant()->language ?? 'en';

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
