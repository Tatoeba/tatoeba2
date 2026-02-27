<?php
use Cake\Core\Configure;
use Cake\Error\Debugger;

$this->layout = 'default';

if (Configure::read('debug')) :
    $this->layout = 'dev_error';

    $this->assign('title', $message);
    $this->assign('templateName', 'error500.ctp');

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
<?php if ($error instanceof Error) : ?>
        <strong>Error in: </strong>
        <?= sprintf('%s, line %s', str_replace(ROOT, 'ROOT', $error->getFile()), $error->getLine()) ?>
<?php endif; ?>
<?php
    echo $this->element('auto_table_warning');

    if (extension_loaded('xdebug')) :
        xdebug_print_function_stack();
    endif;

    $this->end();
endif;
?>
<?php if ($code >= 500): ?>
    <?php /* @translators Title of the page when a fatal error occurs. The
             entire page content is replaced by an error message. */ ?>
    <h2><?= h(__('Server error')) ?></h2>
    <p><?= h(__('An internal error has occurred.')) ?></p>
<?php else: ?>
    <?php /* @translators Title of the error page
             when an unexpected error occurs (very rare) */ ?>
    <h2><?= h(__('Unexpected error')) ?></h2>
    <p><?= h($message) ?></p>
<?php endif; ?>
