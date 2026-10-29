<?php
namespace App\Model\Table;

use Cake\ORM\Table;

class TranslationImportItemsTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('translation_import_items');
        $this->setPrimaryKey('id');

        $this->belongsTo('TranslationImports');
    }
}