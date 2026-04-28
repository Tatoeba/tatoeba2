<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2010  HO Ngoc Phuong Trang <tranglich@gmail.com>
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
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     https://tatoeba.org
 */
$username = h($username);
if ($userExists) {
    $title = format(__("{user}'s Wall messages"), array('user' => $username));
} else {
    $title = format(__("There's no user called {username}"), array('username' => $username));
}
$this->set('title_for_layout', $this->Pages->formatTitle($title));
?>

<?php if ($userExists): ?>
<div id="annexe_content">
    <?= $this->element('users_menu', array('username' => $username)) ?>
</div>
<?php endif; ?>

<div id="main_content">
<section class="md-whiteframe-1dp">
    <?php
    if (!$userExists) {
        $this->CommonModules->displayNoSuchUser($username);
    } else { ?>
        <md-toolbar class="md-hue-2">
            <div class="md-toolbar-tools">
            <h2>
            <?php
            echo $this->Paginator->counter(
                array(
                    'format' => format(
                        __('{user}\'s messages on the Wall (total&nbsp;{n})'),
                        array('user' => $username, 'n' => '{{count}}')
                    )
                )
            );
            ?>
            </h2>
            </div>
        </md-toolbar>

        <md-content>
        <?php
        $this->Pagination->display();
        ?>

        <div class="wall">
        <?php
        foreach ($messages as $message) {
            echo $this->element('wall/message', [
                'message' => $message,
                'isRoot' => true
            ]);
        }
        ?>
        </div>

        <?php
        $this->Pagination->display();
        ?>
        </md-content>
        <?php
    } ?>
</section>
</div>
