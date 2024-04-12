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
 * @link     https://tatoeba.org
 */
namespace App\View\Helper;

use App\Model\CurrentUser;
use App\View\Helper\AppHelper;
use Cake\Core\Configure;


/**
 * Helper to display sentences buttons that are not part of the menu.
 *
 * @category Sentences
 * @package  Helpers
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     https://tatoeba.org
 */
class SentenceButtonsHelper extends AppHelper
{
    public $helpers = array(
        'Html',
        'Languages',
        'Form',
        'Images',
        'Pages',
        'Url',
    );

    /**
     * Display info button which links to the sentence page
     *
     * @param int $sentenceId The sentence id.
     *
     * @return void
     */
    public function displayNavigationButton($sentenceId, $type)
    {
        if ($type == 'mainSentence') {
            $image = $this->Images->svgIcon('sentence-number');
        } else {
            $image = $this->Images->svgIcon('translation');
        }

        echo $this->Html->link(
            $image,
            array(
                "controller" => "sentences",
                "action" => "show",
                $sentenceId
            ),
            array(
                "escape" => false,
                "class" => "navigationIcon " . $type,
                "title" => __("Show sentence's details"),
            )
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
        $elementId = 'link_'.$sentenceId.'_'.$translationId;

        $image = $this->Images->svgIcon(
            'unlink',
            array(
                /* @translators: alt text for unlink translation button (verb) */
                "alt"=>__('Unlink'),
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
                "title" => __('Unlink this translation.'),
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
        $elementId = 'link_'.$sentenceId.'_'.$translationId;

        $image = $this->Images->svgIcon(
            'link',
            array(
                /* @translators: alt text for link translation button (verb) */
                "alt"=>__('Link'),
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
                "title" => __('Make into direct translation.'),
                "onclick" => "translationLink('add', $sentenceId, $translationId, $langFilter); return false"
            )
        );
    }


    /**
     * Display audio button.
     *
     * @param int   $sentenceId     Id of the sentence on which this button is
     *                              displayed.
     * @param int   $sentenceLang   Language of the sentence.
     * @param sting $sentenceAudios Array of audio recordings of the sentence.
     *
     * @return void
     */
    public function audioButton($sentenceId, $sentenceLang, $sentenceAudios)
    {
        $total = count($sentenceAudios);
        if ($total) {
            $startIn = rand(0, $total-1);
            $audioCount = 1;
            $audioLinks = "";
            foreach ($sentenceAudios as $audio) {
                $author = $this->_View->safeForAngular($audio->author);
                if (empty($author)) {
                    $title = __('Play audio');
                } else {
                    $title = format(
                        __('Play audio recorded by {author}', true),
                        array('author' => $author)
                    );
                }
                $class = 'audioButton audioAvailable';
                if ($startIn == 0) {
                    $class .= ' nextAudioToPlay';
                    $audioCount = $this->Html->div("audioButtonCount", $total);
                }
                $audioLink = $this->Html->Link(
                    null,
                    $this->Url->build([
                        'controller' => 'audio',
                        'action' => 'download',
                        $audio->id
                    ]),
                    array(
                        'title' => $title,
                        'class' => $class,
                        'onclick' => 'return false',
                    )
                );

                $audioLinks .= $audioLink;
                $startIn--;
            }

            echo $this->Html->div("audioButtonWrapper", $audioLinks . $audioCount);
        } else {
            echo $this->Html->Link(
                null,
                $this->Pages->getWikiLink('contribute-audio'),
                array(
                    'title' => __('No audio for this sentence. Click to learn how to contribute.'),
                    'class' => 'audioButton audioUnavailable',
                    'onclick' => 'window.open(this.href); return false;',
                )
            );
        }
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
            $class = 'editableFlag';

            // language select
            if (CurrentUser::isAdmin() || CurrentUser::isModerator()) {
                $langArray = $this->Languages->otherLanguagesArray();
            } else {
                $langArray = $this->Languages->profileLanguagesArray(false, [
                    '' => __('other language'),
                ]);
            }
            ?>

            <span id="<?php echo 'selectLangContainer_'.$id; ?>" class="selectLang">
            <?php
            echo $this->Form->select(
                'selectLang_'.$id,
                $langArray,
                array(
                    'id' => 'selectLang_'.$id,
                    "value" => $lang,
                    "class"=>"language-selector",
                    "empty" => false
                ),
                false
            );
            ?>
            </span>

            <?php
        }

        echo $this->Languages->icon(
            $lang,
            array(
                "id" => "flag_".$id,
                "class" => "languageFlag ".$class,
                "data-sentence-id" => $id
            )
        );

    }


    /**
     *
     */
    public function displayCopyButton($text)
    {
        $copyButton = $this->Images->svgIcon('copy');
        echo $this->Html->div('copy-btn', $copyButton,
            array(
                'title' => __('Copy sentence')
            )
        );
    }
}
?>
