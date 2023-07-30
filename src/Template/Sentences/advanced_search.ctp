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

$this->set('isResponsive', true);

$title = __x('title', 'Advanced search');
$this->set('title_for_layout', $this->Pages->formatTitle($title));
?>

<section class="md-whiteframe-1dp">
<md-toolbar>
    <div class="md-toolbar-tools">
        <h2><?php echo $title; ?></h2>
    </div>
</md-toolbar>

<md-content>
<?php echo $this->element('advanced_search_form', array(
    'searchableLists' => $searchableLists,
)); ?>
</md-content>
</section>