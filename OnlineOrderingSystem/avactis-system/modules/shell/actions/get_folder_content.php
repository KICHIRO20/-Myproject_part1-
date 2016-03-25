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
 * @package Shell
 * @author Egor V. Derevyankin
 *
 */

class get_folder_content extends AjaxAction
{
    function get_folder_content()
    {
    }

    function onAction()
    {
        global $application;
        $request = &$application->getInstance('Request');
        global $_RESULT;
        if($request->getValueByKey('file_filter')!=null)
            $_RESULT['folder_content'] = modApiFunc('Shell','getFolderContentList',$request->getValueByKey('folder_path'),$request->getValueByKey('file_filter'));
        else
            $_RESULT['folder_content'] = modApiFunc('Shell','getFolderContentList',$request->getValueByKey('folder_path'));
    }
};

?>