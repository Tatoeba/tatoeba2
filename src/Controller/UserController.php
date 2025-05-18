<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2009  BEN YAALA Salem <salem.benyaala@gmail.com>
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
 *
 * PHP version 5
 *
 * @category PHP
 * @package  Tatoeba
 * @author   BEN YAALA Salem <salem.benyaala@gmail.com>
 * @license  Affero General Public License
 * @link     https://tatoeba.org
 */
namespace App\Controller;

use App\Controller\AppController;
use App\Auth\VersionedPasswordHasher;
use App\Lib\LanguagesLib;
use Cake\Event\Event;
use App\Model\CurrentUser;
use Cake\Datasource\Exception\RecordNotFoundException;

/**
 * Controller for sentence comments.
 *
 * @category Users
 * @package  Controllers
 * @author   BEN YAALA Salem <salem.benyaala@gmail.com>
 * @author   Allan SIMON <allan.simon@supinfo.com>
 * @license  Affero General Public License
 * @link     https://tatoeba.org
 */
class UserController extends AppController
{

    /**
     * ?
     *
     * @var string
     */
    public $name = 'User';

    /**
     * TODO load model only where needed
     *
     * @var array
     */
    public $uses = array(
        'User',
        'UsersLanguages',
        'Contribution',
        'Sentence',
        'SentenceComment',
        'Favorite',
        'SentencesList'
    );

    public $helpers = array('Html', 'Date', 'Languages', 'Countries');

    public $components = array('Auth', 'Flash');

    public function beforeFilter(Event $event)
    {
        $this->Security->config('unlockedActions', [
            'save_banner_setting',
        ]);

        return parent::beforeFilter($event);
    }

    /**
     * Display profile of given user.
     * If no username is given and no user is logged in,
     *     then redirect to home
     * If no username is given (but a user is logged in),
     *     then redirect to current user's profile
     * If username doesn't exist,
     *     then redirect to list of memembers (user logged in or not)
     *
     * @param string $userName User screen identifiant
     *
     * @return void
     */
    public function profile($userName = null)
    {
        $this->helpers[] = 'ClickableLinks';
        $this->helpers[] = 'Members';

        $this->loadModel('Users');
        $this->loadModel('UsersLanguages');

        if (empty($userName)) {
            if (!CurrentUser::isMember()) {
                return $this->redirect(array('controller'=>'pages','action' => 'home'));
            } else {
                return $this->redirect(
                    array('action' => 'profile',
                    CurrentUser::get('username'))
              );
            }
        }

        if (!( $user = $this->Users->getInformationOfUser($userName) )) {
            $this->Flash->set(format(
                __('No user with this username: {username}'),
                array('username' => $userName)
            ));
            return $this->redirect(
                array('controller'=>'users',
                  'action' => 'all')
            );
        }

        $userId = $user->id;
        $userStats = $this->_stats($userId);
        $userLanguages = $this->UsersLanguages->getLanguagesOfUser($userId);

        $isPublic = ($user->settings['is_public'] == 1);
        $isDisplayed = ($isPublic || CurrentUser::isMember());
        $notificationsEnabled = ($user->send_notifications == 1);

        $this->set('userStats', $userStats);
        $this->set('user', $user);
        $this->set('userLanguages', $userLanguages);

        $this->set('isPublic', $isPublic);
        $this->set('isDisplayed', $isDisplayed);
        $this->set('notificationsEnabled', $notificationsEnabled);
    }

    /**
     * Save avatar image of current user.
     *
     * @return void
     */
    public function save_image()
    {
        $redirectURL = array('action' => 'profile', CurrentUser::get('username'));

        $image = null;
        $data = $this->request->getData();
        if (isset($data['image'])) {
            $image = $data['image'];
        }

        // We first check if a file has been correctly uploaded
        $redirect = (empty($data) || empty($image)) ||
                    ($image['error'] != UPLOAD_ERR_OK) ||
                    !is_uploaded_file($image['tmp_name']);
        if ($redirect) {
            $this->Flash->set(
                __('Failed to upload image')
            );
            return $this->redirect($redirectURL);
        }

        // The file size must be < 1mb
        $fileSize = (int) $image['size'] / 1024;
        if ($fileSize > 1024) {
            $this->Flash->set(
                __('Please choose an image that does not exceed 1 MB.')
            );
            return $this->redirect($redirectURL);
        }

        // Check file extension
        $fileExtension = pathinfo($image['name'], PATHINFO_EXTENSION);
        $validExtensions = array('png', 'jpg', 'jpeg', 'gif', 'PNG', 'JPG', 'JPEG', 'GIF');

        if (!in_array($fileExtension, $validExtensions)) {
            $this->Flash->set(
                __('Please choose GIF, JPEG or PNG image format.')
            );
            return $this->redirect($redirectURL);
        }

        // Generate name for picture
        $newFileName =  $this->Auth->user('id') . '.png' ;
        $newFileFullPath128 = WWW_ROOT . 'img' . DS . 'profiles_128' . DS . $newFileName;
        $newFileFullPath36 = WWW_ROOT . 'img' . DS . 'profiles_36'. DS . $newFileName;

        // Use _resize_image method here
        $save128Succed = $this->_resize_image(
            $image['tmp_name'],
            $newFileFullPath128,
            128
        );
        $save36Succed = $this->_resize_image(
            $image['tmp_name'],
            $newFileFullPath36,
            36
        );

        // if all resize has worked we can save it in user information
        if ($save36Succed && $save128Succed) {
            $this->loadModel('Users');
            $user = $this->Users->get($this->Auth->user('id'));
            $user->image = $newFileName;
            $this->Users->save($user);
        } else {
            $this->Flash->set(
                __("Error while saving.")
            );
        }

        return $this->redirect($redirectURL);
    }

