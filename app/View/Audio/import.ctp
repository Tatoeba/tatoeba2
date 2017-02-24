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

$this->set('title_for_layout', $this->Pages->formatTitle(__d('admin', 'Import recordings')));

?>
<div id="main_content">
<div class="module">
<h2><?php echo __d('admin', 'Import recordings'); ?></h2>

<?php if ($filesImported) : ?>
    <h3><?php echo __d('admin', 'Import report'); ?></h3>

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
        unset($filesImported['total']);
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
        <p><?php echo __d('admin', 'The following errors occured during import.'); ?></p>
        <div id="import-report">
            <?php echo join('<br/>', $errors); ?>
        </div>
    <?php endif; ?>
<?php endif; ?>

<h3><?php echo __d('admin', 'Files detected'); ?></h3>
<?php if ($filesToImport): ?>
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
            __d('admin', 'Already has audio'),
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
        $hasaudio = isset($file['hasaudio']) ? (
                        $file['hasaudio'] ?
                        __('Yes') :
                        __('No')
                    ) :
                    __d('admin', 'N/A');
        $isValid = $file['valid'] ?
                   __('Yes') :
                   __('No');
        echo $this->Html->tableCells(
            array(
                $file['fileName'],
                $sentenceId,
                $lang,
                $hasaudio,
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
echo $this->Form->create();
echo $this->Form->input('audioAuthor');
echo $this->Form->end(__d('admin', 'Import'));
?>
</div>
</div>
