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
 * @link     https://tatoeba.org
 */
use App\Model\CurrentUser;

$categories = array(
    'ok' => ['check_circle', __('Sentences marked as "OK"')],
    'unsure' => ['help', __('Sentences marked as "unsure"')],
    'not-ok' => ['error', __('Sentences marked as "not OK"')],
    'all' => ['keyboard_arrow_right', __("All sentences")],
    'outdated' => ['keyboard_arrow_right', __("Outdated reviews")]
);

if (empty($correctnessLabel) || !in_array($correctnessLabel, $categories)) {
    $category = $categories['all'][1];
} else {
    $category = $categories[$correctnessLabel][1];
}

if ($userExists) {
    $title = format(
        __("{user}'s reviews - {category}"),
        array('user' => $username, 'category' => $category)
    );
} else {
    $title = format(__("There's no user called {username}"), array('username' => $username));
}

$this->set('title_for_layout', $this->Pages->formatTitle($title));
?>

<?php if ($userExists) : ?>
<div id="annexe_content" ng-cloak>
    <?php
    if (!CurrentUser::get('settings.users_collections_ratings')) {
        echo '<div class="module">';
        echo $this->Html->tag('p', __('This feature is currently deactivated.'));
        echo $this->Html->tag('p',
            __(
                'You can activate it in your settings: "Activate the feature to review sentences."',
                true
            )
        );
        echo '</div>';
    }
    ?>

    <md-list class="annexe-menu md-whiteframe-1dp" ng-cloak>
        <?php /* @translators: header text in the sidebar of a list of reviews (verb) */ ?>
        <md-subheader><?= __('Filter') ?></md-subheader>
        <?php
        foreach($categories as $categoryKey => $categoryValue) {
            $url = $this->Url->build([
                'action' => 'of',
                $username,
                $categoryKey
            ]);
            ?>
            <md-list-item href="<?= $url ?>">
                <md-icon><?= $categoryValue[0] ?></md-icon>
                <p><?= $categoryValue[1] ?></p>
            </md-list-item>
            <?php
        }
        ?>
    </md-list>
</div>
<?php endif; ?>

<div id="main_content">
    <section class="md-whiteframe-1dp correctness-info">
        <?php
        if (!$userExists) {
            $this->CommonModules->displayNoSuchUser($username);
        } else {
            $title = $this->Paginator->counter(array(
                'format' => $title . ' ' . __("(total {{count}})")
            ));
            ?>
        <md-toolbar class="md-hue-2">
            <div class="md-toolbar-tools">
                <h2><?= $title ?></h2>
            </div>
        </md-toolbar>

        <div class="sortBy">
            <strong><?php echo __("Sort by:") ?> </strong>
            <?php
            /* @translators: sort option in the list of reviews */
            echo $this->Paginator->sort('modified', __("date modified"));
            echo " | ";
            /* @translators: sort option in the list of reviews */
            echo $this->Paginator->sort('created', __("date created"));
            echo " | ";
            /* @translators: sort option in the list of reviews */
            echo $this->Paginator->sort('sentence_id', __("sentence id"));
            ?>
        </div>
        <?php
            $this->Pagination->display();

            $type = 'mainSentence';
            $parentId = null;
            $withAudio = false;
            foreach ($corpus as $item) {
                $sentence = $item->sentence;
                echo '<div>';

                if (empty($sentence->id)) {
                    $sentenceId = $item->sentence_id;
                    $linkToSentence = $this->Html->link(
                        '#'.$sentenceId,
                        array(
                            'controller' => 'sentences',
                            'action' => 'show',
                            $sentenceId
                        )
                    );

                    echo $this->Html->div('sentence deleted',
                        format(
                            __('Sentence {id} has been deleted.'),
                            array('id' => $linkToSentence)
                        )
                    );
                } else {
                    $this->Sentences->displayGenericSentence(
                        $sentence,
                        $type,
                        $parentId,
                        $withAudio
                    );
                }

                $correctness = $item->correctness;
                echo $this->Html->div(
                    'correctness',
                    $this->Images->correctnessIcon($correctness),
                    array('title' => $this->Date->nice($item->modified))
                );

                echo '</div>';
            }

            $this->Pagination->display();
        }
        ?>
    </div>
    <div>
    <?php
    namespace App\View\Helper;

    use App\View\Helper\AppHelper;
    class CommonModulesHelper extends AppHelper
{

    public $helpers = array(
        'Languages',
        'Form',
        'Html',
    );
    
    
    /**
     * Helper for modules and part of module
     *
     * @category Utilities
     * @package  Helpers
     * @author   SIMON Allan <allan.simon@supinfo.com>
     * @license  Affero General Public License
     * @link     https://tatoeba.org
     */
    
   
     class CommonModulesHelper extends AppHelper
    {
    
        public $helpers = array(
            'Languages',
            'Form',
            'Html',
        );
        /**
     * Create a module for filtering a page by language
     *
     * @param int $maxNumberOfParams The number of parameters used in the controller
     *                               for the view which uses this module
     *                               NOTE: the language must be the last parameter
     *
     * @return void
     */
            public function createFilterByLangMod($maxNumberOfParams = 1)
    {
        ?>
        <div class="section md-whiteframe-1dp" layout="column">
            <h2><?php echo __('Filter by language'); ?></h2>
            <?php
            // In order to stay on the same page we reconstruct the path
            // without the language parameter
            $path ='/';
            // language of the interface
            $path .= $this->request->params['lang'] .'/';
            $path .= $this->request->params['controller'].'/';
            $path .= $this->request->params['action'].'/';

            $params = $this->request->params['pass'];
            $numberOfParams = count($params);

            $paramsWithoutLang = $numberOfParams;
            if ($numberOfParams === $maxNumberOfParams) {
                $paramsWithoutLang--;
            }

            for ($i = 0; $i < $paramsWithoutLang; $i++) {
                $path .= $params[$i] .'/';
            }

            $lang = 'und' ;
            if (isset($params[$maxNumberOfParams-1])) {
                $lang  = $params[$maxNumberOfParams-1];
            }

            $langs = $this->Languages->languagesArrayAlone();

            // Avoid loosing the query parameters
            $query = parse_url($this->request->getRequestTarget(), PHP_URL_QUERY);
            if (!empty($query)) {
                $query = '?' . $query;
            }

            echo $this->Form->select(
                'filterLanguageSelect',
                $langs,
                array(
                    "value" => $lang,
                    "onchange" => "
                        if (this.value == 'und') {
                            $(location).attr('href','$path' + '$query');
                        } else {
                            $(location).attr('href','$path' + this.value + '$query');
                        }",
                    // the if is to avoid a duplicate page (with and without "und")
                    "class" => "language-selector",
                    "empty" => false
                ),
                false
            );
            ?>
        </div>
    <?php
    }
    /**
     * Display a module content which indicate the user does not exist
     *
     * @param string $userName The username which doesn't exist.
     *
     * @return void
     */
    public function displayNoSuchUser($username)
    {
        echo '<h2>';
        echo format(
            __("There's no user called {username}"),
            ['username' => $this->_View->safeForAngular($username)]
        );
        echo '</h2>';

        echo $this->Html->link(__('Go back to previous page'), 'javascript:history.back()');
    }
}
?>

</div>
