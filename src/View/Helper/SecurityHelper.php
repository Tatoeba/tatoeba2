<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2015  Gilles Bedel
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
namespace App\View\Helper;

use App\View\Helper\AppHelper;


/**
 * This helper is only here to mess up with the Form helper.
 * It prevents the Form helper to set anti-CSRF hidden fields in every
 * form of the website. These fields are disabled by default and should
 * be enabled or disabled after or before calls to $this->Form->create() and
 * $this->Form->end() for forms which target is subject to anti-CSRF protection,
 * that is to say all forms of the Users controller.
 */
class SecurityHelper extends AppHelper
{
    private $token;

    public function beforeRender($viewFile) {
        $this->token = $this->getView()->getRequest()->getParam('_Token');
        //$this->disableCSRFProtection();
    }

    public function enableCSRFProtection() {
        $this->request['_Token'] = $this->token;
    }

    public function disableCSRFProtection() {
        unset($this->request['_Token']);
    }
}
?>
