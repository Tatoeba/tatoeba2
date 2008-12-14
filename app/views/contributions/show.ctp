<?php
$this->pageTitle = __('Latest activities for : ',true) . $sentence['Sentence']['text'];

// navigation (previous, random, next)
$sentences->displayNavigation($sentence['Sentence']['id']);

echo '<div class="sentences_set">';
	// sentence menu (translate, edit, comment, etc)
	$sentences->displayMenu($sentence['Sentence']['id'], $sentence['Sentence']['lang'], $sentence['Sentence']['correctness'], $specialOptions);

	// sentence and translations
	$sentences->displayGroup($sentence['Sentence'], $sentence['Translation']);
echo '</div>';	

if(count($sentence['Contribution']) > 0){
	
	foreach($sentence['Contribution'] as $logs){
		if(isset($logs['User']['username'])){
			echo $logs['User']['username'];
		}
		echo $logs['datetime'] . $logs['text'];
		echo '<br/>';
	}
}else{
	echo 'nothing special';
}

/*
Array ( [0] => 
	Array ( 
		[sentence_id] => 211 
		[sentence_lang] => jp 
		[translation_id] => 323 
		[translation_lang] => en
		[text] => "When did you buy this?" "Hmm, last week." 
		[action] => insert 
		[user_id] => 
		[datetime] => 2008-12-13 21:32:31 
		[Sentence] => Array ( 
			[id] => 211 
			[lang] => jp 
			[text] => 「いつそれを買ったの」「ええと、先週でした」 
			[correctness] => 
			[user_id] => 
			[created] => 
			[modified] => 
		) 
	) 
) 
*/
?>