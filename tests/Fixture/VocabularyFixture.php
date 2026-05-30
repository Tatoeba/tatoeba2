<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * VocabularyFixture
 *
 */
class VocabularyFixture extends TestFixture
{
    public $table = 'vocabulary';

    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->records = [
            [
                'id' => 1,
                'lang' => 'eng',
                'text' => 'out of the blue',
                'numSentences' => 1,
                'numAdded' => 1,
                'created' => '2019-01-07 19:48:18',
            ],
            [
                'id' => 2,
                'lang' => 'eng',
                'text' => 'added by 2 members',
                'numSentences' => 1,
                'numAdded' => 2,
                'created' => '2020-02-20 02:20:02',
            ],
        ];
        parent::init();
    }
}
