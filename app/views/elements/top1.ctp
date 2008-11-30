<div id="top1">
	<div>Visitor(s) : xx</div>
	
	<div>
	Language : 
	<?php
	$languages = array(
		'eng' => 'English', 
		'fre' => 'Français', 
		'spa' => 'Español', 
		'jap' => '日本語',
		'deu' => 'Deutsch',
		'ita' => 'Italiano'
	);
	
	foreach($languages as $code => $language){
		echo $html->link(
			__($language,true),
			'/'.$code.'/'.$this->params['controller'].'/'.$this->params['action'] // probably not the best way to do it but never mind
		);
		echo ' | ';
	}
	?>
	</div>
	
	<div>
	<?php
	// Log out link
	if($session->read('Auth.User.id')){
		echo $html->link(
			__('Log out',true),
			array(
				"controller" => "users",
				"action" => "logout"
			)
		);
	}
	?>
	</div>
</div>