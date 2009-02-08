<?php
class LanguagesHelper extends AppHelper{
	function languagesArray(){
		$languages = array(
			  'en' => __('English', true)
			, 'jp' => __('Japanese', true)
			, 'fr' => __('French', true)
			, 'de' => __('German', true)
			, 'es' => __('Spanish', true)
			, 'it' => __('Italian', true)
			, 'vn' => __('Vietnamese', true)
			, 'ru' => __('Russian', true)
			, 'ch' => __('Chinese', true)
			, 'ko' => __('Korean', true)
			, 'nl' => __('Dutch', true)
			, 'he' => __('Hebrew', true)
			, 'id' => __('Indonesian', true)
		);
		return $languages;
	}
	
	function codeToName($code){
		$languages = $this->languagesArray();
		if(isset($languages["$code"])){
			return $languages["$code"];
		}else{
			return null;
		}
	}
}
?>