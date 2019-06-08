<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\CurrentUser;
use Cake\ORM\TableRegistry;

trait TatoebaTableTestTrait {
    private function logInAs($username) {
        $users = TableRegistry::get('Users');
        $user = $users->findByUsername($username)->first();
        CurrentUser::store($user->toArray());
    }
}
