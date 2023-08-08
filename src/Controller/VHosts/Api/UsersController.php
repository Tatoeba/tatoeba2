<?php
namespace App\Controller\VHosts\Api;

use App\Controller\VHosts\Api\ApiController;

class UsersController extends ApiController
{
    private function exposedFields() {
        $exposedFields = [
            'fields' => ['username', 'role', 'since'],
            'languages' => ['fields' => [
                'code', 'level', 'details'
            ]],
        ];
        return compact('exposedFields');
    }

    private function fields() {
        return [
            'id',
            'username',
            'role',
            'since',
        ];
    }

    public function get($name) {
        $this->loadModel('Users');
        $query = $this->Users
            ->find('exposedFields', $this->exposedFields())
            ->select($this->fields())
            ->where([
                'username' => $name,
            ])
            ->contain(['UsersLanguages' => ['fields' => [
                'of_user_id',
                'code' => 'language_code',
                'level',
                'details'
            ]]])
            ->formatResults(function($entities) {
                return $entities->map(function($entity) {
                    $entity->since = $entity->since->toDateString();
                    return $entity;
                });
            });

        $results = $query->firstOrFail();
        $response = [
            'data' => $results,
        ];

        $this->set('response', $response);
        $this->set('_serialize', 'response');
        $this->RequestHandler->renderAs($this, 'json');
    }
}
