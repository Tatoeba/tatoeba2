<?php
/**
 * Securimage-Driven Captcha Component.
 *
 * PHP version 5
 *
 * @category PHP
 * @package  Tatoeba
 * @author   debuggeddesigns <unknown@debuggeddesigns.com>
 * @license  MIT license
 * @link     http://tatoeba.org
 */

/**
 * Component for CAPTCHA.
 * http://bakery.cakephp.org/articles/view/captcha-component-with-securimage
 *
 * @category Default
 * @package  Components
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  MIT license
 * @link     http://tatoeba.org
 */
class CaptchaComponent extends Object
{
    public $controller;

    /**
     * ?
     *
     * @param unknown &$controller ?
     *
     * @return void
     */
    public function startup(&$controller)
    {
        $this->controller = &$controller;
    }

    /**
     * Generate image CAPTCHA.
     *
     * @return void
     */
    public function image()
    {
        App::import(
            'Vendor', 'PhpCaptcha', array('file'=>'phpcaptcha/php-captcha.inc.php')
        );
        $imagesPath = APP . 'vendors'. DS .'phpcaptcha' . DS . 'fonts' . DS;

        $aFonts = array(
            $imagesPath.'VeraBd.ttf',
            $imagesPath.'VeraIt.ttf',
            $imagesPath.'Vera.ttf'
        );

        $oVisualCaptcha = new PhpCaptcha($aFonts, 200, 60);

        $oVisualCaptcha->SetNumChars(6);
        $oVisualCaptcha->Create();
    }

    /**
     * Check of user input matches CAPTCHA code.
     *
     * @param string $userCode        Code entered by user.
     * @param bool   $caseInsensitive Set to false if case sensitive.
     *
     * @return void
     */
    public function check($userCode, $caseInsensitive = true)
    {
        App::import(
            'Vendor', 'PhpCaptcha', array('file'=>'phpcaptcha/php-captcha.inc.php')
        );
        if ($caseInsensitive) {
            $userCode = strtoupper($userCode);
        }

        if (!empty($_SESSION[CAPTCHA_SESSION_ID])
            && $userCode == $_SESSION[CAPTCHA_SESSION_ID]
        ) {
            // clear to prevent re-use
            unset($_SESSION[CAPTCHA_SESSION_ID]);
            return true;
        } else {
            return false;
        }
    }
}
?>