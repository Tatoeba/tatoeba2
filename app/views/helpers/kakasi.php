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
App::import('Core', 'Sanitize');


// TODO HACK SPOTTED :
// Kakasi is not supposed to be an helper
// as it deal with information retrieving, it should be set in the model part

class KakasiHelper extends AppHelper{

	function convert($text, $type){
		
        Sanitize::html($text);
		//$text = escapeshellarg(nl2br($text)); // somehow that doesn't work anymore... 
												// and I found out it's probably because escapeshellarg() 
												// doesn't process UTF-8 anymore...
		
		// escaping manually... until there is a better a solution...
		$text = preg_replace("!\(!", "\\(", $text);
		$text = preg_replace("!\)!", "\\)", $text);
		$text = preg_replace("!\*!", "\\*", $text); 
		$text = preg_replace("!\|!", "\\|", $text);
		$text = preg_replace("!\>!", "\\>", $text);
		$text = preg_replace("!\<!", "\\<", $text);
		$text = preg_replace("!\[!", "\\[", $text);
		$text = preg_replace("!\]!", "\\]", $text);
		$text = preg_replace('!"!', '\\"', $text);
		$text = preg_replace("!'!", "\\'", $text);
		$text = preg_replace("!&!", "\\&", $text);
		$text = preg_replace("!#!", "\\#", $text);

        // TODO HACK SPOTTED! use nl1br instead
        // because \r\n is windows only
        // \n => linux based system
        // \r => mac os based system
        // 25 % of tatoeba visit !

		$text = preg_replace("!\\r\\n!", "\\<br/\\>", $text); // to handle new lines
        		
		
		$options = '';
		
		// need to figure out something better...
		$text = preg_replace("!今日は!", "kyou wa", $text); // otherwise it displays "konnichiha"
		$text = preg_replace("!死は生!", "shi wa sei", $text); // otherwise it displays "shi wa u"
		$text = preg_replace("!生の!", "nama no", $text); // otherwise it display "uno"
		$text = preg_replace("!入った!", " haitta ", $text); // otherwise it display "itta"... although sometimes "itta" would be correct...
		$text = preg_replace("!来れば!", " kureba ", $text); // otherwise it display "kore ba"...
		
		switch($type){
			case 'romaji':
				$options = ' -Ja -Ha -Ka -Ea -s';
				$sedlist = 'sedlist';
				break;
				
			case 'furigana':
				$options = ' -JH -s -f ';
				$sedlist = 'sedlist2';
				break;
		}
		
		system("echo $text | iconv -f UTF8 -t SHIFT_JISX0213 | /home/tatoeba/kakasi/bin/kakasi $options |iconv -f SHIFT_JISX0213 -t UTF8 | sed -f /home/tatoeba/www/app/webroot/$sedlist");
	}
}
?>
