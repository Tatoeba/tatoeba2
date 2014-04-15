<?php
/* FavoritesUser Fixture generated on: 2014-04-15 17:46:03 : 1397576763 */
class FavoritesUserFixture extends CakeTestFixture {
	var $name = 'FavoritesUser';

	var $fields = array(
		'favorite_id' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'user_id' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'indexes' => array('favorite_id' => array('column' => array('favorite_id', 'user_id'), 'unique' => 1)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);

	var $records = array(
		array(
			'favorite_id' => '4',
			'user_id' => '7',
		)
	);
}
