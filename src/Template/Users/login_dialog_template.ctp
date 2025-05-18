<?php
use \Cake\Controller\Component\AuthComponent;

$passwordUrl = $this->Url->build([
    'controller' => 'users',
    'action' => 'new_password'
]);
?>

<md-dialog id="login-dialog" aria-label="<?= __('Log in') ?>" ng-cloak>

<?php
$this->Security->enableCSRFProtection();
echo $this->Form->create('User', ['url' => [
    'controller' => 'users', 
    'action' => 'check_login',
    '?' => [AuthComponent::QUERY_STRING_REDIRECT => $redirectUrl],
]]);
?>

<md-toolbar>
    <div class="md-toolbar-tools">
        <h2><?= __('Log in'); ?></h2>
    </div>
</md-toolbar>

<md-dialog-content>
    <md-input-container class="md-block">
        <?php
        echo $this->Form->input('username', [
            'label' => __('Username'),
            'md-autofocus' => ''
        ]);
        ?>
    </md-input-container>

    <md-input-container class="md-block">
        <?php
        echo $this->Form->input('password', [
            'label' => __('Password')
        ]);
        ?>
    </md-input-container>

    <md-checkbox
        ng-model='rememberLogin'
        ng-false-value='0'
        ng-true-value='1' ng-init='rememberLogin = 0'
        class='md-primary'>
        <?= __('Remember me') ?>
    </md-checkbox>
    <?php
    echo $this->Form->checkbox(
        'rememberMe',
        array(
        'value' => '{{rememberLogin}}',
        'ng-checked' => 'rememberLogin == 1',
        'class' => 'ng-hide'
        )
    );
    ?>

    <div layout="column">
        <md-button type="submit" class="md-raised md-primary">
            <?php /* @translators: button to submit login form (verb) */ ?>
            <?php echo __('Log in'); ?>
        </md-button>

        <md-button class="md-primary" href="<?= $passwordUrl ?>" flex>
            <?= __('Forgot your password?'); ?>
        </md-button>

        <md-button ng-click="close()">
            <?php /* @translators: link to close the login box (verb) */ ?>
            <?php echo __('Cancel'); ?>
        </md-button>
    </div>
</md-dialog-content>
<?php
echo $this->Form->end();
$this->Security->disableCSRFProtection();
?>

</md-dialog>
