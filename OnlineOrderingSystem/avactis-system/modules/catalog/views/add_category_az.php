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
 * "Catalog -> Add Category" View.
 *
 * @package Catalog
 * @access  public
 * @author  Vadim Lyalikov
 *
 */


class AddCategory
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * The view constructor.
     * About data flow. All data is transferred.
     * <p> Action -> View :
     * <p> Through session variable @var $SessionPost (created from POST data),
     * especially it's $SessionPost["ViewState"] array, containing current View
     * state information. The state does not include such information like already
     * inputted name, description values. It includes variables, determining the
     * view structure: table or list, image or input field etc. @see @var SessionPost.
     * <p> View -> Action :
     * <p> Through POST data. All form'related session data is removed while
     * processing view output.
     */
    function AddCategory()
    {
        global $application;

        $this->MessageResources = &$application->getInstance('MessageResources');

        $this->pCatalog = &$application->getInstance('Catalog');

        $this->mTmplFiller = &$application->getInstance('TmplFiller');

        $category_id = modApiFunc('Request', 'getValueByKey', 'category_id');
        modApiFunc('CProductListFilter', 'changeCurrentCategoryId', $category_id);

        if(modApiFunc("Session", "is_Set", "SessionPost"))
        {
            $this->copyFormData();
            modApiFunc('Session', 'un_Set', 'SessionPost');
        }
        else
        {
            $this->initFormData();
        }

        $this->_bms_page_stat = 'add';
        $this->_cat_id = 0;
    }


    function copyFormData()
    {
        // eliminate copying on construction
        $SessionPost = modApiFunc("Session", "get", "SessionPost");
        $this->ViewState =
            $SessionPost["ViewState"];

        //Remove some data, that should not be resent to action, from ViewState.
        if(isset($this->ViewState["ErrorsArray"]) &&
           count($this->ViewState["ErrorsArray"]) > 0)
        {
            $this->ErrorsArray = $this->ViewState["ErrorsArray"];
            unset($this->ViewState["ErrorsArray"]);
        }

        $this->POST  =
            array(
                "ParentCategoryID"      => $SessionPost["ParentCategoryID"],
                "CategoryStatus"        => ($SessionPost["CategoryStatus"]),
                "ImageDescriptionText"  => ($SessionPost["ImageDescription"]),
                "PageTitleText"         => ($SessionPost["PageTitle"]),
                "SubcategoryText"       => ($SessionPost["Subcategory"]),
                "DescriptionText"       => ($SessionPost["CategoryDescription"]),
                "MetaKeywordsText"      => ($SessionPost["MetaKeywords"]),
                "MetaDescriptionText"   => ($SessionPost["MetaDescription"]),
                "CategoryShowProductsRecursivelyStatus" => ($SessionPost["CategoryShowProductsRecursivelyStatus"]),
                "SEO_URL_prefix"        => ($SessionPost["SEO_URL_prefix"])
            );
    }

    function initFormData()
    {
        $this->ViewState =
            array(
                "hasCloseScript" => "false",
                "SmallImage" => "",
                "LargeImage" => ""
                 );
        $this->POST  =
            array(
                "ParentCategoryID"      => modApiFunc('CProductListFilter','getCurrentCategoryId'),
                "CategoryStatus"        => CATEGORY_STATUS_ONLINE,
                "ImageDescriptionText"  => "",
                "PageTitleText"         => "",
                "SubcategoryText"       => "",
                "DescriptionText"       => "",
                "MetaKeywordsText"      => "",
                "MetaDescriptionText"   => "",
                "CategoryShowProductsRecursivelyStatus" => CATEGORY_DONOTSHOW_PRODUCTS_RECURSIVELY,
                "SEO_URL_prefix"        => ""
            );
    }

    /**
     * @return String Return a href link to "Catalog Navigator" view.
     */
    function getLinkToCatalogNavigator($cid)
    {
        $_request = new Request();
        $_request->setView  ( 'NavigationBar' );
        $_request->setAction( "SetCurrCat" );
        $_request->setKey   ( "category_id", $cid );

        return $_request->getURL();
    }

    /**
     * @return String Return html code for hidden form fields representing
     * @var $this->ViewState array.
     */
    function outputViewState()
    {
        $retval = "";
        foreach ($this->ViewState as $key => $value)
        {
            $retval .= "<input type=\"hidden\" name=\"ViewState[" .$key . "]\" value=\"" . $value . "\">";
        }
        return $retval;
    }

    function outputSubtitle()
    {
        global $application;
        loadCoreFile('html_form.php');
        $HtmlForm1 = new HtmlForm;
        $this->_Template_Contents["SubmitSaveScript"] = $HtmlForm1->genSubmitScript("AddCatForm");
        $application->registerAttributes($this->_Template_Contents);
        return $this->mTmplFiller->fill("catalog/add_cat/", "subtitle.tpl.html", array());
    }

    function outputCategoryStatus()
    {
        $value = '<select class="form-control input-xmedium input-sm input-inline" name="CategoryStatus" id="CategoryStatus">'
                     .'<option value="'.CATEGORY_STATUS_ONLINE.'" '. ($this->POST['CategoryStatus'] == CATEGORY_STATUS_ONLINE ? 'SELECTED' : '') .'>'. getMsg('SYS','CAT_STATUS_ONLINE') . '</option>'
                     .'<option value="'.CATEGORY_STATUS_OFFLINE.'" '. ($this->POST['CategoryStatus'] == CATEGORY_STATUS_OFFLINE ? 'SELECTED' : '') .'>'. getMsg('SYS','CAT_STATUS_OFFLINE') . '</option>'
                .'</select>';
        return $value;
    }

    function outputCategoryShowProductsRecursivelyStatus()
    {
        $value = '<select class="form-control input-xmedium input-sm input-inline" name="CategoryShowProductsRecursivelyStatus" id="CategoryShowProductsRecursivelyStatus">'
                     .'<option value="'.CATEGORY_DONOTSHOW_PRODUCTS_RECURSIVELY.'" '. ($this->POST['CategoryShowProductsRecursivelyStatus'] == CATEGORY_DONOTSHOW_PRODUCTS_RECURSIVELY ? 'SELECTED' : '') .'>'. getMsg('SYS','CATEGORY_DONOTSHOW_PRODUCTS_RECURSIVELY') . '</option>'
                     .'<option value="'.CATEGORY_SHOW_PRODUCTS_RECURSIVELY.'" '. ($this->POST['CategoryShowProductsRecursivelyStatus'] == CATEGORY_SHOW_PRODUCTS_RECURSIVELY ? 'SELECTED' : '') .'>'. getMsg('SYS','CATEGORY_SHOW_PRODUCTS_RECURSIVELY') . '</option>'
                .'</select>';
        return $value;
    }

    function outputCategoryId()
    {
        return "";
    }

    function outputViewStateConstants()
    {
        loadCoreFile('html_form.php');
        $HtmlForm1 = new HtmlForm();
        $retval = "<input type=\"hidden\"" . $HtmlForm1->genHiddenField("ParentCategoryID", $this->POST["ParentCategoryID"]) . ">" .
                  "<input type=\"hidden\"" . $HtmlForm1->genHiddenField("tree_id", modApiFunc('Request','getValueByKey','tree_id')) . ">" .
                  "<input type=\"hidden\"" . $HtmlForm1->genHiddenField("asc_action", "AddCategoryInfo") . ">";
        //$HtmlForm1->genHiddenField("action", "AddCat") .
        return $retval;
    }

    /**
     * @return String Return html code representing @var $this->ErrorsArray array.
     */
    function outputErrors()
    {
        global $application;
    	if (!is_array($this->ErrorsArray) || sizeof($this->ErrorsArray) == 0)
    	{
    		return;
    	}
    	$result = "";
    	$application->registerAttributes(array('ErrorIndex', 'Error'));
    	$this->_error_index = 0;
    	foreach ($this->ErrorsArray as $error)
    	{
    	    $this->_error_index++;
    		$this->_error = $this->MessageResources->getMessage($error);
    		$result .= $this->mTmplFiller->fill("catalog/add_cat/", "error.tpl.html", array());
    	}
    	return $result;

/*
        global $application;
        $this->ErrorMessages = array();
        $retval = "";

        if(isset($this->ErrorsArray) && count($this->ErrorsArray) >0)
        {
            foreach ($this->ErrorsArray as $key => $value)
            {
                $this->_Template_Contents["ErrorMessageText"] = modApiFunc("application", "getAppIni", $value);
                $application->registerAttributes($this->_Template_Contents);
                $this->ErrorMessages[$value] = $this->_Template_Contents["ErrorMessageText"];
                $retval .= $this->mTmplFiller->fill("catalog/add_cat/", "error.tpl.html", array());;
            }
        }

        return $retval;*/
    }

    /**
     * @return String Return html code for form fields representing images-related information :
     * <ul>
     * <li>Input text fields and "Browse..." button, if image has not been choosen.</li>
     * <li>Image and "Delete" link, otherwise.</li>
     * </ul>
     * @see $this->ViewState ("LargeImage" and "SmallImage" indices)
     */
    function outputCatImageControls($ViewState)
    {
        global $application;
        $this->MessageResources = &$application->getInstance('MessageResources');
        $obj = &$application->getInstance('MessageResources');
        $retval = "";
        loadCoreFile('html_form.php');
        $HtmlForm1 = new HtmlForm();

        $level1_template_contents = array();
        $level1_template_contents["Images"] = "";

//        if(modApiFunc("application", "isImageUploaded", $ViewState["LargeImage"]) &&
//           modApiFunc("application", "isImageUploaded", $ViewState["SmallImage"]))
        if($ViewState["LargeImage"] != "" &&
           $ViewState["SmallImage"] != "")
        {
            $level1_template_contents["UploadControl"] = "";
        }
        else
        {
            $this->_Template_Contents["ImagesUploadSubmit"] = $HtmlForm1->genInputSubmit("ImagesUploadSubmit", "Upload");
            $this->_Template_Contents["SubmitUploadImagesScript"] = "onClick=\" AddCatForm.FormSubmitValue.value = 'UploadImages'; AddCatForm.submit(); disableButtons(new Array('SaveButton1', 'SaveButton2', 'CancelButton1', 'CancelButton2', 'UploadButton'));\"";
            $this->_Template_Contents["ErrorMessage"] = $this->MessageResources->getMessage("SETUP_WARNING_IMAGE_FOLDER_IS_NOT_WRITABLE", array("0" => modApiFunc("Catalog", "getImagesDir")));

            $application->registerAttributes($this->_Template_Contents);

            if(modApiFunc("Catalog", "isImageFolderNotWritable"))
            {
                $level1_template_contents["UploadControl"] = $this->mTmplFiller->fill("catalog/add_cat/", "cat_images.upload_ctrl.error.tpl.html", array());
            }
            else
            {
                $level1_template_contents["UploadControl"] = $this->mTmplFiller->fill("catalog/add_cat/", "cat_images.upload_ctrl.tpl.html", array());
            }
        }

        $imagesUrl = $application->getAppIni('URL_IMAGES_DIR');
        if ($application->getCurrentProtocol() == "https" && $application->getAppIni('HTTPS_URL_IMAGES_DIR'))
        {
            $imagesUrl = $application->getAppIni('HTTPS_URL_IMAGES_DIR');
        }

        $pathImagesDir = $application->getAppIni('PATH_IMAGES_DIR');

        if(//modApiFunc("application", "isImageUploaded", $ViewState["LargeImage"]))
           $ViewState["LargeImage"] != "")
        {
            $this->_Template_Contents["ImageTitle"] = $obj->getMessage( new ActionMessage('CAT_LRGIMG_NAME'));
            $this->_Template_Contents["ImageViewTag"] = "LargeImage";
            $this->_Template_Contents["LargeImageHref"] = $imagesUrl.$ViewState["LargeImage"];
            if ($ViewState['LargeImage']) {
    	       list($width, $height) = getimagesize($pathImagesDir . $ViewState['LargeImage']);
    	       $this->_Template_Contents["LargeImageHeight"] = $height;
    	       $this->_Template_Contents["LargeImageWeight"] = $width;
    	    }
            $this->_Template_Contents["DelImageHref"] = "#";
            $this->_Template_Contents["SubmitDeleteLargeImageScript"] = " onClick=\" AddCatForm.FormSubmitValue.value = 'DeleteLargeImage'; AddCatForm.submit();\"";
            $application->registerAttributes($this->_Template_Contents);
            $level1_template_contents["Images"] .= $this->mTmplFiller->fill("catalog/add_cat/", "image_delete.large.tpl.html", array());
        }
        else
        {
            $this->_Template_Contents["ImageTitle"] = $obj->getMessage( new ActionMessage('CAT_LRGIMG_NAME'));
            $this->_Template_Contents["ImageViewTag"] = "LargeImage";
            $this->_Template_Contents["InputField"] = $HtmlForm1->genInputFileName("LargeImage", 65);
            $this->_Template_Contents["ImageHint"] = '';
            //: move code to HtmlForm
            if(modApiFunc("Catalog", "isImageFolderNotWritable"))
            {
                $this->_Template_Contents["InputField"] .= " disabled ";
            }

            $this->_Template_Contents["AddEditCategoryUploadImageCtrlIndex"] = "1";

            $application->registerAttributes($this->_Template_Contents);
            $level1_template_contents["Images"] .= $this->mTmplFiller->fill("catalog/add_cat/", "image_upload.item.tpl.html", array());
        }

        if(//modApiFunc("application", "isImageUploaded", $ViewState["SmallImage"]))
           $ViewState["SmallImage"] != "")
        {
            $this->_Template_Contents["ImageTitle"] = $obj->getMessage( new ActionMessage('CAT_SMLIMG_NAME'));
            $this->_Template_Contents["ImageViewTag"] = "SmallImage";
            $this->_Template_Contents["Image"] = '<img width="120" src="' . $imagesUrl.$ViewState["SmallImage"] . '">';
            $this->_Template_Contents["DelImageHref"] = "#";
            $this->_Template_Contents["SubmitDeleteSmallImageScript"] = " onClick=\" AddCatForm.FormSubmitValue.value = 'DeleteSmallImage'; AddCatForm.submit();\"";
            $application->registerAttributes($this->_Template_Contents);
            $level1_template_contents["Images"] .= $this->mTmplFiller->fill("catalog/add_cat/", "image_delete.small.tpl.html", array());
        }
        else
        {
            $this->_Template_Contents["ImageTitle"] = $obj->getMessage( new ActionMessage('CAT_SMLIMG_NAME'));
            $this->_Template_Contents["ImageViewTag"] = "SmallImage";
            $this->_Template_Contents["InputField"] = $HtmlForm1->genInputFileName("SmallImage", 65);
            $pi_settings = modApiFunc('Product_Images','getSettings');
            $this->_Template_Contents["ImageHint"] =
                    isset($pi_settings['AUTO_GEN_CAT_SMALL_IMAGE']) && $pi_settings['AUTO_GEN_CAT_SMALL_IMAGE'] == 'Y' && function_exists('gd_info')
                    ? $obj->getMessage( new ActionMessage('CAT_SMLIMG_HINT'))
                    : '';
            //: move code to HtmlForm
            if(modApiFunc("Catalog", "isImageFolderNotWritable"))
            {
                $this->_Template_Contents["InputField"] .= " disabled ";
            }

            $this->_Template_Contents["AddEditCategoryUploadImageCtrlIndex"] = "2";

            $application->registerAttributes($this->_Template_Contents);
            $level1_template_contents["Images"] .= $this->mTmplFiller->fill("catalog/add_cat/", "image_upload.item.tpl.html", array());
        }

        $level1_template_contents["ImageDescription"] = $HtmlForm1->genInputTextField("256", "ImageDescription", "70", prepareHTMLDisplay($this->POST["ImageDescriptionText"]));

        $this->_Template_Contents = $level1_template_contents;
        $application->registerAttributes($this->_Template_Contents);
        $retval .= $this->mTmplFiller->fill("catalog/add_cat/", "cat_images.tpl.html", array());

        return $retval;
    }

    /**
     * Returns the "Catalog -> Add Category" view html code.
     *
     */
    function output()
    {
        global $application;
        loadCoreFile('html_form.php');
        $HtmlForm1 = new HtmlForm();

        $this->MessageResources = &$application->getInstance('MessageResources');
        if($this->ViewState["hasCloseScript"] == "true")
        {
            $this->outputFinalScript();
            return;
        }

        $template_contents_l1 = array("CatImageControls" => $this->outputCatImageControls($this->ViewState));


        $template_contents = array();
        $template_contents= array(
                           "Local_CategoryBookmarks" => getCategoryBookmarks('details',$this->_cat_id,$this->_bms_page_stat),
                           "Subtitle"            => $this->outputSubtitle(),
                           "Errors"              => $this->outputErrors(),

                           "SubcategoriesError"  => isset($this->ErrorMessages['ERR_AZ_CAT_ADDCAT_001']) ? $this->ErrorMessages['ERR_AZ_CAT_ADDCAT_001'] : "",
                           "MetaKeywordsError"   => isset($this->ErrorMessages['ERR_AZ_CAT_ADDCAT_003']) ? $this->ErrorMessages['ERR_AZ_CAT_ADDCAT_003'] : "",
                           "MetaDescriptionError"=> isset($this->ErrorMessages['ERR_AZ_CAT_ADDCAT_004']) ? $this->ErrorMessages['ERR_AZ_CAT_ADDCAT_004'] : "" ,

                           "SubcategoriesInputStyleClass" => isset($this->ErrorMessages['ERR_AZ_CAT_ADDCAT_001']) ? "error" : "",
                           "MetaKeywordsInputStyleClass"  => isset($this->ErrorMessages['ERR_AZ_CAT_ADDCAT_003']) ? "error" : "",
                           "MetaDescriptionInputStyleClass" => isset($this->ErrorMessages['ERR_AZ_CAT_ADDCAT_004']) ? "error" : "",

                           "AddCatForm"          => $HtmlForm1->genForm(modApiFunc("application", "getPagenameByViewname","NavigationBar",-1,-1,'AdminZone'), "POST", "AddCatForm"),
                           "HiddenFormSubmitValue"=> $HtmlForm1->genHiddenField("FormSubmitValue", "UploadImagesAndSave"),
                           "HiddenArrayViewStateConstants"=> $this->outputViewStateConstants(),
                           "HiddenArrayViewState"=> $this->outputViewState(),
//                           "HiddenFieldImagesUploaded" => $HtmlForm1->genHiddenField("ImagesUploaded", "false"),

                           "SubcategoryId"       => $this->outputCategoryId(),
                           "Subcategory"         => $HtmlForm1->genInputTextField("255", "Subcategory", "75", prepareHTMLDisplay($this->POST["SubcategoryText"])),

                           "CategoryStatus"     => $this->outputCategoryStatus(),
                           "CategoryShowProductsRecursivelyStatus" => $this->outputCategoryShowProductsRecursivelyStatus(),

                           "CategoryDescription"         => $HtmlForm1->genInputTextAreaField("77", "CategoryDescription", "10"),
                           "DescriptionText"     => prepareHTMLDisplay($this->POST["DescriptionText"]),

                           "ImageControls"       => $template_contents_l1["CatImageControls"],

                           "PageTitle"           => $HtmlForm1->genInputTextField("256", "PageTitle", "76", prepareHTMLDisplay($this->POST["PageTitleText"])),


                           "MetaKeywords"        => $HtmlForm1->genInputTextAreaField("75", "MetaKeywords", "5"),
                           "MetaKeywordsText"    => prepareHTMLDisplay($this->POST["MetaKeywordsText"]),

                           "MetaDescription"     => $HtmlForm1->genInputTextAreaField("75", "MetaDescription", "5"),
                           "MetaDescriptionText" => prepareHTMLDisplay($this->POST["MetaDescriptionText"]),

                           "SEO_URL_prefix"     => $HtmlForm1->genInputTextField("256", "SEO_URL_prefix", "76", prepareHTMLDisplay($this->POST["SEO_URL_prefix"])),

                           "SubmitSaveScript" => $HtmlForm1->genSubmitScript("AddCatForm"));
        $this->_Template_Contents = $template_contents;
        $application->registerAttributes($this->_Template_Contents);

        $this->MessageResources = &$application->getInstance('MessageResources');
        //: correct error codes
        $output = modApiFunc('TmplFiller', 'fill',
                              './../../js/','validate.msgs.js.tpl',
                              array(
                                    "STRING1024"=> $this->MessageResources->getMessage( new ActionMessage(array('CATADD_001')) ),
                                    "STRING128"=> $this->MessageResources->getMessage( new ActionMessage(array('CATADD_002')) ),
                                    "STRING256"=> $this->MessageResources->getMessage( new ActionMessage(array('CATADD_003')) ),
                                    "STRING512"=> $this->MessageResources->getMessage( new ActionMessage(array('CATADD_004')) )
                                   )
                            );

        return $output.$this->mTmplFiller->fill("catalog/add_cat/", "list.tpl.html",array());
    }

    /**
     * @ describe the function AddCategory->getTag.
     */
    function getTag($tag)
    {
        global $application;
        $value = getKeyIgnoreCase($tag, $this->_Template_Contents);
        if ($value == null)
        {
            switch ($tag)
        	{
        	    case 'Breadcrumb':
                    $obj = &$application->getInstance('Breadcrumb');
                    $value = $obj->output(false);
        	        break;

        	    case 'ErrorIndex':
        	        $value = $this->_error_index;
        	        break;

        	    case 'Error':
        	        $value = $this->_error;
        	        break;
        	};
        }

        return $value;
    }

    function outputFinalScript() {
        $params = array(
                'TreeID' => modApiFunc('Request', 'getValueByKey', 'tree_id'),
                'ParentID' => modApiFunc('Request', 'getValueByKey', 'parent_id'),
                'CategoryID' => modApiFunc('Request', 'getValueByKey', 'new_id'),
                'NewName' => htmlspecialchars(escapeJSScript(modApiFunc('Request', 'getValueByKey', 'new_name'))),
        );
        echo $this->mTmplFiller->fill("catalog/add_cat/", "final_script.tpl.html", $params);
    }
    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */

    /**
     * Pointer to the module object.
     */
    var $pCatalog;

    /**
     * Pointer to the template filler object.
     * It needs to track sequences of identical templates, like lists.
     */
    var $mTmplFiller;
    /**#@-*/

    /**
     * Pointer to the received from action or prepared FORM data.
     */
    var $POST;

    /**
     * View state structure. It comes from action.
     * $SessionPost["ViewState"] structure example:
     * <br>array
     * <br>(
     * <br>    "hasCloseScript"  = "false"           //true/false
     * <br>    "ErrorsArray"     =  array()          //true/false
     * <br>    "LargeImage"      = "image.jpg"       //
     * <br>    "SmallImage"      = "image_small.jpg" //
     * <br>)
     */
    var $ViewState;

    /**
     * List of error ids. It comes from action.
     */
    var $ErrorsArray;
    var $ErrorMessages;

    var $_Template_Contents;

    var $MessageResources;
    var $_error_index;
    var $_error;
}
?>