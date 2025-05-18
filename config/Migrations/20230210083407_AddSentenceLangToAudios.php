<?php
use Migrations\AbstractMigration;

class AddSentenceLangToAudios extends AbstractMigration
{
    public function up()
    {
        foreach (['audios', 'disabled_audios'] as $tableName) {
            $this
                ->table($tableName)
                ->addColumn('sentence_lang', 'string', [
                    'after' => 'sentence_id',
                    'limit' => 4,
                    'null' => true,
                    'default' => null,
                    'comment' => 'Denormalized field from sentences table',
                ])
                ->update();

            $this->execute("update $tableName a join sentences on sentences.id = a.sentence_id set a.sentence_lang = sentences.lang");
        }
    }

    public function down()
    {
        foreach (['audios', 'disabled_audios'] as $tableName) {
            $this
                ->table($tableName)
                ->removeColumn('sentence_lang')
                ->update();
        }
    }
}
