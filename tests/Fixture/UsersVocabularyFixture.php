<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * UsersVocabularyFixture
 *
 */
class UsersVocabularyFixture extends TestFixture
{

    /**
     * Table name
     *
     * @var string
     */
    public $table = 'users_vocabulary';

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
                'user_id' => 4,
                'hash' => '',
                'created' => '2019-01-07 19:50:36',
                'vocabulary_id' => 1
            ],
            [
                'id' => 2,
                'user_id' => 7,
                'hash' => '',
                'created' => '2020-02-20 02:20:02',
                'vocabulary_id' => 2
            ],
            [
                'id' => 3,
                'user_id' => 4,
                'hash' => '',
                'created' => '2020-09-10 11:12:13',
                'vocabulary_id' => 2
            ],
        ];
        parent::init();
    }
}
