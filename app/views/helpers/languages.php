<?php
class LanguagesHelper extends AppHelper{

	function codeToName($code){
		switch($code){
			case 'en' : return __('English', true);
			case 'fr' : return __('French', true);
			case 'jp' : return __('Japanese', true);
			case 'de' : return __('German', true);
			case 'es' : return __('Spanish', true);
		}
		return null;
	}
	
}
?>