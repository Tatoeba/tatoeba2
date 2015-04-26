<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2010  HO Ngoc Phuong Trang <tranglich@gmail.com>
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

/**
 * Helper to display sentences buttons that are not part of the menu.
 *
 * @category Sentences
 * @package  Helpers
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */
class SentenceButtonsHelper extends AppHelper
{
    public $helpers = array(
        'Html',
        'Javascript',
        'Languages',
        'Form',
        'Images'
    );

    /**
     * Display show button for translations. It's the button with the arrow.
     *
     * @param int    $translationId Id of the translation.
     * @param string $type          'direct' or 'indirect'.
     *
     * @return void
     */
    public function translationShowButton($translationId, $type)
    {
        if (!in_array($type, array('direct', 'indirect'))) {
            $type = 'direct';
        }

        $image = $this->Images->svgIcon('translation');
        echo $this->Html->link(
            $image,
            array(
                "controller" => "sentences",
                "action" => "show",
                $translationId
            ),
            array(
                "escape" => false,
                "class" => "translationIcon " . $type,
                "title" => __('Show', true),
            )
        );
    }

    /**
     * Display info button which links to the sentence page
     *
     * @param int $sentenceId The sentence id.
     *
     * @return void
     */
    public function displayInfoButton($sentenceId)
    {
        $infoIcon = $this->Images->svgIcon('sentence-number');
        echo $this->Html->link(
            $infoIcon,
            array(
                "controller"=>"sentences"
                , "action"=>"show"
                , $sentenceId
            ),
            array("escape"=>false, "class"=>"infoIcon")
        );
    }

    /**
     * Display unlink button for translations.
     *
     * @param int $sentenceId    Id of the main sentence.
     * @param int $translationId Id of the translation.
     * @param int $langFilter    The language sentences should be filtered in when redisplaying the list.
     *
     * @return void
     */
    public function unlinkButton($sentenceId, $translationId, $langFilter = 'und')
    {
        echo $this->Javascript->link('links.add_and_delete.js', false);

        $elementId = 'link_'.$sentenceId.'_'.$translationId;

        $image = $this->Images->svgIcon(
            'unlink',
            array(
                "alt"=>__('Unlink', true),
                "title" => __('Unlink this translation.', true),
                "width" => 16,
                "height" => 16
            )
        );
        $langFilter = h(json_encode($langFilter));
        echo $this->Html->link(
            $image,
            array(
                "controller" => "links",
                "action" => "delete",
                $sentenceId,
                $translationId
            ),
            array(
                "escape" => false,
                "class" => "link button",
                "id" => $elementId,
                "onclick" => "translationLink('delete', $sentenceId, $translationId, $langFilter); return false"
            )
        );
    }


    /**
     * Display link button for translations.
     *
     * @param int $sentenceId    Id of the main sentence.
     * @param int $translationId Id of the translation.
     * @param int $langFilter    The language sentences should be filtered in when redisplaying the list.
     *
     * @return void
     */
    public function linkButton($sentenceId, $translationId, $langFilter = 'und')
    {
        echo $this->Javascript->link('links.add_and_delete.js', false);

        $elementId = 'link_'.$sentenceId.'_'.$translationId;

        $image = $this->Images->svgIcon(
            'link',
            array(
                "alt"=>__('Link', true),
                "title" => __('Make into direct translation.', true),
                "width" => 16,
                "height" => 16
            )
        );
        $langFilter = h(json_encode($langFilter));
        echo $this->Html->link(
            $image,
            array(
                "controller" => "links",
                "action" => "add",
                $sentenceId,
                $translationId
            ),
            array(
                "escape" => false,
                "class" => "link button",
                "id" => $elementId,
                "onclick" => "translationLink('add', $sentenceId, $translationId, $langFilter); return false"
            )
        );
    }


    /**
     * Display audio button.
     *
     * @param int   $sentenceId    Id of the sentence on which this button is
     *                             displayed.
     * @param int   $sentenceLang  Language of the sentence.
     * @param sting $sentenceAudio Kind of audio related to this sentence
     *                             (no audio, from shtooka etc.)
     *
     * @return void
     */
    public function audioButton($sentenceId, $sentenceLang, $sentenceAudio)
    {
        switch ($sentenceAudio) {
            // sentence has audio
            case 'shtooka' :
                $onClick = 'return false';
                $path = Configure::read('Path.audio')
                    .$sentenceLang.'/'.$sentenceId.'.mp3'; 
                $css = 'audioAvailable';
                $title = __('Play audio', true);
                echo $this->Javascript->link('sentences.play_audio.js', false);
                break;

            // sentence has no audio
            case 'no' :
            default:
                $onClick = 'return false';
                $css = 'audioUnavailable';
                $path = 'http://en.wiki.tatoeba.org/articles/show/contribute-audio';
                $title = __('No audio for this sentence. Click to learn how to contribute.', true);
                $onClick = 'window.open(this.href); return false;';
                break;
        };

        echo $this->Html->Link(
            null, $path,
            array(
                'title' => $title,
                'class' => "audioButton $css",
                'onclick' => $onClick
            )
        );
    }


    /**
     * Language flag.
     *
     * @param int    $id       Id of the sentence.
     * @param string $lang     Language of the sentence.
     * @param bool   $editable Set to true of flag can be changed.
     *
     * @return void
     */
    public function displayLanguageFlag($id, $lang, $editable = false)
    {
        $class = '';
        if ($editable) {
            $this->Javascript->link('sentences.change_language.js', false);
            $class = 'editableFlag';

            // language select
            $langArray = $this->Languages->profileLanguagesArray(false, true, false);
            $preselectedLang = $lang;
            if (!array_key_exists($lang, $langArray)) {
                $preselectedLang = null;
            }
            ?>

            <span id="<?php echo 'selectLangContainer_'.$id; ?>" class="selectLang">
            <?php
            echo $this->Form->select(
                'selectLang_'.$id,
                $langArray,
                $preselectedLang,
                array(
                    "class"=>"language-selector",
                    "empty" => false
                ),
                false
            );
            ?>
            </span>

            <?php
            // setting data for sentences.change_language.js
            echo "<script type='text/javascript'>
            $(document).ready(function() {
                $('#flag_$id').data('sentenceId', $id);
                $('#flag_$id').data('currentLang', '$lang');
            });
            </script>";
        }

        echo $this->Languages->icon(
            $lang,
            array(
                "id" => "flag_".$id,
                "class" => "languageFlag ".$class,
                "width" => 30,
                "height" => 20
            )
        );

    }
}
?>
