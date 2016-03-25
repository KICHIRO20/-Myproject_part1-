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


class GiftCertificateAddAction extends AjaxAction
{
    function onAction()
    {
        loadClass('GiftCertificateCreator');
        $cr = new GiftCertificateCreator();

        //                                    ,                .             ,                   
        //         $map                 $_POST          .
        //                              ,                     .
        $cr->initByMap($_POST);
        //                     .
        $cr->save();
        if ($cr->isError())
        {
            modApiFunc('Session', 'set', 'gc_update_action_result', 'failed');
            modApiFunc('Session', 'set', 'gc_update_action_errors', $cr->errors);
            modApiFunc('Session', 'set', 'SessionPost', $_POST);
        }
        else
        {
            modApiFunc('Session', 'set', 'gc_update_action_result', 'created');

            if ($cr->sendtype === GC_SENDTYPE_EMAIL && $cr->status === GC_STATUS_ACTIVE)
                modApiFunc('EventsManager','throwEvent','GiftCertificateCreated',$cr);

            $request = new Request();
            $request->setView('GiftCertificateEditView');
            $request->setKey('gc_code', $cr->code);
            global $application;
            $application->redirect($request);
        }
    }
}

?>