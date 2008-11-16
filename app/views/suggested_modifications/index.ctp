<div class="suggestedModifications index">
<h2><?php __('SuggestedModifications');?></h2>
<p>
<?php
echo $paginator->counter(array(
'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)
));
?></p>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $paginator->sort('id');?></th>
	<th><?php echo $paginator->sort('sentence_id');?></th>
	<th><?php echo $paginator->sort('sentence_lang');?></th>
	<th><?php echo $paginator->sort('correction_text');?></th>
	<th><?php echo $paginator->sort('submit_user_id');?></th>
	<th><?php echo $paginator->sort('submit_datetime');?></th>
	<th><?php echo $paginator->sort('apply_user_id');?></th>
	<th><?php echo $paginator->sort('apply_datetime');?></th>
	<th><?php echo $paginator->sort('was_applied');?></th>
	<th class="actions"><?php __('Actions');?></th>
</tr>
<?php
$i = 0;
foreach ($suggestedModifications as $suggestedModification):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $suggestedModification['SuggestedModification']['id']; ?>
		</td>
		<td>
			<?php echo $html->link($suggestedModification['Sentence']['id'], array('controller'=> 'sentences', 'action'=>'view', $suggestedModification['Sentence']['id'])); ?>
		</td>
		<td>
			<?php echo $suggestedModification['SuggestedModification']['sentence_lang']; ?>
		</td>
		<td>
			<?php echo $suggestedModification['SuggestedModification']['correction_text']; ?>
		</td>
		<td>
			<?php echo $suggestedModification['SuggestedModification']['submit_user_id']; ?>
		</td>
		<td>
			<?php echo $suggestedModification['SuggestedModification']['submit_datetime']; ?>
		</td>
		<td>
			<?php echo $suggestedModification['SuggestedModification']['apply_user_id']; ?>
		</td>
		<td>
			<?php echo $suggestedModification['SuggestedModification']['apply_datetime']; ?>
		</td>
		<td>
			<?php echo $suggestedModification['SuggestedModification']['was_applied']; ?>
		</td>
		<td class="actions">
			<?php echo $html->link(__('View', true), array('action'=>'view', $suggestedModification['SuggestedModification']['id'])); ?>
			<?php echo $html->link(__('Edit', true), array('action'=>'edit', $suggestedModification['SuggestedModification']['id'])); ?>
			<?php echo $html->link(__('Delete', true), array('action'=>'delete', $suggestedModification['SuggestedModification']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $suggestedModification['SuggestedModification']['id'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>
</div>
<div class="paging">
	<?php echo $paginator->prev('<< '.__('previous', true), array(), null, array('class'=>'disabled'));?>
 | 	<?php echo $paginator->numbers();?>
	<?php echo $paginator->next(__('next', true).' >>', array(), null, array('class'=>'disabled'));?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('New SuggestedModification', true), array('action'=>'add')); ?></li>
		<li><?php echo $html->link(__('List Sentences', true), array('controller'=> 'sentences', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Sentence', true), array('controller'=> 'sentences', 'action'=>'add')); ?> </li>
	</ul>
</div>
