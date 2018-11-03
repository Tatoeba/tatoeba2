<?php
/* ArosAco Fixture generated on: 2014-10-20 07:39:46 : 1413790786 */
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class ArosAcoFixture extends CakeTestFixture {
	public $name = 'ArosAco';

	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'aro_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'index'),
		'aco_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'index'),
		'_create' => array('type' => 'string', 'null' => false, 'default' => '0', 'length' => 2, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'_read' => array('type' => 'string', 'null' => false, 'default' => '0', 'length' => 2, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'_update' => array('type' => 'string', 'null' => false, 'default' => '0', 'length' => 2, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'_delete' => array('type' => 'string', 'null' => false, 'default' => '0', 'length' => 2, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'ARO_ACO_KEY' => array('column' => array('aro_id', 'aco_id'), 'unique' => 1), 'idx_aros_acos_aro_id_aco_id' => array('column' => array('aro_id', 'aco_id'), 'unique' => 1), 'aco_id' => array('column' => 'aco_id', 'unique' => 0)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);

	public $records = array(
		array(
			'id' => '123',
			'aro_id' => '1',
			'aco_id' => '314',
			'_create' => '1',
			'_read' => '1',
			'_update' => '1',
			'_delete' => '1'
		),
		array(
			'id' => '124',
			'aro_id' => '2',
			'aco_id' => '314',
			'_create' => '-1',
			'_read' => '-1',
			'_update' => '-1',
			'_delete' => '-1'
		),
		array(
			'id' => '125',
			'aro_id' => '2',
			'aco_id' => '358',
			'_create' => '1',
			'_read' => '1',
			'_update' => '1',
			'_delete' => '1'
		),
		array(
			'id' => '126',
			'aro_id' => '2',
			'aco_id' => '443',
			'_create' => '1',
			'_read' => '1',
			'_update' => '1',
			'_delete' => '1'
		),
		array(
			'id' => '127',
			'aro_id' => '2',
			'aco_id' => '424',
			'_create' => '1',
			'_read' => '1',
			'_update' => '1',
			'_delete' => '1'
		),
		array(
			'id' => '128',
			'aro_id' => '2',
			'aco_id' => '377',
			'_create' => '1',
			'_read' => '1',
			'_update' => '1',
			'_delete' => '1'
		),
		array(
			'id' => '129',
			'aro_id' => '2',
			'aco_id' => '460',
			'_create' => '1',
			'_read' => '1',
			'_update' => '1',
			'_delete' => '1'
		),
		array(
			'id' => '130',
			'aro_id' => '2',
			'aco_id' => '461',
			'_create' => '-1',
			'_read' => '-1',
			'_update' => '-1',
			'_delete' => '-1'
		),
		array(
			'id' => '131',
			'aro_id' => '2',
			'aco_id' => '462',
			'_create' => '-1',
			'_read' => '-1',
			'_update' => '-1',
			'_delete' => '-1'
		),
		array(
			'id' => '132',
			'aro_id' => '2',
			'aco_id' => '463',
			'_create' => '-1',
			'_read' => '-1',
			'_update' => '-1',
			'_delete' => '-1'
		),
		array(
			'id' => '133',
			'aro_id' => '2',
			'aco_id' => '498',
			'_create' => '-1',
			'_read' => '-1',
			'_update' => '-1',
			'_delete' => '-1'
		),
		array(
			'id' => '134',
			'aro_id' => '2',
			'aco_id' => '348',
			'_create' => '1',
			'_read' => '1',
			'_update' => '1',
			'_delete' => '1'
		),
		array(
			'id' => '135',
			'aro_id' => '2',
			'aco_id' => '366',
			'_create' => '1',
			'_read' => '1',
			'_update' => '1',
			'_delete' => '1'
		),
		array(
			'id' => '136',
			'aro_id' => '2',
			'aco_id' => '415',
			'_create' => '1',
			'_read' => '1',
			'_update' => '1',
			'_delete' => '1'
		),
		array(
			'id' => '137',
			'aro_id' => '2',
			'aco_id' => '489',
			'_create' => '1',
			'_read' => '1',
			'_update' => '1',
			'_delete' => '1'
		),
		array(
			'id' => '138',
			'aro_id' => '2',
			'aco_id' => '362',
			'_create' => '1',
			'_read' => '1',
			'_update' => '1',
			'_delete' => '1'
		),
		array(
			'id' => '139',
			'aro_id' => '2',
			'aco_id' => '476',
			'_create' => '1',
			'_read' => '1',
			'_update' => '1',
			'_delete' => '1'
		),
		array(
			'id' => '140',
			'aro_id' => '2',
			'aco_id' => '400',
			'_create' => '1',
			'_read' => '1',
			'_update' => '1',
			'_delete' => '1'
		),
		array(
			'id' => '141',
			'aro_id' => '3',
			'aco_id' => '314',
			'_create' => '-1',
			'_read' => '-1',
			'_update' => '-1',
			'_delete' => '-1'
		),
		array(
			'id' => '142',
			'aro_id' => '3',
			'aco_id' => '377',
			'_create' => '1',
			'_read' => '1',
			'_update' => '1',
			'_delete' => '1'
		),
		array(
			'id' => '143',
			'aro_id' => '3',
			'aco_id' => '382',
			'_create' => '1',
			'_read' => '1',
			'_update' => '1',
			'_delete' => '1'
		),
		array(
			'id' => '144',
			'aro_id' => '3',
			'aco_id' => '397',
			'_create' => '-1',
			'_read' => '-1',
			'_update' => '-1',
			'_delete' => '-1'
		),
		array(
			'id' => '145',
			'aro_id' => '3',
			'aco_id' => '443',
			'_create' => '1',
			'_read' => '1',
			'_update' => '1',
			'_delete' => '1'
		),
		array(
			'id' => '146',
			'aro_id' => '3',
			'aco_id' => '445',
			'_create' => '-1',
			'_read' => '-1',
			'_update' => '-1',
			'_delete' => '-1'
		),
		array(
			'id' => '147',
			'aro_id' => '3',
			'aco_id' => '460',
			'_create' => '1',
			'_read' => '1',
			'_update' => '1',
			'_delete' => '1'
		),
		array(
			'id' => '148',
			'aro_id' => '3',
			'aco_id' => '461',
			'_create' => '-1',
			'_read' => '-1',
			'_update' => '-1',
			'_delete' => '-1'
		),
		array(
			'id' => '149',
			'aro_id' => '3',
			'aco_id' => '462',
			'_create' => '-1',
			'_read' => '-1',
			'_update' => '-1',
			'_delete' => '-1'
		),
		array(
			'id' => '150',
			'aro_id' => '3',
			'aco_id' => '463',
			'_create' => '-1',
			'_read' => '-1',
			'_update' => '-1',
			'_delete' => '-1'
		),
		array(
			'id' => '151',
			'aro_id' => '3',
			'aco_id' => '498',
			'_create' => '-1',
			'_read' => '-1',
			'_update' => '-1',
			'_delete' => '-1'
		),
		array(
			'id' => '152',
			'aro_id' => '3',
			'aco_id' => '348',
			'_create' => '1',
			'_read' => '1',
			'_update' => '1',
			'_delete' => '1'
		),
		array(
			'id' => '153',
			'aro_id' => '3',
			'aco_id' => '366',
			'_create' => '1',
			'_read' => '1',
			'_update' => '1',
			'_delete' => '1'
		),
		array(
			'id' => '154',
			'aro_id' => '3',
			'aco_id' => '415',
			'_create' => '1',
			'_read' => '1',
			'_update' => '1',
			'_delete' => '1'
		),
		array(
			'id' => '155',
			'aro_id' => '3',
			'aco_id' => '424',
			'_create' => '1',
			'_read' => '1',
			'_update' => '1',
			'_delete' => '1'
		),
		array(
			'id' => '156',
			'aro_id' => '3',
			'aco_id' => '489',
			'_create' => '1',
			'_read' => '1',
			'_update' => '1',
			'_delete' => '1'
		),
		array(
			'id' => '157',
			'aro_id' => '3',
			'aco_id' => '362',
			'_create' => '1',
			'_read' => '1',
			'_update' => '1',
			'_delete' => '1'
		),
		array(
			'id' => '158',
			'aro_id' => '3',
			'aco_id' => '476',
			'_create' => '1',
			'_read' => '1',
			'_update' => '1',
			'_delete' => '1'
		),
		array(
			'id' => '159',
			'aro_id' => '3',
			'aco_id' => '400',
			'_create' => '1',
			'_read' => '1',
			'_update' => '1',
			'_delete' => '1'
		),
		array(
			'id' => '160',
			'aro_id' => '4',
			'aco_id' => '314',
			'_create' => '-1',
			'_read' => '-1',
			'_update' => '-1',
			'_delete' => '-1'
		),
		array(
			'id' => '161',
			'aro_id' => '4',
			'aco_id' => '377',
			'_create' => '1',
			'_read' => '1',
			'_update' => '1',
			'_delete' => '1'
		),
		array(
			'id' => '162',
			'aro_id' => '4',
			'aco_id' => '382',
			'_create' => '1',
			'_read' => '1',
			'_update' => '1',
			'_delete' => '1'
		),
		array(
			'id' => '163',
			'aro_id' => '4',
			'aco_id' => '397',
			'_create' => '-1',
			'_read' => '-1',
			'_update' => '-1',
			'_delete' => '-1'
		),
		array(
			'id' => '164',
			'aro_id' => '4',
			'aco_id' => '443',
			'_create' => '-1',
			'_read' => '-1',
			'_update' => '-1',
			'_delete' => '-1'
		),
		array(
			'id' => '165',
			'aro_id' => '4',
			'aco_id' => '460',
			'_create' => '1',
			'_read' => '1',
			'_update' => '1',
			'_delete' => '1'
		),
		array(
			'id' => '166',
			'aro_id' => '4',
			'aco_id' => '461',
			'_create' => '-1',
			'_read' => '-1',
			'_update' => '-1',
			'_delete' => '-1'
		),
		array(
			'id' => '167',
			'aro_id' => '4',
			'aco_id' => '462',
			'_create' => '-1',
			'_read' => '-1',
			'_update' => '-1',
			'_delete' => '-1'
		),
		array(
			'id' => '168',
			'aro_id' => '4',
			'aco_id' => '463',
			'_create' => '-1',
			'_read' => '-1',
			'_update' => '-1',
			'_delete' => '-1'
		),
		array(
			'id' => '169',
			'aro_id' => '4',
			'aco_id' => '498',
			'_create' => '-1',
			'_read' => '-1',
			'_update' => '-1',
			'_delete' => '-1'
		),
		array(
			'id' => '170',
			'aro_id' => '4',
			'aco_id' => '348',
			'_create' => '1',
			'_read' => '1',
			'_update' => '1',
			'_delete' => '1'
		),
		array(
			'id' => '171',
			'aro_id' => '4',
			'aco_id' => '366',
			'_create' => '1',
			'_read' => '1',
			'_update' => '1',
			'_delete' => '1'
		),
		array(
			'id' => '172',
			'aro_id' => '4',
			'aco_id' => '415',
			'_create' => '1',
			'_read' => '1',
			'_update' => '1',
			'_delete' => '1'
		),
		array(
			'id' => '173',
			'aro_id' => '4',
			'aco_id' => '424',
			'_create' => '1',
			'_read' => '1',
			'_update' => '1',
			'_delete' => '1'
		),
		array(
			'id' => '174',
			'aro_id' => '4',
			'aco_id' => '489',
			'_create' => '1',
			'_read' => '1',
			'_update' => '1',
			'_delete' => '1'
		),
		array(
			'id' => '175',
			'aro_id' => '4',
			'aco_id' => '476',
			'_create' => '1',
			'_read' => '1',
			'_update' => '1',
			'_delete' => '1'
		),
		array(
			'id' => '176',
			'aro_id' => '4',
			'aco_id' => '400',
			'_create' => '1',
			'_read' => '1',
			'_update' => '1',
			'_delete' => '1'
		),
		array(
			'id' => '177',
			'aro_id' => '6',
			'aco_id' => '314',
			'_create' => '-1',
			'_read' => '-1',
			'_update' => '-1',
			'_delete' => '-1'
		),
		array(
			'id' => '178',
			'aro_id' => '6',
			'aco_id' => '379',
			'_create' => '1',
			'_read' => '1',
			'_update' => '1',
			'_delete' => '1'
		),
		array(
			'id' => '179',
			'aro_id' => '6',
			'aco_id' => '490',
			'_create' => '1',
			'_read' => '1',
			'_update' => '1',
			'_delete' => '1'
		),
		array(
			'id' => '180',
			'aro_id' => '4',
			'aco_id' => '500',
			'_create' => '1',
			'_read' => '1',
			'_update' => '1',
			'_delete' => '1'
		),
		array(
			'id' => '181',
			'aro_id' => '3',
			'aco_id' => '500',
			'_create' => '1',
			'_read' => '1',
			'_update' => '1',
			'_delete' => '1'
		),
		array(
			'id' => '182',
			'aro_id' => '2',
			'aco_id' => '500',
			'_create' => '1',
			'_read' => '1',
			'_update' => '1',
			'_delete' => '1'
		),
		array(
			'id' => '183',
			'aro_id' => '1',
			'aco_id' => '500',
			'_create' => '1',
			'_read' => '1',
			'_update' => '1',
			'_delete' => '1'
		),
	);
}
