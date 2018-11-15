<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2009  HO Ngoc Phuong Trang <tranglich@gmail.com>
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

use App\View\Helper\AppHelper;


/**
 * Helper to display images.
 *
 * @category Default
 * @package  Helpers
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */
class ImagesHelper extends AppHelper
{
    public $helpers = array(
        'Html',
    );

    /**
     * Returns the HTML to display a SVG image.
     *
     * @param string $imageName Name of the SVG file without the extension.
     * @param array  $options   Attributes to add in the <svg> tag, such as width,
     *                          height, class...
     * @param string $id        Identifier of the SVG. If null, it will be
     *                          equal to imageName.
     *
     * @return string
     */
    public function svgIcon($imageName, $options = array(), $id = null) {
        $imgPath = $this->assetTimestamp('/img/' . $imageName . '.svg');
        if (empty($id)) {
            $id = $imageName;
        }

        return $this->Html->tag(
            'svg',
            "<use xlink:href='$imgPath#$id'></use>",
            $options
        );
    }


    public function correctnessIcon($correctness)
    {
        $icons = array(
            1 => 'ok',
            0 => 'unsure',
            -1 => 'not-ok',
        );

        return $this->svgIcon(
            $icons[$correctness],
            array(
                'width' => 20,
                'height' => 16,
                'class' => $icons[$correctness]
            )
        );
    }
}