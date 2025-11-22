<?php
namespace App\Validation;

use App\Model\CurrentUser;
use Cake\Routing\Router;

/**
 * Validation Class. Used for validation of model data
 *
 * Offers different validation methods.
 */
class Validation
{
    public static function getOutboundLinks(string $text)
    {
        if (preg_match_all('/(?:ht|f)tps?:\/\/(?:[\w\.]+\.)?[\w-]+/iu', $text, $matches)) {
            $request = Router::getRequest(true);
            if ($request) {
                $serverHost = $request->host();
            }
            foreach ($matches[0] as $url) {
                $linkHost = parse_url($url, PHP_URL_HOST);
                if ($serverHost) {
                    if ($linkHost != $serverHost) {
                        yield $url;
                    }
                } else {
                    yield $url;
                }
            }
        }
    }

    public static function containsOutboundLink(string $text)
    {
        foreach (self::getOutboundLinks($text) as $link) {
            return true;
        }
        return false;
    }

    public static function isLinkPermitted($check) {
        return CurrentUser::hasOutboundLinkPermission() || !self::containsOutboundLink($check ?? '');
    }
}
