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
 * @package Core
 */

/**
 * Checks the possibility to save and to create files in this folder.
 *
 * @param string $dir_fs_name The name of checking folder.
 */
 function is_dir_writable($dir_fs_name)
 {
    if(@file_exists($dir_fs_name) == FALSE)
        return FALSE;
	$file_name = md5(uniqid(rand(), true)).'test.txt';
	$result = false;
	CProfiler::ioStart($dir_fs_name . $file_name, 'test');
    $fp = @fopen($dir_fs_name . $file_name ,'w');
    if($fp)
    {
        fclose($fp);
        $result = @unlink($dir_fs_name . $file_name);
    }
    CProfiler::ioStop();
    return $result;
 }

?>