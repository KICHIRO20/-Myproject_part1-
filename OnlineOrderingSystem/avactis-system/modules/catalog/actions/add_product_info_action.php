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
 * Catalog->AddProductInfoAction.
 * Adds a new product to the catalog.
 *
 * @package Catalog
 * @access  public
 * @author Alexey Kolesnikov
 */
class AddProductInfoAction extends AjaxAction
{
    function uploadImages(&$SessionPost)
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

                    if ($ext != false)
                    {
                        // upload a unique file name.
                    	$uploadfile = $application->getUploadImageName($_FILES[$image]['name']);
                        // move the uploaded file to the general catalog.
                        if (move_uploaded_file($_FILES[$image]['tmp_name'], $uploadfile))
                        {
                            @chmod($uploadfile, 0644);
                            // save the file to view it to the user.
    //                                $images_dir = $application->getAppIni('RELATIVE_PATH_IMAGES_DIR_ADMIN');
                            $SessionPost["ViewState"][$image] = basename($uploadfile);
                            if ($image == 'LargeImage')
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
                        }
                        else
                        {
                            $SessionPost["ViewState"]["ErrorsArray"][$image] = new ActionMessage(array("PRDADD_006", $image));
                        }
                    }
                    else
                    {
                        $SessionPost["ViewState"]["ErrorsArray"][$image] = new ActionMessage(array("PRDADD_005", $image));
                    }
                }
                else
                {
                    $SessionPost["ViewState"]["ErrorsArray"][$image] = new ActionMessage(array("PRDADD_005", $image));
                }
            }
            else
            {
                if(array_key_exists($image,$_FILES) && $_FILES[$image]['size'] > 0)
                {
                    $SessionPost["ViewState"]["ErrorsArray"][$image] = new ActionMessage(array("PRDADD_011", $image));
                }
            };
        };

        // generate small image from the large image
        if(@$_FILES['SmallImage']['error'] == UPLOAD_ERR_NO_FILE
            && isset($SessionPost["ViewState"]['LargeImage'])
            && !isset($SessionPost["ViewState"]["ErrorsArray"]['LargeImage'])
            && $large_image_path != null)
        {
            $pi_settings = modApiFunc('Product_Images','getSettings');
            if($pi_settings['AUTO_GEN_MAIN_SMALL_IMAGE'] == 'Y' && function_exists('gd_info'))
            {
                $thumb_path = modApiFunc('Product_Images','genThumbnail',0,$large_image_path,$pi_settings['MAIN_IMAGE_SIDE']);
                $thumb_path_info = pathinfo($thumb_path);

                $large_image_path_parts = pathinfo($SessionPost["ViewState"]['LargeImage']);
                $large_image_basename = $large_image_path_parts['basename'];
                $large_image_ext = $large_image_path_parts['extension'];
                $large_image_basename_without_ext = _ml_substr($large_image_basename, 0, -1 * (_ml_strlen("." . $large_image_ext)));

                $thumb_path_parts = pathinfo($thumb_path);
                $thumb_path_ext = $large_image_path_parts['extension'];

                $thumb_short_name = "thumb_" . $large_image_basename_without_ext . "." . $thumb_path_ext;
                $thumb_path_2 = $application->_img_path($thumb_short_name);

                if(@rename($thumb_path, $thumb_path_2))
                {
                    $SessionPost["ViewState"]['SmallImage'] = basename($thumb_path_2);
                };
            };
        };
    }

    /**
     * Validates user input. It checks "New Product Name".
     */
    function isValidProdName($data)//, &$error_message_text)
    {
        $retval = (is_string($data) &&
                _ml_strlen(trim($data)) > 2 &&
                _ml_strlen(trim($data)) < 257);
        return $retval;
    }

    function parseInput(&$request, &$product_type_id, &$SessionPost, /*out*/ &$product_info)
    {
        global $application;

        $membership_vis = "-1"; // -1 means "product is visible for all customers"
        if(isset($SessionPost['MembershipVisibility']))
        {
            $membership_vis = $SessionPost['MembershipVisibility'];
        }

        // save the product name.
        $product_info['Name'] = $request->getValueByKey('Name');
        if (!$this->isValidProdName($product_info['Name']))
        {
            // an error message comes out if the product name is not displayed
            $SessionPost["ViewState"]["ErrorsArray"]['Name'] = new ActionMessage("PRDADD_004");
        }

        $product_type = modApiFunc('Catalog', 'getProductType', $product_type_id);

    // carry out actions for each attribute in this product type.
        foreach ($product_type['attr'] as $view_tag => $attr)
        {
        // skip invisible attributes and an id, as it will be generated later.
            if (!$attr['visible'] || $view_tag == 'ID' || $view_tag == 'MembershipVisibility')
            {
            	continue;
            }
        // define the attribute value from the user input.
            $product_info[$view_tag] = $request->getValueByKey($view_tag);
        // check if the input is required
            if (isEmptyKey($view_tag, $product_info) && $attr['required'])
            {
                $SessionPost["ViewState"]["ErrorsArray"][$view_tag] = new ActionMessage(array("error.required", $attr['name']));
            }
        // validity check by the template.
            if (!isEmptyKey($view_tag, $product_info) && ($attr['patt']))
            {
                 $product_info_view_tag_with_new_lines_stripped = str_replace("\n", "", $product_info[$view_tag]);
                 if(!preg_match($attr['patt'], $product_info_view_tag_with_new_lines_stripped))
                 {
                     switch($attr['patt_type'])
                     {
                         case 'string128' :
                            $SessionPost["ViewState"]["ErrorsArray"][$view_tag] = new ActionMessage(array("error.wrongPatternString128", $attr['name'], $attr['patt_type'])); break;
                         case 'string256' :
                            $SessionPost["ViewState"]["ErrorsArray"][$view_tag] = new ActionMessage(array("error.wrongPatternString256", $attr['name'], $attr['patt_type'])); break;
                         case 'string512' :
                            $SessionPost["ViewState"]["ErrorsArray"][$view_tag] = new ActionMessage(array("error.wrongPatternString512", $attr['name'], $attr['patt_type'])); break;
                         case 'string1024' :
                            $SessionPost["ViewState"]["ErrorsArray"][$view_tag] = new ActionMessage(array("error.wrongPatternString1024", $attr['name'], $attr['patt_type'])); break;
                         default           :
                            $SessionPost["ViewState"]["ErrorsArray"][$view_tag] = new ActionMessage(array("error.wrongPattern", $attr['name'], $attr['patt_type'])); break;
                     }

                 }
            }
            $product_info[$view_tag] = modApiFunc("Localization", "FormatStrToFloat", $product_info[$view_tag], $attr['patt_type']);
            $SessionPost[$view_tag] = modApiFunc("Localization", "FormatStrToFloat", $product_info[$view_tag], $attr['patt_type']);
        }
        $product_info['MembershipVisibility'] = $membership_vis;
    }

    /**
     * The main function is action.
     */
    function onAction()
    {
        global $application;
        $request = $application->getInstance('Request');

        $SessionPost = array();
        if(modApiFunc('Session', 'is_Set', 'SessionPost'))
        {
            _fatal(array( "CODE" => "CORE_050"), __CLASS__, __FUNCTION__);
        }

        // copy all the inputted data to the array $SessionPost
        $SessionPost = $_POST;
        // create an empty array of errors.
        $SessionPost["ViewState"]["ErrorsArray"] = array();

        // define user actions
        switch ($request->getValueByKey('FormSubmitValue'))
        {
            // upload images.
            case 'UploadImages':
            {
                $this->uploadImages($SessionPost);
            }
            break;

            // delete a large image.
            case 'DeleteImage':
            {
                $image = $SessionPost['ViewState']['LargeImage'];
                // if it was a temporary file, delete it from the general catalog.
                if (_ml_strpos(basename($image), 'tmp') === 0)
                {
                    $images_dir = $application->getAppIni('PATH_IMAGES_DIR');
                    @unlink($images_dir . basename($image));
                }
                //
                // delete it from the user interface
                $SessionPost['ViewState']['LargeImage'] = '';
            }
            break;

            case 'SetTypeID':
            {
                $SessionPost['ViewState']['TypeID'] = $request->getValueByKey('SelectTypeID');
            }
            break;

            case 'SetTypeIDKeepingEnteredInfo':
            {
                $SessionPost['ViewState']['TypeID'] = $request->getValueByKey('SelectTypeID');
                $product_type_id = $SessionPost['ViewState']['TypeID'];
                $product_info = array();
                $this->parseInput($request, $product_type_id, $SessionPost, $product_info);
                // clear the error array. It is such a style.
                $SessionPost["ViewState"]["ErrorsArray"] = array();
            }
            break;

            // delete a design
            case 'DeleteSmallImage':
            {
                $image = $SessionPost['ViewState']['SmallImage'];
                // if it was a temporary file, delete it from the general catalog.
                if (_ml_strpos(basename($image), 'tmp') === 0)
                {
                    $images_dir = $application->getAppIni('PATH_IMAGES_DIR');
                    @unlink($images_dir . basename($image));
                }
                // delete it from the user interface
                $SessionPost['ViewState']['SmallImage'] = '';
            }
            break;

            // save the product and output.
            case 'UploadImagesAndSave':
            {
                $this->uploadImages($SessionPost);

                //$nErrors = 0;
                // define a product type and an attribute list.
                $product_type_id = $SessionPost['ViewState']['TypeID'];//$request->getValueByKey('TypeID');
                if($product_type_id == "")
                {
                    $SessionPost["ViewState"]["ErrorsArray"]["Product Type"] = new ActionMessage(array("error.required", "Product Type"));
                    $nErrors++;
                }
                else
                {
                    $product_info = array();
                    $this->parseInput($request, $product_type_id, $SessionPost, $product_info);


                // write to the DB if no errors occur.
                    if (empty($SessionPost["ViewState"]["ErrorsArray"]))
                    {
                        // save images info
                        $product_info['LargeImage'] = $SessionPost['ViewState']['LargeImage'];
                        $product_info['SmallImage'] = $SessionPost['ViewState']['SmallImage'];
                        $category_id = modApiFunc('CProductListFilter','getCurrentCategoryId');
                        $new_product_id = modApiFunc('Catalog', 'addProductInfo', $product_type_id, $category_id, $product_info);

                        $category_info = new CCategoryInfo($category_id);
                        modApiFunc("Paginator", "setPaginatorPageToLast", "Catalog_ProdsList_".$category_id, $category_info->getCategoryTagValue('productsnumber') );

                        // close an user window.
                        if ($SessionPost["addAnother"]=="false")
                        {
                            $SessionPost["ViewState"]["hasCloseScript"] = "true";
                            $SessionPost["ViewState"]["new_product_id"] = $new_product_id;
                            modApiFunc('Session','set','ResultMessage','MSG_PRODUCT_ADDED');
                        }
                        //modApiFunc('Session','set','new_product_id',$new_product_id);
                        //modApiFunc('Session','set','ptype_id_of_new_product',$product_type_id);
                        // save the id of the new product and the id of its type in the session to use it then in hooks
                        //modApiFunc('Session','set','new_product_id',$new_product_id);
                        //modApiFunc('Session','set','ptype_id_of_new_product',$product_type_id);
                        $this->new_product_id=$new_product_id;
                        $this->ptype_id_of_new_product=$product_type_id;
                    }
                }
            }
            break;
        }
        // save customer info in the session to use it in view
        modApiFunc('Session', 'set', 'SessionPost', $SessionPost);
    }

    var $new_product_id = null;
    var $ptype_id_of_new_product = null;
}
?>