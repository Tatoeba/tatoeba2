<?php
declare(strict_types=1);

namespace App\Middleware\UnauthorizedHandler;

use Authorization\Middleware\UnauthorizedHandler\RedirectHandler;
use Psr\Http\Message\ServerRequestInterface;

/**
 * This handler will mimics the behavior of the old Auth component.
 */
class LegacyRedirectHandler extends RedirectHandler
{
    /**
     * Returns the url for the Location header.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request Server request.
     * @param array $options Options.
     * @return string
     */
    protected function getUrl(ServerRequestInterface $request, array $options): string
    {
        if ($referer = $request->referer()) {
            return $referer;
        } else {
            return parent::getUrl($request, $options);
        }
    }
}
