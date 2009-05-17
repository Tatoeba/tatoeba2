<?php
if(count($followers['Follower']) > 0){
	echo '<ul>';
	foreach($followers['Follower'] as $follower){
		echo '<li>'.$follower['username'].'</li>';
	}
	echo '<ul>';
}else{
	__('None');
}
?>