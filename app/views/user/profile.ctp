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

/**
 * profile view for Users.
 *
 * @category Users
 * @package  View
 * @author   BEN YAALA Salem <salem.benyaala@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */
 
if ($user['User']['name'] != '') {
    $this->pageTitle = sprintf(__("Profile of %s", true), $user['User']['name']);
} else {
    $this->pageTitle = sprintf(__("%s's profile", true), $user['User']['username']);
}

if ($is_public or $login) {
    //echo $javascript->includeScript('users.followers_and_following');
?>

<div id="annexe_content">

    <div id="pcontact" class="module">
        <h2><?php __('Contact information'); ?></h2>
        <dl>
            <dt><?php __('Private message'); ?></dt>
            <dd><?php echo $html->link(sprintf(__('Contact %s', true), $user['User']['username']),
            array('controller' => 'privateMessages', 'action' => 'write', $user['User']['username'])); ?></dd>

            <dt><?php __('Others'); ?></dt>
            <dd><?php echo $html->link(sprintf(__("See this user's contributions", true)),
            array('controller' => 'users', 'action' => 'show', $user['User']['id'])); ?></dd>

            <?php
            if(!empty($user['User']['homepage'])){
            ?>
                <dt><?php __('Homepage'); ?></dt>
                <dd><?php echo '<a href="' . $user['User']['homepage'] . '" title="' . $user['User']['username'] . '">' . $user['User']['homepage'] . '</a>'; ?></dd>
            <?php
            }
            ?>
        </dl>
    </div>

    <div class="module">
        <h2><?php __('Activity information'); ?></h2>
        <dl>
            <dt><?php __('Member since'); ?></dt>
            <dd><?php echo date('F j, Y', strtotime($user['User']['since'])); ?></dd>
            <dt><?php __('Last login'); ?></dt>
            <dd><?php echo date('F j, Y \\a\\t G:i', $user['User']['last_time_active']); ?></dd>
            <dt><?php __('Comments posted'); ?></dt>
            <dd><?php echo $userStats['numberOfComments']; ?></dd>
            <dt><?php __('Sentences owned'); ?></dt>
            <dd><?php echo $userStats['numberOfSentences']; ?></dd>
            <dt><?php __('Sentences favorited'); ?></dt>
            <dd><?php echo $userStats['numberOfFavorites']; ?></dd>
        </dl>
    </div>
    <?php
    /*
    <div class="module">
        <h2><?php __('Following'); ?></h2>
        <div class="following"></div>
    </div>

    <div class="module">
        <h2><?php __('Followers'); ?></h2>
        <div class="followers"></div>
    </div>
    */
    ?>
</div>

<div id="main_content">
    <div class="module profile_master_content">
        <h2><?php if($user['User']['name'] != '') echo $user['User']['name'] . ' aka. ' . $user['User']['username'];
        else echo $user['User']['username'] ?></h2>
        <?php 
        /*
        <p class="user followLinkContainer" id="<?php echo '_'.$user['User']['id']; ?>">
        <?php if($session->read('Auth.User.id') && isset($can_follow)){
            if($can_follow){
                $style2 = "style='display: none'";
                $style1 = "";
            }else{
                $style1 = "style='display: none'";
                $style2 = "";
            }
            echo '<a id="start" class="followingOption" '.$style1.'><span class="in_process"></span>'. __('Follow', true). '</a>';
            echo '<a id="stop" class="followingOption" '.$style2.'><span class="in_process"></span>'. __('Unfollow', true). '</a>';
        } ?>
        </p>
        */
        ?>
        <div id="pimg">
<?php
echo $html->image('profiles/' . (empty($user['User']['image']) ? 'tatoeba_user.png' : $user['User']['image'] ), array(
    'alt' => $user['User']['username'],
));
?>
        </div>
    </div>
<?php
if (!empty($user['User']['description'])) {
?>
    <div id="pdescription" class="module">
        <h2><?php __('Something about you') ?></h2>
        <div id="profile_description"><?php echo nl2br($user['User']['description']) ?></div>
    </div>
<?php
}
?>
    <div id="pbasic" class="module">
        <h2><?php __('Basic Information') ?></h2>
        <dl>
<?php
if (!empty($user['User']['name'])) {
?>
            <dt><?php __('Name'); ?></dt>
            <dd><?php echo $user['User']['name'] ?></dd>
<?php
}

$aBirthday = explode('-', substr($user['User']['birthday'], 0, 10));
// 0 => YYYY
// 1 => MM
// 2 => DD
// needed to do a substr because $user['User']['birthday'] is formatted as YYYY-MM-DD HH:mm:ss

if (intval($aBirthday[0] + $aBirthday[1] + $aBirthday[2]) != 0) {
    $iTimestamp = mktime(0, 0, 0, $aBirthday[1], $aBirthday[2], $aBirthday[0]);

?>
            <dt><?php __('Birthday'); ?></dt>
            <dd><?php echo date('F j, Y', $iTimestamp) ?></dd>
<?php
}

if (is_string($user['User']['country_id'])
    and strlen($user['User']['country_id']) == 2) {
?>
            <dt><?php __('Country'); ?></dt>
            <dd><?php echo $user['Country']['name'] ?></dd>
<?php
}
?>
        </dl>
    </div>

</div>

<?php
} else {

    echo '<p>';
    __('This profile is protected. You must login to see it.');
    echo '</p>';

}
?>
