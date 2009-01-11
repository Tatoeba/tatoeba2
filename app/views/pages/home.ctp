<div id="homepage">
<?php 
$this->pageTitle = __('Tatoeba : Collecting example sentences',true); 

echo '<div class="element">';
echo '<h2>';
__('Welcome on Tatoeba Project');
echo '</h2>';

echo '<p style="font-size:16px">';
__('This project aims to build a multilingual corpus. In other words, to collect sentences translated in several languages.');
echo '</p>';
echo '</div>';


echo '<div class="element">';
echo '<h2>';
__('Random sentence'); 
echo '</h2>';
echo $this->element('random_sentence');
echo '</div>';


echo '<div class="element">';
echo '<h2>';
__('Latest contributions');
echo ' ';
$tooltip->displayLogsColors();
echo '</h2>';
echo $this->element('latest_contributions');
echo '</div>';


echo '<div class="element">';
echo '<h2>';
__('Latest comments');
echo '</h2>';
echo $this->element('latest_sentence_comments');
echo '</div>';
?>
</div>