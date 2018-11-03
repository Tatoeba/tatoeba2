<?php
/* SentencesTranslation Fixture generated on: 2014-04-15 01:02:28 : 1397516548 */
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class LinkFixture extends TestFixture {
	public $name = 'Link';
	public $table = 'sentences_translations';

	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'sentence_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'index'),
		'translation_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'index'),
		'sentence_lang' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 4, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'translation_lang' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 4, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'distance' => array('type' => 'integer', 'null' => false, 'default' => '1', 'length' => 2),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'sentence_id' => array('column' => array('sentence_id', 'translation_id'), 'unique' => 1), 'translation_id' => array('column' => 'translation_id', 'unique' => 0), 'sentence_lang' => array('column' => 'sentence_lang', 'unique' => 0), 'translation_lang' => array('column' => 'translation_lang', 'unique' => 0)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	public $records = array(
		/* Here is a little ASCII chart of these fixtures.
		 * Let's try to keep it updated!
		 *
		 *   5-2-1-3
		 *      \|
		 *       4-6-10
		 */
		array(
			'id' => '1',
			'sentence_id' => '1',
			'translation_id' => '2',
			'sentence_lang' => NULL,
			'translation_lang' => NULL,
			'distance' => '1'
		),
		array(
			'id' => '2',
			'sentence_id' => '2',
			'translation_id' => '1',
			'sentence_lang' => NULL,
			'translation_lang' => NULL,
			'distance' => '1'
		),
		array(
			'id' => '3',
			'sentence_id' => '1',
			'translation_id' => '3',
			'sentence_lang' => NULL,
			'translation_lang' => NULL,
			'distance' => '1'
		),
		array(
			'id' => '4',
			'sentence_id' => '3',
			'translation_id' => '1',
			'sentence_lang' => NULL,
			'translation_lang' => NULL,
			'distance' => '1'
		),
		array(
			'id' => '5',
			'sentence_id' => '1',
			'translation_id' => '4',
			'sentence_lang' => NULL,
			'translation_lang' => NULL,
			'distance' => '1'
		),
		array(
			'id' => '6',
			'sentence_id' => '4',
			'translation_id' => '1',
			'sentence_lang' => NULL,
			'translation_lang' => NULL,
			'distance' => '1'
		),
		array(
			'id' => '7',
			'sentence_id' => '2',
			'translation_id' => '4',
			'sentence_lang' => NULL,
			'translation_lang' => NULL,
			'distance' => '1'
		),
		array(
			'id' => '8',
			'sentence_id' => '4',
			'translation_id' => '2',
			'sentence_lang' => NULL,
			'translation_lang' => NULL,
			'distance' => '1'
		),
		array(
			'id' => '9',
			'sentence_id' => '2',
			'translation_id' => '5',
			'sentence_lang' => NULL,
			'translation_lang' => NULL,
			'distance' => '1'
		),
		array(
			'id' => '10',
			'sentence_id' => '5',
			'translation_id' => '2',
			'sentence_lang' => NULL,
			'translation_lang' => NULL,
			'distance' => '1'
		),
		array(
			'id' => '11',
			'sentence_id' => '4',
			'translation_id' => '6',
			'sentence_lang' => NULL,
			'translation_lang' => NULL,
			'distance' => '1'
		),
		array(
			'id' => '12',
			'sentence_id' => '6',
			'translation_id' => '4',
			'sentence_lang' => NULL,
			'translation_lang' => NULL,
			'distance' => '1'
		),
		array(
			'id' => '13',
			'sentence_id' => '10',
			'translation_id' => '6',
			'sentence_lang' => NULL,
			'translation_lang' => NULL,
			'distance' => '1'
		),
		array(
			'id' => '14',
			'sentence_id' => '6',
			'translation_id' => '10',
			'sentence_lang' => NULL,
			'translation_lang' => NULL,
			'distance' => '1'
		),
	);
}
