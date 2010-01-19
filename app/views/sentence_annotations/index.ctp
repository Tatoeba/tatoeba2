<?php
/*
    Tatoeba Project, free collaborative creation of multilingual corpuses project
    Copyright (C) 2009  HO Ngoc Phuong Trang <tranglich@gmail.com>

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

<div id="annexe_content">
	<div class="module">
	<?php
		echo '<h2>Go to...</h2>';
		
		echo $form->create('SentenceAnnotation', array("action" => "show"));
		echo $form->input('sentence_id', array("label" => "Sentence nº"));
		echo $form->end('OK');
	?>
	</div>

	<div class="module">
	<?php
		echo '<h2>Search</h2>';
		
		echo $form->create('SentenceAnnotation', array("action" => "search"));
		echo $form->input('text', array("label" => ""));
		echo $form->end('OK');
	?>
	</div>	

	
	<div class="module">
		<h2>Tips</h2>
		<p>You can use the <strong>Go to</strong> feature to check the text behing a certain id. The id you enter doesn't have to be the id of a Japanese sentence.</p>
		<p>The id of a sentence is indicated its info page (which is basically a page in the "Browse" section of Tatoeba). To get to the info page, just click on the sentence itself. You can also just look at the link that the sentence refers to. The last number represents the id of the sentence.</p>
	</div>
</div>

<div id="main_content">
	<div class="module">
	<h2>How to edit the indices</h2>
	<p>To edit the indices of a sentence, you have to enter the id of that sentence in the <strong>Go to...</strong> section. It will then display the sentence and its indices.</p>
	<p>You will actually be able to edit two fields : the <strong>index</strong> and the id of the <strong>meaning</strong>. The reason why there's a <strong>Meaning</strong> field is because an index is associated to a pair of sentences. More specifically, the <strong>Meaning</strong> field refers to the id of the <strong>English sentence</strong>. Normally, you don't need to edit it.</p>
	</div>
	
	<div class="module">
	<h2>How to add a new index</h2>
	<p>On the page to edit the indices, you will also see a section called <strong>Add new index</strong>.</p>
	<p>You just have to enter your index in the textarea, the id of the associated English sentence in the "Meaning" field, and click "save".</p>
	</div>
	
	<div class="module">
	<h2>How to delete an index</h2>
	<p>It is also possible to delete the index of a sentence. On the right of the index textarea you will see a "delete" link. Clicking on it will prompt a dialog box that asks you to confirm that you want to delete the index.</p>
	</div>
</div>