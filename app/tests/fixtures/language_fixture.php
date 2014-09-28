<?php
/* Language Fixture generated on: 2014-09-14 16:11:51 : 1410711111 */
class LanguageFixture extends CakeTestFixture {
	var $name = 'Language';

	var $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 3, 'key' => 'primary'),
		'code' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 4, 'key' => 'unique', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'numberOfSentences' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 8),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'lang' => array('column' => 'code', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'MyISAM')
	);

	var $records = array(
		array(
			'code' => 'eng',
			'numberOfSentences' => '2',
			'id' => '1',
		),
		array(
			'code' => 'cmn',
			'numberOfSentences' => '1',
			'id' => '2',
		),
		array(
			'code' => 'spa',
			'numberOfSentences' => '1',
			'id' => '3',
		),
		array(
			'code' => 'fra',
			'numberOfSentences' => '2',
			'id' => '4',
		),
		array(
			'code' => 'deu',
			'numberOfSentences' => '1',
			'id' => '5',
		),
		array(
			'code' => 'jpn',
			'numberOfSentences' => '1',
			'id' => '6',
		),
	);
}
