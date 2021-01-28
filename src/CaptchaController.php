<?php

namespace Fractal512\Captcha;

use Exception;
use Illuminate\Routing\Controller;

/**
 * Class CaptchaController
 * @package Mews\Captcha
 */
class CaptchaController extends Controller
{
    /**
     * Instantiate a new controller instance.
     *
     * @param Captcha $captcha
     */
    public function __construct(Captcha $captcha)
    {
        $attempts = $captcha->getAttempts();
        $this->middleware("throttle:$attempts,1")->only('getCaptcha');
    }

    /**
     * get CAPTCHA
     *
     * @param Captcha $captcha
     * @param string $config
     * @return array|mixed
     * @throws Exception
     */
    public function getCaptcha(Captcha $captcha, $config = 'default')
    {
        if (ob_get_contents()) {
            ob_clean();
        }

        return $captcha->create($config);
    }
}
