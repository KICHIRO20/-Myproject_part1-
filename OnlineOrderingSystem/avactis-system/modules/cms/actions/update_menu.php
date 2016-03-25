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
 * @package CMS
 * @author Sergey Kulitsky
 *
 */

class update_menu extends AjaxAction
{
    function update_menu()
    {
    }

    function onAction()
    {
        global $application;
        $request = &$application -> getInstance('Request');

        // getting posted page data
        $data = $request -> getValueByKey('data');
        $delete = $request -> getValueByKey('delete');

        // getting mode
        $action = $request -> getValueByKey('mode');

        if ($action == 'delete' && is_array($delete))
        {
            foreach($delete as $menu_id => $v)
                modApiFunc('CMS', 'deleteMenu', $menu_id);

            // setting ResultMessage
            modApiFunc('Session', 'set', 'ResultMessage',
                       'CMS_MSG_MENU_DELETED');
        }

        // prepare the redirect
        $req_to_redirect = new Request();
        $req_to_redirect -> setView(CURRENT_REQUEST_URL);
        $application -> redirect($req_to_redirect);
    }
}