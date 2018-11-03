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
namespace App\View\Helper;


class SearchHelper extends AppHelper
{
    public $helpers = array('Form', 'Session', 'Languages');

    private $langs;

    public function getLangs() {
        $restrictSearchLangsEnabled = $this->Session->read('restrict_search_langs_enabled');
        if ($restrictSearchLangsEnabled) {
            $langArray = $this->Languages->profileLanguagesArray(false, false);
            $currentUserLanguages = CurrentUser::getProfileLanguages();
        }

        if (!$restrictSearchLangsEnabled || empty($currentUserLanguages)) {
            return $this->Languages->getSearchableLanguagesArray();
        } else {
            return $langArray;
        }
    }

    public function selectLang($fieldName, $selectedLanguage, $options = array()) {
        if (!$this->langs) {
            $this->langs = $this->getLangs();
            array_unshift(
                $this->langs, array('und' => __x('searchbar', 'Any language'))
            );
        }

        $options = array_merge(
            array(
                'class' => 'language-selector',
                'empty' => false,
                'options' => $this->langs,
                'value' => $selectedLanguage,
            ),
            $options
        );
        return $this->Form->input(
            $fieldName,
            $options
        );
    }

    public function highlightMatches($highlight, $text) {
        list($markers, $excerpts) = $highlight;
        foreach ($excerpts as $excerpt) {
            $excerpt = h($excerpt);
            $from = str_replace($markers, '', $excerpt);
            $to = str_replace(
                $markers,
                array('<span class="match">', '</span>'),
                $excerpt
            );
            $text = str_replace($from, $to, $text);
        }
        return $text;
    }
}
?>
