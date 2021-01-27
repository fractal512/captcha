<?php

namespace Fractal512\Captcha\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Fractal512\Captcha\Captcha
 */
class Captcha extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'captcha';
    }
}
