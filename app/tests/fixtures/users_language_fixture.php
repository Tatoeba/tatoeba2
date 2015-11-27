<?php
/* UsersLanguage Fixture generated on: 2015-05-02 14:16:52 : 1430576212 */
class UsersLanguageFixture extends CakeTestFixture {
	var $name = 'UsersLanguage';

	var $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'of_user_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'index'),
		'by_user_id' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'language_code' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 4, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'level' => array('type' => 'integer', 'null' => true, 'default' => '0', 'length' => 2),
		'details' => array('type' => 'binary', 'null' => false, 'default' => NULL),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'user_lang' => array('column' => array('of_user_id', 'by_user_id', 'language_code'), 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	var $records = array(
	);
}
