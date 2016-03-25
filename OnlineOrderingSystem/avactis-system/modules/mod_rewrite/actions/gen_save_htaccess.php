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
 * @package ModRewrite
 * @author Egor V. Derevyankin
 *
 */

class gen_save_htaccess extends AjaxAction
{
    function gen_save_htaccess()
    {
    }

    function onAction()
    {
        global $application;
        $request = &$application->getInstance('Request');
        $layout_path = $request->getValueByKey('storefront_link');

        $hta_content = modApiFunc('Mod_Rewrite','genRewriteBlock',$layout_path);
        $res = modApiFunc('Mod_Rewrite','saveHTAcontent',$hta_content,$layout_path);

        if(empty($res))
        {
            modApiFunc('Session','set','ResultMessage','MSG_HTA_SAVED');
        }
        else
        {
            modApiFunc('Session','set','Errors',$res);
        };

        $r = new Request();
        $r->setView(CURRENT_REQUEST_URL);
        $r->setKey('page_view','MR_Settings');
        $application->redirect($r);
    }
};

?>