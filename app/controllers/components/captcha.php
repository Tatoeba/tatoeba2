<?php 
class CaptchaComponent extends Object
{
    var $controller;
 
    function startup( &$controller ) {
        $this->controller = &$controller;
    }

    function image(){
        App::import('Vendor', 'PhpCaptcha', array('file'=>'phpcaptcha/php-captcha.inc.php'));
        $imagesPath = APP . 'vendors'. DS .'phpcaptcha' . DS . 'fonts' . DS;
		
        $aFonts = array(
            $imagesPath.'VeraBd.ttf',
            $imagesPath.'VeraIt.ttf',
            $imagesPath.'Vera.ttf'
        );
        
        $oVisualCaptcha = new PhpCaptcha($aFonts, 200, 60);
        
        //$oVisualCaptcha->UseColour(true);
        //$oVisualCaptcha->SetOwnerText('Source: '.FULL_BASE_URL);
        $oVisualCaptcha->SetNumChars(6);
        $oVisualCaptcha->Create();
    }
    
    function check($userCode, $caseInsensitive = true){
		App::import('Vendor', 'PhpCaptcha', array('file'=>'phpcaptcha/php-captcha.inc.php'));
        if ($caseInsensitive) {
            $userCode = strtoupper($userCode);
        }
        
        if (!empty($_SESSION[CAPTCHA_SESSION_ID]) && $userCode == $_SESSION[CAPTCHA_SESSION_ID]) {
            // clear to prevent re-use
            unset($_SESSION[CAPTCHA_SESSION_ID]);
            return true;
        }else{
			return false;
		}
    }
}
?>