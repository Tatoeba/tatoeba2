<?php
use Migrations\AbstractMigration;

class NormalizeUserEmptyAudioInfo extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('users');
        $table->changeColumn('audio_license', 'string', [
            'limit' => 50,
            'null' => false,
            'default' => '',
        ]);
        $table->changeColumn('audio_attribution_url', 'string', [
            'limit' => 255,
            'null' => false,
            'default' => '',
        ]);
        $table->update();
    }
}
