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

        if ($request->is('post') ||  // Don't mess with the language
            $request->is('put') ||   // of POST, PUT and AJAX requests
            $request->is('ajax')) {
            $lang = $this->unalias($langInUrl);
        } else {
            /* The following line is for backward compatibility
             * with old cookies and can be replaced with
             * $lang = $request->getCookie('interface_language');
             * one month (i.e. the expiration time of the old cookie)
             * after this PR is deployed. */
            $lang = $this->getCookieLanguage($request);
            $lang = isset($this->allLanguages[$lang]) ?
                    $this->unalias($lang) :
                    null;
            if (!$lang) {
                $lang = $this->getBrowserLanguage($request->acceptLanguage());
            }
            if (!$lang) {
                $lang = $this->unalias($langInUrl) ?: 'eng';
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

        Configure::write('Config.language', $lang);
        $locale = locale_parse(LanguagesLib::languageTag($lang));
        I18n::setLocale($locale['language']);

        $response = $response->withCookie(new Cookie(
            'interface_language',
            $lang,
            new Time('+1 month')
        ));
        $request = $request->withParam('lang', $lang);
        return $next($request, $response);
    }

    /**
     * Helper function to get cookie value for the
     * interface language
     *
     * Since CakePHP 3.5 the CookieComponent is deprecated
     * and one should get the cookie from the request object.
     * (The CookieComponent wouldn't be available here
     * (i.e. inside a middleware) anyways.)
     * But the way how the cookie is written and read using
     * the component differs from the new way and so
     * we should rather get rid of the namespaced
     * 'CakeCookie.interfaceLanguage' and just use
     * 'interface_language' for the cookie name.
     *
     * @param ServerRequest $request
     *
     * @return string|null
     */
    function getCookieLanguage($request) {
        $lang = $request->getCookie('interface_language');
        if (!$lang) {
            $cookie = $request->getCookieCollection()->get('CakeCookie');
            $lang = $cookie ? $cookie->read('interfaceLanguage') : null;
        }
        if (!$lang) {
            $lang = $request->getCookie('CakeCookie[interfaceLanguage]');
        }
        return $lang;
    }

     /**
     * Returns the ISO code of the language in which we should set the interface,
     * considering the languages of the user's browser.
     *
     * @return string|null
     */
    private function getBrowserLanguage($browserLanguages) {
        $configUiLanguages = array_keys(LanguagesLib::activeUiLanguages());
        $supportedLanguages = array();
        foreach ($configUiLanguages as $code) {
            $browserCompatibleCode = LanguagesLib::languageTag($code);
            $supportedLanguages[$browserCompatibleCode] = $code;
        }

        foreach ($browserLanguages as $browserLang) {
            $lang = explode('-', $browserLang)[0];
            if (isset($supportedLanguages[$lang])) {
                return $supportedLanguages[$lang];
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
