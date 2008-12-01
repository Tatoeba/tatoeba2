<?php
$this->pageTitle = __('Please translate : ',true) . $sentence['Sentence']['text'];
?>

<?php $sentences->displayNavigation($sentence['Sentence']['id']); ?>

<?php 
echo '<h1>' . __('Translate this sentence',true) . '</h1>';
$sentences->displayForTranslation($sentence['Sentence'], $sentence['Translation']); 
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