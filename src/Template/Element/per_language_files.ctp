<?php
/*+
 * Tatoeba Project, free collaborative creation of languages corpuses project
 * Copyright (C) 2020  Tatoeba Project
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

if (count($options) == 1): ?>
    <dd>
        <a href="<?= $options[0]['url'] ?>">
            <?= basename($options[0]['url']) ?>
        </a>
    </dd>
<?php else: ?>
    <dd ng-init="<?= $model ?> = '<?= $options[0]['url'] ?>'" ng-cloak>
        <p>
            <a ng-href="{{<?= $model ?>}}">{{<?= $model ?> | filename}}</a>
        </p>
        <md-radio-group ng-model="<?= $model ?>"
        ng-init="<?= $model ?>Select = '<?= $options[1]['url'] ?>'">
            <md-radio-button class="file-selection" value="<?= $options[0]['url'] ?>">
               <?= $options[0]['language'] ?>
            </md-radio-button>
            <div layout="row" layout-align="start center">
                <md-radio-button class="file-selection" ng-value="<?= $model ?>Select" flex="none">
                    <?= __('Only sentences in:') ?>
                </md-radio-button>
                <md-select class="file-selection" ng-model="<?= $model ?>Select"
                ng-change="<?= $model ?> = <?= $model ?>Select" flex="initial">
                    <?php foreach (array_slice($options, 1) as $option): ?>
                        <md-option value="<?= $option['url'] ?>">
                            <?= $option['language'] ?>
                        </md-option>
                    <?php endforeach; ?>
                </md-select>
            </div>
        </md-radio-group>
    </dd>
<?php endif; ?>
