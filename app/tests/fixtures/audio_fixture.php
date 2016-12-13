<?php
/* Audio Fixture generated on: 2016-12-13 06:51:54 : 1481611914 */
class AudioFixture extends CakeTestFixture {
    var $name = 'Audio';

    var $fields = array(
        'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
        'sentence_id' => array('type' => 'integer', 'null' => false, 'default' => NULL),
        'user_id' => array('type' => 'integer', 'null' => true, 'default' => NULL),
        'author' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 50, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
        'licence_id' => array('type' => 'integer', 'null' => false, 'default' => NULL),
        'created' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
        'modified' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
        'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
        'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
    );

    var $records = array(
        array(
            'id' => '1',
            'sentence_id' => '3',
            'user_id' => '4',
            'licence_id' => 1,
            'author' => NULL,
            'created' => '2014-01-20 09:23:49',
            'modified' => '2014-01-21 21:01:21'
        ),
        array(
            'id' => '2',
            'sentence_id' => '4',
            'user_id' => NULL,
            'licence_id' => 2,
            'author' => 'Philippe Petit',
            'created' => '2001-12-02 06:47:30',
            'modified' => '2001-12-12 06:47:30'
        ),
    );
}
