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
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'lang' => ['type' => 'string', 'length' => 4, 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'text' => ['type' => 'binary', 'length' => 1500, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null],
        'numSentences' => ['type' => 'integer', 'length' => 10, 'unsigned' => false, 'null' => true, 'default' => '0', 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'numAdded' => ['type' => 'integer', 'length' => 10, 'unsigned' => false, 'null' => true, 'default' => '0', 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'created' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
            'text_lang_idx' => ['type' => 'unique', 'columns' => ['text', 'lang']],
        ],
        '_options' => [
            'engine' => 'InnoDB',
            'collation' => 'utf8_general_ci'
        ],
    ];
    // @codingStandardsIgnoreEnd

    /**
     * Init method
     *
     * @return void
     */
    public function init()
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
