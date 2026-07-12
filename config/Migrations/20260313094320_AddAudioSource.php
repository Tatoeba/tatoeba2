<?php
use Migrations\BaseMigration;

class AddAudioSource extends BaseMigration
{
    public function change()
    {
        $table = $this->table('audios');
        $table->addColumn('source', 'enum', [
            'after' => 'external',
            'default' => 'tatoeba',
            'null' => false,
            'values' => ['tatoeba', 'commons'],
        ]);
        $table->update();
    }
}
