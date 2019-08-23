<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2009  HO Ngoc Phuong Trang <tranglich@gmail.com>
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
 *
 * PHP version 5
 *
 * @category PHP
 * @package  Tatoeba
 * @author   HO Ngoc Phuong Trang <trang@tatoeba.org>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */
?>
<div class="section" md-whiteframe="1">
    <h2><?php echo __('License') ?></h2>

<?php
echo $this->Sentences->License->getLicenseName($license);

if ($canEdit) {
    echo "<hr>";

    echo $this->Form->create(
        'Sentence',
        array(
            'url' => array('action' => 'edit_license'),
            'type' => 'post',
        )
    );
    echo $this->Form->hidden(
        'id',
        array('value' => $sentenceId)
    );
    $options = array(
        'label' => __('License:'),
        'options' => $this->Sentences->License->getLicenseOptions(),
        'value' => $license,
    );
    if (is_null($license)) {
        $options['empty'] = true;
    }
    echo $this->Form->control('license', $options);
    echo $this->Form->submit(__d('admin', 'Change'));
    echo $this->Form->end();
}

// Workaround for error:
//    The "License" alias has already been loaded with the following config
// Until someone finds a good way to refactor the involved helpers.
$this->helpers()->unload('License');
?>
</div>
