<?php
/* SentenceAnnotation Fixture generated on: 2014-09-14 16:11:53 : 1410711113 */
class SentenceAnnotationFixture extends CakeTestFixture {
	var $name = 'SentenceAnnotation';

	var $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'sentence_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'index'),
		'meaning_id' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'text' => array('type' => 'text', 'null' => false, 'default' => NULL, 'length' => 2000),
		'modified' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'user_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'sentence_id' => array('column' => 'sentence_id', 'unique' => 0)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	var $records = array(
		/* Sentence annotations are yet to be tested (and it's not used on tatoeba.org) */
	);
}
