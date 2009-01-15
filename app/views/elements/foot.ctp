<div id="foot">
<?php 
if (isset($this->params['lang'])) { 
	Configure::write('Config.language',  $this->params['lang']); 
}
/*
	$html->image('cake.power.gif', array('alt'=> __("CakePHP: the rapid development php framework", true), 'border'=>"0")),
	'http://www.cakephp.org/',
	array('target'=>'_new'), null, false
);
*/
echo $html->link(__('Contact me', true), array("controller" => 'pages', "action" => 'contact'));
echo ' | ';
echo $html->link(__('Tatoeba Blog',true), 'http://blog.tatoeba.org');
?>
</div>