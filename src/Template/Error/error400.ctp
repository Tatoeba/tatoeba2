<?php
use Cake\Core\Configure;
use Cake\Error\Debugger;

$this->layout = 'default';

if (Configure::read('debug')) :
    $this->layout = 'dev_error';

    $this->assign('title', $message);
    $this->assign('templateName', 'error400.ctp');

    $this->start('file');
?>
<?php if (!empty($error->queryString)) : ?>
    <p class="notice">
        <strong>SQL Query: </strong>
        <?= h($error->queryString) ?>
    </p>
<?php endif; ?>
<?php if (!empty($error->params)) : ?>
        <strong>SQL Query Params: </strong>
        <?php Debugger::dump($error->params) ?>
<?php endif; ?>
<?= $this->element('auto_table_warning') ?>
<?php
if (extension_loaded('xdebug')) :
    xdebug_print_function_stack();
endif;

$this->end();
endif;
?>
<?php if ($code == 404): ?>
    <?php /* @translators Title of the "404 not found" page */ ?>
    <h2><?= h(__("Page not found")) ?></h2>
    <p><?= format(
        /* @translators Placeholder contains a URL path,
           such as /en/sentences/show/1234 */
        __("The requested address '{0}' was not found on this server."),
        "<strong>{$url}</strong>"
    ) ?></p>
<?php else: ?>
    <?php /* @translators Title of the error page
             for any 4xx error that is not 404 (very rare) */ ?>
    <h2><?= h(__("Client error")) ?></h2>
    <p><?= h($message) ?></p>
<?php endif; ?>
