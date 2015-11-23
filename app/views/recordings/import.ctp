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

$this->set('title_for_layout', $pages->formatTitle(__d('admin', 'Import recordings', true)));

?>
<div id="main_content">
    <div class="module">
        <h2><?php __d('admin', 'Import recordings'); ?></h2>
        <h3><?php __d('admin', 'Files detected'); ?></h3>
        <?php if ($filesToImport): ?>
            <p><?php __dn(
                'admin',
                'The following file has been detected '.
                'inside the import directory.',
                'The following files have been detected '.
                'inside the import directory.',
                count($filesToImport)
            ); ?></p>
        <?php echo $html->nestedList($filesToImport); ?>
        <?php else: ?>
            <p><?php __('admin', 'No files have been detected '.
                                 'inside the import directory.'); ?></p>
        <?php endif; ?>
    </div>
</div>
