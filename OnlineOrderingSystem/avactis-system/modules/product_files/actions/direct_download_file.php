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

class direct_download_file extends AjaxAction
{
    function direct_download_file()
    {
    }

    function onAction()
    {
        if (modApiFunc('Users', 'getZone') == "AdminZone")
        {
            global $application;
            $request = &$application->getInstance('Request');
            $file_id = $request->getValueByKey('file_id');
            modApiFunc('Product_Files','sendProductFile','',$file_id);
        };
        die();
    }
};

?>