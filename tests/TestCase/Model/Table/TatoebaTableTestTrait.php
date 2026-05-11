<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\CurrentUser;

trait TatoebaTableTestTrait {
    private function logInAs($username) {
        $users = $this->getTableLocator()->get('Users');
        $user = $users->findByUsername($username)->first();
        CurrentUser::store($user->toArray());
    }
}
