<?php
/*
    Tatoeba Project, free collaborativ creation of languages corpuses project
    Copyright (C) 2009 Allan SIMON <allan.simon@supinfo.com>

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Affero General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

class Sinogram extends AppModel{

    var $name = "Sinogram";
    var $hasMany = array ('Sinogram_subglyph'); 

    /*
    ** search a sinogram matching the input requirements
    ** subGlyphArray => an array containing in each cells a sublyph
    ** minStrokes => minimum strokes the matching characters should have
    ** maxStrokes => maximum etc...
    */
    
    function search($subGlyphArray , $minStrokes = null , $minStrokes = null ){ 
        // TODO HACK SPOTTED : recursive should be banned ! use containable instead
        $this->Sinogram->recursive = 0 ;

        $onlyOneCharacter = false ;
        if ( count($subGlyphArray) == 1 ) {
            $sinogram = $subGlyphArray[0];
            $onlyOneCharacter = true;
        }
        for ( $i = 0 ; $i < count($subGlyphArray) ; $i++){
            $subGlyphArray[$i] = "'".$subGlyphArray[$i] ."'" ;
        }

        $subglyphsString= implode("," , $subGlyphArray) ;
        $result = $this->query(
            "SELECT Sinogram.id , Sinogram.glyph
             FROM  sinogram_subglyphs , sinograms as Sinogram
             WHERE
                Sinogram.`glyph` = sinogram_subglyphs.`glyph`
                AND  subglyph IN ( ". $subglyphsString  ." )
            GROUP BY glyph 
                HAVING count(DISTINCT sinogram_subglyphs.subglyph) =". count ($subGlyphArray ) .";" 
        );

        // if there's only character, it should be logical that this character match itself
        if ($onlyOneCharacter ){
            $thisGlyph = $this->find('first',array(
                "fields" => array("Sinogram.id","Sinogram.glyph"),
                "conditions" => array("Sinogram.glyph" => $sinogram  )
                )
            );
            array_push($result,$thisGlyph);
            //pr ($result) ;
            //pr ($thisGlyph);
        }


        return $result ;
    }
        
    /*
    ** explode the input sinograms into their composants
    **
    */
    function explode($toExplodeArray ){
        for ( $i = 0 ; $i < count($toExplodeArray) ; $i++){
            $toExplodeArray[$i] = "'".$toExplodeArray[$i] ."'" ;
        }
        $toExplodeString= implode("," , $toExplodeArray) ;

        $results = $this->query(
            "SELECT glyph , subglyph
            FROM  sinogram_subglyphs
            WHERE glyph IN (". $toExplodeString .") ;"
        
         ); 
        return $results;
    }

    function informations($sinogram){
        $this->recursive = 0 ; 
        $return = $this->find('first',array(
            "fields" => array("Sinogram.glyph","Sinogram.strokes" , "Sinogram.english" , "Sinogram.`chin-pinyin`", "Sinogram.`jap-on`" , "Sinogram.`jap-kun`"),
            "conditions" => array("Sinogram.glyph" => $sinogram )

            )
        );

        return $return ;

    }

}
