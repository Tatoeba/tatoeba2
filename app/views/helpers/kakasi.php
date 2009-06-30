<?php
class KakasiHelper extends AppHelper{

	function convert($text, $type){
		
		//$text = escapeshellarg(nl2br($text)); // somehow that doesn't work anymore...
		$text = preg_replace("!\\r\\n!", "\\<br/\\>", $text); // to handle new lines
		$text = preg_replace("!\(!", "\\(", $text); // to handle parenthesis
		$text = preg_replace("!\)!", "\\)", $text);
		$options = '';
		
		$text = preg_replace("!今日は!", "kyou wa", $text); // need to figure out something better...
		
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