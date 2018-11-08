<?php
/**
 * Tatoeba Project, free collaborative creation of languages corpuses project
 * Copyright (C) 2009 Allan SIMON <allan.simon@supinfo.com>
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
 * @author   Allan Simon <allan.simon@supinfo.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class SinogramsTable extends Table
{
    public function initialize(array $config)
    {
        parent::initialize($config);
        $this->hasMany('SinogramSubglyphs');
    }

    /**
    * search a sinogram matching the input requirements
    *
    * @param array $subGlyphArray an array containing in each cells a sublyph
    * @param int   $minStrokes    minimum strokes the matching characters should
    *                             have
    * @param int   $maxStrokes    maximum etc...
    *
    * @return array of matching sinograms
    */

    public function search($subGlyphArray , $minStrokes = -1 , $maxStrokes = -1 )
    {
        if (count($subGlyphArray) == 0) {
            return array();
        }

        // if there's only character in subglyph search then we should
        // execute one more request to try to match itself
        // it's because the characters decomposition data are trees
        // so you have'nt loopback
        $onlyOneCharacter = false ;
        if ( count($subGlyphArray) == 1 ) {
            $sinogram = $subGlyphArray[0];
            $onlyOneCharacter = true;
        }

        // TODO I think there's a better way to do that
        // as IN statement will be very slow when exceeding 5+
        $numberOfSublyph = count($subGlyphArray);
        $result = $this->find('all', array(
            'fields' => array('Sinograms.id', 'Sinograms.glyph'),
            'conditions' => array(
                'subglyph IN' => $subGlyphArray, // IN statement is here
            ),
            'join' => array(array(
                'table' => 'sinogram_subglyphs',
                'alias' => 'SinogramSubglyphs',
                'conditions' => array('SinogramSubglyphs.glyph = Sinograms.glyph'),
            )),
            'group' => array(
                '`Sinograms`.`glyph` '
                .'HAVING count(DISTINCT SinogramSubglyphs.subglyph) = '.$numberOfSublyph
            ),
        ))->toArray();

        // if there's only character, it should be logical that this character match
        // itself
        if ($onlyOneCharacter) {
            $thisGlyph = $this->find("all", array(
                "fields" => array("Sinograms.id","Sinograms.glyph"),
                "conditions" => array("Sinograms.glyph" => $sinogram  )
            ))->first();
            array_push($result, $thisGlyph);
        }


        return $result ;
    }

    /**
    * explode the input sinograms into their composants
    *
    * @param array $toExplodeArray array of sinograms to explod
    *
    * @return array of compounds
    */

    public function explode($toExplodeArray)
    {
        $explodeArraySize = count($toExplodeArray);

        if ($explodeArraySize == 0){
            return array();
        }

        for ($i = 0 ; $i < $explodeArraySize; $i++) {
            $toExplodeArray[$i] = "'".$toExplodeArray[$i] ."'" ;
        }

        $toExplodeString = implode(",", $toExplodeArray);

        $results = $this->_connection->query(
            "SELECT glyph , subglyph
            FROM  sinogram_subglyphs
            WHERE glyph IN (". $toExplodeString .") ;"
        );
        return $results;
    }

    /**
     * get informations of a given sinogram
     *
     * @param string $sinogram the sinogram we want informations of
     *
     * @return array informations concerning this sinogram
     */

    public function informations($sinogram)
    {
        $return = $this->find(
            'all',
            array(
                "fields" => array(
                    "Sinogram.glyph",
                    "Sinogram.strokes",
                    "Sinogram.english",
                    "Sinogram.chin-pinyin",
                    "Sinogram.jap-on",
                    "Sinogram.jap-kun"
                ),
                "conditions" => array("Sinogram.glyph" => $sinogram )
            )
        )->first();

        return $return ;
    }
}
