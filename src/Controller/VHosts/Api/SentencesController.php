<?php
namespace App\Controller\VHosts\Api;

use App\Controller\VHosts\Api\ApiController;
use Cake\ORM\Query;

class SentencesController extends ApiController
{
    private function exposedFields() {
        $sentence = [
            'fields' => ['id', 'text', 'lang', 'script', 'license', 'owner'],
            'audios' => ['fields' => [
                'author', 'license', 'attribution_url',
            ]],
            'transcriptions' => ['fields' => [
                'script', 'text', 'needsReview', 'type', 'html']
            ],
        ];
        $exposedFields = $sentence;
        $exposedFields['translations'] = $sentence;
        return compact('exposedFields');
    }

    private function fields() {
        return [
            'id',
            'text',
            'lang',
            'user_id',
            'correctness',
            'script',
            'license',
        ];
    }

    private function contain() {
        $audioContainment = function (Query $q) {
            $q->select(['id', 'external', 'sentence_id'])
              ->where(['audio_license !=' => '']) # exclude audio that cannot be reused outside of Tatoeba
              ->contain(['Users' => ['fields' => ['username', 'audio_license', 'audio_attribution_url']]]);
            return $q;
        };
        $transcriptionsContainment = [
            'fields' => ['sentence_id', 'script', 'text', 'needsReview'],
        ];
        $indirTranslationsContainment = function (Query $q) use ($audioContainment, $transcriptionsContainment) {
            $q->select($this->fields())
              ->contain([
                  'Users' => ['fields' => ['id', 'username']],
                  'Audios' => $audioContainment,
                  'Transcriptions' => $transcriptionsContainment,
              ]);
            return $q;
        };
        $translationsContainment = function (Query $q) use ($audioContainment, $transcriptionsContainment, $indirTranslationsContainment) {
            $q->select($this->fields())
              ->contain([
                  'Users' => ['fields' => ['id', 'username']],
                  'Audios' => $audioContainment,
                  'Transcriptions' => $transcriptionsContainment,
                  'IndirectTranslations' => $indirTranslationsContainment,
              ]);
            return $q;
        };

        return [
            'Translations' => $translationsContainment,
            'Users' => ['fields' => ['id', 'username']],
            'Audios' => $audioContainment,
            'Transcriptions' => $transcriptionsContainment,
        ];
    }

    public function get($id) {
        $this->loadModel('Sentences');
        $query = $this->Sentences
            ->find('filteredTranslations')
            ->find('exposedFields', $this->exposedFields())
            ->select($this->fields())
            ->where([
                'Sentences.id' => $id,
                'Sentences.license !=' => '',
            ])
            ->contain($this->contain());

        $results = $query->firstOrFail();
        $response = [
            'data' => $results,
        ];

        $this->set('response', $response);
        $this->set('_serialize', 'response');
        $this->RequestHandler->renderAs($this, 'json');
    }
}
