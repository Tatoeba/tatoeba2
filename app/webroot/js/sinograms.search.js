/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2009  Allan SIMON (allan.simon@supinfo.com)
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


$(document).ready(function(){
    
    function loadSinogramInformations(sinogram){
        var rootUrl = get_tatoeba_root_url();
        $.post(
            rootUrl + "/sinograms/load_sinogram_informations",
            { "sinogram" : sinogram  },
            function(data){
                $("#information_part").empty();
                $("#information_part").html(data);
            },
            "html"
        );
    }

    function loadExampleSentence(sinogram){
        var rootUrl = get_tatoeba_root_url();
        $("#example_part").html("<div class='loading'><img src='/img/loading.gif' alt='loading'></div>");
          $.post(
            rootUrl + "/sinograms/load_example_sentence"
            , { "sinogram" : sinogram  } 
            , function(data){
                $("#example_part").empty();
                $("#example_part").html(data);
            }
            , "html"
        );

 
    }

    /*
    ** load radicals with the given number of strokes 
    */
    function loadRadicals(numberOfStroke){
          
        var rootUrl = get_tatoeba_root_url();
        $.post(
            rootUrl + "/sinograms/load_radicals"
            , { "number" : numberOfStroke  } 
            , function(data){
                $("#radicalsList").empty();
                $("#radicalsList").html(data);
            }
            , "html");
    }

    /*
    ** copy a radical to search text input
    */
    function copyRadicalToSearchInput(clickedRadical){
        clickedSubglyph = clickedRadical.html();
        $("#SinogramSubglyphs").val($("#SinogramSubglyphs").val() + clickedSubglyph );


    }

    /*
    ** copy a subglyph given by sendExplodeInformations to search text input
    */
    function copyToSearchInput(clickedSubglyphDiv){
        clickedSubglyph = clickedSubglyphDiv.html();
        $("#SinogramSubglyphs").val($("#SinogramSubglyphs").val() + clickedSubglyph );


    }
 

    /*
    ** send a post request to search characters matching research parameters
    **
    */
    
    function sendSearchInformations(){
        var subglyphs = $("#SinogramSubglyphs").val() ;
        
        if (subglyphs != ''){
            var rootUrl = get_tatoeba_root_url();
            $.post(
                rootUrl + "/sinograms/search"
                , { "data[Sinogram][subglyphs]" : subglyphs } 
                , function(data){
                    $("#searchResults").remove();
                    $("#search_part").append(data);
                }
                , "html"
            );
        } else {
            $("#searchResults").remove();
        }
        return false;
    }
   
    /*
    ** send a post request to retrieve decomposition of some characters
    */
    function sendExplodeInformations(){
        var toExplode = $("#SinogramToExplode").val()
        var rootUrl = get_tatoeba_root_url();
        
        $.post(
            rootUrl + "/sinograms/explode"
            , { "data[Sinogram][toExplode]" : toExplode } 
            , function(data){
                $("#explosionResults").empty();
                $("#explosionResults").html(data);
            }
            , "html"
        );
        return false;
    }


    $("#SinogramSearchForm").submit(function(){
        sendSearchInformations() ;
        return false;
    });

    $("#SinogramExplodeForm").submit(function(){
        sendExplodeInformations();
        return false;
    });

    $('.glyph').live('click',
        function(){
            glyph = $(this).html();
            loadSinogramInformations(glyph);
            loadExampleSentence(glyph);
        }
    );

    $('.subGlyph').live('click',
        function(){
            copyToSearchInput($(this));
        }
    );

    $('.radical').live('click',
        function(){
            copyRadicalToSearchInput($(this));
        }
    );

    $('.radicalStrokesNumber').click(function(){
            loadRadicals($(this).html()); 
        }
    );
});
