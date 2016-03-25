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

class ajax_gen_htaccess extends AjaxAction
{
    function ajax_gen_htaccess()
    {
    }

    function onAction()
    {
        global $application;
        $request = &$application->getInstance('Request');

        $layout_path = $request->getValueByKey('layout_path');

        $hta_content = modApiFunc('Mod_Rewrite','genRewriteBlock',$layout_path);

        #              .      action                               ajax,                   echo.
        echo $hta_content;
    }
};

?>