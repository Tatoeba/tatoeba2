<?php
namespace App\Middleware;

use App\Lib\LanguagesLib;
use Cake\Core\Configure;
use Cake\Http\Cookie\Cookie;
use Cake\I18n\I18n;
use Cake\I18n\Time;

/**
 * Middleware for UI language selection
 *
 * This middleware will set the language for the UI in the following order:
 * 1. Use the language set in the 'interface_language' cookie
 * 2. Use the first available language from the 'Accept-Language' header
 * 3. Use the language from the URL path
 * 4. Use English
 */
class LanguageSelectorMiddleware
{
    private $allLanguages;

    public function __construct() {
        $this->allLanguages = Configure::read('UI.languages');
    }

    public function __invoke($request, $response, $next)
    {
        // Plugin paths should not be processed
        if ($request->getParam('plugin')) {
            return $next($request, $response);
        }

        $langInUrl = $request->getParam('lang');

        if ($request->is('post') || $request->is('put') || $request->is('ajax')) {
            // Only ensure that the language in the URL of POST, PUT or AJAX
            // requests is valid.
            $lang = isset($this->allLanguages[$langInUrl]) ?
                    $this->unalias($langInUrl) :
                    'en';
        } else {
            $lang = $request->getCookie('interface_language');
            $lang = isset($this->allLanguages[$lang]) ?
                    $this->unalias($lang) :
                    null;
            if (!$lang) {
                $lang = $this->getBrowserLanguage($request->acceptLanguage());
            }
            if (!$lang) {
                $lang = $this->unalias($langInUrl) ?: 'en';
            }

            if ($langInUrl !== $lang) {
                $path = $request->getRequestTarget();
                $status = 301;
                if ($langInUrl) {
                    $path = substr($path, strlen($langInUrl) + 1);
                    $status = 302;
                }
                $location = '/' . $lang . $path;
                return $response->withStatus($status)
                                ->withLocation($location);
            }
        }

        I18n::setLocale($lang);

        $response = $response->withCookie(new Cookie(
            'interface_language',
            $lang,
            new Time('+1 month')
        ));
        $request = $request->withParam('lang', $lang);
        return $next($request, $response);
    }

     /**
     * Returns the ISO code of the language in which we should set the interface,
     * considering the languages of the user's browser.
     *
     * @return string|null
     */
    private function getBrowserLanguage($browserLanguages) {
        $supportedLanguages = LanguagesLib::activeUiLanguages();
        $localeVariants = [];
        foreach ($supportedLanguages as $locale => $langDef) {
            unset($langDef[0]); // skip language name
            foreach ($langDef as $alias) {
                $localeVariants[$alias] = $locale;
            }
        }

        foreach ($browserLanguages as $browserLang) {
            if (isset($supportedLanguages[$browserLang])) {
                return $browserLang;
            } elseif (isset($localeVariants[$browserLang])) {
                return $localeVariants[$browserLang];
            }
        }
        return null;
    }

    /**
     * Get correct ISO code for an aliased language
     *
     * @param string $lang ISO code of language
     *
     * @return string|null
     */
    private function unalias($lang) {
        $langInfo = $this->allLanguages[$lang] ?? null;
        return is_array($langInfo) ? $lang: $langInfo;
    }
}
