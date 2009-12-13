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

?>

<div id="annexe_content">
	<div class="module">
		<h2>About this module</h2>
		<p>Author : <?php echo $html->link($conversation["User"]["username"], array("controller"=>"users", "action" => "show", $conversation["User"]["id"]), array("class" => "author")); ?></p>
	</div>
	<?php
		if ($session->read('Auth.User.id') == $conversation["User"]["id"]) {
			?>
			<div class="module">
				<ul>
					<li><?php echo $html->link(__('Edit content', true), array('controller' => 'conversations', 'action' => 'edit', $conversation["Conversation"]["id"]));?></li>
					<li><a href="">Delete</a></li>
				</ul>
			</div>
			<?php
		}
	?>
</div>
<div id="main_content">
	<div class="module">
		<h2>Dialogs</h2>
		<div id="Dialog">
			<h3 class="TitleMainLanguage"><?php echo $html->image($conversation["ConversationTitle"][0]["lang"].'.png'); ?> <?php echo $conversation["ConversationTitle"][0]["title"]; ?></h3>
			<?php
			for ($i = 1; $i < count($conversation["ConversationTitle"]); $i++) {
				?>
				<h3 class="TitleOtherLanguage"><?php echo $html->image($conversation["ConversationTitle"][$i]["lang"].'.png'); ?> <?php echo $conversation["ConversationTitle"][$i]["title"]; ?></h3>
				<?php
			}
			?>
			<div id="DialogContent">
			<?php
			foreach (sortDialogByLanguage($conversation) as $reply) {
				?>
				<div class="DialogReplyContainer">
				<table class="DialogReply">
					<tr>
						<?php
						$default_language = false;
						foreach ($reply as $reply_version) {
							if (!$default_language) {
								?>
								<td class="speaker"><?php echo $reply_version["ConversationsSentence"]["speaker"]; ?></td>
								<td>
									<table class="DialogSentenceLanguages">
								<?php
								$default_language = $reply_version["lang"];
							}
								?>
							<tr>
								<td class="DialogLanguageFlag">
									<?php echo $html->image($reply_version["lang"].'.png'); ?>
								</td>
								<td class="DialogReplyContent">
									<?php echo $html->link($reply_version["text"], array('controller' => 'sentences', 'action' => 'show', $reply_version["id"]));?>
								</td>
							</tr>
							<?php
						}
								?>
							</table>
						</td>
					</tr>
				</table>
				</div>
				<?php
			}
			?>
			</div>
		</div>
	</div>
</div>

<?php
//pr($conversations);
?>
