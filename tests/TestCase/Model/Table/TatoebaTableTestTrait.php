<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\CurrentUser;

trait TatoebaTableTestTrait {
    private function logInAs($username) {
        $users = $this->fetchTable('Users');
        $user = $users->findByUsername($username)->first();
        CurrentUser::store($user->toArray());
    }
}
