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
		if ($mode == 'new') {

			?>
			<h3><?=__('Add a new conversation', true); ?></h3>
			<div style="text-align:center">
				<h4>Choose your languages</h4>
				<span id="LanguagesList">
				</span>
				<?php echo $form->select('DialogMainLanguage', $languages); ?>
				<a id="AddDialogLanguageLink" style="display:none">+ Add another language</a>
				<span id="AddDialogLanguageForm" style="display:none">
					+&nbsp;<?php echo $form->select('DialogTranslationLanguage', $languages); ?>
				</span>
			</div>
			<div id="DialogEditor">
				
			</div>
			<?php
		}
		?>

	</div>
	<div class="module">
		<h2><?=__('Last added conversations', true); ?></h2>

	</div>
</div>

<?php
?>
