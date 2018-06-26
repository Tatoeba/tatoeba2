<?php
/* Sentence Fixture generated on: 2014-10-26 16:46:13 : 1414341973 */
class SentenceFixture extends CakeTestFixture {
	public $name = 'Sentence';

	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'lang' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 4, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'text' => array('type' => 'text', 'null' => false, 'default' => NULL, 'length' => 1500),
		'correctness' => array('type' => 'integer', 'null' => false, 'default' => '0', 'length' => 2),
		'user_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'key' => 'index'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL, 'key' => 'index'),
		'dico_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'key' => 'index'),
		'script' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 4, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'hash' => array('type' => 'binary', 'null' => false, 'default' => NULL, 'length' => 16, 'key' => 'index'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'user_id' => array('column' => 'user_id', 'unique' => 0), 'dico_id' => array('column' => 'dico_id', 'unique' => 0), 'lang' => array('column' => 'lang', 'unique' => 0), 'modified_idx' => array('column' => 'modified', 'unique' => 0), 'hash' => array('column' => 'hash', 'unique' => 0)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public function init() {
		parent::init();
		$dbo = ConnectionManager::getDataSource($this->Schema->connection);
		$dbo->columns['binary'] = array('name' => 'binary');
	}

	public $records = array(
		array(
			'id' => '1',
			'lang' => 'eng',
			'text' => 'The fundamental cause of the problem is that in the modern world, idiots are full of confidence, while the intelligent are full of doubt.',
			'correctness' => NULL,
			'user_id' => '7',
			'created' => '2014-04-15 00:32:08',
			'modified' => '2014-04-15 00:32:08',
			'dico_id' => NULL,
			'script' => null
		),
		array(
			'id' => '2',
			'lang' => 'cmn',
			'text' => '问题的根源是，在当今世界，愚人充满了自信，而智者充满了怀疑。',
			'correctness' => NULL,
			'user_id' => '7',
			'created' => '2014-04-15 00:32:43',
			'modified' => '2014-04-15 00:32:43',
			'dico_id' => NULL,
			'script' => 'Hans'
		),
		array(
			'id' => '3',
			'lang' => 'spa',
			'text' => 'La causa fundamental del problema es que en el mundo moderno, los idiotas están llenos de confianza, mientras que los inteligentes están llenos de dudas.',
			'correctness' => NULL,
			'user_id' => '7',
			'created' => '2014-04-15 00:33:18',
			'modified' => '2014-04-15 00:33:18',
			'dico_id' => NULL,
			'script' => null
		),
		array(
			'id' => '4',
			'lang' => 'fra',
			'text' => 'La cause fondamentale du problème est que dans le monde moderne, les imbéciles sont plein d\'assurance, alors que les gens intelligents sont pleins de doute.',
			'correctness' => NULL,
			'user_id' => '7',
			'created' => '2014-04-15 00:34:28',
			'modified' => '2014-04-15 00:34:28',
			'dico_id' => NULL,
		),
		array(
			'id' => '5',
			'lang' => 'deu',
			'text' => 'Das grundlegende Problem ist, dass in der modernen Welt die Dummköpfe sich vollkommen sicher sind, während die Klugen voller Zweifel sind.',
			'correctness' => NULL,
			'user_id' => '7',
			'created' => '2014-04-15 00:35:03',
			'modified' => '2014-04-15 00:35:03',
			'dico_id' => NULL,
			'script' => null
		),
		array(
			'id' => '6',
			'lang' => 'jpn',
			'text' => 'その問題の根本原因は、現代の世界において、賢明な人々が猜疑心に満ちている一方で、愚かな人々が自信過剰であるということである。',
			'correctness' => NULL,
			'user_id' => '7',
			'created' => '2014-04-15 00:39:23',
			'modified' => '2014-04-15 00:39:36',
			'dico_id' => NULL,
			'script' => null
		),
		array(
			'id' => '7',
			'lang' => 'eng',
			'text' => 'This is a lonely sentence.',
			'correctness' => NULL,
			'user_id' => '7',
			'created' => '2014-04-15 00:49:21',
			'modified' => '2014-04-15 00:49:21',
			'dico_id' => NULL,
			'script' => null
		),
		array(
			'id' => '8',
			'lang' => 'fra',
			'text' => 'Voici une phrase qu’il serait bien de traduire.',
			'correctness' => NULL,
			'user_id' => '7',
			'created' => '2014-04-15 00:52:01',
			'modified' => '2014-04-15 00:52:01',
			'dico_id' => NULL,
			'script' => null
		),
		array(
			'id' => '9',
			'lang' => NULL,
			'text' => 'This sentences purposely misses its flag.',
			'correctness' => NULL,
			'user_id' => '3',
			'created' => '2014-04-15 21:12:03',
			'modified' => '2014-04-15 21:12:03',
			'dico_id' => NULL,
			'script' => null
		),
                array(
                        'id' => '10',
                        'lang' => 'jpn',
                        'text' => 'ちょっと待って。',
                        'correctness' => NULL,
                        'user_id' => '7',
                        'created' => '2014-04-15 21:13:23',
                        'modified' => '2014-04-15 21:13:36',
                        'dico_id' => NULL,
                        'script' => null
                ),
		array(
			'id' => '11',
			'lang' => 'yue',
			'text' => '如果你唔想要我就俾第個㗎喇。',
			'correctness' => NULL,
			'user_id' => '7',
			'created' => '2015-04-15 21:14:00',
			'modified' => '2015-04-15 21:14:00',
			'dico_id' => NULL,
			'script' => 'Hant'
		),
		array(
			'id' => '12',
			'lang' => 'fra',
			'text' => 'La cause fondamentale du problème est que dans le monde moderne, les imbéciles sont emplis d\'assurance, alors que les gens intelligents sont emplis de doute.',
			'correctness' => NULL,
			'user_id' => '7',
			'created' => '2015-06-15 00:34:28',
			'modified' => '2015-06-15 00:34:28',
			'dico_id' => NULL,
                        'script' => null
		),
		// Intentionally nonexistant. It has been deleted.
		// See ContributionFixture.php.
		//array(
		//    'id' => '13',
		//    ...
		//),
		array(
			'id' => '14',
			'lang' => 'eng',
			'text' => 'An orphan sentence.',
			'correctness' => NULL,
			'user_id' => NULL,
			'created' => '2015-08-17 21:56:27',
			'modified' => '2015-08-17 21:56:27',
			'dico_id' => NULL,
			'script' => NULL
		),
	);
}
