<?php
declare(strict_types=1);

namespace App\Error\Renderer;

use Cake\Error\Renderer\HtmlErrorRenderer;
use Cake\Error\PhpError;

class AngularJsSafeHtmlErrorRenderer extends HtmlErrorRenderer
{
    public function render(PhpError $error, bool $debug): string
    {
        /**
         * In debug mode errors and warnings will be displayed in the browser.
         * But whenever the output contains {{ the app will crash because they
         * will be interpreted by the AngularJS compiler.
         * We can prevent the crash if we modify the template string by adding
         * the ng-non-bindable directive.
         */
        $html = parent::render($error, $debug);
        return preg_replace('/>/', ' ng-non-bindable>', $html, 1);
    }
}
