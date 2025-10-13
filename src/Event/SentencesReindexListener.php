<?php
namespace App\Event;

use App\Model\Entity\UsersLanguage;
use ArrayObject;
use Cake\Datasource\ModelAwareTrait;
use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\Datasource\EntityInterface;

class SentencesReindexListener implements EventListenerInterface {

    use ModelAwareTrait;

    public function implementedEvents() {
        return [
            'Model.afterSave' => 'onSave',
            'Model.afterDelete' => 'onDelete',
        ];
    }

    private function enqueueSentencesReindexTask(UsersLanguage $entity) {
        $this->loadModel('Queue.QueuedJobs');
        $this->QueuedJobs->createJob(
            'SentencesReindex',
            [
                'user_id' => $entity->of_user_id,
                'lang' => $entity->language_code,
            ]
        );
        $this->QueuedJobs->wakeUpWorkers();
    }

    public function onSave(Event $event, EntityInterface $entity, ArrayObject $options) {
        if ($entity instanceOf UsersLanguage
            && $entity->has('language_code') && $entity->has('of_user_id') && $entity->isDirty('level')
            && (
                 $entity->isNew() && $entity->level == 5
              || !$entity->isNew() && ($entity->getOriginal('level') == 5 xor $entity->level == 5)
            ))
        {
            $this->enqueueSentencesReindexTask($entity);
        }
    }

    public function onDelete(Event $event, EntityInterface $entity, ArrayObject $options) {
        if ($entity instanceOf UsersLanguage
            && $entity->has('language_code') && $entity->has('of_user_id') && $entity->level == 5) {
            $this->enqueueSentencesReindexTask($entity);
        }
    }
}
