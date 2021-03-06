<?php
/***********************************************************************
| Avactis (TM) Shopping Cart software developed by HBWSL.
| http://www.avactis.com
| -----------------------------------------------------------------------
| All source codes & content (c) Copyright 2004-2010, HBWSL.
| unless specifically noted otherwise.
| =============================================
| This source code is released under the Avactis License Agreement.
| The latest version of this license can be found here:
| http://www.avactis.com/license.php
|
| By using this software, you acknowledge having read this license agreement
| and agree to be bound thereby.
|
 ***********************************************************************/
?><?php
/***********************************************************************
| Avactis (TM) Shopping Cart software developed by HBWSL.
| http://www.avactis.com
| -----------------------------------------------------------------------
| All source codes & content (c) Copyright 2004-2013, HBWSL.
| unless specifically noted otherwise.
| =============================================
| This source code is released under the Avactis License Agreement.
| The latest version of this license can be found here:
| http://www.avactis.com/license.php
|
| By using this software, you acknowledge having read this license agreement
| and agree to be bound thereby.
|
 ***********************************************************************/
?><?php

/**
 * @package Look & Feel
 * @author Sergey Kulitsky
 *
 */

class Edit_Skin extends AjaxAction
{
    function Edit_Skin()
    {
    }

    function onAction()
    {
        global $application;
        $request = &$application -> getInstance('Request');
        // getting posted review data
        $skin = $request -> getValueByKey('skin');
		setcookie('edit_skin', $skin, time() + 2592000, '/');
        // prepare the redirect
        $req_to_redirect = new Request();
        $req_to_redirect -> setView(CURRENT_REQUEST_URL);
        $application -> redirect($req_to_redirect);
    }
}