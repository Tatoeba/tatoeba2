<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2010 HO Ngoc Phuong Trang
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace App\Model\Entity;

use Cake\ORM\Entity;
use App\Model\CurrentUser;
use App\Lib\LanguagesLib;

class Sentence extends Entity
{
    use LanguageNameTrait;

    protected $_virtual = [
        'lang_name',
        'dir',
        'lang_tag',
        'is_favorite',
        'is_owned_by_current_user',
        'permissions',
        'max_visible_translations',
        'current_user_review'
    ];

    protected $_hidden = [
        'favorites_users',
        'highlight',
    ];

    protected function _setLang($value)
    {
        return empty($value) ? null : $value;
    }

    protected function _setText($value)
    {
        return $this->_clean($value);
    }

    private function _clean($text)
    {
        // Remove whitespace and control characters at the beginning
        $text = preg_replace('/^[\p{Z}\p{Cc}]+/u', '', $text);
        // Remove whitespace and control characters at the end
        $text = preg_replace('/[\p{Z}\p{Cc}]+$/u', '', $text);
        // Strip out any byte-order mark that might be present.
        $text = preg_replace("/\xEF\xBB\xBF/", '', $text);
        // Replace all control characters and any series of whitespace
        // with a single space.
        // The control characters must be replaced first, so that a possibly
        // resulting series of whitespace is replaced too.
        $text = preg_replace(['/\p{Cc}+/u', '/\p{Z}{2,}/u'], ' ', $text);
        // Normalize to NFC
        $text = \Normalizer::normalize($text, \Normalizer::FORM_C);
        // MySQL will truncate to a byte length of 1500, which may split
        // a multibyte character. To avoid this, we preemptively
        // truncate to a maximum byte length of 1500. If a multibyte
        // character would be split, the entire character will be
        // truncated.
        $text = mb_strcut($text, 0, 1500, "UTF-8");
        return $text;
    }

    protected function _getLangName()
    {
        return $this->codeToNameAlone($this->lang);
    }

    protected function _getDir()
    {
        return LanguagesLib::getLanguageDirection($this->lang);
    }

    protected function _getLangTag()
    {
        return LanguagesLib::languageTag($this->lang, $this->script);
    }

    protected function _getIsFavorite()
    {
        if ($this->has('favorites_users')) {
            return count($this->favorites_users) > 0;
        }
    }

    protected function _getIsOwnedByCurrentUser()
    {
        return $this->user_id === CurrentUser::get('id');
    }

    protected function _getPermissions()
    {
        if (CurrentUser::isMember()) {
            $user = $this->user;
            $userId = $user ? $user->id : null;
            if (!empty($this->transcriptions)) {
                $editableTranscription = array_filter($this->transcriptions, function($transcription) {
                    return $transcription->markup;
                });    
            } else {
                $editableTranscription = false;
            }
    
            return [
                'canEdit' => CurrentUser::canEditSentenceOfUserId($userId),
                'canTranscribe' => (bool)$editableTranscription,
                'canReview' => (bool)CurrentUser::get('settings.users_collections_ratings'),
                'canAdopt' => CurrentUser::canAdoptOrUnadoptSentenceOfUser($user),
                'canDelete' => CurrentUser::canRemoveSentence($this->id, $userId),
                'canLink' => CurrentUser::isTrusted(),
            ];
        } else {
            return null;
        }
    }
    
    protected function _getCurrentUserReview()
    {
        if (!empty($this->users_sentences)) {
            return $this->users_sentences[0]->correctness;
        }
        return null;
    }

    protected function _getMaxVisibleTranslations()
    {
        if (CurrentUser::isMember() && (int)CurrentUser::getSetting('max_visible_translations') > 0) {
            return CurrentUser::getSetting('max_visible_translations');
        }

        return User::DEFAULT_MAX_VISIBLE_TRANSLATION;
    }

    protected function _getOwner()
    {
        return $this->user ? $this->user->username : null;
    }

    protected function _getIsUnapproved()
    {
        return $this->correctness == \App\Model\Table\SentencesTable::MIN_CORRECTNESS;
    }
    
