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
            'Model.afterSave' => 'reindexNativeSpeakerSentences',
        ];
    }

    public function reindexNativeSpeakerSentences(Event $event, EntityInterface $entity, ArrayObject $options) {
        if ($entity instanceOf UsersLanguage
            && $entity->has('language_code') && $entity->has('of_user_id') && $entity->isDirty('level')
            && ($entity->getOriginal('level') == 5 xor $entity->level == 5)) {
            $sentenceIds = $this->Sentences
                ->find('list', ['valueField' => 'id'])
                ->where([
                    'user_id' => $entity->of_user_id,
                    'lang' => $entity->language_code,
                ])
                ->toList();
            $this->Sentences->flagSentenceAndTranslationsToReindex($sentenceIds);
        }
    }
}
