<?php
namespace App\Event;

use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Event\EventListenerInterface;
use Cake\ORM\TableRegistry;

class StatsListener implements EventListenerInterface {
    public $Languages;

    public function implementedEvents() {
        return [
            'Model.Audio.audioCreated' => 'incrementAudioCount',
            'Model.Audio.audioDeleted' => 'decrementAudioCount',
        ];
    }

    public function __construct() {
        $this->Languages = TableRegistry::get('Languages');
    }

    private function updateAudioCount($event, $offset) {
        $audio = $event->getData('audio');
        $Audios = $event->getSubject();
        try {
            $sentence = $Audios->Sentences->get($audio->sentence_id);
            $stat = $this->Languages->find()
                ->where(['code' => $sentence->lang])
                ->firstOrFail();
            $stat->audio += $offset;
            $this->Languages->save($stat);
        } catch (RecordNotFoundException $e) {
        }
    }

    public function incrementAudioCount($event) {
        $this->updateAudioCount($event, 1);
    }

    public function decrementAudioCount($event) {
        $this->updateAudioCount($event, -1);
    }
}
