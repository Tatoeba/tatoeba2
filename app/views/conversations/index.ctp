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

?>

<div id="main_content">
	<div class="module">
		<h2><?php echo __('Conversations', true); ?></h2>
		<?php echo $html->link(__('Add a new conversation', true), array('controller' => 'conversations', 'action' => 'edit', 'new'));?>
	</div>
	<div class="module">
		<h2><?php echo __('Last added conversations', true); ?></h2>
		<?php
		if (count($conversations) > 0) {
			?>
			<dl class="ConversationTitle">
				<?php
				foreach ($conversations as $conversation) {
					?>
					<dt>
						<?php echo $html->link($conversation["ConversationTitle"][0]["title"], array("controller"=>"conversations", "action" => "show", $conversation["Conversation"]["id"]), array("class" => "ConversationLink")); ?>
						<span class="author"><span> - added by </span>
						<?php echo $html->link($conversation["User"]["username"], array("controller"=>"users", "action" => "show", $conversation["User"]["id"]), array("class" => "author")); ?></span>
					</dt>
					<dd>Available languages : 
						<?php
						$languages = array();
						foreach ($conversation["ConversationTitle"] as $title_version) {
							$languages[] = $title_version["lang"];
							echo $html->image($title_version["lang"].'.png');
						}
						?>
					</dd>
					<?php
					
				}
				?>
			</dl>
			<?php
		} else {
			?>
			<p>No dialog added</p>
			<?php
		}
		?>
	</div>
</div>

<?php
//pr($conversations);
?>
