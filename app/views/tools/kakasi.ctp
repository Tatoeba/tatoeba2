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

<div id="annexe_content">
	<div class="module">
	<h2><?php __('Credits'); ?></h2>
	<p>
	<?php
	echo sprintf(__('This tool is powered by <a href="%s">KAKASI</a> (slightly modified).',true), 'http://kakasi.namazu.org/');
	?>
	</p>
	</div>
	
	<div class="module">
	<h2><?php __('Improvements'); ?></h2>
	<p>
	<?php
	__('The result you will get will not always be perfect. However, you can help us improve this by telling us the mistakes you saw. We will try, if possible, to correct it.');
	?>
	</p>
	<p class="more_link"><?php echo $html->link(__('Feedback',true), array("controller"=>"pages", "action"=>"contact")); ?></p>
	</div>
	
	<div class="module">
	<h2><?php __('More information'); ?></h2>
	<ul>
		<li>
            <a href="http://blog.tatoeba.org/2009/02/tools-for-japanese-romanization.html">
                <?php __('Tools for Japanese romanization') ?>
            </a>
        </li>
		<li>
            <a href="http://blog.tatoeba.org/2009/02/rules-for-romaji.html">
               <?php __(' Rules for romaji in Tatoeba') ?>
            </a>
        </li>
	</ul>
	</div>
</div>

<div id="main_content">
	<div class="module">
	<?php
	echo '<h2>';
	__('Convert Japanese text into romaji or furigana');
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
		array(
            'romaji' => __('romaji',true), 
            'furigana' => __('furigana',true)
        ), 
		array('value' => $type, 'legend' => '')
	);
	echo '</p>';
	echo $form->end(__('Convert',true));
	?>
	</div>
</div>
<script>
furigana();
</script>