    public function remove_image()
    {
        $this->loadModel('Users');
        $user = $this->Users->get(CurrentUser::get('id'));
        $user->image = null;
        $this->Users->save($user);

        return $this->redirect(array('action' => 'profile', CurrentUser::get('username')));
    }

    /**
     * Resize an image and save it
     *
     * @param string $oldFile   Full path to the old picture to resize
     * @param string $newFile   Full path where the resized file will be saved
     * @param int    $dimension Dimension of the new image, if the old picture
     *                          is not squarre the picture will be filled with
     *                          Transparent background
     *
     * @return boolean If save has succeded
     */

    private function _resize_image($oldFile, $newFile, $dimension)
    {
        $oldImage = new \Imagick($oldFile);
        $oldWidth = $oldImage->getImageWidth();
        $oldHeight = $oldImage->getImageHeight();

        if ($oldWidth > $oldHeight) {
            $oldImage->thumbnailImage($dimension, null);
        } else {
            $oldImage->thumbnailImage(null, $dimension);
        }

        $newImage = new \Imagick();
        $newImage->newImage(
            $dimension,
            $dimension,
            new \ImagickPixel("transparent")
        );


        $newImage->compositeImage(
            $oldImage,
            \Imagick::COMPOSITE_OVER,
            0,
            0
        );
        $newImage->setImageFormat("png32");
        $isSuccess = $newImage->writeImage($newFile);


        $newImage->clear();
        $newImage->destroy();
        $oldImage->clear();
        $oldImage->destroy();

        return $isSuccess;
    }

    /**
     * Save profile info.
     *
     * @return void
     */
    public function save_basic()
    {
        $currentUserId = CurrentUser::get('id');
        $data = $this->request->getData();

        $this->loadModel('Users');
        try {
            $user = $this->Users->get($currentUserId);
        } catch (RecordNotFoundException $e) {
            return $this->redirect('/');
        }

        if (!$this->_isUniqueEmail($data, $currentUserId)) {
            return $this->_redirectNonUniqueEmail();
        }

        if (!$this->_isValidBirthday($data)) {
            return $this->_redirectInvalidBirthday();
        }

        if (!$this->_isAcceptedBirthday($data)) {
            return $this->_redirectUnacceptedBirthday();
        }

        if (isset($data['birthday'])) {
            $data['birthday'] = $this->_generateBirthdayDate($data);
        }

        $allowedFields = [
            'name', 'country_id', 'birthday', 'homepage', 'email', 'description'
        ];
        $this->Users->patchEntity($user, $data, ['fields' => $allowedFields]);
        $savedUser = $this->Users->save($user);        

        if ($savedUser) {
            $this->Flash->set(
                __('Profile saved.')
            );
            return $this->redirect(
                array(
                    'action' => 'profile',
                    CurrentUser::get('username')
                )
            );
        } else {
            $this->Flash->set(
                __(
                    'Failed to change email address. Please enter a proper email address.',
                    true
                )
            );
            return $this->redirect(
                array(
                    'controller' => 'user',
                    'action' => 'settings'
                )
            );
        }
    }

    /**
     * Return true if entered email address is unique in database.
     *
     * @param  array   $data
     * @param  int     $currentUserId
     *
     * @return boolean
     */
    private function _isUniqueEmail($data, $currentUserId)
    {
        if (isset($data['email'])) {
            return $this->Users->isEmailUnique(
                $data['email'],
                $currentUserId
            );
        }

        return true;
    }

