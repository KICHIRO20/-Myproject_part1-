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
 * Catalog->UpdateProductInfo Action.
 * Saves product info after editing.
 * It checks the input data.
 *
 * @package Catalog
 * @access  public
 * @author Alexey Kolesnikov
 */
class UpdateProductInfo extends AjaxAction
{

    function uploadImages(&$EditProductInfoForm)
    {
        global $application;

        // not to duplicate the code.
        $large_image_path = null;
        $images = array('SmallImage', 'LargeImage');
        foreach ($images as $image)
        {
            // if the file really exists.
            if (array_key_exists($image,$_FILES) && $_FILES[$image]['size'] > 0/* && getimagesize($_FILES[$image]['tmp_name']) != FALSE*/)
            {
                // define an image type. It should be one of the allowed types.
                if ($application->isAllowedImageType($_FILES[$image]))
                {
                    // define the image file extension
                    $ext = $application->getImageTypeExtension($_FILES[$image]);

                    if($ext != false)
                    {
                        // upload a unique file name.
                        $uploadfile = $application->getUploadImageName($_FILES[$image]['name']);
                        // move the uploaded file to the general catalog.
                        if (move_uploaded_file($_FILES[$image]['tmp_name'], $uploadfile)) {
                            @chmod($uploadfile, 0644);
                            // save the file to view it to the user.
                            if($image == 'LargeImage')
                            {
                                // check if we have to resize uploaded image
					            $piSettings = modApiFunc('Product_Images','getSettings');
					            if($piSettings['RESIZE_LARGE_IMAGE'] == 'Y'
					                && function_exists('gd_info'))
					            {
					                $uploadfile = modApiFunc('Product_Images', 'resizeImage', $uploadfile, $piSettings['LARGE_IMAGE_SIZE']);
					            }
                                $large_image_path = $uploadfile;
                            }
                            $EditProductInfoForm["ViewState"][$image] = basename($uploadfile);
                        }
                        else
                        {
                            $EditProductInfoForm["ViewState"]["ErrorsArray"][$image] = new ActionMessage(array("PRDADD_006", $image));
                        }
                    }
                    else // wrong image file format
                    {
                        $EditProductInfoForm["ViewState"]["ErrorsArray"][$image] = new ActionMessage(array("PRDADD_005", $image));
                    }
                }
                else // never happens ($application->isAllowedImageType() always returns 'true')
                {
                    $EditProductInfoForm["ViewState"]["ErrorsArray"][$image] = new ActionMessage(array("PRDADD_005", $image));
                }
            } // if file doesn't really exist
            else
            {
                if(array_key_exists($image,$_FILES) && $_FILES[$image]['size'] > 0)
                {
                    $EditProductInfoForm["ViewState"]["ErrorsArray"][$image] = new ActionMessage(array("PRDADD_011", $image));
                }
            };
        };

        // generate small image from the large image
        if(@$_FILES['SmallImage']['error'] == UPLOAD_ERR_NO_FILE
            and isset($EditProductInfoForm["ViewState"]['LargeImage'])
            and !isset($EditProductInfoForm["ViewState"]["ErrorsArray"]['LargeImage'])
            and $large_image_path != null)
        {
            $pi_settings = modApiFunc('Product_Images','getSettings');
            if($pi_settings['AUTO_GEN_MAIN_SMALL_IMAGE'] == 'Y' and function_exists('gd_info'))
            {
                $thumb_path = modApiFunc('Product_Images','genThumbnail',0,$large_image_path,$pi_settings['MAIN_IMAGE_SIDE']);
                $thumb_path_info = pathinfo($thumb_path);

                $large_image_path_parts = pathinfo($EditProductInfoForm["ViewState"]['LargeImage']);
                $large_image_basename = $large_image_path_parts['basename'];
                $large_image_ext = $large_image_path_parts['extension'];
                $large_image_basename_without_ext = _ml_substr($large_image_basename, 0, -1 * (_ml_strlen("." . $large_image_ext)));

                $thumb_path_parts = pathinfo($thumb_path);
                $thumb_path_ext = $large_image_path_parts['extension'];

                $thumb_short_name = "thumb_" . $large_image_basename_without_ext . "." . $thumb_path_ext;
                $thumb_path_2 = $application->_img_path($thumb_short_name);

                if(@rename($thumb_path, $thumb_path_2))
                {
                    $EditProductInfoForm["ViewState"]['SmallImage'] = basename($thumb_path_2);
                };
            };
        };
    }


    /**
     * Validates the user input. It check "New Product Name".
     */
    function isValidProdName($data)//, &$error_message_text)
    {
        $retval = (is_string($data) &&
                _ml_strlen(trim($data)) > 2 &&
                _ml_strlen(trim($data)) < 257);
        return $retval;
    }

