<?php
class SuggestedModificationsController extends AppController {

	var $name = 'SuggestedModifications';
	var $helpers = array('Html', 'Form');

	function index() {
		$this->SuggestedModification->recursive = 0;
		$this->set('suggestedModifications', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid SuggestedModification.', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->set('suggestedModification', $this->SuggestedModification->read(null, $id));
	}

	function add() {
		if (!empty($this->data)) {
			$this->SuggestedModification->create();
			if ($this->SuggestedModification->save($this->data)) {
				$this->Session->setFlash(__('The SuggestedModification has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The SuggestedModification could not be saved. Please, try again.', true));
			}
		}
		$sentences = $this->SuggestedModification->Sentence->find('list');
		$this->set(compact('sentences'));
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid SuggestedModification', true));
			$this->redirect(array('action'=>'index'));
		}
		if (!empty($this->data)) {
			if ($this->SuggestedModification->save($this->data)) {
				$this->Session->setFlash(__('The SuggestedModification has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The SuggestedModification could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->SuggestedModification->read(null, $id);
		}
		$sentences = $this->SuggestedModification->Sentence->find('list');
		$this->set(compact('sentences'));
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for SuggestedModification', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->SuggestedModification->del($id)) {
			$this->Session->setFlash(__('SuggestedModification deleted', true));
			$this->redirect(array('action'=>'index'));
		}
	}

}
?>