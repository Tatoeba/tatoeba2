<?php
/* LangStat Fixture generated on: 2014-04-15 21:34:49 : 1397590489 */
class LanguageFixture extends CakeTestFixture {
	var $name = 'Language';
	var $table = 'langStats';

	var $fields = array(
		'lang' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 4, 'key' => 'unique', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'numberOfSentences' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 8),
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 3, 'key' => 'primary'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'lang' => array('column' => 'lang', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'MyISAM')
	);

	var $records = array(
		array(
			'lang' => 'eng',
			'numberOfSentences' => '2',
			'id' => '1',
		),
		array(
			'lang' => 'cmn',
			'numberOfSentences' => '1',
			'id' => '2',
		),
		array(
			'lang' => 'spa',
			'numberOfSentences' => '1',
			'id' => '3',
		),
		array(
			'lang' => 'fra',
			'numberOfSentences' => '2',
			'id' => '4',
		),
		array(
			'lang' => 'deu',
			'numberOfSentences' => '1',
			'id' => '5',
		),
		array(
			'lang' => 'jpn',
			'numberOfSentences' => '1',
			'id' => '6',
		),
	);
}
