<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * UsersLanguagesFixture
 */
class UsersLanguagesFixture extends TestFixture
{
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
                'of_user_id' => 4,
                'by_user_id' => 4,
                'language_code' => 'jpn',
                'level' => 1,
                'details' => '',
                'created' => '2018-10-31 00:00:00',
                'modified' => '2018-10-31 00:00:00'
            ],
            [
                'id' => 2,
                'of_user_id' => 4,
                'by_user_id' => 4,
                'language_code' => 'fra',
                'level' => 5,
                'details' => '',
                'created' => '2018-10-31 00:00:00',
                'modified' => '2018-10-31 00:00:00'
            ],
            [
                'id' => 3,
                'of_user_id' => 7,
                'by_user_id' => 7,
                'language_code' => 'jpn',
                'level' => 5,
                'details' => '',
                'created' => '2018-10-31 00:00:00',
                'modified' => '2018-10-31 00:00:00'
            ],
            [
                'id' => 4,
                'of_user_id' => 3,
                'by_user_id' => 3,
                'language_code' => 'fra',
                'level' => 5,
                'details' => '',
                'created' => '2018-10-31 00:00:00',
                'modified' => '2018-10-31 00:00:00'
            ],
            [
                'id' => 5,
                'of_user_id' => 7,
                'by_user_id' => 7,
                'language_code' => 'fra',
                'level' => 2,
                'details' => '',
                'created' => '2018-10-31 00:00:00',
                'modified' => '2018-10-31 00:00:00'
            ],
            [
                'id' => 6,
                'of_user_id' => 2,
                'by_user_id' => 2,
                'language_code' => 'jpn',
                'level' => 3,
                'details' => '',
                'created' => '2017-10-31 00:00:00',
                'modified' => '2017-10-31 00:00:00'
            ],
        ];
        parent::init();
    }
}
