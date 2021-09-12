<?php
use Migrations\AbstractMigration;

class MultipleAudiosPerSentences extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('audios');
        $table->addColumn('audio_idx', 'integer', [
            'after' => 'sentence_id',
            'default' => 1,
            'limit' => 4,
            'null' => false,
        ])->addIndex(
            /* We want to add a unique constraint on (sentence_id, audio_idx)
             * here. Apparently, adding an index with a unique constraint is
             * the same thing as adding a unique constraint in MySQL.
             */
            ['sentence_id', 'audio_idx'],
            ['unique' => true, 'name' => 'sentence_id_audio_idx']
        )->removeIndex(
            /* Remove this index because the newly-added sentence_id_audio_idx
             * can be equally used to perform lookups on column sentence_id.
             */
            ['sentence_id']
        );
        $table->save();
    }

    public function down()
    {
        $table = $this->table('audios');
        $table->removeIndexByName('sentence_id_audio_idx')
        ->removeColumn('audio_idx')
        ->addIndex(
            ['sentence_id'],
            ['name' => 'sentence_id']
        );
        $table->save();
    }
}
