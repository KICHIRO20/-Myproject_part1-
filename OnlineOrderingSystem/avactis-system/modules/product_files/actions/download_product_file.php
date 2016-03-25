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

class download_product_file extends AjaxAction
{
    function download_product_file()
    {
    }

    function onAction()
    {
        global $application;
        $request = &$application->getInstance('Request');

        $hl_key = $request->getValueByKey('key');

        if(modApiFunc('Product_Files','isDownloadAllowed',$hl_key))
        {
            modApiFunc('Product_Files','sendProductFile',$hl_key);
            die();
        };

        $r = new Request();
        $r->setView('Download');
        $r->setKey('key',$hl_key);
        $application->redirect($r);
    }
};

?>