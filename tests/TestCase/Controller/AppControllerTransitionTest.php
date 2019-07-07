<?php
namespace App\Test\TestCase\Controller;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\IntegrationTestCase;

class AppControllerTransitionTest extends IntegrationTestCase
{
    public $fixtures = array(
        'app.users',
        'app.users_languages',
        'app.private_messages',
    );

    public function controllerSpy($event, $controller = null) {
        parent::controllerSpy($event, $controller);

        $users = TableRegistry::get('Users');
        $user = $users->findByUsername('advanced_contributor')->first()->toArray();
        unset($user['role']);
        $user['group_id'] = 3;

        $this->_controller->Auth->setUser($user);
    }

    public function testTransitionFromGroupIdToRole() {
        $this->get('/fra/private_messages/folder/Inbox');
        $this->assertResponseOk();
    }
}
