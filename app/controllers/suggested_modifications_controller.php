<?php
class SuggestedModificationsController extends AppController {

	var $name = 'SuggestedModifications';
	var $helpers = array('Html', 'Form', 'Comments', 'Sentences');

	function beforeFilter() {
	    parent::beforeFilter(); 
		
		// setting actions that are available to everyone, even guests
	    $this->Auth->allowedActions = array('index','show','add','save_suggestion');
	}
	
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

	function add($sentence_id) {
		$s = new Sentence();
		$sentence = $s->findById($sentence_id);
		
		// checking which options user can access to
		$specialOptions = array('canComment' => false, 'canEdit' => false, 'canDelete' => false);
		if($this->Auth->user('id')){
			$specialOptions['canComment'] = true;
			$specialOptions['canEdit'] = $this->Acl->check(array('Group'=>$this->Auth->user('group_id')), 'controllers/Sentences/edit');
			$specialOptions['canDelete'] = $this->Acl->check(array('Group'=>$this->Auth->user('group_id')), 'controllers/Sentences/delete');
		}
		
		$this->set('specialOptions',$specialOptions);
		$this->set('sentence', $sentence);
	}
	
	function save_suggestion(){
		if (!empty($this->data)) {
			$this->SuggestedModification->create();
			
			$this->data['SuggestedModification']['submit_user_id'] = $this->Auth->user('id');
			$this->data['SuggestedModification']['submit_datetime'] = date("Y-m-d H:i:s");
			
			if ($this->SuggestedModification->save($this->data)) {
				$this->Session->setFlash(__('The suggestion has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The suggestion could not be saved. Please, try again.', true));
			}
		}
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
	
	function validate($id){
	}
	
	function refuse($id){
	}

}
?>