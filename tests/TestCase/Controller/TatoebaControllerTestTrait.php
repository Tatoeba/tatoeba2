<?php
namespace App\Test\TestCase\Controller;

use Cake\ORM\TableRegistry;

trait TatoebaControllerTestTrait {
    private function logInAs($username) {
        $users = TableRegistry::get('Users');
        $user = $users->findByUsername($username)->first();
        $this->session(['Auth' => ['User' => $user->toArray()]]);
        $this->enableCsrfToken();
        $this->enableSecurityToken();
    }

    public function assertRedirectionAs($url, $user, $redirect) {
        if ($user) {
            $who = "user '$user'";
            $this->logInAs($user);
        } else {
            $who = "guest";
        }

        $this->get($url);

        if ($redirect) {
            $this->assertRedirect($redirect, "Failed asserting that $who is being redirected "
                                            ."to '$redirect' when trying to access '$url'.");
        } else {
            $this->assertNoRedirect("Failed asserting that $who can access '$url'.");
            $this->assertResponseOk(); 
        }
    }
}
