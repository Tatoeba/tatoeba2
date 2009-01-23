<?php
if (isset($this->params['lang'])) { 
	Configure::write('Config.language',  $this->params['lang']); 
}
?>

<div id="menu">
<ul>
<?php
$lang = (isset($this->params['lang']))? $this->params['lang'].'/' : '';

// array containing the elements of the menu : $title => $route
$menuElements = array(
	 __('Home',true) 		=> '/'.$lang
	,__('Browse',true) 		=> array("controller" => "sentences", "action" => "show", "random")
	,__('Search',true) 		=> array("controller" => "sentences", "action" => "search")
	,__('Contribute',true) 	=> array("controller" => "sentences", "action" => "contribute")
	// ,__('Logs',true) 		=> array("controller" => "contributions", "action" => "index")
	,__('Comments',true) 	=> array("controller" => "sentence_comments", "action" => "index")
	// ,__('Statistics',true) 	=> array("controller" => "users_statistics")
	,__('Members',true)		=> array("controller" => "users", "action" => "all")
);

// displaying the menu
foreach($menuElements as $title => $route){
	$cssClass = '';
	
	// Checking if we should apply the "current" CSS class to the <li> element
	if(is_array($route)){ // categories other than Home
		if(isset($this->params['pass'][0]) AND isset($route['action']) AND $this->params['pass'][0] == $route['action']){
			$cssClass = 'class="current"';
		}elseif($this->params['controller'] == $route['controller']){
			if(isset($route['action'])){
				if($this->params['action'] == $route['action']){
					$cssClass = 'class="current"';
				}
			}else{
				if($this->params['action'] == 'index'){
					$cssClass = 'class="current"';
				}
			}
		}
	}else{ // Home
		if(isset($this->params['pass'][0]) AND $this->params['pass'][0] == 'home'){
			$cssClass = 'class="current"';
		}
	}
	
	// displaying <li> element
	echo '<li '.$cssClass.'>';
	echo $html->link($title, $route);
	echo '</li>';
}
?>
</ul>
</div>