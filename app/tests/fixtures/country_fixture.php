<?php
/* Country Fixture generated on: 2014-04-15 02:05:51 : 1397520351 */
class CountryFixture extends CakeTestFixture {
	var $name = 'Country';

	var $fields = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 2, 'key' => 'primary', 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'iso3' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 3, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'numcode' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 6),
		'name' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 80, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);

	var $records = array(
		array(
			'id' => 'BA',
			'iso3' => 'BIH',
			'numcode' => '70',
			'name' => 'Bosnia and Herzegovina'
		),
		array(
			'id' => 'FR',
			'iso3' => 'FRA',
			'numcode' => '250',
			'name' => 'France'
		),
		array(
			'id' => 'DE',
			'iso3' => 'DEU',
			'numcode' => '276',
			'name' => 'Germany'
		),
		array(
			'id' => 'JP',
			'iso3' => 'JPN',
			'numcode' => '392',
			'name' => 'Japan'
		),
		array(
			'id' => 'AE',
			'iso3' => 'ARE',
			'numcode' => '784',
			'name' => 'United Arab Emirates'
		),
	);
}
