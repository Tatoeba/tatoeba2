<?php
use Migrations\AbstractMigration;

class CreateTranslationImports extends AbstractMigration
{
    public function change()
    {
        $imports = $this->table('translation_imports', [
            'encoding' => 'utf8mb4',
            'collation' => 'utf8mb4_bin',
        ]);

        $imports
            ->addColumn('user_id', 'integer', ['null' => false])
            ->addColumn('queued_job_id', 'integer', ['null' => true])
            ->addColumn('source_lang', 'string', [
                'limit' => 4,
                'null' => false,
            ])
            ->addColumn('target_lang', 'string', [
                'limit' => 4,
                'null' => false,
            ])
            ->addColumn('context', 'string', [
                'limit' => 255,
                'default' => '',
                'null' => false,
            ])
            ->addColumn('filename', 'string', [
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('status', 'string', [
                'limit' => 20,
                'null' => false,
            ])
            ->addColumn('message', 'string', [
                'limit' => 40,
                'null' => true,
            ])
            ->addColumn('created', 'datetime', ['null' => true])
            ->addColumn('modified', 'datetime', ['null' => true])
            ->addIndex(['user_id', 'created'])
            ->create();

        $items = $this->table('translation_import_items', [
            'encoding' => 'utf8mb4',
            'collation' => 'utf8mb4_bin',
        ]);

        $items
            ->addColumn('translation_import_id', 'integer', [
                'null' => false,
            ])
            ->addColumn('record_number', 'integer', ['null' => false])
            ->addColumn('sentence_id', 'integer', ['null' => false])
            ->addColumn('source_text', 'text', ['null' => false])
            ->addColumn('translation_text', 'text', ['null' => false])
            ->addColumn('translation_id', 'integer', ['null' => true])
            ->addColumn('status', 'string', [
                'limit' => 20,
                'null' => false,
            ])
            ->addColumn('message', 'string', [
                'limit' => 40,
                'null' => true,
            ])
            ->addIndex(
                ['translation_import_id', 'record_number'],
                ['unique' => true]
            )
            ->addIndex(['translation_import_id', 'status'])
            ->create();
    }
}