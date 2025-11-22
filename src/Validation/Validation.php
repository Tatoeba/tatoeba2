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

    private static function linksNotPermittedErrorMessage(array $context) {
        $field = $context['field'] ?? '';
        if ($field == 'description') {
            $message = __(
                'Sorry, you do not have the permission to include links in your profile description. '.
                'Because of spam concerns, new accounts need to be verified before they can use '.
                'outbound links. Please remove any outbound link from your profile description '.
                'in order to continue.'
            );
        } elseif ($field == 'homepage') {
            $message = __(
                'Sorry, you do not have the permission to set a homepage on your profile. '.
                'Because of spam concerns, new accounts need to be verified before they can use '.
                'outbound links. Please remove the homepage from your profile '.
                'in order to continue.'
            );
        } else {
            return false;
        }

        $additionalHelp = '';
        $since = CurrentUser::get('since');
        if ($since && !$since->wasWithinLast('2 hours')) {
            /* Only append help message to accounts created after a while
               to limit spammers from contacting admins right away */
            $pmAdminsLink = Router::url(['controller' => 'private_messages', 'action' => 'write', 'TatoebaAdmins']);
            $additionalHelp = "\n" . format(
                /* @translators: this string is appended to each
                   of the previous "Sorry" messages about links */
                __('You can ask for permission to add links later by '
                  .'{linkStart}sending a message to administrators{linkEnd}.'),
                ['linkStart' => "<a href=\"$pmAdminsLink\" target=\"_blank\">", 'linkEnd' => '</a>']
            );
        }

        return $message . $additionalHelp;
    }

    public static function isLinkPermitted($check, array $context = []) {
        if (CurrentUser::hasOutboundLinkPermission() || !self::containsOutboundLink($check ?? '')) {
            return true;
        } else {
            return self::linksNotPermittedErrorMessage($context);
        }
    }
}
