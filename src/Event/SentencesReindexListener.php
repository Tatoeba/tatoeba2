<?php
namespace App\Event;

use App\Model\Entity\UsersLanguage;
use ArrayObject;
use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\Datasource\EntityInterface;
use Cake\ORM\TableRegistry;

class SentencesReindexListener implements EventListenerInterface {
    public $Sentences;

    public function __construct() {
        $this->Sentences = TableRegistry::get('Sentences');
    }

    public function implementedEvents() {
        return [
            'Model.afterSave' => 'onSave',
            'Model.afterDelete' => 'onDelete',
        ];
    }

    private function reindexNativeSpeakerSentences(UsersLanguage $entity) {
       $sentenceIds = $this->Sentences
           ->find('list', ['valueField' => 'id'])
           ->where([
               'user_id' => $entity->of_user_id,
               'lang' => $entity->language_code,
           ])
           ->toList();
       $this->Sentences->flagSentenceAndTranslationsToReindex($sentenceIds);
    }

    public function onSave(Event $event, EntityInterface $entity, ArrayObject $options) {
        if ($entity instanceOf UsersLanguage
            && $entity->has('language_code') && $entity->has('of_user_id') && $entity->isDirty('level')
            && (
                 $entity->isNew() && $entity->level == 5
              || !$entity->isNew() && ($entity->getOriginal('level') == 5 xor $entity->level == 5)
            ))
        {
            $this->reindexNativeSpeakerSentences($entity);
        }
    }

    public function onDelete(Event $event, EntityInterface $entity, ArrayObject $options) {
        if ($entity instanceOf UsersLanguage
            && $entity->has('language_code') && $entity->has('of_user_id') && $entity->level == 5) {
            $this->reindexNativeSpeakerSentences($entity);
        }
    }
}
