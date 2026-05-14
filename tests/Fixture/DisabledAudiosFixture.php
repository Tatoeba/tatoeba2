<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * DisabledAudiosFixture
 */
class DisabledAudiosFixture extends TestFixture
{
    public function init(): void
    {
        $this->records = [
            [
                'id' => 4,
                'sentence_id' => 3,
                'sentence_lang' => 'spa',
                'user_id' => 2,
                'external' => NULL,
                'source' => 'tatoeba',
                'created' => '2022-01-20 09:23:49',
                'modified' => '2022-01-21 21:01:21'
            ],
        ];
        parent::init();
    }
}
