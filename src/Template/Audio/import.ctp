<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2015  Gilles Bedel
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

use Cake\Core\Configure;

$this->set('title_for_layout', $this->Pages->formatTitle(__d('admin', 'Import recordings')));

$canImport = !$lastImportJob || $lastImportJob->completed;
?>
<div id="main_content">
<div class="module">
<h2><?php echo __d('admin', 'Import recordings'); ?></h2>

<?php if ($filesImported) : ?>
    <?php
        $dateTime = $lastImportJob->completed->i18nFormat([\IntlDateFormatter::LONG, \IntlDateFormatter::LONG]);
    ?>

    <h3><?= __d('admin', format('Last import report (completed on {dateTime})', compact('dateTime'))) ?></h3>

    <div id="import-totals">
        <?php echo format(
            __dn('admin',
                 'A total of {numberOfFiles} recording has been imported.',
                 'A total of {numberOfFiles} recordings have been imported.',
                 $filesImported['total'],
                 true),
            array('numberOfFiles' => $filesImported['total'])
        ); ?>
        <br/>
        <?php
        if (!empty($filesImported['replaced'])) {
            echo format(
                __dn('admin',
                     '{numberOfFiles} was a replacement of an existing recording.',
                     '{numberOfFiles} were replacements of existing recordings.',
                     $filesImported['replaced'],
                     true),
                array('numberOfFiles' => $filesImported['replaced'])
            );
            echo '<br/>';
        }
        unset($filesImported['total']);
        unset($filesImported['replaced']);
        $subTotals = array();
        foreach ($filesImported as $lang => $numberOfFiles) {
            $subTotals[] = format(
                __dn('admin',
                     '{numberOfFiles} was {language}.',
                     '{numberOfFiles} were {language}.',
                     $numberOfFiles,
                     true),
                array('numberOfFiles' => $numberOfFiles,
                      'language' => $this->Languages->codeToNameToFormat($lang))
            );
        }
        echo join('<br/>', $subTotals);
        ?>
    </div>

    <?php if ($errors) : ?>
        <p><?= __d('admin', 'The following errors occurred during the last import.') ?></p>
        <div id="import-report">
            <?= $this->safeForAngular(join('<br/>', $errors)) ?>
        </div>
    <?php endif; ?>
<?php endif; ?>

<?php
if (!$canImport) {
    echo $this->Html->tag('h3', __d('admin', 'Audio import status'));
    if (!$lastImportJob->fetched) {
        $timeAgo = $lastImportJob->created->timeAgoInWords();
        $status = __d('admin', format(
            'Import task enqueued {timeAgo}. Please refresh '.
            'the page in a little while to check if import is completed.',
            compact('timeAgo')
        ));
    } else {
        $timeAgo = $lastImportJob->fetched->timeAgoInWords();
        $status = __d('admin', format(
            'An import is currently running since {timeAgo}. Please refresh '.
            'the page in a little while to check if import is completed.',
            compact('timeAgo')
        ));
    }
    echo $this->Html->tag('p', $status);
}
?>

<h3><?php echo __d('admin', 'Files detected'); ?></h3>
<?php
echo $this->Form->create(null, ['ng-non-bindable' => '']);
if ($filesToImport): ?>
    <p><?php echo format(
        __dn(
            'admin',
            'A total of {count} file has been detected '.
            'inside the import directory.',
            'A total of {count} files have been detected '.
            'inside the import directory.',
            count($filesToImport),
            true
        ),
        array('count' => count($filesToImport))
    ); ?></p>
    <table id="files-detected">
    <?php
    echo $this->Html->tableHeaders(
        array(
            __d('admin', 'File name'),
            __d('admin', 'Sentence id'),
            __d('admin', 'Language'),
            __d('admin', 'Existing audio'),
            __d('admin', 'Replace existing?'),
            __d('admin', 'May be imported'),
        )
    );
    foreach ($filesToImport as $file) {
        $lang = isset($file['lang']) ?
                $this->Languages->codeToNameAlone($file['lang']) :
                __d('admin', 'N/A');
        $sentenceId = isset($file['sentenceId']) ?
                      $this->Html->link(
                          $file['sentenceId'], array(
                              'controller' => 'sentences',
                              'action' => 'show',
                              $file['sentenceId']
                          )
                      ) :
                      __d('admin', 'Invalid');
        if ($file['valid']) {
            $checkboxName = 'replace['.$file['sentenceId'].']';
            $disabled = count($file['audios']) == 0;
            $replaceCheckbox = $this->Form->checkbox($checkboxName, [
                'value' => '1',
                'checked' => !$disabled,
                'disabled' => $disabled,
            ]);
        } else {
            $replaceCheckbox = '';
        }
        $hasaudio = isset($file['audios']) ?
                    (
                        count($file['audios']) > 0 ?
                        (
                            implode(
                                __(', '),
                                array_map(
                                    function ($audio) { return $audio->user->username; },
                                    $file['audios']
                                )
                            )
                        ) :
                        __d('admin', 'None')
                    ) :
                    __d('admin', 'N/A');
        $isValid = $file['valid'] ?
                   __d('admin', 'Yes') :
                   __d('admin', 'No');

        echo $this->Html->tableCells(
            array(
                $file['fileName'],
                $sentenceId,
                $lang,
                $hasaudio,
                $replaceCheckbox,
                $isValid,
            ),
            array('class' => 'even')
        );
    }
    ?>
    </table>
<?php else: ?>
    <p><?php echo __d('admin', 'No files have been detected '.
                      'inside the import directory.'); ?></p>
<?php endif; ?>
<?php
echo $this->Form->input('audioAuthor', ['required' => true]);
echo $this->Form->submit(__d('admin', 'Import'), $canImport ? [] : ['disabled' => true]);
echo $this->Form->end();
?>
</div>
</div>
