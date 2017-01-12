<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2014 Gilles Bedel
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

if ($validationErrors) {
    $prefix = h(__n(
        'Please fix the following error or press the Cancel button.',
        'Please fix the following errors or press the Cancel button.',
        count($validationErrors),
        true
    ));
    $errors = $html->nestedList($validationErrors);
    echo $html->div('validation-errors', $prefix.$errors, array(
        'escape' => false
    ));
}

if ($transcr && $lang) {
    $transcriptions->displayTranscriptions($transcr, $lang, $sentenceOwnerId);
}
?>
