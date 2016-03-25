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
 * @package QuickBooks
 * @author Egor V. Derevyankin
 *
 */

class update_qb_settings extends AjaxAction
{
    function update_qb_settings()
    {
    }

    function onAction()
    {
        global $application;
        $request = &$application->getInstance('Request');
        $settings = $request->getValueByKey('qbs');
        $settings['MIN_QIS'] = intval($settings['MIN_QIS']);
        modApiFunc('Quick_Books','updateSettings',$settings);
	modApiFunc('Session','set','ResultMessage','MSG_QUICKBOOK_SETTINGS_UPDATED');

        $req_to_redirect = new Request();
        $req_to_redirect->setView(CURRENT_REQUEST_URL);
        $req_to_redirect->setKey('page_view','QB_Settings');
        $application->redirect($req_to_redirect);
    }
};

?>