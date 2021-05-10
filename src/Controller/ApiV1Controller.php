<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 *
 * @category PHP
 * @package  Tatoeba
 * @license  Affero General Public License
 * @link     https://tatoeba.org
 */
namespace App\Controller;

use App\Controller\AppController;
use App\Form\SentencesSearchForm;
use App\Model\CurrentUser;
use Cake\Core\Configure;
use Exception;

/**
 * Controller for Api.
 *
 * @category Sentences
 * @package  Controllers
 * @author   githubshrek
 * @license  Affero General Public License
 * @link     https://tatoeba.org
 */
class ApiV1Controller extends AppController
{
    public $name = 'Sentences';

    /**
     * Show Json of specified sentence id.
     * 
     * @param mixed $id Id of the sentence.
     * 
     * @return void
     */
    public function sentence($id = null) {
        // We will handle the output directly, so as not to display the header and footer
        $this->disableAutoRender();

        if (is_numeric($id)) {
            // Retrieve the sentence
            $sentence = $this->Sentences->getSentenceWith($id, [
                'translations' => true,
                'sentenceDetails' => true
            ]);


            return $this->getResponse()
                ->withStringBody($sentence);
        }
    }

    /**
     * Show in Json format a list of sentence ids which matches a given search.
     * 
     * This method is based on the SentencesController.php search() method.
     *
     * @return void
     */
    public function search()
    {
        // We will handle the output directly, so as not to display the header and footer
        $this->disableAutoRender();

        if (!Configure::read('Search.enabled')) {
            $this->render('search_disabled');
            return;
        }

        /* Apply search criteria and sort */
        $search = new SentencesSearchForm();
        $search->setData($this->request->getQueryParams());

        /* Control input */
        if ($search->generateRandomSeedIfNeeded()) {
            return $this->redirect(Router::url($search->getData()));
        }
        $search->checkUnwantedCombinations();

        $limit = CurrentUser::getSetting('sentences_per_page');
        $sphinx = $search->asSphinx();
        $sphinx['page'] = $this->request->query('page');
//        $sphinx['limit'] = $limit;

        // TODO: improve on hard-coded limit to the number of API results
        $sphinx['limit'] = 100; // Limit the API to returning 100 entries.

        $model = 'Sentences';
        $query = $this->Sentences
            ->find('hideFields')
            ->find('withSphinx')
            ->find('nativeMarker')
            ->find('filteredTranslations', [
                'translationLang' => $search->getData('to'),
            ])
            ->select($this->Sentences->fields())
            ->contain($this->Sentences->contain(['translations' => true]));

        $this->paginate = [
            'limit' => $limit,
            'sphinx' => $sphinx,
        ];

        try {
            $results = $this->paginate($query);
            $real_total = $this->Sentences->getRealTotal();
            $results = $this->Sentences->addHighlightMarkers($results);
            $this->set(compact('results', 'real_total'));
        } catch (Exception $e) {
            $syntax_error = strpos($e->getMessage(), 'syntax error,') !== FALSE;
            if ($syntax_error) {
                $this->set('syntax_error', true);
            } else {
                $this->loadComponent('Error');
                $error_code = $this->Error->traceError('Search error: ' . $e->getMessage());
                $this->set('error_code', $error_code);
            }
        }

        $strippedQuery = preg_replace('/"|=/', '', $search->getData('query'));
        $this->loadModel('Vocabulary');
        $vocabulary = $this->Vocabulary->findByText($strippedQuery);

        $searchableLists = $search->getSearchableLists(CurrentUser::get('id'));
        $ignored = $search->getIgnoredFields();

        $this->set($search->getData());
        $this->set(compact('ignored', 'searchableLists', 'vocabulary'));
        $this->set(
            'is_advanced_search',
            !is_null($this->request->getQuery('trans_to'))
        );

        $ids = array();
        foreach ($results as $sentence) {
            $ids[] = $sentence->id;
        }
    
        $ids_json = json_encode($ids);
        $ids_json = '{ "ids" : ' . $ids_json . ' }';
        return $this->getResponse()
            ->withStringBody($ids_json);
    }
}
