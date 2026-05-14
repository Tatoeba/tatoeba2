<?php

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class LastContributionsFixture extends TestFixture {
	public $records = [
		[
			'sentence_id' => '51',
			'sentence_lang' => 'eng',
			'translation_id' => NULL,
			'translation_lang' => NULL,
			'script' => NULL,
			'text' => 'An original sentence added under CC0.',
			'action' => 'insert',
			'user_id' => '3',
			'datetime' => '2017-04-10 01:26:00',
			'type' => 'sentence',
			'id' => '110'
		],
		[
			'sentence_id' => '52',
			'sentence_lang' => 'eng',
			'translation_id' => NULL,
			'translation_lang' => NULL,
			'script' => NULL,
			'text' => 'An original sentence with a null license.',
			'action' => 'insert',
			'user_id' => '3',
			'datetime' => '2017-04-10 01:27:00',
			'type' => 'sentence',
			'id' => '111'
		],
		[
			'sentence_id' => '53',
			'sentence_lang' => 'eng',
			'translation_id' => NULL,
			'translation_lang' => NULL,
			'script' => NULL,
			'text' => 'Another original sentence (not initially added as a translation).',
			'action' => 'insert',
			'user_id' => '4',
			'datetime' => '2017-04-11 13:49:10',
			'type' => 'sentence',
			'id' => '112'
		],
	];
}
