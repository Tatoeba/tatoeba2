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
 * @link     https://tatoeba.org
 */
use App\Model\CurrentUser;
?>
<div class="section md-whiteframe-1dp">
    <?php /* @translators: header text on sentence page */ ?>
    <h2><?php echo __('Sentence text') ?></h2>

<?php
echo format(
    /* @translators: placeholder is the name of the license
       of the sentence text, could be CC BY 2.0 FR or CC0 */
    __('License: {}'),
    $this->SentenceLicense->getLicenseName($license)
);
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
        /* @translators: text in front of the dropdown to modify the
           license of the sentence text, could be CC BY 2.0 FR or CC0 */
        'label' => __('License:'),
        'options' => $this->SentenceLicense->getLicenseOptions(CurrentUser::isAdmin()),
        'value' => $license,
    );
    echo $this->Form->control('license', $options);
    echo $this->Form->submit(__('Change license'));
    echo $this->Form->end();
}
?>
</div>
