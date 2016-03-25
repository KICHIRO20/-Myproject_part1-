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
 * @package ProductFiles
 * @author Egor V. Derevyankin
 *
 */

class update_hl_edate extends AjaxAction
{
    function update_hl_edate()
    {
    }

    function onAction()
    {
        global $application;
        $request = &$application->getInstance('Request');

        $hl_id = $request->getValueByKey('hl_id');
        $opid = $request->getValueByKey('opid');
        $edate = explode("/",$request->getValueByKey('edate'));

        $ts = mktime($edate[3],$edate[4],0,$edate[1],$edate[2],$edate[0]);

        modApiFunc('Product_Files','updateHotlinkExpireDate',$opid,$hl_id,$ts);

        $r = new Request();
        $r->setView(CURRENT_REQUEST_URL);
        $r->setKey('page_view','PF_OrderHotlinks');
        $r->setKey('opid',$request->getValueByKey('opid'));

        $application->redirect($r);
    }
};

?>