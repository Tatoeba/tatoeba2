<?php
use App\Model\Table\TranslationImportsTable;

$this->set(
    'title_for_layout',
    $this->Pages->formatTitle(__('Import translations'))
);

$languages = $this->Languages->onlyLanguagesArray();
$labels = TranslationImportsTable::labels();
?>

<div id="main_content" class="translation-imports">
    <div class="module">
        <h2><?= __('Import translations') ?></h2>

        <p>
            <?= __('Upload your own translations in a UTF-8 CSV or TSV file. ' .
                'You can review every record before importing.') ?>
        </p>
        <pre class="import-example" dir="ltr">sentence_id,source_text,translation_text</pre>
        <p>
            <?= __('Use these three columns in this order. The header row is ' .
                'optional. Copy the source text exactly as it appears on Tatoeba.')
            ?>
        </p>
        <p class="import-help">
            <?= __('Maximum: 500 records and 1 MiB. Quote fields containing ' .
                'delimiters or newlines. Double quotation marks inside quoted ' .
                'fields must be doubled.') ?>
        </p>

        <?= $this->Form->create(null, [
            'type' => 'file',
            'url' => ['action' => 'create'],
            'ng-cloak' => true,
        ]) ?>

        <div class="import-fields">
            <div>
                <label for="import-source-lang">
                    <?= __('Sentence language:') ?>
                </label>
                <?= $this->element('language_dropdown', [
                    'id' => 'import-source-lang',
                    'name' => 'source_lang',
                    'languages' => $languages,
                    'selectedLanguage' => 'importSourceLanguage',
                ]) ?>
            </div>

            <div>
                <label for="import-target-lang">
                    <?= __('Translation language:') ?>
                </label>
                <?= $this->element('language_dropdown', [
                    'id' => 'import-target-lang',
                    'name' => 'target_lang',
                    'languages' => $languages,
                    'selectedLanguage' => 'importTargetLanguage',
                ]) ?>
            </div>

            <?= $this->Form->control('delimiter', [
                'type' => 'select',
                'options' => [
                    'comma' => __('Comma'),
                    'semicolon' => __('Semicolon'),
                    /* @translators: the tab character used as a CSV separator */
                    'tab' => __('Tab'),
                ],
                /* @translators: CSV column separator (comma, semicolon or tab) */
                'label' => __('Delimiter'),
            ]) ?>
        </div>

        <md-input-container class="md-block">
            <?= $this->Form->control('context', [
                'label' => __('Project or source of these translations'),
                'maxlength' => 255,
                'dir' => 'auto',
            ]) ?>
        </md-input-container>

        <?= $this->Form->control('file', [
            'type' => 'file',
            'accept' => '.csv,.tsv,.txt',
            'label' => __('Translation file'),
            'required' => true,
        ]) ?>

        <md-button type="submit" class="md-raised md-primary"
                   ng-disabled="!importSourceLanguage.code ||
                       !importTargetLanguage.code">
            <?php /* @translators: button to preview a translation CSV upload */ ?>
            <?= __('Preview translations') ?>
        </md-button>
        <?= $this->Form->end() ?>
    </div>

    <div class="module">
        <h2><?= __('Your imports') ?></h2>

        <?php if ($imports->isEmpty()): ?>
            <p><?= __('You have no translation imports yet.') ?></p>
        <?php endif; ?>

        <ul class="import-history" ng-non-bindable>
            <?php foreach ($imports as $import): ?>
                <li>
                    <div>
                        <?= $this->Html->link(
                            $import->filename,
                            ['action' => 'view', $import->id]
                        ) ?>
                        <span class="import-status">
                            <?php if ($import->status === 'previewed'): ?>
                                <?= __('Preview') ?>
                            <?php else: ?>
                                <?= h($labels[$import->status] ?? $import->status) ?>
                            <?php endif; ?>
                        </span>
                    </div>
                    <div class="import-help">
                        <?= h(format(__('Import #{id}'), ['id' => $import->id])) ?>
                        ·
                        <?= h($this->Languages->codeToNameAlone(
                            $import->source_lang
                        )) ?>
                        →
                        <?= h($this->Languages->codeToNameAlone(
                            $import->target_lang
                        )) ?>
                        <?php if ($import->created): ?>
                            · <?= h($import->created->i18nFormat([
                                \IntlDateFormatter::MEDIUM,
                                \IntlDateFormatter::SHORT,
                            ])) ?>
                        <?php endif; ?>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>

        <?php $this->Pagination->display(); ?>
    </div>
</div>
