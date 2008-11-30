<div id="top1">
	<div>Visitor(s) : xx</div>
	
	<div>
	Language : 
	<?php
	$languages = array(
		'eng' => 'English', 
		'fre' => 'Français', 
		'spa' => 'Español', 
		'jpn' => '日本語',
		'deu' => 'Deutsch',
		'ita' => 'Italiano'
	);
	
	foreach($languages as $code => $language){
		$path  = '/'.$code.'/';
		$path .= ($this->params['controller'] == 'pages') ? '' : $this->params['controller'].'/';
		$path .= ($this->params['action'] == 'display') ? '' : $this->params['action'].'/';
		// probably not the best way to do it but never mind
		
		echo $html->link( __($language,true), $path);
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