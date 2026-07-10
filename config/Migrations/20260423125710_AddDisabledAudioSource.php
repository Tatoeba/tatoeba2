<?php
use Migrations\BaseMigration;

class AddDisabledAudioSource extends BaseMigration
{
    public function change()
    {
        $table = $this->table('disabled_audios');
        $table->addColumn('source', 'enum', [
            'after' => 'external',
            'default' => 'tatoeba',
            'null' => false,
            'values' => ['tatoeba', 'commons'],
        ]);
        $table->update();
    }
}
