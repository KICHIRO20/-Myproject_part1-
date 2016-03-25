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


class GiftCertificateUpdateAction extends AjaxAction
{
    function onAction()
    {
        $gc_code = modApiFunc('Request', 'getValueByKey','gc_code');
        if (Validator::isNotEmpty($gc_code) &&
            modApiFunc('GiftCertificateApi', 'isCodeValid', $gc_code) &&
            modApiFunc('GiftCertificateApi', 'doesCodeExist', $gc_code))
        {
            loadClass('GiftCertificateUpdater');
            //                                                                  
            $cr = new GiftCertificateUpdater($gc_code);
            //         ,                                                   
            if ($cr->isError())
            {
                modApiFunc('Session', 'set', 'gc_update_action_result', 'failed');
                modApiFunc('Session', 'set', 'gc_update_action_errors', $cr->errors);
                modApiFunc('Session', 'set', 'SessionPost', $_POST);
                return;
            }
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
                modApiFunc('Session', 'set', 'gc_update_action_result', 'updated');

                if ($cr->sendtype === GC_SENDTYPE_EMAIL && $cr->status === GC_STATUS_ACTIVE)
                    modApiFunc('EventsManager','throwEvent','GiftCertificateCreated',$cr);
            }
        }
    }
}

?>