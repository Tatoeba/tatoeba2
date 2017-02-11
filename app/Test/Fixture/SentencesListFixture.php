<?php
/* SentencesList Fixture generated on: 2014-04-15 02:32:40 : 1397521960 */
class SentencesListFixture extends CakeTestFixture {
	public $name = 'SentencesList';
	public $import = array('records' => true);

	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'is_public' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'name' => array('type' => 'text', 'null' => false, 'default' => NULL, 'length' => 450),
		'user_id' => array('type' => 'integer', 'null' => true, 'default' => NULL),
		'numberOfSentences' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 8),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);

	public $records = array(
		array(
			'id' => '1',
			'is_public' => '1',
			'name' => 'Interesting French sentences',
			'user_id' => 7,
			'numberOfSentences' => 2,
			'created' => '2014-04-15 00:54:01',
			'modified' => '2014-04-15 00:54:12',
		)
	);
}
