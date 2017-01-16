<?php
/* ReindexFlag Fixture generated on: 2015-11-27 23:54:27 : 1448668467 */
class ReindexFlagFixture extends CakeTestFixture {
	var $name = 'ReindexFlag';

	var $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'sentence_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'index'),
		'lang_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 3),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'idx_sentence_id' => array('column' => 'sentence_id', 'unique' => 0)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'InnoDB')
	);

	var $records = array(
	);
}
