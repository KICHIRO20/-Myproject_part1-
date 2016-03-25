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

class change_hotlink_status extends AjaxAction
{
    function change_hotlink_status()
    {
    }

    function onAction()
    {
        global $application;
        $request = &$application->getInstance('Request');
        modApiFunc('Product_Files','changeHotlinkStatus',$request->getValueByKey('opid'),$request->getValueByKey('hl_id'));

        $r = new Request();
        $r->setView(CURRENT_REQUEST_URL);
        $r->setKey('page_view','PF_OrderHotlinks');
        $r->setKey('opid',$request->getValueByKey('opid'));

        $application->redirect($r);
    }
};

?>