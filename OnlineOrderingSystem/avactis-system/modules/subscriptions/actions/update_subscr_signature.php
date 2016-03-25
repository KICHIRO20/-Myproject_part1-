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

class update_subscr_signature extends AjaxAction
{
    function update_subscr_signature()
    {
    }
    function onAction()
    {
        global $application;

        $signature = modApiFunc('Request', 'getValueByKey', 'signature_html');
        modApiFunc('Configuration', 'setValue', array(SYSCONFIG_NEWSLETTERS_SIGNATURE => $signature));

//        $SessionPost = array('ViewState' => array('hasCloseScript' => 'true'));
        modApiFunc('Session', 'set', 'SessionPost', $SessionPost);

        $request = new Request();
        $request->setView(CURRENT_REQUEST_URL);
        $application->redirect($request);
    }
}
?>