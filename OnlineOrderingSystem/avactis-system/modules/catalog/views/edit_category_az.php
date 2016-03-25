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

_use(dirname(__FILE__).'/add_category_az.php');
/**
 * Catalog module.
 * "Catalog -> Edit Category" View.
 *
 * @package Catalog
 * @access  public
 *
 */
class EditCategory extends AddCategory
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * The view constructor.
     */
    function EditCategory()
    {
        global $application;

        $this->mTmplFiller = &$application->getInstance('TmplFiller');

        if(modApiFunc("Session", "is_Set", "SessionPost"))
        {
            $this->copyFormData();
            modApiFunc('Session', 'un_Set', 'SessionPost');
        }
        else
        {
            $this->initDBFormData();
        }

        $this->_bms_page_stat = 'edit';
        $this->_cat_id = modApiFunc('Catalog', 'getEditableCategoryID');
    }

    /**
     *
     *
     * @return
     */
    function initDBFormData()
    {
            $cid=modApiFunc('Catalog', 'getEditableCategoryID');
            $catInfo = new CCategoryInfo($cid);

            $this->ViewState =
                array(
                    "hasCloseScript"   => "false",
                    "SmallImage" => basename($catInfo->getCategoryTagValue('smallimagesrc')),
                    "LargeImage" => basename($catInfo->getCategoryTagValue('largeimagesrc'))
                     );

            $this->POST  =
                array(
                    "CategoryID"            => $cid,
                    "CategoryStatus"        => $catInfo->getCategoryTagValue('status'),
                    "ImageDescriptionText"  => $catInfo->getCategoryTagValue('imagealttext'),
                    "ParentCategoryID"      => modApiFunc('CProductListFilter','getCurrentCategoryId'),
                    "PageTitleText"         => $catInfo->getCategoryTagValue('pagetitle'),
                    "SubcategoryText"       => $catInfo->getCategoryTagValue('name'),
                    "DescriptionText"       => $catInfo->getCategoryTagValue('description'),
                    "MetaKeywordsText"      => $catInfo->getCategoryTagValue('metakeywords'),
                    "MetaDescriptionText"   => $catInfo->getCategoryTagValue('metadescription'),
                    "CategoryShowProductsRecursivelyStatus" => $catInfo->getCategoryTagValue('showproductsrecursivelystatus'),
                    "SEO_URL_prefix"        => urldecode($catInfo->getCategoryTagValue('seo_url_prefix'))
                );

//            $this->ViewState =
//                array(
//                    "hasCloseScript"   => "false",
//                    "SmallImage" => ($catInfo['SmallImageShowThatFileIsNotUploaded'] == '1') ? "" : basename($catInfo['SmallImageSrc']),
//                    "LargeImage" => ($catInfo['LargeImageShowThatFileIsNotUploaded'] == '1') ? "" : basename($catInfo['LargeImageSrc'])
//                     );
//            $this->POST  =
//                array(
//                    "CategoryID"            => $cid,
//                    "CategoryStatus"        => $catInfo['Status'],
//                    "ImageDescriptionText"  => $catInfo['ImageAltText'],
//                    "ParentCategoryID"      => modApiFunc("Catalog", "getCurrentCategoryID"),
//                    "PageTitleText"         => $catInfo['PageTitle'],
//                    "SubcategoryText"       => $catInfo['Name'],
//                    "DescriptionText"       => $catInfo['Description'],
//                    "MetaKeywordsText"      => $catInfo['MetaKeywords'],
//                    "MetaDescriptionText"   => $catInfo['MetaDescription'],
//                    "CategoryShowProductsRecursivelyStatus" => $catInfo['ShowProductsRecursivelyStatus'],
//                    "SEO_URL_prefix"        => $catInfo['SEO_URL_prefix']
//                );
    }

    /**
     * The derived method Catalog_AddCategory::copyFormData() adds one field 'CategoryID'
     * to the array $this->POST.
     *
     */
    function copyFormData()
    {
        AddCategory::copyFormData();
        $SessionPost = modApiFunc("Session", "get", "SessionPost");
        $this->POST['CategoryID'] = $SessionPost['CategoryID'];
    }

    /**
     * Outputs the view subtitle.
     */
    function outputSubtitle()
    {
        global $application;
        loadCoreFile('html_form.php');
        $HtmlForm1 = new HtmlForm;
        $this->_Template_Contents["SubmitSaveScript"] = $HtmlForm1->genSubmitScript("AddCatForm");
        $application->registerAttributes($this->_Template_Contents);
        return $this->mTmplFiller->fill("catalog/edit_cat/", "subtitle.tpl.html",
                      array(
//                      "SubmitSaveScript" => $HtmlForm1->genSubmitScript("AddCatForm")
                           )
                      );
    }

    function outputCategoryId()
    {
        global $application;
        $obj = &$application->getInstance('MessageResources');
        $SubcategoryID = $obj->getMessage( new ActionMessage('CAT_ID_NAME'));
        $retval = "<tr><td class=\"label popup_dialog_body_left_padded\"><span class=\"required\">".$SubcategoryID."*</span></td>";
        $retval.= "<td class=\"help\"><div onclick=\"getAttrHelp('0','SubcategoryID', 'cat_attr');return false;\" style=\"cursor:pointer;\"><img src=\"images/question.gif\"></div></td>";
        $retval.= "<td class=\"value popup_dialog_body_right_padded\">".$this->POST['CategoryID']."</td></tr>";
        return $retval;
    }

    /**
     * Outputs the hidden fields ViewState.
     */
    function outputViewStateConstants()
    {
        //$retval = Catalog_AddCategory::outputViewStateConstants();
        loadCoreFile('html_form.php');
        $HtmlForm1 = new HtmlForm();
        $retval = "<input type=\"hidden\"" . $HtmlForm1->genHiddenField("ParentCategoryID", $this->POST["ParentCategoryID"]) . ">" .
                  "<input type=\"hidden\"" . $HtmlForm1->genHiddenField("tree_id", modApiFunc('Request','getValueByKey','tree_id')) . ">" .
                  "<input type=\"hidden\"" . $HtmlForm1->genHiddenField("asc_action", "UpdateCategoryInfo") . ">";
        $retval.= "<input type=\"hidden\"" . $HtmlForm1->genHiddenField("CategoryID", $this->POST["CategoryID"]) . ">";
        return $retval;
    }

    function outputFinalScript() {
        $params = array(
                'TreeID' => modApiFunc('Request', 'getValueByKey', 'tree_id'),
                'CategoryID' => modApiFunc('Request', 'getValueByKey', 'old_id'),
                'NewName' => htmlspecialchars(escapeJSScript(modApiFunc('Request', 'getValueByKey', 'new_name'))),
        );
        echo $this->mTmplFiller->fill("catalog/edit_cat/", "final_script.tpl.html", $params);
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
     * Needs to track sequences of identical templates, like lists.
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
     * array
     * (
     *     "hasCloseScript"  = "false"           //true/false
     *     "ErrorsArray"     =  array()          //true/false
     *     "LargeImage"      = "image.jpg"       //
     *     "SmallImage"      = "image_small.jpg" //
     * )
     */
    var $ViewState;

    /**
     * List of error ids. It comes from action.
     */
    var $ErrorsArray;
}
?>