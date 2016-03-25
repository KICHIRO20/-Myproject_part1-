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
 * @package ProductImages
 * @author Egor V. Derevyankin
 *
 */

class add_image_to_product extends AjaxAction
{
    function add_image_to_product()
    {
    }

    function onAction()
    {
        global $application;
        $request = &$application->getInstance('Request');
        $product_id = $request->getValueByKey('product_id');
        $uploaded_image = $request->getValueByKey('uploaded_image');
        $alt_text = preparehtmldisplay($request->getValueByKey('new_image_alt_text'));

        $res = array(
            'error' => UPLOAD_ERR_OK
        );

        if($uploaded_image != '')
        {
            $new_path = $uploaded_image; //modApiFunc('Product_Images', 'moveImageToImagesDir', $product_id, $uploaded_image);
            if($new_path == null)
            {
                $res['error'] = UPLOAD_ERR_CANT_MOVE_FILE;
                $res['error_msg'] = modApiFunc('Shell', 'getMsgByErrorCode', $res['error']);
            }
        }
        else
        {
            $res = modApiFunc('Product_Images', 'moveUploadedFileToImagesDir', $product_id, 'new_product_image');
            if($res['error'] != UPLOAD_ERR_OK)
                $res['error_msg'] = modApiFunc('Shell', 'getMsgByErrorCode', $res['error']);
            else
                $new_path = $res['full_path'];
        };

        if($res['error'] == UPLOAD_ERR_OK)
        {
            $thumb_path = modApiFunc('Product_Images', 'genThumbnail', $product_id, $new_path);
            modApiFunc('Product_Images', 'addImageToProduct', $product_id, $new_path, $alt_text, $thumb_path);
        }

        global $_RESULT;
        $_RESULT = $res;
    }
};

?>