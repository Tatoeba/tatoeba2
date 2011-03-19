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
 * @link     http://tatoeba.org
 */


App::import('Core', 'Sanitize');

/**
 * Controller for sentence comments.
 *
 * @category Users
 * @package  Controllers
 * @author   BEN YAALA Salem <salem.benyaala@gmail.com>
 * @author   Allan SIMON <allan.simon@supinfo.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
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
        'Contribution',
        'Sentence',
        'SentenceComment',
        'Favorite'
    );

    public $helpers = array('Html', 'Date');

    /**
     * ?
     *
     * @todo Restrict actions if needed. I don't know much about this stuff.
     *
     * @return void
     */
    public function beforeFilter()
    {
        parent::beforeFilter();

        $this->Auth->allowedActions = array(
            'profile',
            'edit_profile',
            'settings'
        );
    }


    /**
     * Display profile of given user.
     * If no username is specified, redirect to index
     * (that is current user's profile).
     * If wrong username is specified, redirect to list of members.
     *
     * @param string $userName User screen identifiant
     *
     * @return void
     */
    public function profile($userName)
    {
        $this->helpers[] = 'ClickableLinks';
        $this->helpers[] = 'Members';
        
        $userName = Sanitize::paranoid($userName, array('_'));
        $infoOfUser = $this->User->getInformationOfUser($userName);
        $user = $infoOfUser['User'];
        $countryName = $infoOfUser['Country']['name'];
        $userStatus = $infoOfUser['Group']['name'];
        $groupId = $infoOfUser['Group']['id'];
        $userId = $user['id']; 
        $userStats = $this->_stats($userId);
        
        $isPublic = ($user['is_public'] == 1);
        $isDisplayed = ($isPublic || CurrentUser::isMember());
        $notificationsEnabled = ($user['send_notifications'] == 1);
        
        $this->set('userStats', $userStats);
        $this->set('user', $user);
        $this->set('countryName', $countryName);
        $this->set('userStatus', $userStatus);
        $this->set('groupId', $groupId);
        
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
        $image = null;
        if (isset($this->data['profile_image']['image'])) {
            $image = $this->data['profile_image']['image'];
        }
        $redirectURL = array('action' => 'profile', CurrentUser::get('username'));
        
        // We first check if a file has been correctly uploaded
        $redirect = (empty($this->data) || !empty($image));
        $recirect = ($image['error'] != UPLOAD_ERR_OK);
        $redirect = (!is_uploaded_file($image['tmp_name']));
        if ($redirect) {
            $this->redirect($redirectURL);
        }
        
        // The file size must be < 1mo
        $fileSize = (int) $image['size'] / 1024;
        if ($fileSize > 1024) {
            $this->Session->setFlash(
                __('Please choose an image that do not exceed 1 MB.', true)
            );
            $this->redirect($redirectURL);
        }

        // Check file extension
        $fileExtension = pathinfo($image['name'], PATHINFO_EXTENSION);
        $validExtensions = array('png', 'jpg', 'jpeg', 'gif');
        
        if (!in_array($fileExtension, $validExtensions)) {
            $this->Session->setFlash(
                __('Please choose GIF, JPEG or PNG image format.', true)
            );
            $this->redirect($redirectURL);
        }
        
        // Generate name for picture
        $email = $this->Auth->user('email');
        $newFileName =  md5($email) . '.png' ;
        $newFileFullPath128 = IMAGES . "profiles_128". DS . $newFileName;
        $newFileFullPath36 = IMAGES . "profiles_36". DS . $newFileName;
        
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
            $this->User->id = $this->Auth->user('id');
            $this->User->save(
                array(
                    'User' => array(
                        'image' => $newFileName
                     )
                )
            );
        
        } else {
            $this->Session->setFlash(
                __("Error while saving.", true)
            );
        }

        $this->redirect($redirectURL);
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
        $oldImage = new Imagick($oldFile);
        $oldWidth = $oldImage->getImageWidth();  
        $oldHeight = $oldImage->getImageHeight();
        
        if ($oldWidth > $oldHeight) {
            $oldImage->thumbnailImage($dimension, null);
        } else {
            $oldImage->thumbnailImage(null, $dimension);
        }
         
        $newImage = new Imagick();     
        $newImage->newImage(
            $dimension,
            $dimension,
            new ImagickPixel("transparent")
        );
        
        
        $newImage->compositeImage(
            $oldImage,
            Imagick::COMPOSITE_OVER,
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
     * Save user's description about himself/herself.
     *
     * @return void
     */
    public function save_description()
    {
        $currentUserId = CurrentUser::get('id');
        if (empty($currentUserId)) {
            $this->redirect('/');
        }
        
        if (!empty($this->data)) {
            $this->User->id = $currentUserId;
            $this->User->saveField(
                'description',
                $this->data['User']['description']
            );
        }

        $this->redirect(
            array(
                'action' => 'profile',
                CurrentUser::get('username')
            )
        );
    }

    /**
     * Save name, birthday and country
     *
     * @return void
     */
    public function save_basic()
    {
        $currentUserId = CurrentUser::get('id');
        if (empty($currentUserId)) {
            $this->redirect('/');
        }
        
        $saved = false;
        if (!empty($this->data)) {
            $this->data['User']['id'] = $currentUserId;
            $saved = $this->User->save($this->data);
        }
        
        if ($saved) {
            $this->redirect(
                array(
                    'action' => 'profile',
                    CurrentUser::get('username')
                )
            );
        }
    }


    /**
     * Save email and personal URL.
     * TODO Delete me.
     *
     * @return void
     */
    public function save_contact()
    {
        if (!empty($this->data)) {
            $userData['User'] = $this->data['profile_contact'];
            $this->User->id = $this->Auth->user('id');
            
            if ($this->User->save($userData)) {
                $flashMsg = __('Your contact information have been saved.', true);
            } else {
                $flashMsg = __(
                    'An error occured while saving. Please try again or contact '.
                    'us to report this.',
                    true
                );
            }

            $this->Session->setFlash($flashMsg);
        }

        $this->redirect(array('action' => 'index'));
    }

    /**
     * Save option settings. Options are :
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
            $this->redirect('/');
        }
        
        if (!empty($this->data)) {
            $this->data['User']['id'] = $currentUserId;
            if ($this->User->save($this->data)) {
                $flashMsg = __('Your settings have been saved.', true);
            } else {
                $flashMsg = __(
                    'An error occured while saving. Please try again or contact '.
                    'us to report this.',
                    true
                );
            }

            $this->Session->setFlash($flashMsg);
        }

        $this->redirect(
            array(
                'action' => 'profile',
                 CurrentUser::get('username')
            )
        );
    }

    /**
     * Change password.
     *
     * @return void
     */
    public function save_password()
    {
        if (!empty($this->data)) {
            
            $userId = $this->Auth->user('id');
            
            $submittedPassword = $this->Auth->password(
                $this->data['User']['old_password']
            );
            $actualPassword = $this->User->getPassword($userId);
            
            $newPassword1 = $this->data['User']['new_password'];
            $newPassword2 = $this->data['User']['new_password2'];

            if ($submittedPassword == $actualPassword
                && $newPassword1 == $newPassword2
            ) {

                $newPassword1 = $this->Auth->password($newPassword1);
                
                $this->User->id = $userId;
                if ($this->User->saveField('password', $newPassword1)) {
                    $flashMsg = __('New password has been saved.', true);
                } else {
                    $flashMsg = __('An error occured while saving.', true);
                }

            } else {

                $flashMsg = __('Password error. Please try again.', true);

            }

            $this->Session->setFlash($flashMsg);
        }

        $this->redirect(array('action' => 'index'));
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
        $numberOfSentences = $this->Sentence->numberOfSentencesOwnedBy($userId);
        $numberOfComments
            = $this->SentenceComment->numberOfCommentsOwnedBy($userId);

        $numberOfContributions
            = $this->Contribution->numberOfContributionsBy($userId);
        $numberOfFavorites  = $this->Favorite->numberOfFavoritesOfUser($userId);

        $userStats = array(
            'numberOfComments'      => $numberOfComments ,
            'numberOfSentences'     => $numberOfSentences ,
            'numberOfContributions' => $numberOfContributions,
            'numberOfFavorites'     => $numberOfFavorites
        );
        return $userStats;
    }
    
    
    /**
     * Edit personal information and description.
     *
     * @return void
     */
    public function edit_profile()
    {
        $currentUserId = CurrentUser::get('id');
        if (empty($currentUserId)) {
            $this->redirect('/');
        }
        
        $user = $this->User->getInformationOfCurrentUser($currentUserId);
        $this->data = $user;
        // Hack. It seems that it screws up the "action" params in <form> 
        // when the id is set. So we're setting it to null.
        $this->data['User']['id'] = null; 
        
        $this->loadModel('Country');
        $tmpCountries = $this->Country->findAll();
        $countries = array();
        foreach ($tmpCountries as $country) {
            $countryId = $country['Country']['id'];
            $countries[$countryId] = $country['Country']['name'];
        }
        $this->set('countries', $countries);
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
            $this->redirect('/');
        }
        
        $settings = $this->User->getSettings($currentUserId);
        $this->set('settings', $settings['User']);
    }
}
?>
