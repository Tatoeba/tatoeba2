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

class SentenceAnnotation extends AppModel{
	var $name = 'SentenceAnnotation';
	var $belongsTo = array('Sentence');
	
    /**
     * Get annotations for the sentence specified.
     */
    function getAnnotationsForSentenceId($sentenceId){	
		$this->recursive = -1;
        return $this->findAllBySentenceId($sentenceId);
    }
    
	/**
     * Get annotations for the sentence specified.
     */
	function search($query){
        $query = preg_replace("/<space>/", " ", $query);
        return $this->find(
			'all'
			, array(
				'conditions' => array('SentenceAnnotation.text LIKE' => '%'.$query.'%')
			)
		);
    }
	
	/**
	 * Replace text in results of a search by some other text.
	 */
	function replaceTextInAnnotations($textToReplace, $textReplacing){
        $textToReplace = preg_replace("/<space>/", " ", $textToReplace);
		$annotations = $this->search($textToReplace);
		$newAnnotations = array();
		
		foreach($annotations as $annotation){
			$pattern = quotemeta($textToReplace);
			$annotation['SentenceAnnotation']['text'] = preg_replace(
				"/$pattern/"
				, $textReplacing
				, $annotation['SentenceAnnotation']['text']
			);
			
			$newAnnotations[] = $annotation;
			
			$this->id = $annotation['SentenceAnnotation']['id'];
			$this->saveField('text', $annotation['SentenceAnnotation']['text']);
		}
		
		return $newAnnotations;
	}
}
?>
