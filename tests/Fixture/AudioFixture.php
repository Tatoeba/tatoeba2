<?php
/* Audio Fixture generated on: 2016-12-13 06:51:54 : 1481611914 */
namespace App\Test\Fixture;

class AudioFixture extends CakeTestFixture {
    public $name = 'Audio';

    public $fields = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
        'sentence_id' => array('type' => 'integer', 'null' => false, 'default' => NULL),
        'user_id' => array('type' => 'integer', 'null' => true, 'default' => NULL),
        'external' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 500, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'created' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
        'modified' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
        'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    public $records = array(
        array(
            'id' => '1',
            'sentence_id' => '3',
            'user_id' => '4',
            'external' => NULL,
            'created' => '2014-01-20 09:23:49',
            'modified' => '2014-01-21 21:01:21'
        ),
        array(
            'id' => '2',
            'sentence_id' => '4',
            'user_id' => NULL,
            'external' => '{username:"Philippe Petit"}',
            'created' => '2001-12-02 06:47:30',
            'modified' => '2001-12-12 06:47:30'
        ),
        array(
            'id' => '3',
            'sentence_id' => '12',
            'user_id' => NULL,
            'external' => '{username:"Philippe Petit"}',
            'created' => '2001-12-02 06:47:30',
            'modified' => '2001-12-12 06:47:30'
        ),
    );
}
