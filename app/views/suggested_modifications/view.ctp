<div class="suggestedModifications view">
<h2><?php  __('SuggestedModification');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Id'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $suggestedModification['SuggestedModification']['id']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Sentence'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $html->link($suggestedModification['Sentence']['id'], array('controller'=> 'sentences', 'action'=>'view', $suggestedModification['Sentence']['id'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Sentence Lang'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $suggestedModification['SuggestedModification']['sentence_lang']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Correction Text'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $suggestedModification['SuggestedModification']['correction_text']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Submit User Id'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $suggestedModification['SuggestedModification']['submit_user_id']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Submit Datetime'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $suggestedModification['SuggestedModification']['submit_datetime']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Apply User Id'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $suggestedModification['SuggestedModification']['apply_user_id']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Apply Datetime'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $suggestedModification['SuggestedModification']['apply_datetime']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Was Applied'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $suggestedModification['SuggestedModification']['was_applied']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Edit SuggestedModification', true), array('action'=>'edit', $suggestedModification['SuggestedModification']['id'])); ?> </li>
		<li><?php echo $html->link(__('Delete SuggestedModification', true), array('action'=>'delete', $suggestedModification['SuggestedModification']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $suggestedModification['SuggestedModification']['id'])); ?> </li>
		<li><?php echo $html->link(__('List SuggestedModifications', true), array('action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New SuggestedModification', true), array('action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Sentences', true), array('controller'=> 'sentences', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Sentence', true), array('controller'=> 'sentences', 'action'=>'add')); ?> </li>
	</ul>
</div>
