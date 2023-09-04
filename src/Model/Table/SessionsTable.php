<?php
namespace App\Model\Table;

use App\Model\CurrentUser;
use Cake\ORM\Table;

class SessionsTable extends Table
{
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
        ]);
    }

    public function beforeSave($event, $entity, $options = [])
    {
        if ($entity->isDirty('data')) {
            $entity->user_id = CurrentUser::get('id');
        }
        return true;
    }
}
