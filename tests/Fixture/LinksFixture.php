<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * LinksFixture
 */
class LinksFixture extends TestFixture
{
    public $table = 'sentences_translations';

    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'sentence_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'translation_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'sentence_lang' => ['type' => 'string', 'length' => 4, 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'translation_lang' => ['type' => 'string', 'length' => 4, 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'distance' => ['type' => 'smallinteger', 'length' => 2, 'unsigned' => false, 'null' => false, 'default' => '1', 'comment' => '', 'precision' => null],
        '_indexes' => [
            'translation_id' => ['type' => 'index', 'columns' => ['translation_id'], 'length' => []],
            'translation_lang' => ['type' => 'index', 'columns' => ['translation_lang'], 'length' => []],
            'sentence_lang_translation_lang_idx' => ['type' => 'index', 'columns' => ['sentence_lang', 'translation_lang'], 'length' => []],
        ],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
            'sentence_id' => ['type' => 'unique', 'columns' => ['sentence_id', 'translation_id'], 'length' => []],
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
                'id' => '1',
                'sentence_id' => '1',
                'translation_id' => '2',
                'sentence_lang' => 'eng',
                'translation_lang' => 'cmn',
                'distance' => '1'
            ],
            [
                'id' => '2',
                'sentence_id' => '2',
                'translation_id' => '1',
                'sentence_lang' => 'cmn',
                'translation_lang' => 'eng',
                'distance' => '1'
            ],
            [
                'id' => '3',
                'sentence_id' => '1',
                'translation_id' => '3',
                'sentence_lang' => 'eng',
                'translation_lang' => 'spa',
                'distance' => '1'
            ],
            [
                'id' => '4',
                'sentence_id' => '3',
                'translation_id' => '1',
                'sentence_lang' => 'spa',
                'translation_lang' => 'eng',
                'distance' => '1'
            ],
            [
                'id' => '5',
                'sentence_id' => '1',
                'translation_id' => '4',
                'sentence_lang' => 'eng',
                'translation_lang' => 'fra',
                'distance' => '1'
            ],
            [
                'id' => '6',
                'sentence_id' => '4',
                'translation_id' => '1',
                'sentence_lang' => 'fra',
                'translation_lang' => 'eng',
                'distance' => '1'
            ],
            [
                'id' => '7',
                'sentence_id' => '2',
                'translation_id' => '4',
                'sentence_lang' => 'cmn',
                'translation_lang' => 'fra',
                'distance' => '1'
            ],
            [
                'id' => '8',
                'sentence_id' => '4',
                'translation_id' => '2',
                'sentence_lang' => 'fra',
                'translation_lang' => 'cmn',
                'distance' => '1'
            ],
            [
                'id' => '9',
                'sentence_id' => '2',
                'translation_id' => '5',
                'sentence_lang' => 'cmn',
                'translation_lang' => 'deu',
                'distance' => '1'
            ],
            [
                'id' => '10',
                'sentence_id' => '5',
                'translation_id' => '2',
                'sentence_lang' => 'deu',
                'translation_lang' => 'cmn',
                'distance' => '1'
            ],
            [
                'id' => '11',
                'sentence_id' => '4',
                'translation_id' => '6',
                'sentence_lang' => 'fra',
                'translation_lang' => 'jpn',
                'distance' => '1'
            ],
            [
                'id' => '12',
                'sentence_id' => '6',
                'translation_id' => '4',
                'sentence_lang' => 'jpn',
                'translation_lang' => 'fra',
                'distance' => '1'
            ],
            [
                'id' => '13',
                'sentence_id' => '10',
                'translation_id' => '6',
                'sentence_lang' => 'jpn',
                'translation_lang' => 'jpn',
                'distance' => '1'
            ],
            [
                'id' => '14',
                'sentence_id' => '6',
                'translation_id' => '10',
                'sentence_lang' => 'jpn',
                'translation_lang' => 'jpn',
                'distance' => '1'
            ],
            [
                'id' => '15',
                'sentence_id' => '55',
                'translation_id' => '56',
                'sentence_lang' => 'fra',
                'translation_lang' => 'bod', // purposely wrong flag
                'distance' => '1'
            ],
            [
                'id' => '16',
                'sentence_id' => '56',
                'translation_id' => '55',
                'sentence_lang' => 'bod', // purposely wrong flag
                'translation_lang' => 'fra',
                'distance' => '1'
            ],
            [
                'id' => '17',
                'sentence_id' => '55',
                'translation_id' => '57',
                'sentence_lang' => 'fra',
                'translation_lang' => 'jpn',
                'distance' => '1'
            ],
            [
                'id' => '18',
                'sentence_id' => '57',
                'translation_id' => '55',
                'sentence_lang' => 'jpn',
                'translation_lang' => 'fra',
                'distance' => '1'
            ],
            [
                'id' => '19',
                'sentence_id' => '58',
                'translation_id' => '59',
                'sentence_lang' => 'eng',
                'translation_lang' => 'eng',
                'distance' => '1'
            ],
            [
                'id' => '20',
                'sentence_id' => '59',
                'translation_id' => '58',
                'sentence_lang' => 'eng',
                'translation_lang' => 'eng',
                'distance' => '1'
            ],
            [
                'id' => '21',
                'sentence_id' => '58',
                'translation_id' => '60',
                'sentence_lang' => 'eng',
                'translation_lang' => 'eng',
                'distance' => '1'
            ],
            [
                'id' => '22',
                'sentence_id' => '60',
                'translation_id' => '58',
                'sentence_lang' => 'eng',
                'translation_lang' => 'eng',
                'distance' => '1'
            ],
            [
                'id' => '23',
                'sentence_id' => '60',
                'translation_id' => '61',
                'sentence_lang' => 'eng',
                'translation_lang' => 'eng',
                'distance' => '1'
            ],
            [
                'id' => '24',
                'sentence_id' => '61',
                'translation_id' => '60',
                'sentence_lang' => 'eng',
                'translation_lang' => 'eng',
                'distance' => '1'
            ],
            [
                'id' => '25',
                'sentence_id' => '60',
                'translation_id' => '62',
                'sentence_lang' => 'eng',
                'translation_lang' => 'eng',
                'distance' => '1'
            ],
            [
                'id' => '26',
                'sentence_id' => '62',
                'translation_id' => '60',
                'sentence_lang' => 'eng',
                'translation_lang' => 'eng',
                'distance' => '1'
            ],
            [
                'id' => '27',
                'sentence_id' => '59',
                'translation_id' => '63',
                'sentence_lang' => 'eng',
                'translation_lang' => 'eng',
                'distance' => '1'
            ],
            [
                'id' => '28',
                'sentence_id' => '63',
                'translation_id' => '59',
                'sentence_lang' => 'eng',
                'translation_lang' => 'eng',
                'distance' => '1'
            ],
            [
                'id' => '29',
                'sentence_id' => '59',
                'translation_id' => '64',
                'sentence_lang' => 'eng',
                'translation_lang' => 'eng',
                'distance' => '1'
            ],
            [
                'id' => '30',
                'sentence_id' => '64',
                'translation_id' => '59',
                'sentence_lang' => 'eng',
                'translation_lang' => 'eng',
                'distance' => '1'
            ],
        ];
        parent::init();
    }
}
