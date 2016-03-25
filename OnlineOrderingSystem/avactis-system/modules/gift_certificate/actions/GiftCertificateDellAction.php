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

class GiftCertificateDellAction extends AjaxAction
{

    function GiftCertificateDellAction()
    {

    }

    function onAction()
    {
        global $application;
        $request = $application->getInstance('Request');
        $gc_code = $request->getValueByKey('gc_code');

        execQuery('DELETE_GC_BY_CODE', array("gc_code" => $gc_code));

        $request = new Request();
        $request->setView(CURRENT_REQUEST_URL);
        $application->redirect($request);

    }
}
?>