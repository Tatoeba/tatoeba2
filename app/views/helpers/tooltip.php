<?php
/*
    Tatoeba Project, free collaborativ creation of languages corpuses project
    Copyright (C) 2009  TATOEBA Project(should be changed)

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
class TooltipHelper extends AppHelper {
	var $helpers = array('Html');
	
	function display($text){
		echo '<span class="info" href="#">';
		echo $this->Html->image(
			'attention_with_cursor.png', 
			array('alt'=> '!')
		);
		echo '<span>'.$text.'</span>';
		echo '</span>';
	}
	
	function displayWarningDiv($preview, $text){
		echo '<div class="warning">';
		echo $this->Html->image(
			'warning.png', 
			array('alt'=> '!')
		);
		echo '<strong>';
		echo $preview;
		echo '</strong>';
		echo '<span>'.$text.'</span>';
		echo '</div>';
	}
	
	function displayWarning($text){
		echo '<span class="info" href="#">';
		echo $this->Html->image(
			'warning_with_cursor.png', 
			array('alt'=> '!')
		);
		echo '<span>'.$text.'</span>';
		echo '</span>';
	}
	
	function displayLogsColors(){
		$tooltipText  = __('Meaning of the colors :',true);
		$tooltipText .= '<table id="logsLegend">';
		$tooltipText .= '<tr>';
		$tooltipText .= '<td class="sentenceAdded">' . __('sentence added',true) .'</td>';
		//$tooltipText .= '<td class="linkAdded">' . __('link added',true) .'</td>';
		$tooltipText .= '</tr>';

		$tooltipText .= '<tr>';
		$tooltipText .= '<td class="sentenceDeleted">' . __('sentence deleted',true) . '</td>';
		//$tooltipText .= '<td class="linkDeleted">' . __('link deleted',true) . '</td>';
		$tooltipText .= '</tr>';
		
		$tooltipText .= '<tr>';
		$tooltipText .= '<td class="sentenceModified">' . __('sentence modified',true) . '</td>';
		//$tooltipText .= '<td class="correctionSuggested">' . __('correction suggested',true) . '</td>';
		$tooltipText .= '</tr>';
		
		$tooltipText .= '</table>';
		$this->display($tooltipText);
	}
	
	function displayAdoptTooltip(){
		$tooltipMsg  = '<p>';
		$tooltipMsg .= __('Whenever you add a sentence or a translation, <strong>you</strong> are the creator of the sentence. ', true);
		$tooltipMsg .= __('Other "normal" users cannot modify the sentences that <strong>you</strong> have added. ',true);
		$tooltipMsg .= __('But there are about <strong>300,000 sentences</strong> (which is a lot) that have been added by <strong>"no one"</strong> and these orphan sentences <strong>need a parent</strong>. ',true);
		$tooltipMsg .= __('Adopting sort of makes you the creator of the sentence (in other words, you can - but don\'t have to - modify it after the adoption).',true);
		$tooltipMsg .= '</p>';
		
		$tooltipMsg .= '<p>';
		$tooltipMsg .= __('The goal is to find a <strong>parent for each sentence</strong> in Tatoeba, which is an important step towards <strong>improving the quality</strong> of the corpus. ',true);
		$tooltipMsg .= __('It is therefore preferable that you adopt a sentence that you can understand because in case it has a mistake, you will be able to correct it.',true);
		$tooltipMsg .= '</p>';
		$tooltipMsg .= __('You can read more about it here : <a href="http://blog.tatoeba.org/2009/01/new-validation-system.html">in English</a>, or <a href="http://blog.tatoeba.org/2009/01/nouveau-systme-de-validation.html">in French</a>.',true);
		
		$this->displayWarningDiv(__('Get involved, adopt sentences!',true), $tooltipMsg);
	}
	
	function displayIndirectTranslationTooltip(){
		$tooltipMsg  = '<p>';
		$tooltipMsg .= __('Let\'s suppose you translate an English sentence into French, and someone translates your French translation into Spanish. In short : English ==> French ==> Spanish.', true);
		$tooltipMsg .= '<br/>';
		$tooltipMsg .= __('The <strong>French</strong> sentence is a <strong>direct</strong> translation of the English sentence. The <strong>Spanish</strong> sentence is an <strong>indirect</strong> translation of the English sentence.', true);
		$tooltipMsg .= '</p>';
		
		$tooltipMsg .= '<p>';
		$tooltipMsg .= __('There is currently no way to "upgrade" an indirect translation into a direct translation. Also, note that for performance reasons, indirect translations are not always displayed, but only when you are viewing or translating a sentence.',true);
		$tooltipMsg .= '</p>';
		
		$this->displayWarningDiv(__('Green : direct translation / Orange : indirect translation',true), $tooltipMsg);
	}
	
	function displayMainSentenceTooltip(){
		$tooltipText  = __('Your translation should be <strong>based on the main sentence</strong> (the one at the <strong>top</strong>).',true);
		$tooltipText .= '<br/>';
		$tooltipText .= __('The other sentences <strong>below</strong> are displayed only so you know what <strong>translations</strong> have already been added.',true);
		$tooltipText .= '<br/>';
		$tooltipText .= __('If you understand better one of the translations, you can <strong>click</strong> on it to make it as the <strong>main sentence</strong>, and translate from there.', true);
		$this->displayWarning($tooltipText);
	}
}
?>
