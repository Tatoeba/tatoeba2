<div class="latestActivities index">
<h2><?php __('LatestActivities');?></h2>
<p>
<?php
echo $paginator->counter(array(
'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)
));
?></p>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $paginator->sort('sentence_id');?></th>
	<th><?php echo $paginator->sort('sentence_lang');?></th>
	<th><?php echo $paginator->sort('translation_id');?></th>
	<th><?php echo $paginator->sort('translation_lang');?></th>
	<th><?php echo $paginator->sort('text');?></th>
	<th><?php echo $paginator->sort('action');?></th>
	<th><?php echo $paginator->sort('user_id');?></th>
	<th><?php echo $paginator->sort('datetime');?></th>
</tr>
<?php
$i = 0;
foreach ($latestActivities as $latestActivity):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $latestActivity['LatestActivity']['sentence_id']; ?>
		</td>
		<td>
			<?php echo $latestActivity['LatestActivity']['sentence_lang']; ?>
		</td>
		<td>
			<?php echo $latestActivity['LatestActivity']['translation_id']; ?>
		</td>
		<td>
			<?php echo $latestActivity['LatestActivity']['translation_lang']; ?>
		</td>
		<td>
			<?php echo $latestActivity['LatestActivity']['text']; ?>
		</td>
		<td>
			<?php echo $latestActivity['LatestActivity']['action']; ?>
		</td>
		<td>
			<?php echo $latestActivity['LatestActivity']['user_id']; ?>
		</td>
		<td>
			<?php echo $latestActivity['LatestActivity']['datetime']; ?>
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
		<li><?php echo $html->link(__('New LatestActivity', true), array('action'=>'add')); ?></li>
	</ul>
</div>
