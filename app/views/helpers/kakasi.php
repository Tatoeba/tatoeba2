<?php
class KakasiHelper extends AppHelper{

	function convert($text, $type){
		$text = escapeshellarg(nl2br($text));
		$options = '';
		
		switch($type){
			case 'romaji':
				$options = ' -Ja -Ha -Ka -Ea -s';
				break;
				
			case 'furigana':
				$options = ' -JH -s -f ';
				break;
		}
		
		system("echo $text | iconv -f UTF8 -t SHIFT_JISX0213 | kakasi $options |iconv -f SHIFT_JISX0213 -t UTF8 | sed -f /home/tatoeba/www/sedlist");
	}
}
?>