    /**
     * Checks if the last character of a sentence is a typical character in
     * that sentence's language. Assumes that the sentence is not empty.
     * 
     * @param string $sentence The sentence to be checked.
     * @param string $lang Three-letter-code of the language of the sentence.
     * 
     * @return true|string Returns true, if the last character is judged to be OK,
     *                     and the offending character in all other cases.
     */
    public function isCorrectLastCharacter() 
    {
        $sentence = $this->sentence;
        $lang = $this->lang;
        
        $last = mb_substr($sentence, mb_strlen($sentence)-1);

        if (in_array($lang, ['eng', 'rus', 'ita', 'epo', 'kab', 'deu', 'tur', 'fra',
                             'ber', 'por', 'spa', 'hun', 'heb', 'nld', 'ukr', 'fin',
                             'lit', 'pol', 'ces', 'mar', 'tat', 'mkd', 'tgl', 'tok',
                             'dan', 'swe', 'lat', 'srp', 'ina', 'tlh', 'ron', 'lfn',
                             'vie', 'slk', 'ind', 'bul', 'oci', 'swc', 'shi', 'hau',
                             'nds', 'nob', 'lvs', 'kor', 'bel', 'ido', 'nnb', 'isl',
                             'aze', 'ile', 'gos', 'nno', 'cat', 'kmr'])) {
            if (mb_strpos(".?!\"”':…)»“", $last)!==false) return true;
            return $last;
        }
    
        if (in_array($lang, ['jpn', 'cmn', 'yue'])) {
            if (mb_strpos("。？！」…）：", $last)!==false) return true;
            return $last;
        }
    
        if (in_array($lang, ['ara', 'pes', 'ckb'])) {
            if (mb_strpos(".؟!\")", $last)!==false) return true;
            return $last;
        }

        if (in_array($lang, ['hin', 'ben', 'asm'])) {
            if (mb_strpos("।?!.\"", $last)!==false) return true;
            return $last;
        }

        if ($lang=='tig') {
            if (mb_strpos("።:?.!፧፥፡", $last)!==false) return true;
            return $last;
        }
    
        if ($lang=='hye') {
            if (mb_strpos("։:", $last)!==false) return true;
            return $last;
        }
      
        if ($lang=='ell') {
            if (mb_strpos(".;!\"'”“»:…)", $last)!==false) return true;
            return $last;
        }
      
        if ($lang=='yid') {
            if (mb_strpos(".?!״”‟\":", $last)!==false) return true;
            return $last;
        }
    
        if ($lang=='zgh') {
            if (mb_strpos(".?!\"", $last)!==false) return true;
            return $last;
        }

        if ($lang=='jbo') {
            if (mb_strpos("aeiou.", $last)!==false) return true;
            return $last;
        }
        
        // accept everything for languages that are not yet checked
        return true;
    }
    
