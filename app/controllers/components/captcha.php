<?php 
/*
    Tatoeba Project, free collaborativ creation of languages corpuses project
    Copyright (C) 2009  TATOEBA Project(should be changed)

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Affero General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
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
