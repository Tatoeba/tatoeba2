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
 * @link     https://tatoeba.org
 */

class MembersHelper extends AppHelper
{
    public $helpers = array('Html');

    /**
     *
     */
    public function image($user = null, $options = array())
    {
        $username = null;
        $imageName = null;
        if ($user) {
            $user = (object)$user;
            $username = $user->username;
            $imageName = $user->image;
        }
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
     * Gives i18n for a role name.
     *
     * @param string $role Name of the role in the database.
     *
     * @return string
     */
    public function groupName($role)
    {
        switch ($role) {
            /* @translators: one of the user status displayed on profile pages (noun) */
            case User::ROLE_ADMIN             : return __('admin');
            /* @translators: one of the user status displayed on profile pages */
            case User::ROLE_CORPUS_MAINTAINER : return __('corpus maintainer');
            /* @translators: one of the user status displayed on profile pages */
            case User::ROLE_ADV_CONTRIBUTOR   : return __('advanced contributor');
            /* @translators: one of the user status displayed on profile pages */
            case User::ROLE_CONTRIBUTOR       : return __('contributor');
            /* @translators: one of the user status displayed on profile pages */
            case User::ROLE_INACTIVE          : return __('inactive');
            /* @translators: one of the user status displayed on profile pages */
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
