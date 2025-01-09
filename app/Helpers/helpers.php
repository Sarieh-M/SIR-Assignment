<?php

if (!function_exists('current_locale_dir')) {
    function current_locale_dir()
    {
        return app()->getLocale() === 'ar' ? 'rtl' : 'ltr';
    }
}
