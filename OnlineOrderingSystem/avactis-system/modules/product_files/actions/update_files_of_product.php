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

class update_files_of_product extends AjaxAction
{
    function update_files_of_product()
    {
    }

    function onAction()
    {
        global $application;
        $request = &$application->getInstance('Request');

        $product_id = $request->getValueByKey('product_id');
        $files_descr = $request->getValueByKey('files_descr');

        foreach($files_descr as $file_id => $file_descr)
            modApiFunc('Product_Files','updatePFileDescr',$file_id,$file_descr);

        modApiFunc('Session','set','ResultMessage','MSG_FILES_WERE_UPDATED');

        $r = new Request();
        $r->setView('PF_FilesList');
        $r->setKey('product_id',$product_id);
        $application->redirect($r);
    }
};

?>