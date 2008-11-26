<?php
class SentenceComment extends AppModel{
	var $name = 'SentenceComment';
	
	var $belongsTo = array('Sentence');
}
?>