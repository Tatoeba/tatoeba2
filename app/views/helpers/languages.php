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
			, 'uk' => __('Ukrainian', true)
            , 'cs' => __('Czech',true)
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
