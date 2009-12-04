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
			if(isset($mostFrequentWords) AND count($mostFrequentWords) > 0 AND $resultsInfo['sentencesCount'] > 0){
				?>
				<h2><?=__('Most frequent words in the target language'); ?></h2>
				<div id="mostFrequentWords">
					<?php
					foreach($mostFrequentWords as $word){
						echo '<span style="font-size:'.$word['fontSize'].'%" title="'.$word['details'].'">';
						echo $word['word'];
						echo '</span> ';
					}
					?>
				</div>
				<?php
			}
		?>
	</div>
	<div class="module">
		<h2>Tips for optimizing results</h2>
	</div>
	<div class="module">
		<h2>Add as a new sentence</h2>

	</div>
</div>
<div id="main_content">
	<div class="module">
	<?php
	if(isset($query)){
		$query = stripslashes($query);

		echo '<h2>Search : ' . htmlentities($query, ENT_QUOTES, 'UTF-8') . ', <em>' . $resultsInfo['sentencesCount'] . ' result(s)</em></h2>';

		if(isset($results)){
			$pagination->displaySearchPagination($resultsInfo['pagesCount'], $resultsInfo['currentPage'], $query, $from, $to);

			foreach($results as $index=>$sentence){
				echo '<div class="sentences_set searchResult">';
				// sentence menu (translate, edit, comment, etc)
				$specialOptions[$index]['belongsTo'] = $sentence['User']['username']; // TODO set up a better mechanism
				$sentences->displayMenu($sentence['Sentence']['id'], $sentence['Sentence']['lang'], $specialOptions[$index], $scores[$index]);

				// sentence and translations
				$sentence['User']['canEdit'] = $specialOptions[$index]['canEdit']; // TODO set up a better mechanism
				$sentences->displayGroup($sentence['Sentence'], $sentence['Translation'], $sentence['User']);
				echo '</div>';
			}

			$pagination->displaySearchPagination($resultsInfo['pagesCount'], $resultsInfo['currentPage'], $query, $from, $to);

		}else{
			__('No results for this search');
		}
	}
	?>
	</div>
</div>

