<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2020 Tatoeba Project
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

use Cake\View\Helper;

/**
 * Helper to ensure that AngularJS templates are added to the template cache
 * using a script block with type="text/ng-template", while avoiding duplicates.
 */
class AngularTemplateHelper extends Helper
{
    public $helpers = ['Html'];

    private $seenIds = [];

    /**
     * Adds a template to the scriptBottom block if it is not already present.
     *
     * @param string $template AngularJS template
     * @param string $id       Unique identifier for the template
     */
    public function addTemplate($template, $id) {
        if (isset($this->seenIds[$id])) return;

        $this->Html->scriptBlock(
            $template,
            [
                'block' => 'scriptBottom',
                'type' => 'text/ng-template',
                'id' => $id,
            ]
        );

        $this->seenIds[$id] = true;
    }
}
