<?php
namespace App\Controller\VHosts\Api;

use App\Controller\VHosts\Api\ApiController;
use App\Model\Search;
use Cake\ORM\Query;

/**
 * @OA\Schema(
 *   schema="Sentence",
 *   description="A sentence object that contains both sentence text and metadata about the sentence.",
 *   @OA\Property(property="id", description="The sentence identifier", type="integer", example="1234")
 * )
 */
class SentencesController extends ApiController
{
    private function exposedFields() {
        $sentence = [
            'fields' => ['id', 'text', 'lang', 'script', 'license', 'owner'],
            'audios' => ['fields' => [
                'author', 'license', 'attribution_url', 'download_url',
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
              ->where(['IndirectTranslations.license !=' => ''])
              ->contain([
                  'Users' => ['fields' => ['id', 'username']],
                  'Audios' => $audioContainment,
                  'Transcriptions' => $transcriptionsContainment,
              ]);
            return $q;
        };
        $translationsContainment = function (Query $q) use ($audioContainment, $transcriptionsContainment, $indirTranslationsContainment) {
            $q->select($this->fields())
              ->where(['Translations.license !=' => ''])
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

    /**
     * @OA\PathItem(path="/unstable/sentences/{id}",
     *   @OA\Parameter(name="id", in="path", required=true, description="The sentence identifier.",
     *     @OA\Schema(ref="#/components/schemas/Sentence/properties/id")
     *   ),
     *   @OA\Get(
     *     summary="Get a sentence",
     *     description="Get sentence text as well as metadata about this sentence and related sentences.",
     *     tags={"Sentences"},
     *     @OA\Response(response="200", description="Success."),
     *     @OA\Response(response="400", description="Invalid ID parameter."),
     *     @OA\Response(response="404", description="There is no sentence with that ID or it has been deleted.")
     *   )
     * )
     */
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

        try {
            $results = $query->firstOrFail();
        } catch (\InvalidArgumentException $e) {
            return $this->response->withStatus(400, 'Invalid parameter "id"');
        }
        $response = [
            'data' => $results,
        ];

        $this->set('response', $response);
        $this->set('_serialize', 'response');
        $this->RequestHandler->renderAs($this, 'json');
    }

    private function _prepareSearch() {
        $search = new Search();
        $search->filterByCorrectness(false);

        $q = $this->getRequest()->getQuery('q');
        $search->filterByQuery($q);

        $lang = $this->getRequest()->getQuery('lang');
        if ($lang) {
            $lang = $search->filterByLanguage($lang);
            if (is_null($lang)) {
                return $this->response->withStatus(400, 'Invalid parameter "lang"');
            }
        } else {
            return $this->response->withStatus(400, 'Required parameter "lang" missing');
        }

        $trans = $this->getRequest()->getQuery('trans');
        if ($trans) {
            $trans = $search->filterByTranslationLanguage($trans);
            if (is_null($trans)) {
                return $this->response->withStatus(400, 'Invalid parameter "trans"');
            } else {
                $search->filterByTranslation('limit');
            }
        }

        $includeUnapproved = $this->getRequest()->getQuery('include_unapproved');
        if (!is_null($includeUnapproved)) {
            if ($includeUnapproved == 'yes') {
                $search->filterByCorrectness(null);
            } else {
                return $this->response->withStatus(400, 'Parameter "include_unapproved" can only have "yes" as value');
            }
        }

        return $search;
    }

    public function search() {
        $search = $this->_prepareSearch();
        if (!($search instanceOf Search)) {
            return $search;
        }

        $sphinx = $search->asSphinx();
        $sphinx['page'] = $this->request->getQuery('page');
        $limit = $this->request->getQuery('limit', self::DEFAULT_RESULTS_NUMBER);
        $sphinx['limit'] = $limit > self::MAX_RESULTS_NUMBER ? self::MAX_RESULTS_NUMBER : $limit;

        $this->loadModel('Sentences');

        $query = $this->Sentences
            ->find('withSphinx')
            ->find('filteredTranslations', [
                'translationLang' => $search->getTranslationFilter('language'),
            ])
            ->find('exposedFields', $this->exposedFields())
            ->select($this->Sentences->fields())
            ->where(['Sentences.license !=' => '']) // FIXME use Manticore filter instead
            ->contain($this->contain());

        $this->paginate = [
            'limit' => self::DEFAULT_RESULTS_NUMBER,
            'maxLimit' => self::MAX_RESULTS_NUMBER,
            'sphinx' => $sphinx,
        ];
        $results = $this->paginate($query);
        $response = [
            'data' => $results,
        ];

        $this->set('results', $response);
        $this->set('_serialize', 'results');
        $this->RequestHandler->renderAs($this, 'json');
    }
}
