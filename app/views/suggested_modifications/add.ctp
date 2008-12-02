<?php
$this->pageTitle = __('Suggestion correction for : ',true) . $sentence['Sentence']['text'];

// navigation (previous, random, next)
$sentences->displayNavigation($sentence['Sentence']['id']);

echo '<h2>' . __('Your correction',true) . '</h2>';

echo '<div class="sentences_set">';
	// sentence menu (translate, edit, comment, etc)
	$sentences->displayMenu($sentence['Sentence']['id'], $sentence['Sentence']['lang'], $sentence['Sentence']['correctness'], $specialOptions);

	// sentence and translations
	$sentences->displayForCorrection($sentence['Sentence'], $sentence['Translation']);
echo '</div>';
?>


<h2><?php __('Comment(s)') ?></h2>
<ul>
<?php
if(count($sentence['SentenceComment']) > 0){
	foreach($sentence['SentenceComment'] as $comment){
		echo '<li>';
		echo $comment['text'];
		echo '</li>';
	}
}else{
	echo '<i>';
	__('There are no comments for now.');
	echo '</i>';
	echo '<br/>';
	echo $html->link(
		__('Add a comment',true),
		array(
			"controller" => "sentence_comments",
			"action" => "add",
			$sentence['Sentence']['id']
		));
}
?>
</ul>