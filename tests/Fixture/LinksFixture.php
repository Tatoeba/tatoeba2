<?php
/* SentencesTranslation Fixture generated on: 2014-04-15 01:02:28 : 1397516548 */
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class LinksFixture extends TestFixture {
	public $table = 'sentences_translations';

	public $fields = array(
		'id' => ['type' => 'integer', 'null' => false, 'default' => null],
		'sentence_id' => ['type' => 'integer', 'null' => false, 'default' => null],
		'translation_id' => ['type' => 'integer', 'null' => false, 'default' => null],
		'sentence_lang' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 4, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'translation_lang' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 4, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'distance' => ['type' => 'integer', 'null' => false, 'default' => '1', 'length' => 2],
		'_indexes' => [
			'translation_id' => ['type' => 'index', 'columns' => ['translation_id'], 'length' => []],
			'sentence_lang' => ['type' => 'index', 'columns' => ['sentence_lang'], 'length' => []],
			'translation_lang' => ['type' => 'index', 'columns' => ['translation_lang'], 'length' => []],
		],
		'_constraints' => ['primary' => ['type' => 'primary', 'columns' => ['id']], 'sentence_id' => ['type' => 'unique', 'columns' => ['sentence_id', 'translation_id']]],
		'_options' => ['charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM']
	);

	public $records = array(
		/* Here is a little ASCII chart of these fixtures.
		 * Let's try to keep it updated!
		 *
		 *   5-2-1-3     55-57
		 *      \|       |
		 *       4-6-10  56
		 */
		array(
			'id' => '1',
			'sentence_id' => '1',
			'translation_id' => '2',
			'sentence_lang' => 'eng',
			'translation_lang' => 'cmn',
			'distance' => '1'
		),
		array(
			'id' => '2',
			'sentence_id' => '2',
			'translation_id' => '1',
			'sentence_lang' => 'cmn',
			'translation_lang' => 'eng',
			'distance' => '1'
		),
		array(
			'id' => '3',
			'sentence_id' => '1',
			'translation_id' => '3',
			'sentence_lang' => 'eng',
			'translation_lang' => 'spa',
			'distance' => '1'
		),
		array(
			'id' => '4',
			'sentence_id' => '3',
			'translation_id' => '1',
			'sentence_lang' => 'spa',
			'translation_lang' => 'eng',
			'distance' => '1'
		),
		array(
			'id' => '5',
			'sentence_id' => '1',
			'translation_id' => '4',
			'sentence_lang' => 'eng',
			'translation_lang' => 'fra',
			'distance' => '1'
		),
		array(
			'id' => '6',
			'sentence_id' => '4',
			'translation_id' => '1',
			'sentence_lang' => 'fra',
			'translation_lang' => 'eng',
			'distance' => '1'
		),
		array(
			'id' => '7',
			'sentence_id' => '2',
			'translation_id' => '4',
			'sentence_lang' => 'cmn',
			'translation_lang' => 'fra',
			'distance' => '1'
		),
		array(
			'id' => '8',
			'sentence_id' => '4',
			'translation_id' => '2',
			'sentence_lang' => 'fra',
			'translation_lang' => 'cmn',
			'distance' => '1'
		),
		array(
			'id' => '9',
			'sentence_id' => '2',
			'translation_id' => '5',
			'sentence_lang' => 'cmn',
			'translation_lang' => 'deu',
			'distance' => '1'
		),
		array(
			'id' => '10',
			'sentence_id' => '5',
			'translation_id' => '2',
			'sentence_lang' => 'deu',
			'translation_lang' => 'cmn',
			'distance' => '1'
		),
		array(
			'id' => '11',
			'sentence_id' => '4',
			'translation_id' => '6',
			'sentence_lang' => 'fra',
			'translation_lang' => 'jpn',
			'distance' => '1'
		),
		array(
			'id' => '12',
			'sentence_id' => '6',
			'translation_id' => '4',
			'sentence_lang' => 'jpn',
			'translation_lang' => 'fra',
			'distance' => '1'
		),
		array(
			'id' => '13',
			'sentence_id' => '10',
			'translation_id' => '6',
			'sentence_lang' => 'jpn',
			'translation_lang' => 'jpn',
			'distance' => '1'
		),
		array(
			'id' => '14',
			'sentence_id' => '6',
			'translation_id' => '10',
			'sentence_lang' => 'jpn',
			'translation_lang' => 'jpn',
			'distance' => '1'
		),
		array(
			'id' => '15',
			'sentence_id' => '55',
			'translation_id' => '56',
			'sentence_lang' => 'fra',
			'translation_lang' => 'jpn',
			'distance' => '1'
		),
		array(
			'id' => '16',
			'sentence_id' => '56',
			'translation_id' => '55',
			'sentence_lang' => 'jpn',
			'translation_lang' => 'fra',
			'distance' => '1'
		),
		array(
			'id' => '17',
			'sentence_id' => '55',
			'translation_id' => '57',
			'sentence_lang' => 'fra',
			'translation_lang' => 'jpn',
			'distance' => '1'
		),
		array(
			'id' => '18',
			'sentence_id' => '57',
			'translation_id' => '55',
			'sentence_lang' => 'jpn',
			'translation_lang' => 'fra',
			'distance' => '1'
		),
	);
}
