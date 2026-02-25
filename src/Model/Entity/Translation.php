<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;
use App\Lib\LanguagesLib;
use App\Model\CurrentUser;

class Translation extends Entity
{
    use LanguageNameTrait;

    protected $_virtual = [
        'lang_name',
        'dir',
        'lang_tag',
    ];

    protected $_hidden = [
        '_joinData',
        'SentencesTranslations',
    ];

    protected function _getLangName()
    {
        return $this->codeToNameAlone($this->lang);
    }

    protected function _getDir()
    {
        return LanguagesLib::getLanguageDirection($this->lang);
    }

    protected function _getLangTag()
    {
        return LanguagesLib::languageTag($this->lang, $this->script);
    }

    protected function _getOwner()
    {
        return $this->user ? $this->user->username : null;
    }

    protected function _getIsUnapproved()
    {
        return $this->correctness == \App\Model\Table\SentencesTable::MIN_CORRECTNESS;
    }
    
    public function addTranslationOwner($translations)
    {
        $user_id = CurrentUser::get('id');
        
        $this['isLinkOwner'] = false;        
        foreach ($translations as $translation) {
            if ($translation['translation_id'] == $this->id 
                && $translation['user_id'] == $user_id) {
                $this['isLinkOwner'] = true;
                return;
            }
        }
    }
}
