<?php
/* Transcription Fixture generated on: 2014-10-26 15:19:49 : 1414336789 */
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class TranscriptionsFixture extends TestFixture {
	public $name = 'Transcription';

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
