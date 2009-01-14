<?php
$onlineVisitors = $this->requestAction('/visitors/online');
if (isset($this->params['lang'])) { 
	Configure::write('Config.language',  $this->params['lang']); 
}
?>

<div id="top1">
	<div class="logout">
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

	<div class="online_visitors">
	<?php
	__('Visitor(s) : ');
	echo $onlineVisitors;
	?>
	</div>
	
	<div class="language_choice">
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
</div>