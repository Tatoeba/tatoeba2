<?php
/*
    Tatoeba Project, free collaborative creation of multilingual corpuses project
    Copyright (C) 2009  HO Ngoc Phuong Trang <tranglich@gmail.com>

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Affero General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

App::import('Core', 'Sanitize');

class SentenceAnnotationsController extends AppController {
	var $name = 'SentenceAnnotations';
	
	function index(){
	}
	
	/**
	 * Display annotations for given sentence.
	 */
	function show($sentenceId = null){
		if($sentenceId == null){
			$this->redirect(array("action" => "show", $this->data['SentenceAnnotation']['sentence_id']));
		}else{
			Sanitize::html($sentenceId);
			$this->SentenceAnnotation->Sentence->recursive = -1;
			$this->set('sentence', $this->SentenceAnnotation->Sentence->findById($sentenceId));
			$this->set('annotations', $this->SentenceAnnotation->getAnnotationsForSentenceId($sentenceId));
		}
	}
	
	/**
	 * Save annotation.
	 */
	function save(){
		if (!empty($this->data)) {
			if(!isset($this->data['SentenceAnnotation']['id'])){
				$this->SentenceAnnotation->create();
			}
			if($this->SentenceAnnotation->save($this->data)){
				$this->redirect(array("action" => "show", $this->data['SentenceAnnotation']['sentence_id']));
			}
		}
	}
	
	
	/**
	 * Save annotation.
	 */
	function delete($id, $sentence_id){
		if($this->SentenceAnnotation->del($id)){
			$this->redirect(array("action" => "show", $sentence_id));
		}
	}
	
	
	/**
	 * Search annotations.
	 */
	function search($query = null){
		if($query == null){
			
			$this->redirect(array('action'=>'search', $this->data['SentenceAnnotation']['text']));
			
		}else{
		
			Sanitize::html($query);
			
			$annotations = null;
			if(rtrim($query) != ''){
				$annotations = $this->SentenceAnnotation->getAnnotationsMatchingQuery($query);
			}
			$this->set('query', $query);
			$this->set('annotations', $annotations);
			
		}
	}
	
	
	/**
	 * Replace text in annotations by some other text.
	 * TODO The replacement process needs optimization...
	 */
	function replace(){
		$textToReplace = $this->data['SentenceAnnotation']['textToReplace'];
		$textReplacing = $this->data['SentenceAnnotation']['textReplacing'];
		$newAnnotations = $this->SentenceAnnotation->replaceTextInAnnotations(
			$textToReplace, $textReplacing
		);
		$this->set('textToReplace', $textToReplace);
		$this->set('textReplacing', $textReplacing);
		$this->set('annotations', $newAnnotations);
	}
}
?>