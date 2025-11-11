<?php
/* Transcription Fixture generated on: 2014-10-26 15:19:49 : 1414336789 */
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class TranscriptionsFixture extends TestFixture {
	public $name = 'Transcription';

	public $fields = array(
		'id' => ['type' => 'integer', 'null' => false, 'default' => null],
		'sentence_id' => ['type' => 'integer', 'null' => false, 'default' => null],
		'script' => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 4, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'text' => ['type' => 'text', 'null' => false, 'default' => null, 'length' => 10000],
		'user_id' => ['type' => 'integer', 'null' => true, 'default' => null],
		'needsReview' => ['type' => 'boolean', 'null' => false, 'default' => '1'],
		'created' => ['type' => 'datetime', 'null' => false, 'default' => null],
		'modified' => ['type' => 'datetime', 'null' => false, 'default' => null],
		'_constraints' => ['primary' => ['type' => 'primary', 'columns' => ['id']], 'unique_transcriptions' => ['type' => 'unique', 'columns' => ['sentence_id', 'script']]],
		'_options' => ['charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM']
	);

	public $records = array(
		array(
			'id' => 1,
			'sentence_id' => 6,
			'script' => 'Hrkt',
			'text' => 'その[問題|もんだい]の[根本|こんぽん][原因|げんいん]は、[現代|げんだい]の[世界|せかい]において、[賢明|けんめい]な[人々|ひとびと]が[猜疑心|さいぎしん]に[満|み]ちている[一方|いっぽう]で、[愚|おろ]かな[人々|ひとびと]が[自信|じしん][過剰|かじょう]であるということである。',
			'user_id' => 7,
			'needsReview' => 0,
			'created' => '2014-10-18 17:43:32',
			'modified' => '2014-10-18 17:43:32'
		),
		array(
			'id' => 2,
			'sentence_id' => 2,
			'script' => 'Hant',
			'text' => '問題的根源是，在當今世界，愚人充滿了自信，而智者充滿了懷疑。',
			'user_id' => null,
			'needsReview' => 0,
			'created' => '2014-10-18 17:43:32',
			'modified' => '2014-10-18 17:43:32'
		),
		array(
			'id' => 3,
			'sentence_id' => 10,
			'script' => 'Hrkt',
			'text' => 'ちょっと [待|ま]って 。',
			'user_id' => null,
			'needsReview' => 1,
			'created' => '2014-10-18 17:43:32',
			'modified' => '2014-10-18 17:43:32'
		),
		array(
			'id' => 4,
			'sentence_id' => 66,
			'script' => 'Latn',
			'text' => 'Ishingni qil!',
			'user_id' => null,
			'needsReview' => 0,
			'created' => '2020-01-22 22:22:22',
			'modified' => '2020-01-22 22:22:22'
		),
	);
}
