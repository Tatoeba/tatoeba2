<?php
/* SentencesList Fixture generated on: 2014-04-15 02:32:40 : 1397521960 */
class SentencesListFixture extends CakeTestFixture {
	public $name = 'SentencesList';
	public $import = array('records' => true);

	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'unsigned' => false, 'key' => 'primary'),
		'is_public' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'name' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 450, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'),
		'user_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'unsigned' => false),
		'numberOfSentences' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 8, 'unsigned' => true),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'visibility' => array('type' => 'string', 'null' => false, 'default' => 'unlisted', 'length' => 10, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'), // enum('private','unlisted','public')
		'editable_by' => array('type' => 'string', 'null' => false, 'default' => 'creator', 'length' => 10, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'), // enum('creator','anyone','no_one')
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'InnoDB')
	);

	public $records = array(
		array(
			'id' => '1',
			'name' => 'Interesting French sentences',
			'user_id' => 7,
			'numberOfSentences' => 2,
			'created' => '2014-04-15 00:54:01',
			'modified' => '2014-04-15 00:54:12',
			'visibility' => 'unlisted',
			'editable_by' => 'creator'
		)
	);
}
