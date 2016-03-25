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

class shell_upload_file extends AjaxAction
{
    function shell_upload_file()
    {
    }

    function onAction()
    {
        global $application;
        $request = &$application->getInstance('Request');
        $MR = &$application->getInstance('MessageResources','shell-messages','AdminZone');
        global $_RESULT;

        $src_file = $_FILES['src_file'];

        if($src_file['error'] != UPLOAD_ERR_OK)
        {
            $_RESULT['error'] = $MR->getMessage('E_FILE_UPLOADING').' ';
            switch($src_file['error'])
            {
                case UPLOAD_ERR_INI_SIZE:
                    $_RESULT['error'].=$MR->getMessage('E_UPLOAD_ERR_INI_SIZE');
                    break;
                case UPLOAD_ERR_FORM_SIZE:
                    $_RESULT['error'].=$MR->getMessage('E_UPLOAD_ERR_FORM_SIZE');
                    break;
                case UPLOAD_ERR_PARTIAL:
                    $_RESULT['error'].=$MR->getMessage('E_UPLOAD_ERR_PARTIAL');
                    break;
                case UPLOAD_ERR_NO_FILE:
                    $_RESULT['error'].=$MR->getMessage('E_UPLOAD_ERR_NO_FILE');
                    break;
                case UPLOAD_ERR_NO_TMP_DIR:
                    $_RESULT['error'].=$MR->getMessage('E_UPLOAD_ERR_NO_TMP_DIR');
                    break;
                case UPLOAD_ERR_CANT_WRITE:
                    $_RESULT['error'].=$MR->getMessage('E_UPLOAD_ERR_CANT_WRITE');
                    break;
                case UPLOAD_ERR_EXTENSION:
                    $_RESULT['error'].=$MR->getMessage('E_UPLOAD_ERR_EXTENSION');
                    break;
            };
            return;
        };

        $_RESULT['error'] = '';

        if(!is_uploaded_file($src_file['tmp_name']))
        {
            $_RESULT['error'] = $MR->getMessage('E_FILE_UPLOADING').' '.$MR->getMessage('E_UPLOAD_POSIBLE_ATTACK');
            return;
        };

        $cache_dir = str_replace("\\","/",realpath($application->getAppIni('PATH_CACHE_DIR'))).'/__shell_upload_'.time().'/';
        mkdir($cache_dir);
        if(!move_uploaded_file($src_file['tmp_name'],$cache_dir.$src_file['name']))
        {
            $_RESULT['error'] = $MR->getMessage('E_FILE_UPLOADING').' '.$MR->getMessage('E_UPLOAD_CANT_MOVE_FILE');
            return;
        };

        $_RESULT['full_path'] = $cache_dir.$src_file['name'];
        $_RESULT['base_name'] = $src_file['name'];
    }
};

?>