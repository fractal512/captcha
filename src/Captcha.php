<?php

namespace Fractal512\Captcha;

/**
 * Laravel 5 Stateless Captcha Package
 *
 * @copyright Copyright (c) 2021 Fractal512
 * @version 1.x
 * @author fractal512
 * @contact
 * @web https://github.com/fractal512/captcha
 * @date 2021-01-26
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 */

use Exception;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Support\Str;
use Illuminate\Support\HtmlString;

/**
 * Class Captcha
 * @package Fractal512\Captcha
 */
class Captcha
{
    /**
     * @var Repository
     */
    protected $config;

    /**
     * @var string
     */
    protected $characters;

    /**
     * @var string
     */
    protected $fontsDirectory;

    /**
     * @var Str
     */
    protected $str;

    /**
     * @var string
     */
    protected $captchaDirectory;

    /**
     * @var string
     */
    protected $captcha;

    /**
     * @var int
     */
    protected $expire = 300;

    /**
     * Constructor
     *
     * @param Repository $config
     * @param Str $str
     * @throws Exception
     * @internal param Validator $validator
     */
    public function __construct(
        Repository $config,
        Str $str
    ) {
        $this->config = $config;
        $this->str = $str;
        $this->characters = config(
            'captcha.characters',
            'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'
        );
        $this->expire = config(
            'captcha.expire',
            300
        );
        $this->fontsDirectory = config(
            'captcha.fontsDirectory',
            dirname(__DIR__) . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'fonts'
        );
        $captchaDirectory = config(
            'captcha.captchaDirectory',
            base_path('storage' . DIRECTORY_SEPARATOR . 'captcha')
        );
        if ( ! is_dir($captchaDirectory) ) {
            mkdir($captchaDirectory);
        }
        $this->captchaDirectory = $captchaDirectory;
    }

    /**
     * @param string $config
     * @return void
     */
    protected function configure($config)
    {
        if ($this->config->has('captcha.' . $config)) {
            foreach ($this->config->get('captcha.' . $config) as $key => $val) {
                $this->{$key} = $val;
            }
        }
    }

    /**
     * Generate captcha image source
     *
     * @param string $config
     * @return string
     */
    public function src($config = 'default')
    {
        return url('captcha/' . $config) . '?' . $this->str->random(8);
    }

    /**
     * Generate captcha image html tag
     *
     * @param string $config
     * @param array $attrs
     * $attrs -> HTML attributes supplied to the image tag where key is the attribute and the value is the attribute value
     * @return string
     */
    public function img($config = 'default', $attrs = [])
    {
        $attrs_str = '';
        foreach ($attrs as $attr => $value) {
            if ($attr == 'src') {
                //Neglect src attribute
                continue;
            }

            $attrs_str .= $attr . '="' . $value . '" ';
        }
        return new HtmlString('<img src="' . $this->src($config) . '" ' . trim($attrs_str) . '>');
    }

    /**
     * Create captcha image
     *
     * @param string $config
     * @return array|mixed
     */
    public function create($config = 'default')
    {
        $this->configure($config);
        $this->captcha = $this->generate();

        $this->cleanOldCaptcha();
        $this->addNewCaptcha();

        ob_start();
        $im = imagecreatetruecolor(110, 40);
        $bgColor = imagecolorallocate($im, rand(0, 255), rand(0, 255), rand(0, 255));
        $grey = imagecolorallocate($im, 128, 128, 128);
        $txtColor = imagecolorallocate($im, rand(0, 255), rand(0, 255), rand(0, 255));
        imagefilledrectangle($im, 0, 0, 109, 39, $bgColor);
        $font = $this->fontsDirectory . DIRECTORY_SEPARATOR . 'Pacifico-Regular.ttf';
        $angle = rand((-8), 8);
        imagettftext($im, 18, $angle, 11, 31, $grey, $font, $this->captcha);
        imagettftext($im, 18, $angle, 10, 30, $txtColor, $font, $this->captcha);
        imagepng($im);
        imagedestroy($im);
        $image = ob_get_clean();

        $headers = [
            'Content-type' => 'image/png'
        ];
        return response()->stream(function () use ($image) {
            echo $image;
        }, 200, $headers);
    }

    /**
     * Generate captcha text
     *
     * @return string
     */
    protected function generate()
    {
        $chars = $this->characters;
        $count = strlen($chars);
        $i = 0;
        $length = 5;
        $pass = '' ;

        while ($i < $length) {
            $num = rand( 0, ($count-1) );
            $tmp = substr($chars, $num, 1);
            $pass = $pass . $tmp;
            $i++;
        }

        return $pass;
    }

    /**
     * Delete all expired captcha files.
     *
     * @return void
     */
    protected function cleanOldCaptcha()
    {
        $time = time();

        if ($handle = opendir($this->captchaDirectory)) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != "..") {
                    $chunks = explode('-', $entry);
                    $timelapse = $time - $chunks[1];
                    if($timelapse > $this->expire){
                        unlink($this->captchaDirectory . DIRECTORY_SEPARATOR . $entry);
                    }
                }
            }
            closedir($handle);
        }
    }

    /**
     * Create new captcha file.
     *
     * @return void
     */
    protected function addNewCaptcha()
    {
        touch( $this->captchaDirectory . DIRECTORY_SEPARATOR . $this->captcha . '-' . time() );
    }

    /**
     * Captcha check
     *
     * @param string $value
     * @return bool
     */
    public function check($value)
    {
        $time = time();

        if ($handle = opendir($this->captchaDirectory)) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != "..") {
                    $chunks = explode('-', $entry);
                    if($chunks[0] === $value){
                        unlink($this->captchaDirectory . DIRECTORY_SEPARATOR . $entry);
                        return true;
                    }
                }
            }
            closedir($handle);
        }

        return false;
    }
}
