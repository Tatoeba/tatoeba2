<?php
namespace App\Test\TestCase\Controller;

use Cake\ORM\TableRegistry;
use Cake\Utility\Text;

trait TatoebaControllerTestTrait {
    private function logInAs($username) {
        $users = TableRegistry::get('Users');
        $user = $users->findByUsername($username)->first();
        $this->session(['Auth' => ['User' => $user->toArray()]]);
        $this->enableCsrfToken();
        $this->enableSecurityToken();
    }

    public function assertAccessUrlAs($url, $user, $response) {
        if ($user) {
            $who = "user '$user'";
            $this->logInAs($user);
        } else {
            $who = "guest";
        }

        $this->_securityToken = false;
        $this->_csrfToken = false;
        $this->get($url);

        if (is_string($response)) {
            $this->assertRedirect($response, "Failed asserting that $who is being redirected "
                                            ."to '$response' when trying to access '$url'.");
        } elseif (is_bool($response)) {
            if ($response) {
                $this->assertResponseOk("Failed asserting that $who can access '$url'.");
            } else {
                $this->assertResponseError("Failed asserting that $who cannot access '$url'.");
            }
        } elseif (is_int($response)) {
            $this->assertResponseCode($response);
        }
    }

    public function assertAjaxAccessUrlAs($url, $user, $response) {
        $this->addHeader('X-Requested-With', 'XMLHttpRequest');
        $this->assertAccessUrlAs($url, $user, $response);
    }

    public function ajaxPost($url, $data = []) {
        $this->addHeader('X-Requested-With', 'XMLHttpRequest');
        if (is_string($data)) {
            $token = Text::uuid();
            $this->cookie('csrfToken', $token);
            $this->configRequest(['headers' => ['X-CSRF-Token' => $token]]);
        }
        $this->post($url, $data);
    }

    public function addHeader($header, $value) {
        if (!isset($this->_request['headers'])) {
            $this->_request['headers'] = [];
        }
        $this->_request['headers'][$header] = $value;
    }

    public function assertNoFlashMessage() {
        $this->assertSession(null, 'Flash.flash.0.message');
    }
}
