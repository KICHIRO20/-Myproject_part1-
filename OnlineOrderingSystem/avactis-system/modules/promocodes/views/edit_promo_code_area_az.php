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

//_use(dirname(__FILE__).'/add_promo_code_az.php');

/**
 * PromoCodes module.
 * "PromoCodes -> Edit Promo Code" View.
 *
 * @package PromoCodes
 * @access  public
 *
 */
class EditPromoCodeArea
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
    function EditPromoCodeArea()
    {
        global $application;

        $this->mTmplFiller = &$application->getInstance('TmplFiller');
        $request = &$application->getInstance('Request');

        $this->pcid = $request->getValueByKey('PromoCode_id');
        $this->promoCodeInfo = modApiFunc('PromoCodes', 'getPromoCodeInfo', $this->pcid);
        $this->affected = modApiFunc("PromoCodes", "getCatsProductsAffected", $this->pcid);

        if(modApiFunc("Session", "is_Set", "SessionPost"))
        {
            $this->copyFormData();
            modApiFunc('Session', 'un_Set', 'SessionPost');
        }
        else
        {
            $this->initDBFormData();
        }
    }

    function initDBFormData()
    {
        $this->ViewState =
            array(
                "hasCloseScript"   => "false"
                 );
    }

    function copyFormData()
    {
        $SessionPost = modApiFunc("Session", "get", "SessionPost");
        $this->ViewState =
            $SessionPost["ViewState"];

        return;
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
            $result .= $this->mTmplFiller->fill("promo_codes/add_promo_code/", "error.tpl.html", array());
        }
        return $result;
    }

    /**
     *
     */
    function outputSubtitle()
    {
        global $application;
        loadCoreFile('html_form.php');
        $HtmlForm1 = new HtmlForm;

        $fields = array(
             '{PCCODE}' => $this->promoCodeInfo['promo_code']
            ,'{PCCN}'   => $this->promoCodeInfo['campaign_name']
        );

        $this->_Template_Contents = array(
                           "BookmarksBlock"   => $this->outputBookmarksBlock("area", $this->pcid, "edit"),
                           "SubmitSaveScript" => $HtmlForm1->genSubmitScript("AddPromoCodeForm")
                           ,"PromoCodeIdAndCampaign" => strtr(getMsg('PROMOCODES', "PROMO_CODE_ID_AND_CAMPAIGN"), $fields)
                    );
        $application->registerAttributes($this->_Template_Contents);
        return $this->mTmplFiller->fill("promo_codes/edit_promo_code_area/", "subtitle.tpl.html",
                      array(
//                      "SubmitSaveScript" => $HtmlForm1->genSubmitScript("AddPromoCodeForm")
                           )
                      );
    }

    function out_jsCatArray()
    {
        $js_code = 'var cat_array = new Array();'."\n";

        if(!empty($this->affected['cats']))
        {
            foreach($this->affected['cats'] as $cat_id)
            {
                $js_code .= 'cat_array[cat_array.length] = '.$cat_id.";\n";
            }
        }

        return $js_code;
    }

    function out_jsProductArray()
    {
        $js_code = 'var product_array = new Array();'."\n";

        if(!empty($this->affected['prods']))
        {
            foreach($this->affected['prods'] as $pr_id)
            {
                $js_code .= 'product_array[product_array.length] = '.$pr_id.";\n";
            }
        }

        return $js_code;
    }

    function out_ProductList()
    {
        $html_code = '';

        if(!empty($this->affected['prods']))
        {
            global $application;

            foreach($this->affected['prods'] as $pr_id)
            {
                $obj = new CProductInfo($pr_id);

                if ($obj->isProductIdCorrect())
                {
                    $tags = array(
                        'ProductID' => $pr_id
                       ,'ProductName' => $obj->getProductTagValue('Name')
                       ,'jsControlPListFunc' => ' '.str_replace(array('%PID%'),array($pr_id), $this->pb_obj->getControlPListFunction())
                    );

                    $this->_Template_Contents = $tags;
                    $application->registerAttributes($this->_Template_Contents);
                    $this->mTmplFiller = &$application->getInstance('TmplFiller');
                    $html_code .= $this->mTmplFiller->fill("promo_codes/edit_promo_code_area/", "prod_item.tpl.html",array());
                }
            }
        }

        return $html_code;
    }

    function out_CategoriesList()
    {
        $html_code = '';

        if(!empty($this->affected['cats']))
        {
            global $application;

            foreach($this->affected['cats'] as $cat_id)
            {
                $obj = new CCategoryInfo($cat_id);

                // CCategoryInfo::isCategoryIdCorrect() does not exist
                if ($obj->_fCategoryIDIsIncorrect !== true)
                {
                    $tags = array(
                        'CategoryID'   => $cat_id
                       ,'CategoryName' => $obj->getCategoryTagValue('Name')
                    );

                    $this->_Template_Contents = $tags;
                    $application->registerAttributes($this->_Template_Contents);
                    $this->mTmplFiller = &$application->getInstance('TmplFiller');
                    $html_code .= $this->mTmplFiller->fill("promo_codes/edit_promo_code_area/", "cat_item.tpl.html",array());
                }
            }
        }

        return $html_code;
    }

    /**
     *                     ViewState
     */
    function outputViewStateConstants()
    {
        //$retval = Catalog_AddPromoCode::outputViewStateConstants();
        loadCoreFile('html_form.php');
        $HtmlForm1 = new HtmlForm();
        $retval = "<input type=\"hidden\"" . $HtmlForm1->genHiddenField("asc_action", "UpdatePromoCodeArea") . ">";
        $retval.= "<input type=\"hidden\"" . $HtmlForm1->genHiddenField("PromoCodeID", $this->pcid) . ">";
        return $retval;
    }

    /**
     * @return String Return html code for hidden form fields representing @var $this->ViewState array.
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

    function output()
    {
        global $application;
        loadCoreFile('html_form.php');
        $this->Hints = &$application->getInstance('Hint');
        $HtmlForm1 = new HtmlForm();

        $this->MessageResources = &$application->getInstance('MessageResources',"promo-codes-messages", "AdminZone");

        if($this->ViewState["hasCloseScript"] == "true")
        {
            modApiFunc("application", "closeChild_UpdateParent");
        }

        $pbrowser_params = array(
            'show_category_path' => true
           ,'buttons' => array(
                'add_cat' => array(
                    'label' => 'BTN_ADDCAT'
                   ,'style_class' => 'button button_8em'
                   ,'callback' => 'addCategoryToCatList(%CID%,%CNAME%);'
                   ,'default_state' => 'disabled'
                   ,'enable_condition' => 'category_selected'
                )
                ,'add_prod' => array(
                    'label' => 'BTN_ADDPRD'
                   ,'style_class' => 'button button_8em'
                   ,'callback' => 'addProductToRPList(%PID%,%PNAME%);'
                   ,'default_state' => 'disabled'
                   ,'enable_condition' => 'product_selected'
                )
            )
           ,'choosed_control_array' => 'product_array'
        );

        loadClass('ProductsBrowser');
        $this->pb_obj = new ProductsBrowser();

        $request = new Request();
        $request->setView('EditPromoCodeArea');
        $request->setAction('UpdatePromoCodeArea');

        $template_contents= array(
                           "Subtitle"            => $this->outputSubtitle(),
                           "Errors"              => $this->outputErrors(),

                           "Local_ProductsBrowser" => $this->pb_obj->output($pbrowser_params),
                           'jsProductArray'     => $this->out_jsProductArray(),
                           'jsCatArray'         => $this->out_jsCatArray(),
                           'ProductList'        => $this->out_ProductList(),
                           'CategoriesList'     => $this->out_CategoriesList(),
                           'RPFormAction'       => $request->getURL(),
                           'jsControlPListFunc' => str_replace(array('%PID%'),array('product_id'),$this->pb_obj->getControlPListFunction()),

                           "AddPromoCodeForm"     => $request->getURL(),
                           "HiddenFormSubmitValue"=> $HtmlForm1->genHiddenField("FormSubmitValue", "SaveArea"),
                           "HiddenArrayViewStateConstants"=> $this->outputViewStateConstants(),
                           "HiddenArrayViewState"=> $this->outputViewState(),

                           "SubmitSaveScript" => $HtmlForm1->genSubmitScript("AddPromoCodeForm")
                    );
        $this->_Template_Contents = $template_contents;
        $application->registerAttributes($this->_Template_Contents);

        return $this->mTmplFiller->fill("promo_codes/edit_promo_code_area/", "list.tpl.html",array());
    }

    /**#@-*/

    function gen_bmOnClick($bm_location)
    {
        $code = "window.location='";
        $request = new Request();
        $request->setView($bm_location['view']);
        if(isset($bm_location['action']))
            $request->setAction($bm_location['action']);
        if(isset($bm_location['keys']))
            foreach($bm_location['keys'] as $key => $value)
                $request->setKey($key,$value);
        $code .= $request->getURL();
        $code .= "';";
        return $code;
    }

    function outputBookmarks()
    {
        global $application;

        $html_code = "";

        foreach($this->bms as $page => $bm)
        {
            $tpl_content = array(
                "bmClass" => ($page == $this->page) ? 'active' : ((strstr($this->status_depends[$this->page.'_'.$this->page_status],$page.'_notavail')) ? 'notavail' : 'inactive')
               ,"bmIcon" => $bm['icon'] . ((@strstr($this->status_depends[$this->page.'_'.$this->page_status],$page.'_notavail')) ? '-na' : '')
               ,"bmText" => $this->MessageResources->getMessage($bm['title'])
               ,"bmOnClick" => ($page == $this->page or @strstr($this->status_depends[$this->page.'_'.$this->page_status],$page.'_notavail')) ? '' : $this->gen_bmOnClick($bm['location'])
               ,"bmName" => $page
            );
            $tpl_file = 'bookmark';

            $this->_Template_Contents=$tpl_content;
            $application->registerAttributes($this->_Template_Contents);
            $this->mTmplFiller = &$application->getInstance('TmplFiller');
            $html_code .= $this->mTmplFiller->fill("promo_codes/bookmarks/", "{$tpl_file}.tpl.html",array());
        }

        return $html_code;
    }

    function outputBookmarksBlock()
    {
        global $application;

        $this->page=func_get_arg(0);
        $this->entity_id=func_get_arg(1);
        if(func_num_args()==3)
            $this->page_status=func_get_arg(2);
        else
            $this->page_status='add';

        $this->_initBookmarks();

        $tpl_content = array(
            "bmBGColor" => 'transparent'
           ,"Bookmarks" => $this->outputBookmarks()
           ,"RightSpace" => ($this->_need_right_space) ? '<td width="100%" class="bookmarks_space">&nbsp;</td>' : ''
        );

        $this->_Template_Contents=$tpl_content;
        $application->registerAttributes($this->_Template_Contents);
        $this->mTmplFiller = &$application->getInstance('TmplFiller');
        return $this->mTmplFiller->fill("promo_codes/bookmarks/", "container.tpl.html",array());
    }

    function _initBookmarks()
    {
        $this->bms = array('details' => array(
                                'title' => 'PROMO_DETAILS'
                               ,'icon' => 'p-details'
                               ,'location' => array(
                                    'view' => 'EditPromoCode'
                                   ,'action' => 'SetEditablePromoCode'
                                   ,'keys' => array(
                                                'PromoCode_id' => $this->pcid
                                              )
                                )
                           ),
                           'area' => array(
                                 'title' => 'PROMO_AREA'
                                ,'icon' => 'p-categories'
                                ,'location' => array(
                                     'view' => 'EditPromoCodeArea'
                                    ,'action' => 'SetEditablePromoCode'
                                    ,'keys' => array(
                                               'PromoCode_id' => $this->pcid
                                              )
                                )
                           )
                     );

        $this->status_depends = array(
            'area_edit' => ''
           ,'details_add' => 'area_notavail'
        );

        $this->_need_right_space = true;
    }

    /**
     * Returns the tag output, whose name is specified in $tag.
     */
    function getTag($tag)
    {
        global $application;
        $value = "";

        if ($tag == "Error")
        {
            $value = $this->_error;
        }
        elseif ($tag == "ErrorIndex")
        {
            $value = $this->_error_index;
        }
        else
        {
            $value = getKeyIgnoreCase($tag, $this->_Template_Contents);
        }

        return $value;
    }

    var $MR;
    var $bms;
    var $m_bms;
    var $page;
    var $entity_id;
    var $page_status;
    var $status_depends;
    var $_need_right_space;

    var $pcid;
    var $affected;

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */

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
     * View state structure. Comes from action.
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
     * List of error ids. Comes from action.
     */
    var $ErrorsArray;
}
?>