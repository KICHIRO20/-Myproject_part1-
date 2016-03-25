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
 * ShippingCostCalculator module.
 * "ShippingCostCalculator -> Add FS Rule" View.
 *
 * @package ShippingCostCalculator
 * @access  public
 * @author  Ravil Garafutdinov
 *
 */


class AddFsRule
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    function AddFsRule()
    {
        global $application;

        $this->MessageResources = &$application->getInstance('MessageResources',"shipping-cost-calculator-messages", "AdminZone");

        $this->terminator_outed = false;

        $this->mTmplFiller = &$application->getInstance('TmplFiller');

        if(modApiFunc("Session", "is_Set", "SessionPost"))
        {
            $this->copyFormData();
            modApiFunc('Session', 'un_Set', 'SessionPost');
        }
        else
        {
            $this->initFormData();
        }
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
                "FsRuleName" => ($SessionPost["FsRuleName"]),
                "FsRuleMinSubtotal" => ($SessionPost["FsRuleMinSubtotal"]),
                "FsRuleStrictCart"   => ($SessionPost["FsRuleStrictCart"])
            );
    }

    function initFormData()
    {
        $this->ViewState =
            array(
                "hasCloseScript" => "false",
                 );
        $this->POST  =
            array(
                "FsRuleName" => '',
                "FsRuleMinSubtotal" => '0',
                "FsRuleStrictCart"   => SCC_STRICT_CART
            );
    }

    /**
     * @return String Return a href link to "Promo Codes Navigator" view.
     */
    function getLinkToPromoCodesNavigator($cid)
    {
        $_request = new Request();
        $_request->setView  ( 'FsRuleNavigationBar' );
        return $_request->getURL();
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

    function outputSubtitle()
    {
        global $application;
        loadCoreFile('html_form.php');
        $HtmlForm1 = new HtmlForm;
        $this->_Template_Contents = array(
                           "BookmarksBlock"   => $this->outputBookmarksBlock('scc_details', 1),
                           "SubmitSaveScript" => $HtmlForm1->genSubmitScript("AddFsRuleForm")
                    );
        $application->registerAttributes($this->_Template_Contents);
        return $this->mTmplFiller->fill("shipping_cost_calculator/add_fs_rule/", "subtitle.tpl.html", array());
    }

//==== functions used in EditFS_AZ, stubs here =================

    function outputEffectiveAreaLaconic()
    {
        return '';
    }

    function outputFsEffectiveAreaDetails()
    {
        return '';
    }

    function outputFsRuleStrictCartSelect()
    {
        return '';
    }

//===================================================================

    function outputViewStateConstants()
    {
        loadCoreFile('html_form.php');
        $HtmlForm1 = new HtmlForm();
        $retval = "<input type=\"hidden\"" . $HtmlForm1->genHiddenField("asc_action", "AddFsRuleInfo") . ">";
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
            $result .= $this->mTmplFiller->fill("shipping_cost_calculator/add_fs_rule/", "error.tpl.html", array());
        }
        return $result;
    }

    function getAction()
    {
        return "AddFsRuleInfo";
    }

    /**
     *
     */
    function output()
    {
        global $application;
        loadCoreFile('html_form.php');
        $this->Hints = &$application->getInstance('Hint');
        $HtmlForm1 = new HtmlForm();

        $this->MessageResources = &$application->getInstance('MessageResources',"shipping-cost-calculator-messages", "AdminZone");
        if($this->ViewState["hasCloseScript"] == "true")
        {
            modApiFunc("application", "UpdateParentsParent");
            //modApiFunc("application", "closeChild_UpdateParent");
        }

        $request = new Request();
        $request->setView("EditFsRule");
        $request->setAction($this->getAction());
        $form_url = $request->getURL();

        $template_contents= array(
                           "Subtitle"            => $this->outputSubtitle(),
                           "Errors"              => $this->outputErrors(),

                           "FsRuleNameError"  => isset($this->ErrorMessages['ERR_AZ_SCC_ADD_PROMO_CODE_001']) ? $this->ErrorMessages['ERR_AZ_SCC_ADD_PROMO_CODE_001'] : "",
                           "FsRuleNameInputStyleClass" => isset($this->ErrorMessages['ERR_AZ_SCC_ADD_PROMO_CODE_001']) ? "error" : "",
                           "FsRuleName"         => $HtmlForm1->genInputTextField("128", "FsRuleName", "50", prepareHTMLDisplay($this->POST["FsRuleName"])),
                           "FsRuleFieldHint" => $this->Hints->getHintLink(array('SCC_FSTABLE_RULE_NAME_LABEL', 'shipping-cost-calculator-messages')),

                           "FsRuleEffectiveAreaLaconic" => $this->outputEffectiveAreaLaconic(),
                           "FsRuleStrictCartSelect" => $this->outputFsRuleStrictCartSelect(),

                           "FsRuleMinSubtotalError"  => isset($this->ErrorMessages['ERR_AZ_SCC_ADD_PROMO_CODE_005']) ? $this->ErrorMessages['ERR_AZ_SCC_ADD_PROMO_CODE_005'] : "",
                           "FsRuleMinSubtotalInputStyleClass" => isset($this->ErrorMessages['ERR_AZ_SCC_ADD_PROMO_CODE_005']) ? "error" : "",
                           "FsRuleMinSubtotal"         => $HtmlForm1->genInputTextField("10", "FsRuleMinSubtotal", "10", prepareHTMLDisplay($this->POST["FsRuleMinSubtotal"])),
                           "FsRuleMinSubtotalFormat" => modApiFunc("Localization", "format_settings_for_js", "currency"),
                           "FsRuleMinSubtotalSign" => modApiFunc("Localization", "getCurrencySign"),
                           "FsRuleMinSubtotalFieldHint" => $this->Hints->getHintLink(array('SCC_MIN_SUBTOTAL_LABEL', 'shipping-cost-calculator-messages')),

                           "AddFsRuleForm"     => $HtmlForm1->genForm($form_url, "POST", "AddFsRuleForm"),
                           "HiddenFormSubmitValue"=> $HtmlForm1->genHiddenField("FormSubmitValue", "Save"),
                           "HiddenArrayViewStateConstants"=> $this->outputViewStateConstants(),
                           "HiddenArrayViewState"=> $this->outputViewState(),

                           "SubmitSaveScript" => $HtmlForm1->genSubmitScript("AddFsRuleForm")
                    );

        $this->_Template_Contents = $template_contents;
        $application->registerAttributes($this->_Template_Contents);

        $SpecMessageResources = &$application->getInstance('MessageResources');
        //: correct error codes
        $output = '';
        $output = modApiFunc('TmplFiller', 'fill',
                              './../../js/','validate.msgs.js.tpl',
                              array(
                                    "STRING1024"=> $SpecMessageResources->getMessage( new ActionMessage(array('CATADD_001')) ),
                                    "STRING128"=> $SpecMessageResources->getMessage( new ActionMessage(array('CATADD_002')) ),
                                    "STRING256"=> $SpecMessageResources->getMessage( new ActionMessage(array('CATADD_003')) ),
                                    "STRING512"=> $SpecMessageResources->getMessage( new ActionMessage(array('CATADD_004')) ),
                                    "INTEGER" => $SpecMessageResources->getMessage( new ActionMessage(array('PRDADD_001')))
                                   ,"FLOAT"   => $SpecMessageResources->getMessage( new ActionMessage(array('PRDADD_002')))
                                   ,"CURRENCY"=> addslashes($SpecMessageResources->getMessage( new ActionMessage(array('CURRENCY_FIELD',
                                                         modApiFunc("Localization", "FloatToFormatStr", 12.35, "currency"),
                                                         modApiFunc("Localization", "FloatToFormatStr", 34, "currency"),
                                                         modApiFunc("Localization", "FloatToFormatStr", 99.00, "currency")))))
                                   ,"WEIGHT"   => addslashes($SpecMessageResources->getMessage( new ActionMessage(array('WEIGHT_FIELD',
                                                         modApiFunc("Localization", "FloatToFormatStr", 23.325, "weight"),
                                                         modApiFunc("Localization", "FloatToFormatStr", 34, "weight"),
                                                         modApiFunc("Localization", "FloatToFormatStr", 99.2, "weight")))))
                                   ,"ITEM"     => $SpecMessageResources->getMessage( new ActionMessage(array('ITEM_FIELD')))
                                   )
                            );

        return $output.$this->mTmplFiller->fill("shipping_cost_calculator/add_fs_rule/", "list.tpl.html",array());
    }

    /**
     * @                      AddPromoCode->getTag.
     */
    function getTag($tag)
    {
        global $application;
        $value = getKeyIgnoreCase($tag, $this->_Template_Contents);
        if ($value == null)
        {
            switch ($tag)
            {
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
    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

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
            $html_code .= $this->mTmplFiller->fill("shipping_cost_calculator/bookmarks/", "{$tpl_file}.tpl.html",array());
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
        return $this->mTmplFiller->fill("shipping_cost_calculator/bookmarks/", "container.tpl.html",array());
    }

    function _initBookmarks()
    {
        $this->fsr_id = 0;
        if (isset($this->POST["FsRule_id"]))
            $this->fsr_id = $this->POST["FsRule_id"];

        $this->bms = array('scc_details' => array(
                                'title' => 'SCC_FS_RULE_DETAILS_BM_LABEL'
                               ,'icon' => 'p-details'
                               ,'location' => array(
                                    'view' => 'EditFsRule'
                                   ,'keys' => array(
                                                'FsRule_id' => $this->fsr_id
                                              )
                                )
                           ),
                           'scc_area' => array(
                                 'title' => 'SCC_FS_RULE_AREA_BM_LABEL'
                                ,'icon' => 'p-categories'
                                ,'location' => array(
                                     'view' => 'EditFsRuleArea'
                                    ,'keys' => array(
                                               'FsRule_id' => $this->fsr_id
                                              )
                                )
                           )
                     );

        $this->status_depends = array(
            'scc_details_edit' => ''
           ,'scc_details_add' => 'scc_area_notavail'
        );

        $this->_need_right_space = true;
    }

    var $fsr_id;
    var $MR;
    var $bms;
    var $m_bms;
    var $page;
    var $entity_id;
    var $page_status;
    var $status_depends;
    var $_need_right_space;

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
     * List of error ids. Comes from action.
     */
    var $ErrorsArray;
    var $ErrorMessages;

    var $_Template_Contents;

    var $MessageResources;
    var $_error_index;
    var $_error;
}
?>