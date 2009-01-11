<div id="logsLegend">
<span class="sentenceAdded"><?php __('sentence added') ?></span>
<span class="linkAdded"><?php __('link added') ?></span>
<span class="sentenceModified"><?php __('sentence modified') ?></span>
<?php //<span class="correctionSuggested"> __('correction suggested') </span> ?>
<span class="sentenceDeleted"><?php __('sentence deleted') ?></span>
<span class="linkDeleted"><?php __('link deleted') ?></span>
</div>

<table id="logs">
<?php
foreach ($contributions as $contribution){
	$logs->entry($contribution['Contribution'], $contribution['User']);
}
?>
</table>