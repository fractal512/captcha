<?php

if (!function_exists('captcha')) {
    /**
     * @param string $config
     * @return array|mixed
     * @throws Exception
     */
    function captcha($config = 'default')
    {
        return app('captcha')->create($config);
    }
}

if (!function_exists('captcha_src')) {
    /**
     * @param string $config
     * @return string
     */
    function captcha_src($config = 'default')
    {
        return app('captcha')->src($config);
    }
}

if (!function_exists('captcha_img')) {

    /**
     * @param string $config
     * @return string
     */
    function captcha_img($config = 'default', $attrs = [])
    {
        return app('captcha')->img($config, $attrs);
    }
}

if (!function_exists('captcha_check')) {
    /**
     * @param string $value
     * @return bool
     */
    function captcha_check($value)
    {
        return app('captcha')->check($value);
    }
}
