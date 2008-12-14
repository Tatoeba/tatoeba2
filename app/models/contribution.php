<?php
class Contribution extends AppModel {

	var $name = 'Contribution';
	
	var $belongsTo = array('Sentence', 'User');

}
?>