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
 * @package ProductOptions
 * @author Egor V. Derevyankin
 *
 */

class get_uploaded_file extends AjaxAction
{
    function get_uploaded_file()
    {}

    function onAction()
    {
        if(modApiFunc('Users', 'getZone') != "AdminZone")
        {
            die();
        };

        $r = new Request();
        $file_path = base64_decode($r->getValueByKey('fp'));

        if(file_exists($file_path) and is_readable($file_path))
        {
            header ("Pragma: no-cache");
            header ("Expires: " . gmdate("D, d M Y H:i:s", time()) . " GMT");
            header ("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header ("Content-Type: application/octet-stream");
            header ("Content-Length: ".filesize($file_path));
            header ("Content-Disposition: attachment; filename=\"".basename($file_path)."\"");
            readfile($file_path);
            die();
        };
    }
};

?>