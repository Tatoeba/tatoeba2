<?php
pr($favorites);
if(count($favorites['Favorite']) > 0){
	echo '<ul>';
	foreach($favorites['Favorite'] as $favorite){
		echo '<li>'.$favorite['text'].'</li>';
	}
	echo '<ul>';
}else{
	__('None');
}

?>
