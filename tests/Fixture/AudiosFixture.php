<?php
/* Audio Fixture generated on: 2016-12-13 06:51:54 : 1481611914 */
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class AudiosFixture extends TestFixture {
    public $name = 'Audio';
    public $useTable = 'audios';

    public $fields = array(
		'id' => ['type' => 'integer', 'null' => false, 'default' => null],
		'sentence_id' => ['type' => 'integer', 'null' => false, 'default' => null],
		'user_id' => ['type' => 'integer', 'null' => true, 'default' => null],
		'external' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 500, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'created' => ['type' => 'datetime', 'null' => false, 'default' => null],
        'modified' => ['type' => 'datetime', 'null' => false, 'default' => null],
        'lang' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 4, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'_constraints' => ['primary' => ['type' => 'primary', 'columns' => ['id']]],
		'_options' => ['charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB']
	);

    public $records = array(
        array(
            'id' => '1',
            'sentence_id' => '3',
            'lang' => 'spa',
            'user_id' => '4',
            'external' => NULL,
            'created' => '2014-01-20 09:23:49',
            'modified' => '2014-01-21 21:01:21'
        ),
        array(
            'id' => '2',
            'sentence_id' => '4',
            'lang' => 'fra',
            'user_id' => NULL,
            'external' => '{"username":"Philippe Petit"}',
            'created' => '2001-12-02 06:47:30',
            'modified' => '2001-12-12 06:47:30'
        ),
        array(
            'id' => '3',
            'sentence_id' => '12',
            'lang' => 'fra',
            'user_id' => NULL,
            'external' => '{"username":"Philippe Petit"}',
            'created' => '2001-12-02 06:47:30',
            'modified' => '2001-12-12 06:47:30'
        ),
    );
}
