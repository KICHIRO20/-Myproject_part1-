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

class Shell
{
    function Shell()
    {
    }

    function install()
    {
    }

    function uninstall()
    {
    }

    function getFolderContentList($folder_path,$file_filter='/.*/')
    {
        $folder_content = array(
            'folders' => array()
           ,'files' => array()
           ,'folder_path' => $folder_path
        );

        if(!file_exists($folder_path) or !is_dir($folder_path))
            return $folder_content;


        $folder_path = str_replace("\\","/",realpath($folder_path));
        if(_ml_substr($folder_path,-1,1)!="/")
            $folder_path.="/";
        $folder_content['folder_path'] = $folder_path;


        $folder = dir($folder_path);
        $pseudo_folders = array();
        while(($entry = $folder->read()) !== false)
        {
            if($entry=='.' or $entry=='..')
            {
                $pseudo_folders[]=$entry;
                continue;
            };
            if(is_dir($folder_path.$entry))
                $folder_content['folders'][]=$entry;

            if(is_file($folder_path.$entry) and preg_match($file_filter,$entry))


                $folder_content['files'][]=$entry;
        };
        $folder->close();

        sort($folder_content['folders']);
        sort($folder_content['files']);

        for($i=count($pseudo_folders);$i>0;$i--)
            array_unshift($folder_content['folders'],$pseudo_folders[$i-1]);

        return $folder_content;
    }

    function clearFolderContent($folder_path)
    {
        if(!file_exists($folder_path) or !is_dir($folder_path))
            return false;
        $folder_path = str_replace("\\","/",realpath($folder_path));
        if(_ml_substr($folder_path,-1,1)!="/")
            $folder_path.="/";

        $folder = dir($folder_path);
        while(($entry = $folder->read()) !== false)
        {
            if($entry=='..' or $entry=='.')
                continue;

            if(is_dir($folder_path.$entry))
            {
                $this->clearFolderContent($folder_path.$entry);
                @rmdir($folder_path.$entry);
            };
            if(is_file($folder_path.$entry))
                @unlink($folder_path.$entry);
        };
        $folder->close();

        return true;
    }

    function removeDirectory($dir_path)
    {
        if(!file_exists($dir_path) or !is_dir($dir_path))
            return false;
        $dir_path = str_replace("\\","/",realpath($dir_path));
        if(_ml_substr($dir_path,-1,1)!="/")
            $dir_path.="/";
        $this->clearFolderContent($dir_path);
        @rmdir($dir_path);
        return true;
    }

    function isFileFromDirectoryOrSubdirectories($dir_path, $file_path)
    {
        $d = realpath($dir_path);
        $f = realpath($file_path);
        return (_ml_substr($f, 0, _ml_strlen($d)) == $d);
    }

    function getMsgByErrorCode($err_code)
    {
        global $application;
        $MR = &$application->getInstance('MessageResources','shell-messages','AdminZone');

        switch($err_code)
        {
                case UPLOAD_ERR_INI_SIZE:
                    return $MR->getMessage('E_UPLOAD_ERR_INI_SIZE');
                    break;
                case UPLOAD_ERR_FORM_SIZE:
                    return $MR->getMessage('E_UPLOAD_ERR_FORM_SIZE');
                    break;
                case UPLOAD_ERR_PARTIAL:
                    return $MR->getMessage('E_UPLOAD_ERR_PARTIAL');
                    break;
                case UPLOAD_ERR_NO_FILE:
                    return $MR->getMessage('E_UPLOAD_ERR_NO_FILE');
                    break;
                case UPLOAD_ERR_NO_TMP_DIR:
                    return $MR->getMessage('E_UPLOAD_ERR_NO_TMP_DIR');
                    break;
                case UPLOAD_ERR_CANT_WRITE:
                    return $MR->getMessage('E_UPLOAD_ERR_CANT_WRITE');
                    break;
                case UPLOAD_ERR_EXTENSION:
                    return $MR->getMessage('E_UPLOAD_ERR_EXTENSION');
                    break;
                case UPLOAD_ERR_POSIBLE_ATTACK:
                    return $MR->getMessage('E_UPLOAD_POSIBLE_ATTACK');
                    break;
                case UPLOAD_ERR_CANT_MOVE_FILE:
                    return $MR->getMessage('E_UPLOAD_CANT_MOVE_FILE');
                    break;
                case UPLOAD_ERR_CANT_CP_FILE:
                    return $MR->getMessage('E_UPLOAD_CANT_CP_FILE');
                    break;
                case UPLOAD_FILE_IS_NOT_IMAGE:
                    return $MR->getMessage('E_UPLOAD_FILE_IS_NOT_IMAGE');
                    break;
        }

        return null;
    }

    function getMaxUploadSize()
    {
        $post_max_size = $this->__return_bytes(ini_get('post_max_size'));
        $file_max_size = $this->__return_bytes(ini_get('upload_max_filesize'));
        return ($file_max_size < $post_max_size ? $file_max_size : $post_max_size);
    }

    function __return_bytes($val)
    {
        $val = trim($val);
        $last = _ml_strtolower($val{_byte_strlen($val)-1});
        switch($last)
        {
            // The 'G' modifier is available since PHP 5.1.0
            case 'g':
                $val *= 1024;
            case 'm':
                $val *= 1024;
            case 'k':
                $val *= 1024;
        }

        return $val;
    }

};

?>