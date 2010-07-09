<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2010 Allan SIMON <allan.simon@supinfo.com>
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
 * PHP version 5
 *
 * @category PHP
 * @package  Tatoeba
 * @author   Allan SIMON <allan.simon@supinfo.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */

/**
 * Controller for tags
 *
 * @category Tags
 * @package  Controllers
 * @author   Allan SIMON <allan.simon@supinfo.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */

class TagsController extends AppController
{
    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    public $name = 'Tags';
   
    public $components = array('CommonSentence'); 
    /**
     * Before filter.
     * 
     * @return void
     */
    public function beforeFilter()
    {
        parent::beforeFilter();
        // setting actions that are available to everyone, even guests
        $this->Auth->allowedActions = array(
            "show_sentences_with_tag",
            'view_all',
        );
    } 

    /**
     * Add a tag to a Sentence
     * 
     * @return void
     */
    public function add_tag()
    {
        $tagName = $this->data['Tag']['tag_name'];
        $sentenceId = Sanitize::paranoid($this->data['Tag']['sentence_id']);
        $userId = CurrentUser::get("id"); 

        // if we try to access the page without POST info, we redirect to
        // the home page
        if (empty($tagName) || empty($sentenceId)) {
            $this->redirect(
                array(
                    'controller' => 'pages',
                    'action' => 'home',
                )
            );
        }

        // save and check if the tag has been added
        if (!$this->Tag->addTag($tagName, $userId, $sentenceId)) {
            $infoMessage = sprintf(
                __("Tag '%s' already exists for sentence #%s", true),
                $tagName,
                $sentenceId
            );
            $this->Session->setFlash($infoMessage);
        }
        
        $this->redirect(
            array(
                'controller' => 'sentences',
                'action' => 'show',
                $sentenceId
            ) 
        );
    
    }

    /**
     * Display all tags page
     * @TODO it's only a "better than nothing" page yet
     *
     */
    public function view_all()
    {
        
        $this->helpers[] = 'Tags';
        
        $allTags = $this->Tag->getAllTags();
        $this->set("allTags", $allTags);
    }

    /**
     * Remove a tag from a sentence when on this sentence page
     *
     * @param int $tagId      Id of the tag to remove from the this sentence
     * @param int $sentenceId Id of the sentence to remove the tag from
     *
     * @return void
     */

    public function remove_tag_from_sentence($tagId, $sentenceId)
    {
        if (!empty($tagId) && !empty($sentenceId)) {
            $this->Tag->removeTagFromSentence($tagId, $sentenceId);
        }
        $this->redirect(
            array(
                'controller' => 'sentences',
                'action' => 'show',
                $sentenceId
            ) 
        );
    
    }


    /**
     * Remove a tag from a sentence when on the "show all sentences with
     * this tag" page
     *
     * @param int $tagId      Id of the tag to remove from the this sentence
     * @param int $sentenceId Id of the sentence to remove the tag from
     *
     * @return void
     */
    public function remove_tag_of_sentence_from_tags_show($tagId, $sentenceId)
    {
        if (!empty($tagId) && !empty($sentenceId)) {
            $this->Tag->removeTagFromSentence($tagId, $sentenceId);
        }
        $this->redirect($_SERVER['HTTP_REFERER']);
    
    }


    /**
     * Display a list of all sentences with a given tag
     *
     * @param string $tagInternalName Internal name of the tag
     * @param string $lang     Filter only sentences in this language.
     *
     * @return void
     */
    public function show_sentences_with_tag($tagInternalName, $lang = null) 
    {

        $this->helpers[] = 'Pagination';
        $this->helpers[] = 'CommonModules';
        $this->helpers[] = 'Tags';

        $tag = $this->Tag->getInfoFromInternalName($tagInternalName); 
        $tagId = $tag['Tag']['id'];
        $tagName = $tag['Tag']['name'];
        
        $this->paginate = $this->Tag->paramsForPaginate($tagInternalName, 10, $lang);

        $sentencesIdsTaggerIds = $this->paginate('TagsSentences');
        
        $taggerIds = array();
        $sentenceIds = array();

        foreach ($sentencesIdsTaggerIds as $sentenceIdTaggerId) {
            $taggerIds[] = $sentenceIdTaggerId['TagsSentences']['user_id'];    
            $sentenceIds[] = $sentenceIdTaggerId['TagsSentences']['sentence_id'];   
        } 
        $allSentences = $this->CommonSentence->getAllNeededForSentences(
            $sentenceIds
        );

        $this->set('langFilter', $lang);
        $this->set('tagId', $tagId);
        $this->set('allSentences', $allSentences);
        $this->set('tagName', $tagName);
        $this->set('tagInternalName', $tagInternalName);
        $this->set('taggerIds', $taggerIds);

    }

}
?>
