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
 * Catalog module.
 * This action is responsible for adding new category.
 *
 * @package Catalog
 * @access  public
 * @author  Vadim Lyalikov
 */
class AddCategoryInfo extends AjaxAction
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * Action constructor.
     */
    function AddCategoryInfo()
    {
    }

    /**
     * Gets Action name.
     *
     * @return string Action name
     */
    function ACT_NM()
    {
        return 'AddCategoryInfo';
    }

    /**
     * Validates the user input. It checks "New Category Name".
     */
    function isValidCatName($data)//, &$error_message_text)
    {
        $retval = (is_string($data) &&
                _ml_strlen(trim($data)) > 0 &&
                _ml_strlen(trim($data)) < 256);
        return $retval;
    }

    function isValidCatStatus($data)
    {
        $retval = ($data == CATEGORY_STATUS_ONLINE || $data == CATEGORY_STATUS_OFFLINE);
        return $retval;
    }

    function isValidCatShowProductsRecursivelyStatus($data)
    {
        $retval = ($data == CATEGORY_SHOW_PRODUCTS_RECURSIVELY || $data == CATEGORY_DONOTSHOW_PRODUCTS_RECURSIVELY);
        return $retval;
    }

    /**
     * Validates the user input. It checks "Image Description".
     */
    function isValidImageDescription($data)//, &$error_message_text)
    {
        $retval = (is_string($data) &&
                _ml_strlen(trim($data)) < 257);
        return $retval;
    }

    /**
     * Validates the user input. It checks "Page Title".
     */
    function isValidPageTitle($data)//, &$error_message_text)
    {
        $retval = (is_string($data) &&
                _ml_strlen(trim($data)) < 257);
        return $retval;
    }

    /**
     * Validates the user input. It check "Meta Keywords" and "Meta Description".
     */
    function isValidMetaField($data)
    {
        $retval = (
                _ml_strlen(trim($data)) <= 1024
                  );
        return $retval;
    }

    function initData($data)
    {
        $this->parent_id = $data["ParentCategoryID"];
        $this->new_name = $data["Subcategory"];
    }

    function saveDataToDB($data)
    {
    	$this->new_id = modApiFunc("Catalog", "addCategory",
                   $data["ParentCategoryID"],
                   $data["Subcategory"],
                   $data["CategoryStatus"],
                   $data["CategoryDescription"],
                   $data["ViewState"]["LargeImage"],
                   $data["ViewState"]["SmallImage"],
                   $data["ImageDescription"],
                   $data["PageTitle"],
                   $data["MetaKeywords"],
                   $data["MetaDescription"],
                   $data["CategoryShowProductsRecursivelyStatus"],
                   $data["SEO_URL_prefix"]
                  );
    }

    function uploadImages(&$SessionPost)
    {
        global $application;

        // not to duplicate the code.
        $images = array('SmallImage', 'LargeImage');
        foreach ($images as $image)
        {
            // if the file really exists.
            if (array_key_exists($image,$_FILES) && $_FILES[$image]['size'] > 0 && getimagesize($_FILES[$image]['tmp_name']) != FALSE)
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
    //                                $images_dir = $application->getAppIni('RELATIVE_PATH_IMAGES_DIR_ADMIN');
                            $SessionPost["ViewState"][$image] = basename($uploadfile);
                            if ($image == 'LargeImage') {
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
                    $SessionPost["ViewState"]["ErrorsArray"][$image] = new ActionMessage(array("PRDADD_005", $image));
                }
            }
        }

        // generate small image from the large image
        if(@$_FILES['SmallImage']['error'] == UPLOAD_ERR_NO_FILE
            && isset($SessionPost["ViewState"]['LargeImage'])
            && !isset($SessionPost["ViewState"]["ErrorsArray"]['LargeImage'])
            && isset($large_image_path))
        {
            $pi_settings = modApiFunc('Product_Images','getSettings');
            if($pi_settings['AUTO_GEN_CAT_SMALL_IMAGE'] == 'Y' && function_exists('gd_info'))
            {
                $thumb_path = modApiFunc('Product_Images', 'genThumbnail', 0, $large_image_path, $pi_settings['CAT_IMAGE_SIDE']);
                $thumb_path_info = pathinfo($thumb_path);

                $large_image_path_parts = pathinfo($SessionPost["ViewState"]['LargeImage']);
                $large_image_basename = $large_image_path_parts['basename'];
                $large_image_ext = $large_image_path_parts['extension'];
                $large_image_basename_without_ext = _byte_substr($large_image_basename, 0, -1 * (_byte_strlen("." . $large_image_ext)));

                $thumb_path_parts = pathinfo($thumb_path);
                $thumb_path_ext = $large_image_path_parts['extension'];

                $thumb_short_name = "thumb_" . $large_image_basename_without_ext . "." . $thumb_path_ext;
                $thumb_path_2 = $application->_img_path($thumb_short_name);

                if(@rename($thumb_path, $thumb_path_2))
                {
                    $SessionPost["ViewState"]['SmallImage'] = basename($thumb_path_2);
                }
            }
        }

    }

    /**
     * Action: AddCat.
     *
     * Adds a new category record to the DB, or saves current form state
     * parameters, like uploaded image names etc.
     * <p> Subactions are
     * <ul>
     *     <li>"UploadImages"</li>
     *     <li>"DeleteSmallImage"</li>
     *     <li>"DeleteLargeImage"</li>
     *     <li>"Save"</li>
     * </ul>
     * The main action is "Save". Any other subaction may occur 0 or any number
     * of times. Subactions change the "View State".
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

        $SessionPost = $_POST;
        $this->initData($SessionPost);

        switch($request->getValueByKey('FormSubmitValue'))
        {
            case "UploadImages" :
            {
                $this->uploadImages($SessionPost);
                break;
            }

            case "DeleteSmallImage" :
            {
                $image = $SessionPost['ViewState']['SmallImage'];
                // if it was a temporary file, delete it from the general catalog.
                if (_ml_strpos(basename($image), 'tmp') === 0)
                {
                    $images_dir = $application->getAppIni('PATH_IMAGES_DIR');
                    @unlink($images_dir . basename($image));
                }
                // delete it from the user interface
                $SessionPost["ViewState"]["SmallImage"]     = "";
                break;
            }

            case "DeleteLargeImage" :
            {
                $image = $SessionPost['ViewState']['LargeImage'];
                // if it was a temporary file, delete it from the general catalog.
                if (_ml_strpos(basename($image), 'tmp') === 0)
                {
                    $images_dir = $application->getAppIni('PATH_IMAGES_DIR');
                    @unlink($images_dir . basename($image));
                }
                // delete it from the user interface
                $SessionPost["ViewState"]["LargeImage"]     = "";
                break;
            }

            case "UploadImagesAndSave" :
            {
                $nErrors = 0;
                $SessionPost["ViewState"]["ErrorsArray"] = array();
                $this->uploadImages($SessionPost);

                loadCoreFile('html_form.php');
                $HtmlForm1 = new HtmlForm();

                $error_message_text = "";

                if(!$this->isValidCatName($SessionPost["Subcategory"], $error_message_text))
                {
                    $nErrors++;
                    $SessionPost["ViewState"]["ErrorsArray"]["Subcategory"] = new ActionMessage(array("ERR_AZ_CAT_ADDCAT_005"));
                    //"ERR_AZ_CAT_ADDCAT_001";
//                    $SessionPost["Subcategory"] .= "<ERROR: Invalid input. See error list.>";
                }

                if(!$this->isValidCatStatus($SessionPost["CategoryStatus"], $error_message_text))
                {
                    $nErrors++;
                    $SessionPost["ViewState"]["ErrorsArray"]["CategoryStatus"] = new ActionMessage(array("ERR_AZ_CAT_ADDCAT_008"));
                    $SessionPost["CategoryStatus"] = CATEGORY_STATUS_ONLINE;
                }

                if(!$this->isValidCatShowProductsRecursivelyStatus($SessionPost["CategoryShowProductsRecursivelyStatus"], $error_message_text))
                {
                    $nErrors++;
                    $SessionPost["ViewState"]["ErrorsArray"]["CategoryShowProductsRecursivelyStatus"] = new ActionMessage(array("ERR_AZ_CAT_ADDCAT_009"));
                    $SessionPost["CategoryShowProductsRecursivelyStatus"] = CATEGORY_DONOTSHOW_PRODUCTS_RECURSIVELY;
                }

                if(!$this->isValidImageDescription($SessionPost["ImageDescription"], $error_message_text))
                {
                    $nErrors++;
                    $SessionPost["ViewState"]["ErrorsArray"]["ImageDescription"] = new ActionMessage(array("ERR_AZ_CAT_ADDCAT_006"));
                    //"ERR_AZ_CAT_ADDCAT_001";
//                    $SessionPost["Subcategory"] .= "<ERROR: Invalid input. See error list.>";
                }

                if(!$this->isValidPageTitle($SessionPost["PageTitle"], $error_message_text))
                {
                    $nErrors++;
                    $SessionPost["ViewState"]["ErrorsArray"][] =
                    $SessionPost["ViewState"]["ErrorsArray"]["PageTitle"] = new ActionMessage(array("ERR_AZ_CAT_ADDCAT_007"));
//                    $SessionPost["ViewState"]["ErrorsArray"][] = "ERR_AZ_CAT_ADDCAT_002";
//                    $SessionPost["PageTitle"] .= "<ERROR: Invalid input. See error list.>";
                }

                if(!$this->isValidMetaField($SessionPost["MetaKeywords"], $error_message_text))
                {
                    $nErrors++;
                    $SessionPost["ViewState"]["ErrorsArray"]["MetaKeywords"] = new ActionMessage(array("ERR_AZ_CAT_ADDCAT_003"));
                    //"ERR_AZ_CAT_ADDCAT_003";
                }

                if(!$this->isValidMetaField($SessionPost["MetaDescription"], $error_message_text))
                {
                    $nErrors++;
                    $SessionPost["ViewState"]["ErrorsArray"][] =
                    $SessionPost["ViewState"]["ErrorsArray"]["MetaDescription"] = new ActionMessage(array("ERR_AZ_CAT_ADDCAT_004"));
                    //"ERR_AZ_CAT_ADDCAT_004";
                }

                $nErrors = sizeof($SessionPost["ViewState"]["ErrorsArray"]);

                if($nErrors == 0)
                {
                    unset($SessionPost["ViewState"]["ErrorsArray"]);
                    $this->saveDataToDB($SessionPost);
                    $SessionPost["ViewState"]["hasCloseScript"] = "true";
                }
                break;
            }
            default : _fatal(array( "CODE" => "CORE_051"), __CLASS__, __FUNCTION__, $request->getValueByKey('FormSubmitValue'));
        }

        modApiFunc('Session', 'set', 'SessionPost', $SessionPost);
        // get view name by action name.
        $this->redirect();
    }

    /**
     * Redirects after action.
     */
    function redirect()
    {
        global $application;

        $request = new Request();
        $request->setView('AddCategory');
        $request->setKey('tree_id', modApiFunc('Request', 'getValueByKey', 'tree_id'));
        $request->setKey('parent_id', $this->parent_id);
        $request->setKey('new_id', $this->new_id);
        $request->setKey('new_name', urlencode($this->new_name));
        $application->redirect($request);
    }

    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */

    /**
     * Corresponding View file.
     */
    var $ViewFilename;

    /**#@-*/
}
?>