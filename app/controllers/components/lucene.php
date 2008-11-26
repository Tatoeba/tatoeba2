<?php
class LuceneComponent extends Object{
	function search($query){
		$query = urlencode($query);
		$luceneUrl = "http://tatoeba.fr:28080/tatoeba/search.jsp?query=";
		$url = $luceneUrl . $query;
		
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
		
		$response = $json['responseData']['sentencesIds'];
		
		return $response;
	}
}
?>