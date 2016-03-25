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
 * @package Images
 * @author Vadim Lyalikov
 *
 */
class images_upload_url extends AjaxAction
{
    function images_upload_url()
    {
    }

    //From ajax
    function onAction()
    {
        $image_obj = modApiFunc("Images", "process_images_upload_url" , "alt_text", "input_text", "image_id");
        $res = modApiFunc("Images", "getImageData", $image_obj->get_id());
        $res['image_obj'] = $image_obj;
        global $_RESULT;
        $_RESULT = $res;
    }
};

?>