<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

class Language extends Entity
{
    use LanguageNameTrait;

    const MAX_LEVEL = 5;

    protected $_virtual = [
        'name',
    ];

    protected function _getName()
    {
        return $this->codeToNameAlone($this->code);
    }
}
