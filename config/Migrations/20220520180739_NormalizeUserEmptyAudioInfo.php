<?php
use Migrations\BaseMigration;

class NormalizeUserEmptyAudioInfo extends BaseMigration
{
    public function up()
    {
        $table = $this->table('users');
        foreach (['audio_license', 'audio_attribution_url'] as $column) {
            $this->getUpdateBuilder()
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
