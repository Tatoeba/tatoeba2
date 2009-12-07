<?php
/*
    Tatoeba Project, free collaborative creation of multilingual corpuses project
    Copyright (C) 2009  HO Ngoc Phuong Trang (tranglich@gmail.com)

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Affero General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

/*
echo $form->inputs(
	array(
		'legend' => 'Sentence '.$order,
		'speaker'.$order => array('label' => __('Speaker', true).' : ', 'class' => 'speaker'),
		'content_from'.$order => array('label' =>'Content ()', 'class' => 'content_from'),
		'content_to'.$order => array('label' =>'Content ()', 'class' => 'content_to')));
*/	

echo $javascript->link('sentences.conversations.js', true);
echo $form->create('Conversation');
?>
<div id="sentencesList">
	<fieldset id="DialogTitle">
		<legend>Dialog title</legend>
		<table id="DialogTitleLanguages">
			<tr>
				<td class="DialogLanguageFlag">
					<?php echo $html->image($main_language.'.png'); ?>
				</td>
				<td>
					<?php echo $form->input('title'.$main_language, array('label' => '')); ?>
				</td>
			</tr>
		</table>
	</fieldset>
	<fieldset>
		<legend>Sentence 1</legend>
		<table>
			<tr>
			<td class="speaker"><?php echo $form->input('speaker1', array('label' => __('Speaker', true), "class" => "SpeakerInput")); ?></td>
			<td>
				<table id="DialogSentenceLanguages_1" class="DialogSentenceLanguages">
					<tr>
						<td class="DialogLanguageFlag">
							<?php echo $html->image($main_language.'.png'); ?>
						</td>
						<td>
						<?php
							echo $form->input('content'.$main_language.'1', array('label' => '', 'class' => 'content'));
						?>
						</td>
					</tr>
				</table>
			</td>
			</tr>
		</table>
	</fieldset>
	
	<fieldset>
		<legend>Sentence 2</legend>
		<table>
			<tr>
			<td class="speaker"><?php echo $form->input('speaker2', array('label' => __('Speaker', true), "class" => "SpeakerInput")); ?></td>
			<td>
				<table id="DialogSentenceLanguages_2" class="DialogSentenceLanguages">
					<tr>
						<td class="DialogLanguageFlag">
							<?php echo $html->image($main_language.'.png'); ?>
						</td>
						<td>
						<?php
							echo $form->input('content'.$main_language.'2', array('label' => '', 'class' => 'content'));
						?>
						</td>
					</tr>
				</table>
			</td>
			</tr>
		</table>
	</fieldset>
</div>
<?php

echo $form->hidden('languages', array('value' => $main_language));
echo $form->hidden('nb_replies', array('value' => 2));
?>
<a id="addNewReply"><?=__('Add a new reply', true); ?></a>
<?php
echo $form->end(__('Save this conversation', true));
?>