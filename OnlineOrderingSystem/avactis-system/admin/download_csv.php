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
include('../admin.php');

function readfile_chunked ($filename, $start)
{
    $filesize = filesize($filename);
    $chunksize = 1*(1024); // how many bytes per chunk
    $buffer = '';
    $handle = fopen($filename, 'rb');
    fseek($handle,$start);
    if ($handle === false)
    {
        return false;
    }
    while (!feof($handle))
    {
        $buffer = fread($handle, $chunksize);
        print $buffer;
    }
    return fclose($handle);
}


    global $application;

    // required for IE, otherwise Content-disposition is ignored
    if(ini_get('zlib.output_compression'))  ini_set('zlib.output_compression', 'Off');


    if (!isset($_GET['sid'])) die();

    $sid = $_GET['sid'];
    $set = modApiFunc("TaxRateByZip", "getSet", $sid);

    $file = $set[0]["filename"];
    $full_filename = $fullpath = $application->getAppIni("PRODUCT_FILES_DIR") . "TaxRateByZip_CSVs/" . $set[0]["filename"];
    $local_filename = $set[0]["filecaption"];

    //                         ,                            PATH_ASC_ROOT
    if(!modApiFunc("Shell", "isFileFromDirectoryOrSubdirectories", $application->getAppINI('PATH_ASC_ROOT'), $full_filename)
       || basename($file) == 'config.php')
    {
        exit(0);
    }

    $start = 0;
    $filesize = filesize($full_filename);
    if ( isset($_SERVER['HTTP_RANGE']) )
    {
        // Support for partial transfers enabled and browser requested a partial transfer
        header("HTTP/1.1 206 Partial content\n");
        $start = preg_replace(array("/(\040*|)bytes(\040*|)=(\040*|)/","/(\040*|)\-.*$/"),array("",""),$_SERVER['HTTP_RANGE']);
        if ( $filesize < $start)
        {
                header("HTTP/1.1 411 Length Required\n");
                echo "Trying to download past the end of the file. You have probably requested the wrong file. Please try again.";
        }
        $transfer_size = $filesize - $start;
        header("Accept-Ranges: bytes");
        header("Content-Range: bytes ".$transfer_size."-".($filesize-1)."/".$filesize);
        header("Content-Length:".$transfer_size."\n");
    }
    else
    {
        header("HTTP/1.1 200 OK\n");
        header ("Content-Length: ".filesize($full_filename));
    }


    header ("Pragma: public");
    header ("Expires: 0");
    header ("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header ("Cache-Control: private",false);
    header ("Content-Type: application/zip");
    header ("Content-Disposition: attachment; filename=\"".$local_filename."\"");
    header ("Content-Transfer-Encoding: binary");


    readfile_chunked($full_filename, $start);

?>