    /**
     * Checks if the first character of a sentence is a typical character in
     * that sentence's language. Assumes that the sentence is not empty.
     * 
     * @param string $sentence The sentence to be checked.
     * @param string $lang Three-letter-code of the language of the sentence.
     * 
     * @return true|string Returns true, if the first character is judged to be OK,
     *                     and the offending character in all other cases.
     */
    public function isCorrectFirstCharacter() 
    {   
        $sentence = $this->sentence;
        $lang = $this->lang;
     
        $ok = [
               'eng' => [0x22, 0x27, [0x30, 0x39], [0x41, 0x5A], 0x201C],
               'rus' => [0x22, [0x30, 0x39], 0xAB, [0x0410, 0x042F]],
               'ita' => [0x22, [0x30, 0x39], [0x41, 0x5A], 0xC8],
               'epo' => [0x22, [0x30, 0x39], [0x41, 0x5A], 0x0108, 0x011C, 0x015C, 
                         0x0134, 0x201E],
               'kab' => [0x22, [0x41, 0x5A], 0x010C, 0x0190, 0x0194, 0x01E6,
                         0x1E24, 0x1E5A, 0x1E62, 0x1E6C, 0x1E92],
               'deu' => [0x22, [0x30, 0x39], [0x41, 0x5A], 0xC4, 0xD6, 0xDC, 0x0447, 
                         0x201E],
               'tur' => [0x22, [0x30, 0x39], [0x41, 0x5A], 0xC7, 0xD6, 0x0130, 0x015E,
                         0x0B2B],
               'fra' => [[0x30, 0x39], [0x41, 0x5A], 0xAB, 0xC0, 0xC7, 0xC9, 0xCA, 0xD4],
               'ber' => [0x22, [0x30, 0x39], [0x41, 0x5A], 0x010C, 0x0190, 0x0194, 
                         0x01E6, 0x0393, 0x1E0C, 0x1E24, 0x1E5A, 0x1E62, 0x1E6C, 0x1E92],
               'por' => [0x22, [0x30, 0x39], [0x41, 0x5A], [0xC0, 0xC2], 0xC9, 0xD3],
               'spa' => [0x22, [0x30, 0x39], [0x41, 0x5A], 0xA1, 0xAB, 0xBF, 0xC1, 0xC9, 
                         0xCD, 0xDA],
               'hun' => [0x2D, [0x30, 0x39], [0x41, 0x5A], 0xC1, 0xC9, 0xCD, 0xD3, 0xD6, 
                         0xDA, 0xDC, 0x0150, 0x2014],
               'jpn' => [0x300C, 0x3042, 0x3044, 0x3046, 0x3048, [0x304A, 0x3062],
                         [0x3064, 0x3082], 0x3084, 0x3086, [0x3088, 0x308D], 
                         [0x308F, 0x3092], 0x30A2, 0x30A4, 0x30A6, 0x30A8, 
                         [0x30AA, 0x30C2], [0x30C4, 0x30E2], 0x30E4, 0x30E6, 
                         [0x30E8, 0x30ED], [0x30EF, 0x30F2], 0x30F4, [0x30F7, 0x30FA], 
                         [0x4E00, 0x9FFF], [0xFF11, 0xFF19]],
               'heb' => [0x22, [0x05D0, 0x05EA]],
               'nld' => [0x22, 0x27, [0x41, 0x5A], 0x201E],
               'ukr' => [0x22, 0xAB, [0x0400, 0x042F]],
               'fin' => [0x22, [0x41, 0x5A], 0xBB, 0xC4, 0xD5, 0x201D],
               'lit' => [0x22, 0x27, [0x41, 0x5A], 0x104, 0x010C, 0x116, 0x118, 0x012E, 
                         0x0160, 0x016A, 0x172, 0x017D],
               'pol' => [0x22, [0x41, 0x5A], 0x0106, 0x0141, 0x015A, 0x0179, 0x017B, 0x201E],
               'ces' => [0x22, [0x41, 0x5A], 0xDA, 0x010C, 0x0158, 0x0160, 0x017D, 0x201E],
               'cmn' => [0x201C, [0x4E00, 0x9FFF]],
               'mar' => [0x22, [0x0904, 0x0939], [0x0966, 0x096F]],
               'tat' => [[0x41, 0x5A], 0xC4, 0xC7, 0xCA, 0xD6, 0xDC, 0x011E, 0x0130, 0x015E, 
                         [0x0410, 0x042F], 0x0496, 0x04AE, 0x04BA, 0x04D8, 0x04E8, 0x201C, 
                         0x201D],
               'mkd' => [[0x0400, 0x042F], 0x201E],
               'tgl' => [0x22, 0x27, [0x41, 0x5A]],
               'tok' => [0x22, 0x61, 0x65, [0x69, 0x70], [0x73, 0x75], 0x77],
               'ara' => [0x22, [0x0620, 0x064A]],
               'dan' => [0x22, [0x41, 0x5A], 0xBB, 0xC5, 0xC6, 0xC9, 0xD8],
               'swe' => [0x22, [0x41, 0x5A], 0xC4, 0xC5, 0xD6, 0x201D],
               'tig' => [[0x1200, 0x135A]],
               'lat' => [0x22, [0x41, 0x5A]],
               'srp' => [[0x41, 0x5A], 0x010C, 0x0160, 0x017D, [0x0400, 0x042F]],
               'hye' => [0xAB, [0x0531, 0x0556]],
               'ell' => [0x22, 0x27, 0xAB, 0x0386, [0x0388, 0x038F], [0x0391, 0x03A9]],
               'ina' => [0x22, [0x41, 0x5A], 0x2014],
               'tlh' => [0x27, 0x44, 0x48, 0x51, 0x53, 0x62, 0x63, 0x67, 0x6A, [0x6C, 0x6E], 
                         [0x70, 0x72], 0x74, 0x76, 0x77, 0x79, 0xAB],
               'ron' => [0x22, [0x41, 0x5A], 0xCE, 0x0102, 0x0218, 0x021A, 0x201E],
               'lfn' => [0x22, [0x41, 0x5A], [0x0410, 0x042F]],
               'pes' => [0x22, 0xAB, [0x0620, 0x064A], 0x067E, 0x0686, 0x0698, 0x06A9, 
                         0x06AF, 0x06CC],
               'vie' => [0x22, [0x41, 0x5A], 0xC1, 0xC2, 0xD4, 0xDD, 0x0102, 0x0110, 
                         0x01AF, 0x1EDE],
               'slk' => [[0x41, 0x5A], 0xC1, 0xDA, 0x010C, 0x010E, 0x013D, 0x0160, 
                         0x0164, 0x017D, 0x201E],
               'ind' => [0x22, [0x41, 0x5A]],
               'yid' => [[0x05D0, 0x05EA], 0x05F0, 0x201E],
               'bul' => [0x22, [0x0410, 0x042F]],
               'oci' => [0x22, [0x41, 0x5A], 0xC7, 0xC8, 0xD2],
               'swc' => [0x22, [0x41, 0x5A], 0xAB],
               'shi' => [0x22, [0x41, 0x5A], 0x0190, 0x0194, 0x1E0C, 0x1E24, 0x1E5A, 
                         0x1E6C, 0x1E92],
               'hau' => [0x22, [0x41, 0x5A], 0x018A, 0x0198, 0x01B3],
               'zgh' => [0x22, [0x2D30, 0x2D67]],
               'yue' => [0x300C, [0x4E00, 0x9FFF]],
               'nds' => [[0x41, 0x5A], 0xD6, 0xDC, 0x2019, 0x201E],
               'nob' => [0x22, [0x41, 0x5A], 0xAB, 0xC5, 0xD8],
               'jbo' => [0x2E, [0x61, 0x7A]],
               'hin' => [0x22, [0x0904, 0x0939]],
               'lvs' => [0x22, [0x41, 0x5A], 0x0100, 0x010C, 0x0112, 0x013B, 0x0160, 
                         0x016A, 0x017D, 0x201C],
               'ben' => [0x22, [0x0985, 0x09EF]],
               'kor' => [0x22, [0x30, 0x39], [0xAC00, 0xD7A3]],
               'asm' => [0x22, [0x0985, 0x09B9], 0x09F0],
               'bel' => [0x22, 0xAB, [0x0400, 0x042F]],
               'ido' => [0x22, [0x41, 0x5A]],
               'nnb' => [0x22, [0x41, 0x5A], 0xAB],
               'isl' => [[0x41, 0x5A], 0xC1, 0xC6, 0xC9, 0xCD, 0xD3, 0xD6, 0xDA, 0xDE, 0x201E],
               'aze' => [0x22, [0x41, 0x5A], 0xC7, 0xD6, 0xDC, 0x0130, 0x015E, 0x018F],
               'ckb' => [[0x0626, 0x0648], [0x0660, 0x0669], [0x066E, 0x06D3]],
               'ile' => [0x22, [0x41, 0x5A], 0xDA],
               'gos' => [0x22, [0x41, 0x5A], 0x6B, 0x6E, 0x74],
               'nno' => [[0x41, 0x5A], 0xAB, 0xC5, 0xD8],
               'cat' => [0x22, [0x41, 0x5A], 0xC9],
               'kmr' => [0x22, [0x41, 0x5A], 0xC7, 0xCA, 0xCE, 0x015E],
               ];
        
        // accept everything for languages that are not yet checked
        if (!isset($ok[$lang])) return true;
        
        $first = mb_ord(mb_substr($sentence, 0, 1));
        
        foreach ($ok[$lang] as $pattern) {
            if (is_array($pattern)) {
                if ($first >= $pattern[0] && $first <= $pattern[1]) return true;
            } else {
                if ($first === $pattern) return true;
            }
        }
        
        return mb_substr($sentence, 0, 1);
    }
}
