<?php
namespace App\Controller\VHosts\Api;

use App\Controller\VHosts\Api\ApiController;

class SentencesController extends ApiController
{
    private function exposedFields() {
        $sentence = [
            'fields' => ['id', 'text', 'lang', 'script', 'license', 'owner'],
            'audios' => ['fields' => [
                'author', 'license'
            ]],
            'transcriptions' => ['fields' => [
                'script', 'text', 'needsReview', 'type', 'html', 'markup']
            ],
        ];
        $exposedFields = $sentence;
        $exposedFields['translations'] = $sentence;
        $exposedFields['translations']['indirect_translations'] = $sentence;
        return compact('exposedFields');
    }

    public function get($id) {
        $this->loadModel('Sentences');
        $query = $this->Sentences
            ->find('exposedFields', $this->exposedFields())
            ->find('filteredTranslations')
            ->select($this->Sentences->fields())
            ->where(['Sentences.id' => $id])
            ->contain($this->Sentences->contain(['translations' => true]));

        $results = $query->firstOrFail();
        $response = [
            'data' => $results,
        ];

        $this->set('response', $response);
        $this->set('_serialize', 'response');
        $this->RequestHandler->renderAs($this, 'json');
    }
}