    /**
     * Redirect for non unique email address.
     *
     * @return void
     */
    private function _redirectNonUniqueEmail()
    {
        $this->Flash->set(
            __("That email address already exists. Please try another.")
        );
        return $this->redirect(
            array(
                    'controller' => 'user',
                    'action' => 'settings'
                )
        );
    }

    /**
     * Return true if entered birthday is a valid date or is not complete/set.
     *
     * @param  array  $data
     *
     * @return boolean
     */
    private function _isValidBirthday($data)
    {
        if (isset($data['birthday'])) {
            $year = $data['birthday']['year'];

            $month = $data['birthday']['month'];

            $day = $data['birthday']['day'];

            if ($year && $month && $day) {
                return checkdate($month, $day, $year);
            } elseif ($month && $day) {
                // Use 2016 because its a leap year.
                $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, 2016);

                if ($day > $daysInMonth) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Redirect for invalid birthday.
     *
     * @return void
     */
    private function _redirectInvalidBirthday()
    {
        $this->Flash->set(
            __("The entered birthday is an invalid date. Please try again.")
        );
        return $this->redirect(
            array(
                    'controller' => 'user',
                    'action' => 'edit_profile'
                )
        );
    }

    /**
     * Return true if birthday is an accepted birthday type.
     *
     * @param  array  $data
     *
     * @return boolean
     */
    private function _isAcceptedBirthday($data)
    {
         // Accepted birthday types. Each entry must be in reverse alphabetical
         // order (year, month, day).
        $acceptedBirthdays = [
            ['year', 'month', 'day'],
            ['year'],
            ['month', 'day'],
            ['year', 'month']
        ];

        if (isset($data['birthday'])) {
            $setValues = array_keys(array_filter($data['birthday']));

            rsort($setValues);

            if (!empty($setValues) && !in_array($setValues, $acceptedBirthdays)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Redirect for unaccepted birthday type.
     *
     * @return void
     */
    private function _redirectUnacceptedBirthday()
    {
        $this->Flash->set(
            __("The entered birthday is incomplete. ".
                "Accepted birthdays: full date, month and day, year and month, only year.", true)
        );
        return $this->redirect(
            array(
                    'controller' => 'user',
                    'action' => 'edit_profile'
                )
        );
    }

    /**
     * Fill unset birthday fields with zeros if birthday has at least one
     * user-set field. If all fields empty, return original array.
     *
     * @return array
     */
    private function _generateBirthdayDate($data)
    {
        foreach ($data['birthday'] as $key => $value) {
            if ($value == '' && $key == 'year') {
                $birthday[$key] = '0000';
            } elseif ($value == '') {
                $birthday[$key] = '00';
            } else {
                $birthday[$key] = $value;
            }
        }

        $birthdayString = implode('', $birthday);

        if ($birthdayString == '00000000') {
            return null;
        } elseif ($birthdayString == '02290000') {
            // Mysql wont save a partial leap year date so change year to 1904
            // and catch in date view helper.
            $birthday['year'] = '1904';
        }

        return $birthday['year'].'-'.$birthday['month'].'-'.$birthday['day'];
    }

    /**
     * Save option settings. Options are :
     *  - use advanced selectors for language selection
     *  - send notification emails
     *  - set profile as public
     *
     * @todo Application language
     *
     * @return void
     */
    public function save_settings()
    {
        $currentUserId = CurrentUser::get('id');
        if (empty($currentUserId)) {
            return $this->redirect('/');
        }

        $data = $this->request->getData();
        if (!empty($data)) {
            $this->loadModel('Users');
            $data['settings']['lang'] = $this->_language_settings(
                $data['settings']['lang']
            );
            $user = $this->Users->get($currentUserId);
            $allowedFields = ['send_notifications', 'settings'];
            $this->Users->patchEntity($user, $data, ['fields' => $allowedFields]);
            $savedUser = $this->Users->save($user);
            if ($savedUser) {
                $flashMsg = __('Your settings have been saved.');
            } else {
                $flashMsg = __(
                    'An error occurred while saving. Please try again or contact '.
                    'us to report this.',
                    true
                );
            }

            $this->Flash->set($flashMsg);
        }

        return $this->redirect(array('action' => 'settings'));
    }


    /**
     * Check language settings entered by the user and return corrected string
     * (if correction is needed).
     *
     * A correct string should be composed of ISO codes that are present in the
     * list of supported languages, separated by commas.
     * For instance: eng,deu,jpn,ita.
     *
     * @param string $userInput desired language settings
     *
     * @return array
     */
    private function _language_settings($userInput)
    {
        $userInput = str_replace(' ', '', $userInput);
        $userLangs = explode(',', $userInput);
        $tmpLanguagesArray = array();
        foreach ($userLangs as $lang) {
            if (array_key_exists($lang, LanguagesLib::languagesInTatoeba())) {
                $tmpLanguagesArray[] = $lang;
            }
        }

        $languageSettings = implode(',', $tmpLanguagesArray);

        if (empty($languageSettings)) {
            $languageSettings = null;
        }

        return $languageSettings;
    }


    /**
     * Change password.
     *
     * @return void
     */
    public function save_password()
    {
        $data = $this->request->getData();
        if (!empty($data)) {
            $this->loadModel('Users');
            $userId = $this->Auth->user('id');

            $passwordHasher = new VersionedPasswordHasher();
            $submittedPassword = $data['old_password'];
            $actualPassword = $this->Users->getPassword($userId);

            $newPassword1 = $data['new_password'];
            $newPassword2 = $data['new_password2'];

            if (empty($newPassword1)) {
                $flashMsg = __('New password cannot be empty.');
            }
            elseif ($newPassword1 != $newPassword2) {
                $flashMsg = __("New passwords do not match.");
            }
            elseif ($passwordHasher->check($submittedPassword, $actualPassword)) {
                $user = $this->Users->get($userId);
                $this->Users->patchEntity($user, ['password' => $newPassword1]);
                $savedUser = $this->Users->save($user);
                if ($savedUser) {
                    $flashMsg = __('New password has been saved.');
                } else {
                    $flashMsg = __('An error occurred while saving.');
                }

            } else {

                $flashMsg = __('Password error. Please try again.');

            }

            $this->Flash->set($flashMsg);
        }

        return $this->redirect(array('action' => 'settings'));
    }

    /**
     * Retrieve stats about the user.
     * This is displayed on homepage for now...
     *
     * @param integer $userId User indentifiant
     *
     * @return array
     */
    private function _stats($userId)
    {
        $this->loadModel('Audios');
        $this->loadModel('Sentences');
        $this->loadModel('SentenceComments');
        $this->loadModel('Contributions');
        $this->loadModel('Favorites');
        $numberOfSentences = $this->Sentences->numberOfSentencesOwnedBy($userId);
        $numberOfComments
            = $this->SentenceComments->numberOfCommentsOwnedBy($userId);

        $numberOfContributions
            = $this->Contributions->numberOfContributionsBy($userId);
        $numberOfFavorites  = $this->Favorites->numberOfFavoritesOfUser($userId);
        $numberOfAudios = $this->Audios->numberOfAudiosBy($userId);
        return compact(
            'numberOfComments',
            'numberOfSentences',
            'numberOfContributions',
            'numberOfFavorites',
            'numberOfAudios'
        );
    }


    /**
     * Edit personal information and description.
     *
     * @return void
     */
    public function edit_profile()
    {
        $this->loadModel('Users');

        $currentUserId = CurrentUser::get('id');
        if (empty($currentUserId)) {
            return $this->redirect('/');
        }

        $userInfo = $this->Users->getInformationOfCurrentUser($currentUserId);
        $this->set('user', $userInfo);
    }


    /**
     * Edit personal information.
     *
     * @return void
     */
    public function settings()
    {
        $currentUserId = CurrentUser::get('id');
        if (empty($currentUserId)) {
            return $this->redirect('/');
        }

        $this->loadModel('Users');
        $userSettings = $this->Users->getSettings($currentUserId);
        $this->set('userSettings', $userSettings);
    }


    public function language($lang = null)
    {
        $userId = CurrentUser::get('id');
        $username = CurrentUser::get('username');
        $userLanguage = null;

        if (!empty($lang)) {
            $this->loadModel('UsersLanguages');
            $userLanguage = $this->UsersLanguages->getLanguageInfoOfUser($lang, $userId);
        }

        $this->set('userLanguage', $userLanguage);
        $this->set('ofUserId', $userId);
        $this->set('username', $username);
    }

    public function accept_new_terms_of_use()
    {
        $this->saveSetting($this->request->getData('settings'));

        $this->Flash->set(__('You have accepted the new terms of use.'));
        return $this->redirect($this->referer());
    }

    public function save_banner_setting()
    {
        $savedUser = $this->saveSetting($this->request->getData());

        $acceptsJson = $this->request->accepts('application/json');
        if ($acceptsJson) {
            $this->set('saved', (bool)$savedUser);
            $this->loadComponent('RequestHandler');
            $this->set('_serialize', ['saved']);
            $this->RequestHandler->renderAs($this, 'json');
        }
    }

    private function saveSetting($data) {
        $this->loadModel('Users');
        $user = $this->Users->get(CurrentUser::get('id'));
        $this->Users->patchEntity($user, [
            'settings' => $data
        ]);
        $savedUser = $this->Users->save($user);

        return $savedUser;
    }
}
