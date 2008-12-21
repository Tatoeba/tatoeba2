<?php
class GoogleLanguageApiComponent extends Object{
	var $text;
	var $langSrc;
	var $langDest;
	var $translateUrl = "http://ajax.googleapis.com/ajax/services/language/translate?v=1.0";
	
	/* 
	 * This function uses cURL : http://de.php.net/manual/en/curl.installation.php
	 *
	 * The language detection is done by Google's language detect API.
	 * Google documentation : http://code.google.com/apis/ajaxlanguage/documentation/reference.html
	 *
	 * The function returns an associative array with the indices "language", "isReliable" and "confidence".	 
	 */
	function detectLang(){
		$textToAnalyze = urlencode($this->text);
		
		$langDetectUrl = "http://ajax.googleapis.com/ajax/services/language/detect?v=1.0";
		$url = $langDetectUrl . "&q=" . $textToAnalyze;
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_REFERER, "http://tatoeba.fr");
		$body = curl_exec($ch);
		curl_close($ch);

		// now, process the JSON string
		$json = json_decode($body, true);

		if ($json['responseStatus'] != 200){
			return false;
		}

		return $json['responseData'];
	}
	
	function google2TatoebaCode($googleCode){
		switch($googleCode){
			case 'de' :		return 'de';
			case 'en' :		return 'en';
			case 'es' :		return 'es';
			case 'fr' :		return 'fr';
			case 'iw' : 	return 'he';
			case 'it' :		return 'it';
			case 'ja' :		return 'jp';
			case 'ko' :		return 'ko';
			case 'nl' :		return 'nl';
			case 'ru' :		return 'ru';
			case 'vi' : 	return 'vn';
			case 'zh-CN' : 	return 'zh';
			case 'zh-TW' : 	return 'zh';
		}
		return null;
	}
}
?>