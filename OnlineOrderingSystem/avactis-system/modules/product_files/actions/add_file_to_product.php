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

class add_file_to_product extends AjaxAction
{
    function add_file_to_product()
    {
    }

    function onAction()
    {
        global $application;
        $request = &$application->getInstance('Request');
        $product_id = $request->getValueByKey('product_id');
        $file_descr = $request->getValueByKey('new_file_descr');
        if($file_descr == null)
            $file_descr = '';

        $file_path = $request->getValueByKey('new_file_name_hidden');

        if(preg_match("/^__shell_upload_\d+$/",basename(dirname($file_path))))
        {
            $result = modApiFunc('Product_Files','moveFileToPFDir',$product_id,$file_path);
            $is_uploaded = true;
            modApiFunc('Shell','removeDirectory',dirname($file_path));
        }
        else
        {
            $result = array(
                'error' => UPLOAD_ERR_OK
               ,'full_path' => $file_path
               ,'base_name' => basename($file_path)
               ,'file_size' => filesize($file_path)
            );
            $is_uploaded = false;
        }

        if($result['error'] == UPLOAD_ERR_OK)
        {
            modApiFunc('Product_Files','addFileToProduct',$product_id,$result['base_name'],$result['full_path'],$file_descr,$is_uploaded);
            modApiFunc('Session','set','ResultMessage','MSG_FILE_ADDED');
        }
        else
        {
            modApiFunc('Session','set','Errors',array($result['error']));
        }

        $r = new Request();
        $r->setView('PF_FilesList');
        $r->setKey('product_id',$product_id);
        $application->redirect($r);
    }
};

?>