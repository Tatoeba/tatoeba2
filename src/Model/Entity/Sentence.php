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
}
