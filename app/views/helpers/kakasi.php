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
class KakasiHelper extends AppHelper{

	function convert($text, $type){
		
		//$text = escapeshellarg(nl2br($text)); // somehow that doesn't work anymore...
		$text = preg_replace("!\\r\\n!", "\\<br/\\>", $text); // to handle new lines
		$text = preg_replace("!\(!", "\\(", $text); // to handle parenthesis
		$text = preg_replace("!\)!", "\\)", $text);
		$options = '';
		
		// need to figure out something better...
		$text = preg_replace("!今日は!", "kyou wa", $text); // otherwise it displays "konnichiha"
		$text = preg_replace("!死は生!", "shi wa sei", $text); // otherwise it displays "shi wa u"
		$text = preg_replace("!生の!", "nama no", $text); // otherwise it display "uno"
		
		switch($type){
			case 'romaji':
				$options = ' -Ja -Ha -Ka -Ea -s';
				break;
				
			case 'furigana':
				$options = ' -JH -s -f ';
				break;
		}
		
		//system("echo $text | iconv -f UTF8 -t SHIFT_JISX0213 | kakasi $options |iconv -f SHIFT_JISX0213 -t UTF8 | sed -f /home/tatoeba/www/app/webroot/sedlist");
	}
}
?>
