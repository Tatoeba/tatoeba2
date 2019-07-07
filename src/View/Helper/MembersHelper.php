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
 * @link     http://tatoeba.org
 */
namespace App\View\Helper;

use App\Model\Entity\User;
use App\View\Helper\AppHelper;


/**
 * Helper for members.
 *
 * @category Utilities
 * @package  Helpers
 * @author   SIMON Allan <allan.simon@supinfo.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */

class MembersHelper extends AppHelper
{
    public $helpers = array('Html');

    /**
     * To display the names of the people listed in Team & Credits.
     *
     * @param string $username    Username of the member.
     * @param string $realName    Real name of the member.
     * @param string $description Description of the member's participation.
     *
     * @return void
     */
    public function creditsToUser($realName, $username = null, $description = null)
    {
        $realName = h($realName);
        ?>
        <div class="person">
            <div class="realName">
            <?php
            echo $realName;
            ?>
            </div>

            <div class="description">
            <?php echo $description; ?>
            </div>

            <?php
            if (!empty($username)) {
                ?>
                <div class="username">
                <?php
                $profileIcon = $this->Html->image(
                    IMG_PATH . 'profile.png',
                    array(
                        "alt" => __('Profile'),
                        "width" => 14,
                        "height" => 14
                    )
                );
                echo $this->Html->link(
                    $profileIcon.' '.$username,
                    array(
                        'controller' => 'user',
                        'action' => 'profile',
                        $username
                    ),
                    array(
                        'escape' => false
                    )
                );
                ?>
                </div>
                <?php
            }
            ?>

        </div>
        <?php
    }


    /**
     *
     */
    public function image($username = null, $imageName = null, $options = array())
    {
        if (empty($imageName)) {
            $imageName = 'unknown-avatar.png';
        }
        $options = $options + array(
            'width' => 36,
            'height' => 36,
        );
        if ($username) {
            $options['url'] = [
                'controller' => 'user',
                'action' => 'profile',
                $username
            ];
            $options['alt'] = $username;
        } else {
            $options['alt'] = __('Former member');
        }
        return $this->Html->image(
            '/img/profiles_36/'.$imageName,
            $options
        );
    }

    /**
     * Display "Edit" button in the profile.
     *
     * @param array  $linkPath Path to the edit page.
     * @param String $label    Label of the button. If null, it displays "Edit".
     *
     * @return void
     */
    public function displayEditButton($linkPath, $label = null)
    {
        if (empty($label)) {
            $label = __('Edit');
        }
        ?>
        <div class="editOption">
        <?php
        echo $this->Html->link(
            $label,
            $linkPath
        );
        ?>
        </div>
        <?php
    }


    /**
     * Gives i18n for a role name.
     *
     * @param string $role Name of the role in the database.
     *
     * @return string
     */
    public function groupName($role)
    {
        switch ($role) {
            case User::ROLE_ADMIN             : return __('admin');
            case User::ROLE_CORPUS_MAINTAINER : return __('corpus maintainer');
            case User::ROLE_ADV_CONTRIBUTOR   : return __('advanced contributor');
            case User::ROLE_CONTRIBUTOR       : return __('contributor');
            case User::ROLE_INACTIVE          : return __('inactive');
            case User::ROLE_SPAMMER           : return __('suspended');
            default                           : return null;
        }
    }


    public function displayLanguageLevel($level)
    {
        $result = '<div ng-cloak class="languageLevel">';
        $maxLanguageLevel = 5;
        if (isset($level)) {
            for ($i = 0; $i < $maxLanguageLevel; $i++) {
                if ($i < $level) {
                    $result .= '<md-icon class="md-primary">star</md-icon>';
                } else {
                    $result .= '<md-icon>star_border</md-icon>';
                }
            }
        }
        $result .= '</div>';

        return $result;
    }
}
?>
