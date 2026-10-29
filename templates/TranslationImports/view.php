<?php
use App\Lib\LanguagesLib;
use App\Model\Table\TranslationImportsTable;

$this->set(
    'title_for_layout',
    $this->Pages->formatTitle(__('Translation import'))
);

$labels = TranslationImportsTable::labels();
$sourceLangTag = LanguagesLib::languageTag($import->source_lang);
$targetLangTag = LanguagesLib::languageTag($import->target_lang);
$statusLabel = $labels[$import->status] ?? $import->status;

if ($import->status === 'previewed' && $readyCount === 0) {
    $statusLabel = __('Nothing to import');
}
?>

<div id="main_content" class="translation-imports">
    <div class="module">
        <h2><?= __('Translation import') ?></h2>

        <p ng-non-bindable><?= h($import->filename) ?></p>
        <?php if ($import->context !== ''): ?>
            <p class="import-help" ng-non-bindable><?= h($import->context) ?></p>
        <?php endif; ?>
        <p>
            <?= h($this->Languages->codeToNameAlone($import->source_lang)) ?>
            →
            <?= h($this->Languages->codeToNameAlone($import->target_lang)) ?>
        </p>
        <p class="import-status" role="status"><?= h($statusLabel) ?></p>

        <?php if ($import->message): ?>
            <p><?= h($labels[$import->message] ?? $import->message) ?></p>
        <?php endif; ?>

        <dl class="import-counts">
            <?php foreach ($counts as $status => $count): ?>
                <div>
                    <dt><?= h($labels[$status]) ?></dt>
                    <dd><?= h($count) ?></dd>
                </div>
            <?php endforeach; ?>
        </dl>

        <?php if ($import->status === 'previewed' && $readyCount === 0): ?>
            <p>
                <?= __('No records are ready to import. Check the details below, ' .
                    'then upload a corrected file.') ?>
            </p>
        <?php endif; ?>

        <?php if ($import->status === 'previewed' && $readyCount > 0): ?>
            <p>
                <?= __('Review every translation below. Whitespace and Unicode ' .
                    'are normalized just as when adding a sentence normally.') ?>
            </p>
            <p class="import-help">
                <?= __('Existing sentences may be reused instead of added again.') ?>
            </p>

            <?= $this->Form->create(null, [
                'url' => ['action' => 'confirm', $import->id],
                'ng-cloak' => true,
            ]) ?>

            <md-checkbox ng-model="importConfirmed"
                         ng-init="importConfirmed = false" class="md-primary">
                <?= __('I made these translations and have checked ' .
                    'the preview.') ?>
            </md-checkbox>
            <?= $this->Form->checkbox('confirmed', [
                'value' => '1',
                'class' => 'ng-hide',
                'ng-model' => 'importConfirmed',
            ]) ?>

            <md-button type="submit" class="md-raised md-primary"
                       ng-disabled="!importConfirmed">
                <?= __('Import translations') ?>
            </md-button>
            <?= $this->Form->end() ?>
        <?php endif; ?>

        <?php if ($readyCount > 0 && in_array(
            $import->status,
            ['queued', 'importing', 'failed'],
            true
        )): ?>
            <?php if ($import->status === 'queued'): ?>
                <p>
                    <?= __('Your import is waiting to be processed. ' .
                        'Refresh this page to check its progress.') ?>
                </p>
            <?php endif; ?>
            <p>
                <?= __('If processing has stopped, resume the remaining records. ' .
                    'Completed records will not be repeated.') ?>
            </p>

            <?= $this->Form->create(null, [
                'url' => ['action' => 'resume', $import->id],
            ]) ?>
            <md-button type="submit" class="md-raised md-primary">
                <?= __('Resume remaining records') ?>
            </md-button>
            <?= $this->Form->end() ?>
        <?php endif; ?>

        <?php if ($import->status === 'previewed'): ?>
            <?= $this->Form->create(null, [
                'url' => ['action' => 'discard', $import->id],
            ]) ?>
            <md-button type="submit">
                <?= __('Discard preview') ?>
            </md-button>
            <?= $this->Form->end() ?>
        <?php endif; ?>

        <p class="import-navigation">
            <?= $this->Html->link(
                __('Refresh'),
                ['action' => 'view', $import->id]
            ) ?>
            ·
            <?= $this->Html->link(
                __('Download JSON report'),
                ['action' => 'report', $import->id]
            ) ?>
            ·
            <?= $this->Html->link(
                __('Back to your imports'),
                ['action' => 'index']
            ) ?>
        </p>
    </div>

    <div class="module">
        <h3><?= __('Translation records') ?></h3>
        <p class="import-help import-table-help">
            <?= __('Scroll horizontally to see all columns.') ?>
        </p>
        <div class="import-table" role="region" tabindex="0"
             aria-label="<?= h(__('Translation records')) ?>">
            <table>
                <thead>
                    <tr>
                        <th scope="col">
                            <?php /* @translators: one logical CSV record,
                                which can contain multiple physical lines */ ?>
                            <?= __('Record') ?>
                        </th>
                        <th scope="col"><?= __('Source') ?></th>
                        <th scope="col"><?= __('Translation') ?></th>
                        <th scope="col"><?= __('Status') ?></th>
                        <th scope="col"><?= __('Details') ?></th>
                    </tr>
                </thead>
                <tbody ng-non-bindable>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td><?= h($item->record_number) ?></td>
                            <td>
                                <?= $this->Html->link(
                                    '#' . $item->sentence_id,
                                    [
                                        'controller' => 'sentences',
                                        'action' => 'show',
                                        $item->sentence_id,
                                    ]
                                ) ?>
                                <br>
                                <span dir="auto" lang="<?= h($sourceLangTag) ?>">
                                    <?= h($item->source_text) ?>
                                </span>
                            </td>
                            <td dir="auto" lang="<?= h($targetLangTag) ?>">
                                <?= h($item->translation_text) ?>
                            </td>
                            <td><?= h($labels[$item->status]) ?></td>
                            <td>
                                <?php if ($item->message): ?>
                                    <?= h(
                                        $labels[$item->message] ?? $item->message
                                    ) ?>
                                <?php endif; ?>

                                <?php if ($item->translation_id): ?>
                                    <?= $this->Html->link(
                                        '#' . $item->translation_id,
                                        [
                                            'controller' => 'sentences',
                                            'action' => 'show',
                                            $item->translation_id,
                                        ]
                                    ) ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php $this->Pagination->display(); ?>
    </div>
</div>
