<?php
/*
    Tatoeba Project, free collaborativ creation of languages corpuses project
    Copyright (C) 2009  HO Ngoc Phuong Trang (tranglich@gmail.com)

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
		$url = $langDetectUrl . "&q=" . $textToAnalyze.$textToAnalyze; 
			// send text in duplicate to re-balance confidence score, because the score
			// depends on the length of the text.
		
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
			case 'ar' :		return 'ara';
			case 'bg' :		return 'bul';
            case 'cs' :     return 'ces';
			case 'de' :		return 'deu';
            case 'el' :     return 'ell';
			case 'en' :		return 'eng';
			case 'eo' :		return 'epo';
			case 'es' :		return 'spa';
			case 'fi' :		return 'fin';
			case 'fr' :		return 'fra';
			case 'iw' : 	return 'heb';
			case 'it' :		return 'ita';
			case 'id' :		return 'ind';
			case 'ja' :		return 'jpn';
			case 'ko' :		return 'kor';
			case 'nl' :		return 'nld';
			case 'pt' :		return 'por';
			case 'pt-BR' :	return 'por';
			case 'pt-PT' :	return 'por';
			case 'ru' :		return 'rus';
			case 'sv' :		return 'swe';
			case 'tr' :		return 'tur';
			case 'uk' :		return 'ukr';
			case 'vi' : 	return 'vie';
			case 'zh-CN' : 	return 'cmn';
			case 'zh-TW' : 	return 'cmn';
		}
		return null;
	}
}
?>
