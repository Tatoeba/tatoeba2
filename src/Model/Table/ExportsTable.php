<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class ExportsTable extends Table
{
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('exports');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->belongsTo('Queue.QueuedJobs');
        $this->belongsTo('Users');
    }

    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->scalar('name')
            ->maxLength('name', 255)
            ->requirePresence('name', 'create')
            ->notEmpty('name');

        $validator
            ->scalar('description')
            ->requirePresence('description', 'create')
            ->notEmpty('description');

        $validator
            ->scalar('url')
            ->maxLength('url', 2048)
            ->allowEmpty('url');

        $validator
            ->scalar('filename')
            ->maxLength('filename', 255)
            ->allowEmpty('filename');

        $validator
            ->dateTime('generated')
            ->allowEmpty('generated');

        $validator
            ->integer('queued_job_id')
            ->allowEmpty('queued_job_id', 'create');

        $validator
            ->scalar('status')
            ->requirePresence('status', 'create')
            ->notEmpty('status');

        return $validator;
    }

    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['queued_job_id'], 'QueuedJobs'));
        $rules->add($rules->existsIn(['user_id'], 'Users'));

        return $rules;
    }

    public function getExportsOf($userId)
    {
        return $this->find()
           ->select(['name', 'url', 'status'])
           ->where(['user_id' => $userId]);
    }
}
