<?php
class LanguagesHelper extends AppHelper{
	function languagesArray(){
		$languages = array(
			  'ar' => __('Arabic', true)
			, 'en' => __('English', true)
			, 'jp' => __('Japanese', true)
			, 'fr' => __('French', true)
			, 'de' => __('German', true)
			, 'es' => __('Spanish', true)
			, 'it' => __('Italian', true)
			, 'vn' => __('Vietnamese', true)
			, 'ru' => __('Russian', true)
			, 'zh' => __('Chinese', true)
			, 'ko' => __('Korean', true)
			, 'nl' => __('Dutch', true)
			, 'he' => __('Hebrew', true)
			, 'id' => __('Indonesian', true)
			, 'pt' => __('Portuguese', true)
			, 'fi' => __('Finnish', true)
			, 'bg' => __('Bulgarian', true)
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