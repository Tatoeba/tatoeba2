<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * FavoritesUsersFixture
 */
class FavoritesUsersFixture extends TestFixture
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
                'favorite_id' => 4,
                'user_id' => 7,
                'created' => '2020-03-03 03:03:03'
            ],
        ];
        parent::init();
    }
}