    /**
     * @ describe the function SaveProduct->onAction.
     */
    function onAction()
    {
        global $application;
        $request = $application->getInstance('Request');

        $EditProductInfoForm = array();
        if(modApiFunc('Session', 'is_Set', 'EditProductInfoForm'))
        {
            _fatal(array( "CODE" => "CORE_050"), __CLASS__, __FUNCTION__);
        }

        $EditProductInfoForm = $_POST;

        $membership_vis = "-1"; // -1 means "product is visible for all customers"
        if(isset($EditProductInfoForm['MembershipVisibility']))
        {
            $membership_vis = $EditProductInfoForm['MembershipVisibility'];
        }
        $EditProductInfoForm['MembershipVisibility'] = $membership_vis;

        $EditProductInfoForm["ViewState"]["ErrorsArray"] = array();

        # force PerItemShippinfCost to 0.00 if FreeShipping is enabled
        if (isset($EditProductInfoForm['FreeShipping']) && $EditProductInfoForm['FreeShipping'] == "1")
        {
        	$EditProductInfoForm['PerItemShippingCost'] = "0.00";
        }

        switch ($request->getValueByKey('FormSubmitValue'))
        {
            case 'EnableEditor':
                modApiFunc('Session','set','ProductInfoWYSIWYGEditorEnabled', true);
                $this->uploadImages($EditProductInfoForm);
            break;

            case 'UploadImages':
            {
                $this->uploadImages($EditProductInfoForm);
            }
            break;

            case 'DeleteImage':
            {
                $image = $EditProductInfoForm['ViewState']['LargeImage'];
                if (_ml_strpos(basename($image), 'tmp') === 0)
                {
                    $images_dir = $application->getAppIni('PATH_IMAGES_DIR');
                    @unlink($images_dir . basename($image));
                }
                $EditProductInfoForm['ViewState']['LargeImage'] = '';
            }
            break;

            case 'DeleteSmallImage':
            {
                $image = $EditProductInfoForm['ViewState']['SmallImage'];
                if (_ml_strpos(basename($image), 'tmp') === 0)
                {
                    $images_dir = $application->getAppIni('PATH_IMAGES_DIR');
                    @unlink($images_dir . basename($image));
                }
                $EditProductInfoForm['ViewState']['SmallImage'] = '';
            }
            break;

            case 'UploadImagesAndSave':
            {
                $nErrors = 0;

                $this->uploadImages($EditProductInfoForm);
                $product_type_id = $request->getValueByKey('TypeID');
                $product_type = modApiFunc('Catalog', 'getProductType', $product_type_id);
                $product_id = $request->getValueByKey('ID');

                $product_info = array();

                foreach ($product_type['attr'] as $view_tag => $attr)
                {
                    // skip invisible attributes
                    if (!$attr['visible'])
                    {
                    	continue;
                    }
                    $product_info[$view_tag] = $request->getValueByKey($view_tag);
                    // check if it is reqiured
                    if (isEmptyKey($view_tag, $product_info) && $attr['required'])
                    {
                        $EditProductInfoForm["ViewState"]["ErrorsArray"][$view_tag] = new ActionMessage(array("error.required", $attr['name']));
                        $nErrors++;
                    }
                    // validity check
                    if (!isEmptyKey($view_tag, $product_info) && isset($attr['patt']))
                    {
                        $product_info_view_tag_with_new_lines_stripped = str_replace("\n", "", $product_info[$view_tag]);
                        if(!preg_match($attr['patt'], $product_info_view_tag_with_new_lines_stripped))
                        {
                            // @ use the correct error codes.
                            $EditProductInfoForm["ViewState"]["ErrorsArray"][$view_tag] = new ActionMessage(array('error.wrongPattern', $attr['name'], $attr['patt_type']));
                            $nErrors++;
                        }
                    }
                    $product_info[$view_tag] = modApiFunc("Localization", "FormatStrToFloat", $product_info[$view_tag], $attr['patt_type']);
                    $SessionPost[$view_tag] = modApiFunc("Localization", "FormatStrToFloat", $product_info[$view_tag], $attr['patt_type']);
                }
                $product_info['Name'] = $request->getValueByKey('Name');

                $product_info['Name'] = $request->getValueByKey('Name');
                if (!$this->isValidProdName($product_info['Name']))
                {
                    // an error message comes out if the product name is not displayed
                    $EditProductInfoForm["ViewState"]["ErrorsArray"]['Name'] = new ActionMessage("PRDADD_004");
                    $nErrors++;
                }

                $nErrors = sizeof($EditProductInfoForm["ViewState"]["ErrorsArray"]);
                if ($nErrors == 0)
                {
                    $product_info['LargeImage'] = $EditProductInfoForm['ViewState']['LargeImage'];
                    $product_info['SmallImage'] = $EditProductInfoForm['ViewState']['SmallImage'];

                    $membership_vis = "-1"; // -1 means "product is visible for all customers"
                    if(isset($product_info['MembershipVisibility']))
                    {
                        $membership_vis = $product_info['MembershipVisibility'];
                    }
                    $product_info['MembershipVisibility'] = $membership_vis;

                    modApiFunc('Catalog', 'updateProductInfo', $product_id, $product_type_id, $product_info);
                    //$EditProductInfoForm["ViewState"]["hasCloseScript"] = "true";
                    $redirect_request = new Request();
                    $redirect_request->setView('Catalog_EditProduct');
                    $redirect_request->setAction('SetCurrentProduct');
                    $redirect_request->setKey('prod_id',$product_id);
                    $redirect_request->setKey('disable_url_mod','1'); //           URLModifier,                               GET
                    $redirect_request->setKey('keep_editor_state','true');
                    modApiFunc('Session','set','SavedOk',1);
                    $application->redirect($redirect_request);
                    modApiFunc('Session','set','mustReloadParent',1);
                }
            }
            break;
        }
        if(modApiFunc('Session','is_set','mustReloadParent'))
        {
            modApiFunc('Session', 'un_set', 'EditProductInfoForm');
        }
        else
        {
            modApiFunc('Session', 'set', 'EditProductInfoForm', $EditProductInfoForm);
        }
    }
}

?>