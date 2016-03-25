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

class images_delete_image extends AjaxAction
{
    function images_delete_image()
    {
    }

    function onAction()
    {
        global $application;
        $request = &$application->getInstance('Request');
        $image_id = $request->getValueByKey('image_id');

        $res = modApiFunc("Images", "deleteImages", array($image_id));
        //:          ,                    $res           .
        //$image_obj->set_error("ERR_IMAGE_ID_NOT_FOUND_IN_DB", $image_id);
      	$image_obj = new image_obj();
        $res = modApiFunc("Images", "getImageData", $image_obj->get_id());
        $res['image_obj'] = $image_obj;
        global $_RESULT;
        $_RESULT = $res;
    }
};

?>