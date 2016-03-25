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

/**
 * @package Customer_Reviews
 * @author Sergey E. Kulitsky
 *
 */

class sign_in_required extends AjaxAction
{
    function sign_in_required()
    {
    }

    function onAction()
    {
        global $application;
        $request = &$application -> getInstance('Request');

        $return_url = $request -> getValueByKey('returnURL');
        modApiFunc('Session', 'set', 'toURLAfterSignIn', $return_url);

        $req_to_redirect = new Request();
        $req_to_redirect -> setView('CustomerSignIn');

        $application -> redirect($req_to_redirect);
    }
}