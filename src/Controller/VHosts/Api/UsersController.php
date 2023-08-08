<?php
namespace App\Controller\VHosts\Api;

use App\Controller\VHosts\Api\ApiController;

class UsersController extends ApiController
{
    private function fields() {
        return [
            'username',
            'role',
            'since',
        ];
    }

    public function get($name) {
        $this->loadModel('Users');
        $query = $this->Users
            ->find()
            ->select($this->fields())
            ->where([
                'username' => $name,
            ])
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
