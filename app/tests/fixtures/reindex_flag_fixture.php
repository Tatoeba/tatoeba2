<?php
/* ReindexFlag Fixture generated on: 2015-06-25 03:46:16 : 1435203976 */
class ReindexFlagFixture extends CakeTestFixture {
	var $name = 'ReindexFlag';

	var $fields = array(
		'sentence_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'lang_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 3),
		'indexes' => array('PRIMARY' => array('column' => 'sentence_id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'InnoDB')
	);

	var $records = array(
	);
}
