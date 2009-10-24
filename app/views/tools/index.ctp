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

$javascript->link('furigana', false);
$this->pageTitle = __('Convert Japanese text into romaji or furigana',true);
?>
<div class="content">
	<div class="module">
	<?php
	echo '<h2>';
	__('Convert Japanese text into romaji or furigana (powered by <a target="_blank" href="http://kakasi.namazu.org/">KAKASI</a>)');
	echo '</h2>';
	
	$query = isset($_GET['query']) ? $_GET['query'] : '';
	$type = isset($_GET['type']) ? $_GET['type'] : 'romaji';
	
	if($query != ''){
		echo '<div id="conversion">';
		$kakasi->convert($query, $type);
		echo '</div>';
	}
	
	echo $form->create('Tool', array("action" => "kakasi", "type" => "get"));
	echo $form->textarea('query', array("label" => '', "value" => $query));
	echo '<p>';
	__('Convert japanese text into : ');
	echo $form->radio(
		'type', 
		array('romaji' => 'romaji', 'furigana' => 'furigana'), 
		array('value' => $type, 'legend' => '')
	);
	echo '</p>';
	echo $form->end(__('Convert',true));
	?>
	
	<script>
	furigana();
	</script>
	</div>
</div>
