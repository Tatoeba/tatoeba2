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
?>
<fieldset>
	<legend>Sentence <?php echo $order; ?></legend>
	<table>
		<tr>
		<td class="speaker"><?php echo $form->input('speaker'.$order, array('label' => __('Speaker', true), "class" => "SpeakerInput")); ?></td>
		<td>
			<table id="DialogSentenceLanguages_<?php echo $order; ?>" class="DialogSentenceLanguages">
				<?php
				$dialog_languages_tab = explode(";", $dialog_languages);
				foreach ($dialog_languages_tab as $dialog_language) {
					?>
					<tr>
						<td class="DialogLanguageFlag">
							<?php echo $html->image($dialog_language.'.png'); ?>
						</td>
						<td>
						<?php
							echo $form->input('ConversationContent'.$dialog_language, array('label' => '', 'class' => 'content'));
						?>
						</td>
					</tr>
					<?php
				}
				?>
			</table>
		</td>
		</tr>
	</table>
</fieldset>