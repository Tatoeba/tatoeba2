<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2016  Gilles Bedel
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
namespace App\View\Helper;

use App\View\Helper\AppHelper;


class AudioHelper extends AppHelper
{
    public $helpers = array(
        'Html',
        'License' => array(
            'availableLicences' => array(
                '',
                'CC BY 4.0',
                'CC BY-NC 4.0',
                'CC BY-SA 4.0',
                'CC BY-NC-ND 3.0',
            ),
        ),
    );

    private function defaultAttribUrl($username) {
        return array(
            'controller' => 'user',
            'action' => 'profile',
            $username
        );
    }

    public function displayAudioInfo($audio) {
        if ($audio['User']) {
            $username  = $audio['User']['username'];
            $license   = $audio['User']['audio_license'];
            $attribUrl = $audio['User']['audio_attribution_url'];
            if (empty($attribUrl)) {
                $attribUrl = $this->defaultAttribUrl($username);
            }
        } else {
            $username  = $audio['external']['username'];
            $license   = $audio['external']['license'];
            $attribUrl = $audio['external']['attribution_url'];
        }
        if (!empty($attribUrl)) {
            $username = $this->Html->link($username, $attribUrl);
        }
        $license = $this->License->getLicenseName($license);
?>
<ul>
  <li><?php echo format(__('Recorded by: {username}'), compact('username')); ?></li>
  <li><?php echo format(__('License: {license}'), compact('license')); ?></li>
</ul>
<?php
    }

    public function formatLicenceMessage($audioSettings, $username) {
        $url = empty($audioSettings['audio_attribution_url']) ?
               $this->defaultAttribUrl($username) :
               $audioSettings['audio_attribution_url'];
        $userLink = $this->Html->link($username, $url);

        $license = $audioSettings['audio_license'];
        if (empty($license)) {
            $msg = __('You may not reuse the following audio recordings '.
                      'outside the Tatoeba project because {userName} has '.
                      'not chosen any license for them.');
        } elseif ($license == 'Public domain') {
            $msg = __('The following audio recordings by '.
                      '{userName}, are licensed under the public domain.');
        } elseif ($this->License->isKnownLicense($license)) {
            $license = $this->License->licenseLink($license);
            $msg = __('The following audio recordings by '.
                      '{userName}, are licensed under the {licenseName} '.
                      'license.');
        } else {
            $msg = __('The following audio recordings by '.
                      '{userName}, are licensed under an unknown license.');
        }
        return format($msg, array(
            'userName' => $userLink,
            'licenseName' => $license
        ));
    }
}
?>
