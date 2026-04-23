<?php
use Migrations\AbstractMigration;

class AddDisabledAudioSource extends AbstractMigration
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
