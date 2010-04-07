<?php
/**
    Tatoeba Project, free collaborativ creation of languages corpuses project
    Copyright (C) 2009 Etienne Deparis <etienne.deparis@umaneti.net>

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
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */

echo $this->element('pmmenu');

if ($isAReply) {
    if ($replyToTitle == '') {
        $replyToTitle = __('Re: [no subject]', true);
    } else {
        $replyToTitle = 'Re: ' . $replyToTitle;
    }
}
?>
<div id="main_content">
    <div class="module">
        <h2><?php echo __('Send new message', true); ?></h2>
        <?php
        echo $this->element('pmtoolbox');

        $msgFinalPreContent = '';

        if (isset($replyToContent)) {
            $msgFinalPreContent = $replyToContent;
        } else if (isset($msgPreContent)) {
            $msgFinalPreContent = $msgPreContent;
        }

        if (isset($errorString)) {
            echo '<div style="border:1px solid red;color:red;">'
            . $errorString
            . '</div>';
        }

        echo $form->create('PrivateMessage', array('action' => 'send'));
        echo $form->input(
            'recpt',
            array(
                'label' => __('to', true),
                'default' => $toUserLogin,
                'type' => 'text',
                'maxLength' => 255
            )
        );
        echo $form->input(
            'title',
            array(
                'default' => $replyToTitle,
                'type' => 'text'
            )
        );
        echo $form->input(
            'content',
            array(
                'label' => '',
                'default' => $msgFinalPreContent
            )
        );
        echo $form->end(__('Send', true));
        ?>
    </div>
</div>
