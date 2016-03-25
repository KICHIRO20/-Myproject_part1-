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
 * @package Manufacturers
 * @author Vadim Lyalikov
 *
 */

class update_manufacturers_settings extends AjaxAction
{
    function update_manufacturers_settings()
    {
    }

    function onAction()
    {
        global $application;
        $request = &$application->getInstance('Request');
        $_sets = $request->getValueByKey('pi_sets');

        $_sets['THUMB_SIDE'] = intval($_sets['THUMB_SIDE']);
        if($_sets['THUMB_SIDE'] < 1)
            $_sets['THUMB_SIDE'] = 1;

        $_sets['THUMBS_PER_LINE'] = intval($_sets['THUMBS_PER_LINE']);
        if($_sets['THUMBS_PER_LINE'] < 1)
            $_sets['THUMBS_PER_LINE'] = 1;

        modApiFunc('Manufacturers','updateSettings',$_sets);
        modApiFunc('Session','set','ResultMessage','MSG_MNF_SETTINGS_UPDATED');
        $r = new Request();
        $r->setView(CURRENT_REQUEST_URL);
        $r->setKey('page_view','MNF_Settings');

        $application->redirect($r);
    }
};

?>