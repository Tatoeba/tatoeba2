<?php
namespace App\Model\Entity;

use App\Lib\LanguagesLib;

/**
 * Allows an Entity to get language names from ISO codes.
 */
trait LanguageNameTrait
{
    private function langAsAlone($name)
    {
        return format(
        /* @translators: this special string allows you to tweak how language
           names are displayed when they are not used inside another string.
           For instance, in language lists, on flag mouseover or on the stats
           page. You may translate this string using a declension modifier,
           for instance {language.alone} */
            __('{language}'),
            array('language' => $name)
        );
    }

    /**
     * Return array of languages in Tatoeba + all languages, ready
     * to be used inside a format() call. You MUST use the return
     * value as a variable inside a format() call. If not,
     * use languagesArrayAlone() instead.
     *
     * @return array
     */
    public function languagesArrayToFormat()
    {
        $languages = LanguagesLib::languagesInTatoeba();
        $options = ['und' => __('All languages')];

        return $options + $languages;
    }

    /**
     * Return name of the language from the ISO code, ready to
     * be used inside a format() call. You MUST use the return
     * value as a variable inside a format() call. If not,
     * use codeToNameAlone() instead.
     *
     * @param string $code ISO-639-3 code.
     *
     * @return string
     */
    public function codeToNameToFormat($code)
    {
        $languages = $this->languagesArrayToFormat();
        if (isset($languages["$code"])) {
            return $languages["$code"];
        } else {
            /* @translators: dropdown option for unknown language */
            return __('unknown');
        }
    }

    /**
     * Return name of the language from the ISO code, formatted
     * like it's displayed when alone on the UI (on lists or flags).
     *
     * @param string $code ISO-639-3 code.
     *
     * @return string
     */
    public function codeToNameAlone($code) {
        return $this->langAsAlone($this->codeToNameToFormat($code));
    }
}
