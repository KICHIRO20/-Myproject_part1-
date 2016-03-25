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
 * @package ProductImage
 * @author Egor V. Derevyankin
 *
 */

class upload_image_for_preview extends AjaxAction
{
    function upload_image_for_preview()
    {
    }

    function onAction()
    {
        global $application;
        $request = &$application->getInstance('Request');

        $res = modApiFunc('Product_Images','moveUploadedFileToImagesDir',$request->getValueByKey('product_id'),'new_product_image','tmp.');

        if($res['error'] != UPLOAD_ERR_OK)
            $res['error_msg'] = modApiFunc('Shell','getMsgByErrorCode',$res['error']);

        $ss = getimagesize($res['full_path']);
        $res['preview_sizes'] = modApiFunc('Product_Images','convertSizes',$ss[0],$ss[1],200);

        global $_RESULT;
        $_RESULT = $res;
    }
};

?>