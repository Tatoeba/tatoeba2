<div id="foot">
<?php 
if (isset($this->params['lang'])) { 
	Configure::write('Config.language',  $this->params['lang']); 
}

echo $html->link(__('Contact me', true), array("controller" => 'pages', "action" => 'contact'));
echo ' | ';
echo $html->link(__('Tatoeba Blog',true), 'http://blog.tatoeba.org');
echo ' | ';
echo $html->link(__('Downloads',true), array("controller" => 'pages', "action" => 'download-tatoeba-example-sentences'));
echo ' | ';
echo $html->link(__('Romaji & Furigana',true), array("controller" => 'tools', "action" => 'kakasi'));
echo ' | ';
echo $html->link(__('Team & Credits', true), array("controller" => 'pages', "action" => 'tatoeba-team-and-credits'));

?>
</div>