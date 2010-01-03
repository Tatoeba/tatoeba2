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
class LanguagesHelper extends AppHelper{
	function languagesArray(){
		$languages = array(
            // can't use 'any' as it's the code for anyin language
            // only und is used for "undefined" 
        //      'und' => __('All languages',true)
			 'ara' => __('Arabic', true)
			, 'eng' => __('English', true)
			, 'jpn' => __('Japanese', true)
			, 'fra' => __('French', true)
			, 'deu' => __('German', true)
			, 'spa' => __('Spanish', true)
			, 'ita' => __('Italian', true)
			, 'vie' => __('Vietnamese', true)
			, 'rus' => __('Russian', true)
			, 'cmn' => __('Chinese', true)
			, 'kor' => __('Korean', true)
			, 'nld' => __('Dutch', true)
			, 'heb' => __('Hebrew', true)
			, 'ind' => __('Indonesian', true)
			, 'por' => __('Portuguese', true)
			, 'fin' => __('Finnish', true)
			, 'bul' => __('Bulgarian', true)
			, 'ukr' => __('Ukrainian', true)
            , 'ces' => __('Czech',true)
            , 'epo' => __('Esperanto',true)
            , 'ell' => __('Modern Greek',true)
            , 'tur' => __('Turkish',true)
            , 'swe' => __('Swedish',true)
            , 'nob' => __('Norwegian (Bokmål)',true)
            , 'zsm' => __('Malay',true)
            , 'est' => __('Estonian',true)
            , 'wuu' => __('Shanghainese',true)// TODO to change when shanghainese will not be the only wu dialect
		);
        asort($languages);
        array_unshift( $languages , array('und' => __('All languages',true)) );
        
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
