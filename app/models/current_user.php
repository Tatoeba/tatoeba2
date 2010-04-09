<?php
/*
 * Static methods that can be used to retrieve the logged in user
 * from anywhere
 *
 * Copyright (c) 2008 Matt Curry
 * www.PseudoCoder.com
 * http://github.com/mcurry/cakephp/tree/master/snippets/static_user
 * http://www.pseudocoder.com/archives/2008/10/06/accessing-user-sessions-from-models-or-anywhere-in-cakephp-revealed/
 *
 * @author      Matt Curry <matt@pseudocoder.com>
 * @license     MIT
 *
 */
 
class CurrentUser extends AppModel
{
    public $useTable = null;
    
    public public $hasMany = array(
        'SentenceComments',
        'Contributions',
        'Sentences',
        'SentencesLists'
    );
    
    function &getInstance($user=null) 
    {
        static $instance = array();

        if ($user) {
        $instance[0] =& $user;
        }

        if (!$instance) {
        trigger_error(__("User not set.", true), E_USER_WARNING);
        return false;
        }

        return $instance[0];
    }
     
    function store($user) 
    {
        if (empty($user)) {
        return false;
        }

        CurrentUser::getInstance($user);
    }
     
    function get($path) {
        $_user =& CurrentUser::getInstance();
        
        $path = str_replace('.', '/', $path);
        if (strpos($path, 'User') !== 0) {
        $path = sprintf('User/%s', $path);
        }

        if (strpos($path, '/') !== 0) {
        $path = sprintf('/%s', $path);
        }

        $value = Set::extract($path, $_user);

        if (!$value) {
        return false;
        }

        return $value[0];
    }
    
    
    /**
     * Indicates if current user is admin or not.
     * 
     * @return bool
     */
    public function isAdmin()
    {
        return CurrentUser::get('group_id') == 1;
    }
    
    
    /**
     * Indicates if current user is trusted or not.
     * 
     * @return bool
     */
    public function isTrusted()
    {
        return CurrentUser::get('group_id') < 4;
    }
    
    
    /**
     * Indicates if current user is owner of the sentence with given id.
     *
     * @param int $sentenceId Id of the sentence.
     * 
     * @return bool
     */
    public function isOwnerOfSentence($sentenceId)
    {
        $sentence = $this->Sentence->find(
            'first',
            array(
                'fields' => array(),
                'conditions' => array(
                    'Sentence.id' => $sentenceId,
                    'Sentence.user_id' => CurrentUser::get('id')
                ),
                'contain' => array()
            )
        );
        return !empty($sentence);
    }
    
    /**
     * Indicates if current user can link/unlink translations to the sentence of
     * given id.
     *
     * @param int $sentenceId Id of the main sentence.
     * 
     * @return bool
     */
    public function canLinkAndUnlink($sentenceId)
    {
        if (CurrentUser::isAdmin()) {
            return true;
        }
        
        $sentenceBelongsToUser = CurrentUser::isOwnerOfSentence($sentenceId);
        
        return $sentenceBelongsToUser && CurrentUser::isTrusted();
    }
}
?>