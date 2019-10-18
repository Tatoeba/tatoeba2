<?php
$controller = $this->request->params['controller'];
$action = $this->request->params['action'];
$input = $this->request->params['pass'][0];
if ($currentId == null) {
    $currentId = intval($input);
    $next = $currentId + 1;
    $prev = $currentId - 1;
}

$this->Html->script('sentences.random.js', array('block' => 'scriptBottom'));
$langArray = $this->Languages->languagesArrayAlone();
$selectedLanguage = $this->request->getSession()->read('random_lang_selected');
?>

<div class="navigation" layout="row" ng-cloak>
    <div layout="column" flex>
        <div layout="row" layout-align="center center">
            <md-tooltip>
                <?= __('Language for previous, next or random sentence'); ?>
            </md-tooltip>
            <?php
            echo $this->Form->select(
                "randomLangChoiceInBrowse",
                $langArray,
                array(
                    'id' => 'randomLangChoiceInBrowse',
                    "value" => $selectedLanguage,
                    'class' => 'language-selector',
                    'data-current-sentence-id' => $currentId,
                    'empty' => false
                ),
                false
            );
            ?>
            <div id="loadingAnimationForNavigation" style="display:none">
                <?php echo $this->Html->div('loader-small loader', ''); ?>
            </div>
        </div>

        <div layout="row" layout-align="start center" layout-margin >
            <?php
            $prevClass = 'inactive';
            $prevLink = '';

            if (!empty($prev)) {
                $prevClass = 'active';
                $prevLink = $this->Url->build([
                    'controller' => $controller,
                    'action' => $action,
                    $prev
                ]);
            }

            $nextClass = 'inactive';
            $nextLink = '';

            if (!empty($next)) {
                $nextClass = 'active';
                $nextLink = $this->Url->build([
                    'controller' => $controller,
                    'action' => $action,
                    $next
                ]);
            }

            $randomLink = $this->Url->build([
                'controller' => 'sentences',
                'action' => 'show',
                $selectedLanguage
            ]);
            ?>

            <md-button href="<?= $prevLink ?>" class="md-primary" flex>
                <md-icon>keyboard_arrow_left</md-icon>
                <?= __('previous') ?>
            </md-button>

            <md-button href="<?= $randomLink ?>" class="md-primary" flex>
                <?= __('random') ?>
            </md-button>

            <md-button href="<?= $nextLink ?>" class="md-primary" flex>
                <?= __('next') ?>
                <md-icon>keyboard_arrow_right</md-icon>
            </md-button>
        </div>
    </div>

    <?php
    // go to form
    echo $this->Form->create('Sentence', [
        'id' => 'go-to-form',
        'url' => ['action' => 'go_to_sentence'],
        'type' => 'get',
        'layout' => 'row',
        'layout-align' => 'center center'
    ]);
    ?>
    <md-input-container layout="row" layout-align="start center">
        <?php
        echo $this->Form->input('sentence_id', [
            "type" => "text",
            "label" => __('Show sentence #: '),
            "value" => $input,
            "lang" => "",
            "dir" => "ltr",
        ]);
        ?>
        <md-button type="submit" class="go-button">
            <md-icon>arrow_forward</md-icon>
        </md-button>
    </md-input-container>
    <?php
    echo $this->Form->end();
    ?>
</div>