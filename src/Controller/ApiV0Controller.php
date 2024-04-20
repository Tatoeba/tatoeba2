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
class ApiV0Controller extends AppController
{
    /**
     * Show Json of specified sentence id.
     * 
     * @param mixed $id Id of the sentence.
     * 
     * @return void
     */
    public function sentence($id = null) {
        $this->loadModel('Sentences');

        if (is_numeric($id)) {
            // Retrieve the sentence
            $sentence = $this->Sentences->getSentenceWith($id, [
                'translations' => true,
                'sentenceDetails' => true
            ]);

            return $this->response
                ->withType('application/json')
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
        $this->loadModel('Sentences');

        $search = new SentencesSearchForm();
        $search->setData($this->request->getQueryParams());

        $limit = 10;
        $sphinx = $search->asSphinx();
        $sphinx['page'] = $this->request->getQuery('page');
        $sphinx['limit'] = $limit;

        $query = $this->Sentences
            ->find('hideFields')
            ->find('withSphinx')
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
        } catch (Exception $e) {
            throw new \Cake\Http\Exception\BadRequestException();
        }
    
        $json = json_encode([
            'paging' => $this->request->params['paging'],
            'results' => $results
        ]);
        return $this->response
            ->withType('application/json')
            ->withStringBody($json);
    }
}
