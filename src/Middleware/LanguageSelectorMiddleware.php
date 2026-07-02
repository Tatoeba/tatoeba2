<?php
namespace App\Middleware;

use App\Lib\LanguagesLib;
use Cake\Core\Configure;
use Cake\Http\Cookie\Cookie;
use Cake\I18n\I18n;
use Laminas\Diactoros\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Middleware for UI language selection
 *
 * This middleware will set the language for the UI in the following order:
 * 1. Use the language set in the 'interface_language' cookie
 * 2. Use the first available language from the 'Accept-Language' header
 * 3. Use the language from the URL path
 * 4. Use English
 */
class LanguageSelectorMiddleware implements MiddlewareInterface
{
    private $allLanguages;

    public function __construct() {
        $this->allLanguages = Configure::read('UI.languages');
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // Plugin paths should not be processed
        if ($request->getParam('plugin')) {
            return $handler->handle($request);
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
                return new RedirectResponse($location, $status);
            }
        }

        $this->setLocale($lang);
        $request = $request->withParam('lang', $lang);

        $response = $handler->handle($request);

        $response = $this->ensureCakeResponse($response);

        return $response->withCookie(Cookie::create(
            'interface_language',
            $lang,
            ['expires' => '+1 month']
        ));
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

    /**
     * Set locale while working around PHP 8.2 fatal errors.
     */
    private function setLocale(string $locale) {
        try {
            // We use isLenient() here but pretty much any method
            // of IntlDateFormatter would trigger the error
            datefmt_create($locale)->isLenient();
        } catch (\Error $e) {
            // This locale does not support datetime formatting so fall back to
            // English instead of crashing every time we try to format a date.
            // Known affected locales: gos (Gronings), hoc (Ho)
            \Cake\I18n\FrozenTime::setDefaultLocale('en');
        }
        I18n::setLocale($locale);
    }

    /**
     * Transform a potential non-cakephp-response into one.
     */
    private function ensureCakeResponse(ResponseInterface $response): \Cake\Http\Response {
        if ($response instanceOf \Cake\Http\Response) {
            return $response;
        }
        $cakeResponse = (new \Cake\Http\Response())
            ->withProtocolVersion($response->getProtocolVersion())
            ->withStatus($response->getStatusCode(), $response->getReasonPhrase())
            ->withBody($response->getBody());
        foreach ($response->getHeaders() as $name => $values) {
            $cakeResponse = $cakeResponse->withHeader($name, $values);
        }
        return $cakeResponse;
    }
}
