<?php
/*
    Tatoeba Project, free collaborative creation of multilingual corpuses project
    Copyright (C) 2009 Etienne Deparis <etienne.deparis@umaneti.net>

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Affero General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
if  ($session->check('Message.auth')) $session->flash('auth');

?>

<script type="text/javascript">
<!--
	var status = true;
	function displayLoginForm(){
		if(status){
			document.getElementById('UserLoginForm_FromBar').style.display = 'block';
			status = false;
		}else{
			document.getElementById('UserLoginForm_FromBar').style.display = 'none';
			status = true;
		}
	}
-->
</script>


<ul id="UserLoginLinkList">
	<li onclick="javascript:displayLoginForm();" class="login_pseudo_link"><?php echo __('Login', true); ?></li>
</ul>

<form id="UserLoginForm_FromBar" method="post" action="/eng/users/login" style="display:none;">
	<fieldset style="display:none;">
		<input type="hidden" name="_method" value="POST" />
	</fieldset>
	<fieldset>
		<label for="UserUsername"><?php echo __('Username:', true); ?> </label>
		<input name="data[User][username]" type="text" maxlength="20" value="" id="UserUsername" /><br/>
		<label for="UserPassword"><?php echo __('Password:', true); ?> </label>
		<input type="password" name="data[User][password]" value="" id="UserPassword" />
		<input type="hidden" name="data[User][rememberMe]" value="0" id="UserRememberMe_" /><br/>
		<label for="UserRememberMe" class="notInBlackBand"><?php echo __('Remember me', true); ?></label>
		<input type="checkbox" name="data[User][rememberMe]" value="1" id="UserRememberMe" /><br/>
		<input type="submit" value="<?php echo __('Log in', true); ?>" />
        <input type="hidden" name="redirectTo"  value="<?php echo $_SERVER['REQUEST_URI'] ;?>" />
	</fieldset>
	<p>
		<?php echo $html->link(__('Password forgotten?',true), array(
												"controller" => "users",
												"action" => "new_password"
			)); ?>
		<span onclick="javascript:displayLoginForm();" class="login_pseudo_link" style="float:right;"><?php echo __('Close', true); ?></span>
	</p>
</form>
