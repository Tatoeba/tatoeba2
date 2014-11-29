<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2014  Gilles Bedel
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

class PagesHelper extends AppHelper
{
    public function formatTitle($pageTitle) {
        return $pageTitle . __(' - Tatoeba', true);
    }

    public function formatTitleWithResultCount($paginator, $title) {
        $n = $paginator->counter(array('format' => '%count%'));
        /* @translators: this formats the title at the top of every page
           that shows a list of sentence (search, browse by language,
           adopt sentences…) by appending the number of results. Note
           the use of &nbsp; which is a non-breaking space. */
        $title = format(__n('{title} ({n}&nbsp;result)',
                            '{title} ({n}&nbsp;results)',
                            $n, true),
                        compact('title', 'n')
        );
        $title = sprintf('<h2>%s</h2>', $title);
        return $title;
    }
}
?>
