<?php
/* FavoritesUser Fixture generated on: 2014-09-14 16:11:49 : 1410711109 */
class FavoritesUserFixture extends CakeTestFixture {
	public $name = 'FavoritesUser';

	public $fields = array(
		'favorite_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'user_id' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'indexes' => array('favorite_id' => array('column' => array('favorite_id', 'user_id'), 'unique' => 1)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);

	public $records = array(
		array(
			'favorite_id' => '4',
			'user_id' => '7',
		)
	);
}
