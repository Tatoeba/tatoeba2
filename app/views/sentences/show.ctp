<?php
$this->pageTitle = __('Translations for : ',true) . $sentence['Sentence']['text'];
?>

<h2>
<?php 
__('Sentence'); 
echo ' ' . $sentence['Sentence']['id'] . ' / correctness ' . $sentence['Sentence']['correctness'];
?>
</h2>

<?php echo $sentence['Sentence']['text']; ?>

<?php
// delete link
echo '[';
echo $html->link(
	__('Delete',true), 
	array(
		"controller" => "sentences",
		"action" => "delete",
		$sentence['Sentence']['id']
	), 
	null, 
	'Are you sure?');
echo ']';

// edit link
echo '[';
echo $html->link(
	__('Edit',true),
	array(
		"controller" => "sentences",
		"action" => "edit",
		$sentence['Sentence']['id']
	));
echo ']';

// translate link
echo '[';
echo $html->link(
	__('Translate',true),
	array(
		"controller" => "sentences",
		"action" => "translate",
		$sentence['Sentence']['id']
	));
echo ']';

// suggest correction link
echo '[';
echo $html->link(
	__('Suggest correction',true),
	array(
		"controller" => "suggested_modifications",
		"action" => "add",
		$sentence['Sentence']['id']
	));
echo ']';
?>

<h2><?php __('Translation(s)') ?></h2>
<ul>
<?php
if(count($sentence['Translation']) > 0){
	foreach($sentence['Translation'] as $translation){
		echo '<li>';
		echo $html->link('(' . $translation['id'] . '-' . $translation['lang'] . ')',
			array(
				"controller" => "sentences",
				"action" => "show",
				$translation['id']
				));
		echo ' ' . $translation['text'];
		echo '</li>';
	}
}else{
	echo '<i>';
	__('There are no translations for now.');
	echo '</i>';
	echo '<br/>';
	echo $html->link(
		__('Add a translation',true),
		array(
			"controller" => "sentences",
			"action" => "translate",
			$sentence['Sentence']['id']
		));
}
?>
</ul>