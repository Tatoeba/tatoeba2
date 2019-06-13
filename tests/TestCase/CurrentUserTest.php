<?php
namespace App\Test\TestCase\Model;

use App\Model\CurrentUser;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

class CurrentUserTest extends TestCase
{
    public $fixtures = array(
        'app.users',
        'app.users_languages',
    );

    public function tearDown()
    {
        parent::tearDown();
        CurrentUser::store(null);
    }

    public function testTransitionFromGroupIdToRole() {
        $users = TableRegistry::get('Users');
        $user = $users->findByUsername('advanced_contributor')->first()->toArray();
        unset($user['role']);
        $user['group_id'] = 3;
        CurrentUser::store($user);

        $this->assertTrue(CurrentUser::isMember());
        $this->assertTrue(CurrentUser::isTrusted());
        $this->assertFalse(CurrentUser::isModerator());
        $this->assertFalse(CurrentUser::isAdmin());
    }
}
