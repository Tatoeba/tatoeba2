<?php
namespace App\Test\TestCase\Controller;

use Cake\ORM\TableRegistry;

trait LogInAsTrait {
    private function logInAs($username) {
        $users = TableRegistry::get('Users');
        $user = $users->findByUsername($username)->first();
        $this->session(['Auth' => ['User' => $user->toArray()]]);
        $this->enableCsrfToken();
        $this->enableSecurityToken();
    }
}
