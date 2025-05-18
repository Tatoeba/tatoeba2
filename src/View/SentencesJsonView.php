<?php
namespace App\View;

use Cake\View\JsonView;
use App\Model\CurrentUser;

class SentencesJsonView extends JsonView
{
    public function initialize()
    {
        parent::initialize();
        $this->loadHelper('Sentences');
    }

    protected function _serialize($serialize): string
    {
        $data = $this->_dataToSerialize($serialize);

        if (isset($data['sentence'])) {    
            $data['sentence']->expandLabel = $this->Sentences->getExpandLabel($data['sentence']);
        }
        
        return parent::_serialize($serialize);;
    }
}