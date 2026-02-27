<?php
use Migrations\AbstractMigration;

class NormalizeUserEmptyAudioInfo extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('users');
        foreach (['audio_license', 'audio_attribution_url'] as $column) {
            $this->getQueryBuilder()
                 ->update('users')
                 ->set($column, '')
                 ->where(["$column IS" => null])
                 ->execute();
        }
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
