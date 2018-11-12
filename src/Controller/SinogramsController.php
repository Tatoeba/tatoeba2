<?php
 /**
    Tatoeba Project, free collaborative creation of multilingual corpuses project
    Copyright (C) 2009  Allan SIMON <allan.simon@supinfo.com>

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
 *
 * PHP version 5
 *
 * @category PHP
 * @package  Tatoeba
 * @author   Allan Simon <allan.simon@supinfo.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */
namespace App\Controller;

use App\Controller\AppController;


/**
 * Controller Class for sinograms
 *
 * @category PHP
 * @package  Tatoeba
 * @author   Allan Simon <allan.simon@supinfo.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
*/

class SinogramsController extends AppController
{

    public $name = 'Sinograms';
    public $components = array('Permissions');
    public $helpers = array('Form','Html');
    public $uses = array('Sinogram','Sentence');

    /**
     * to know who can do what
     *
     * @return void
     */

    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        $this->Auth->allow();

        $this->Security->unlockedActions = array(
          'search',
          'explode',
          'load_sinogram_informations',
          'load_example_sentence',
          'load_radicals'
        );
    }

    /**
     * index page
     *
     * @return void
     */


    public function index()
    {


    }

    /**
     * search all sinograms matching research criteria
     *
     * @return void
     */

    public function search()
    {
        $inputSubglyphs = $_POST["data"]["Sinogram"]["subglyphs"] ;
        /*use preg_match instead of str_split as we're working with utf8 characters*/
        preg_match_all('/./u', $inputSubglyphs, $array);

        /*launching the request*/
        $results = $this->Sinogram->search($array[0]);

        /*keep sinogram only*/
        $glyphs = array();
        $numberOfResults = count($results);
        for ($i = 0 ; $i < $numberOfResults; $i++) {
            array_push($glyphs, $results[$i]['Sinogram']['glyph']);
        }
        /*send them to the view*/

        $this->set("glyphs", $glyphs);
        //pr ($glyphs) ;
    }


    /**
     * retrieve characters from the form and explode them into compounds
     *
     * @return void
     */

    public function explode()
    {
        $toExplodeGlyphs = $_POST["data"]["Sinogram"]["toExplode"] ;
        /*use preg_match instead of str_split as we're working
         with utf8 characters*/
        preg_match_all('/./u', $toExplodeGlyphs, $array);

        $results = $this->Sinogram->explode($array[0]);
        $numberOfResults = count($results);

        /*
            regroup the result in this way
            [sinogram] => array(subglyph );

        */
        $explodedSinogramsArray = array();

        for ($i = 0 ; $i < $numberOfResults; $i++) {
            $currentGlyph = $results[$i]["sinogram_subglyphs"]["glyph"];
            if (!isset($explodedSinogramsArray[$currentGlyph])) {
                $explodedSinogramsArray[$currentGlyph] = array();
            }

            array_push(
                $explodedSinogramsArray[$currentGlyph],
                $results[$i]["sinogram_subglyphs"]["subglyph"]
            );

        }
        // pr ($explodedSinogramsArray ) ;
        $this->set("toExplodeGlyphs", $array[0]);
        $this->set("explodedSinogramsArray", $explodedSinogramsArray);

    }


    /**
     * load the informations we know about a sinograms
     *
     * @return void
     */

    public function load_sinogram_informations()
    {
        $sinogram = "噥";
        if (strlen(utf8_decode($_POST["sinogram"])) == 1) {
            $sinogram = $_POST["sinogram"];
        }
        $sinogramInformations = $this->Sinogram->informations($sinogram);




        $this->set("sinogramInformations", $sinogramInformations["Sinogram"]);
    }

    /**
     * load a sentence using this character
     *
     * @return void
     */

    public function load_example_sentence()
    {
        $sinogram = "噥";
        if (strlen(utf8_decode($_POST["sinogram"]))== 1) {
            $sinogram = $_POST["sinogram"];
        }
        $sentenceId
            = $this->Sentence->searchOneExampleSentenceWithSinogram($sinogram);
        $sentenceWhichUseThisSinogram = null ;

        if ($sentenceId != null ) {

            $sentenceWhichUseThisSinogram
                = $this->Sentence->getSentenceWithId($sentenceId);
            $specialOptions
                = $this->Permissions->getSentencesOptions(
                    $sentenceWhichUseThisSinogram, $this->Auth->user('id')
                );

            $this->set('specialOptions', $specialOptions);
            $this->set("sentenceFound", true);
        } else {
            $this->set("sentenceFound", false);
        }
        $this->set("sinogram", $sinogram);
        $this->set("sentence", $sentenceWhichUseThisSinogram);
    }


    /**
     * used to display the radicals list on the right panel
     *
     * @return void
     */
    public function load_radicals()
    {
        $numberOfStrokes = $_POST["number"];

        $radicalsArray = array();
        //TODO should include this array in the model to be more MVC compliant
        $radicalsArray["1"] = array (
            '一','丨','丶','丿','乀','乁','乙','乚','乛','亅'
        );
        $radicalsArray["2"] = array (
            '二','亠','人','亻','儿','入','丷','八','冂','冖','冫'
            ,'几','凵','刀','刂','力','勹','匕','匚','匸'
            ,'十','卜','卩','厂','厶','又','讠'
        );
        $radicalsArray["3"] = array (
            '口','囗','土','士','夂','夊','夕','大','女','子',
            '宀','寸','小','尢','尸','屮','山','巛','川','工',
            '己','巾','干','幺','广','廴','廾','弋','弓','彐',
            '彑','彡','彳','忄','扌','氵','丬','犭','纟','艹',
            '阝','长','门','阝','飞','饣','马'
        );
        $radicalsArray["4"] = array (
            '尣','心','戈','戶','户','手','支','攴','攵','文'
            ,'斗','斤','方','无','日','曰','月','木','欠','止'
            ,'歹','殳','毋','比','毛','氏','气','水','火','灬'
            ,'爪','爫','父','爻','爿','片','牙','牛','犬','王'
            ,'礻','罓','耂','肀','月','见','贝','车','辶','韦'
            ,'風','斗'
        );
        $radicalsArray["5"] = array (
            '母','氺','玄','玉','瓜','瓦','甘','生','用','田'
            ,'疋','疒','癶','白','皮','皿','目','矛','矢','石'
            ,'示','禸','禾','穴','立','罒','衤','钅','龙'
        );
        $radicalsArray["6"] = array (
            '竹','米','糸','糹','缶','网','羊','羽','老','而'
            ,'耒','耳','聿','肉','臣','自','至','臼','舌','舛'
            ,'舟','艮','色','艸','虍','虫','血','行','衣','襾'
            ,'西','覀','赱','辵','页','齐'
        );
        $radicalsArray["7"] = array (
            '見','角','言','訁','谷','豆','豕','豸','貝','赤'
            ,'走','足','身','車','辛','辰','邑','酉','釆','里'
            ,'長','鸟','鹵','麦','龟'
        );
        $radicalsArray["8"] = array (
            '金','釒','門','阜','隶','隹','雨','靑','青','非'
            ,'飠','鱼','黾','齿'
        );
        $radicalsArray["9"] = array (
            '面','革','韋','韭','音','頁','风','飛','食','首'
            ,'香'
        );
        $radicalsArray["10+"] = array (
            '馬','骨','高','髟','鬥','鬯','鬲','鬼','魚','鳥'
            ,'卤','鹿','麥','麻','黄','黃','黍','黑','黹','黽'
            ,'鼎','鼓','鼔','鼠','鼻','齊','齒','龍','龜','龠'
        );

        $this->set("numberOfStrokes", $numberOfStrokes);
        $this->set("radicals", $radicalsArray[$numberOfStrokes]);

    }
}
