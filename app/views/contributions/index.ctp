<div id="logsLegend">
<span class="sentenceAdded"><?php __('sentence added') ?></span>
<span class="translationAdded"><?php __('translation added') ?></span>
<span class="sentenceModified"><?php __('sentence modified') ?></span>
<span class="correctionSuggested"><?php __('correction suggested') ?></span>
<span class="sentenceDeleted"><?php __('sentence deleted') ?></span>
<span class="translationDeleted"><?php __('translation deleted') ?></span>
</div>

<table id="logs">
<?php
foreach ($contributions as $contribution){
	$logs->entry($contribution['Contribution'], $contribution['User']);
}
?>
</table>