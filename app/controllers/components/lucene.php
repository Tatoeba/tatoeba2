<?php
class LuceneComponent extends Object{
	function search($query, $lang_src = null, $lang_dest = null, $page = null){
		$query = $this->processQuery($query); // take out the small words
		$query = urlencode($query);
		//$luceneUrl = "http://tatoeba.fr:28080/tatoeba/search.jsp?query=";
		$luceneUrl = "http://localhost:8080/tatoeba/search.jsp?query=";
		$url = $luceneUrl . $query;
		
		if($lang_src != null){
			$url .= "&lang_src=" . $lang_src;
		}
		if($lang_dest != null){
			$url .= "&lang_dest=" . $lang_dest;
		}
		if($page != null){
			$url .= "&page=" . ($page-1); // because page 1 is at index 0
		}
		
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
		
		$response = $json['responseData'];
		
		return $response;
	}
	
	function processQuery($query){
		$query = rtrim($query); // deleting unnecessary spaces at the end and beginning
		$query = preg_replace("!\[!","",$query);
		$query = preg_replace("!\]!","",$query);
		if(!preg_match('!^"!', $query) AND !preg_match('!"$!', $query)){
			$query = preg_replace("!^[a-z]{1,3} !i", " ", $query); // deleting little words at the beginning
			$query = preg_replace("! [a-z]{1,3}\.?$!i", " ", $query); // deleting little words at the end
			$query = preg_replace("! [a-z]{1,3} (([a-z]{1,3} )?){1,5}!i", " ", $query); // deleting little words in the middle
			if(rtrim($query) == ''){ $query = '"'.rtrim($query).'"'; }
		}
		return $query;
	}
}
?>