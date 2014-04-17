<?php
/* Tag Fixture generated on: 2014-04-15 17:53:42 : 1397577222 */
class TagFixture extends CakeTestFixture {
	var $name = 'Tag';

	var $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'internal_name' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 50, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'name' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 50, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'description' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 500, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'user_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'key' => 'index'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'nbrOfSentences' => array('type' => 'integer', 'null' => false, 'default' => '0', 'key' => 'index'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'user_id' => array('column' => 'user_id', 'unique' => 0), 'nbr_sentences_idx' => array('column' => 'nbrOfSentences', 'unique' => 0)),
	);

	var $records = array(
		array(
			'id' => '1',
			'internal_name' => '@needs_native_check',
			'name' => '@needs native check',
			'description' => 'Sentences we wish to be checked by a native.',
			'user_id' => '2',
			'created' => '2013-04-14 13:10:02',
			'nbrOfSentences' => '1',
		),
	);
}
