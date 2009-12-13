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

function sortDialogByLanguage($sentences) {
	$sentences_sorted = array();
	$nb_langues = count($sentences["ConversationTitle"]);
	for ($i = 0; $i < count($sentences["ConversationTitle"]); $i++) {
		for ($j = 0; $j < (count($sentences["Sentence"])/$nb_langues); $j++) {
			$sentences_sorted[$j][$sentences["ConversationTitle"][$i]["lang"]] = $sentences["Sentence"][$i + $nb_langues*$j];
		}
	}
	
	return $sentences_sorted;
}

$languages = array(
	  'eng' => __('English', true)
	, 'jpn' => __('Japanese', true)
	, 'fra' => __('French', true)
	, 'deu' => __('German', true)
	, 'spa' => __('Spanish', true)
	, 'ita' => __('Italian', true)
	, 'ind' => __('Indonesian', true)
	, 'vie' => __('Vietnamese', true)
	, 'por' => __('Portuguese', true)
	, 'rus' => __('Russian', true)
	, 'cmn' => __('Chinese', true)
	, 'kor' => __('Korean', true)
	, 'nld' => __('Dutch', true)
);
$selectedLanguageFrom = 'eng';
$selectedLanguageTo = 'cmn';

echo $javascript->link('sentences.conversations.js', true);
echo $javascript->link('autocompletion/jquery.bgiframe.min.js', true);
echo $javascript->link('autocompletion/jquery.ajaxQueue.js', true);
echo $javascript->link('autocompletion/thickbox-compressed.js', true);
echo $javascript->link('autocompletion/jquery.autocomplete.js', true);
?>

<div id="content">
	<div class="module">
		<h2><?=__('Conversations', true); ?></h2>
		<?php
			?>
			<h3><?=__('Edit a conversation', true); ?></h3>
			<div style="text-align:center">
				<h4>Choose your languages</h4>
				<span id="LanguagesList">
					<?php
					foreach ($conversation["ConversationTitle"] as $conversation_version) {
						?>
						<span class="DialogSelectedLanguage list-box"><?php echo $languages[$conversation_version["lang"]];?><a class="closebutton"></a></span>
						<?php
					}
					?>
				</span>
				<a id="AddDialogLanguageLink">+ Add another language</a>
				<span id="AddDialogLanguageForm" style="display:none">
					+&nbsp;<?php echo $form->select('DialogTranslationLanguage', $languages); ?>
				</span>
			</div>
			<div id="DialogEditor">
			<div id="sentencesList">	
				<fieldset id="DialogTitle">
					<legend>Dialog title</legend>
					<table id="DialogTitleLanguages">
						<?php
							for ($i = 0; $i < count($conversation["ConversationTitle"]); $i++) {
								?>
								<tr>
									<td class="DialogLanguageFlag">
										<?php echo $html->image($conversation["ConversationTitle"][$i]["lang"].'.png'); ?>
									</td>
									<td>
										<?php echo $form->input('title'.$conversation["ConversationTitle"][$i]["lang"], array('label' => '', 'value' => $conversation["ConversationTitle"][$i]["title"])); ?>
									</td>
								</tr>
								<?php
							}
							?>
					</table>
				</fieldset>
				<?php
				$i = 1;
				foreach (sortDialogByLanguage($conversation) as $reply) {
					?>
					<fieldset>
						<legend>Sentence <?php echo $i;?></legend>
						<table>
							<tr>
								<?php
								$default_language = false;
								foreach ($reply as $reply_version) {
									if (!$default_language) {
										?>
										<td class="speaker"><?php echo $form->input('speaker'.$i, array('label' => __('Speaker', true), "class" => "SpeakerInput", "value" => $reply_version["ConversationsSentence"]["speaker"])); ?></td>
										<td>
											<table id="DialogSentenceLanguages_<?php echo $i;?>" class="DialogSentenceLanguages">
										<?php
										$default_language = $reply_version["lang"];
									}
										?>
									<tr>
										<td class="DialogLanguageFlag">
											<?php echo $html->image($reply_version["lang"].'.png'); ?>
										</td>
										<td class="DialogReplyContent">
											<?php echo $form->input('content'.$reply_version["lang"].$i, array('label' => '', 'class' => 'content', 'value' => $reply_version["text"]));?>
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
					<?php
					$i++;
				}
				?>
			</div>
			</div>
	</div>
</div>

<?php
?>
