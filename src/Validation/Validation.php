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
    public static function containsOutboundLink(string $text)
    {
        if (preg_match_all('/(?:ht|f)tps?:\/\/(?:[\w\.]+\.)?[\w-]+/iu', $text, $matches)) {
            $request = Router::getRequest(true);
            if ($request) {
                $serverHost = $request->host();
                foreach ($matches as $match) {
                    $url = $match[0];
                    $linkHost = parse_url($url, PHP_URL_HOST);
                    if ($linkHost != $serverHost) {
                        return true;
                    }
                }
            } else {
                return true;
            }
        }
        return false;
    }

    public static function isLinkPermitted($check) {
        return CurrentUser::hasOutboundLinkPermission() || !self::containsOutboundLink($check ?? '');
    }
}
