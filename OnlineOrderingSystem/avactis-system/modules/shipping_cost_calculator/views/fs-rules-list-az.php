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
 *
 * @package Shipping Cost Calculator
 * @access  public
 * @author Ravil Garafutdinov
 */
class FreeShippingRulesList
{
    function FreeShippingRulesList()
    {
        global $application;
        $this->MessageResources = &$application->getInstance('MessageResources',"shipping-cost-calculator-messages", "AdminZone");
        $this->mTmplFiller = &$application->getInstance('TmplFiller');

        $this->rulesList = execQuery('SELECT_SCC_FS_RULES', array());

        if(modApiFunc("Session", "is_Set", "SessionPost"))
        {
            $this->copyFormData();
            modApiFunc('Session', 'un_Set', 'SessionPost');
        }
        else
        {
            $this->initFormData();
        }

        loadCoreFile('html_form.php');
    }

    function initFormData()
    {
        $this->ViewState =
            array(
                "hasCloseScript"  => "false"
               ,"FormSubmitValue" => "save"
            );

    }

    /**
     * Copies data from the global POST to the local POST array.
     */
    function copyFormData()
    {
        $SessionPost = modApiFunc("Session", "get", "SessionPost");
        $this->ViewState = $SessionPost["ViewState"];
        if(isset($this->ViewState["ErrorsArray"]) &&
           count($this->ViewState["ErrorsArray"]) > 0)
        {
            $this->ErrorsArray = $this->ViewState["ErrorsArray"];
            unset($this->ViewState["ErrorsArray"]);
        }
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

    /**
     * @return HTML code for the errors
     */
    function outputErrors()
    {

        if (!is_array($this->ErrorsArray) || sizeof($this->ErrorsArray) == 0)
        {
            return;
        }
        global $application;

        $return_html_code="";
        foreach($this->ErrorsArray as $index => $value)
        {
            $this->_Template_Contents = array(
                                            "ErrorIndex"    => $index+1,
                                            "Error"            => $this->MessageResources->getMessage($value)
                                        );
            $application->registerAttributes($this->_Template_Contents);
            $return_html_code.=$this->mTmplFiller->fill("shipping_cost_calculator/", "error.tpl.html", array());
        };
        return $return_html_code;
    }

    function outputItems()
    {
        global $application;
        $output = '';
        $request = new Request();
        $request->setView("EditFsRule");

        $i=0;
        foreach ($this->rulesList as $rule)
        {
            $request->setKey("FsRule_id", $rule['id']);
            $url = $request->getURL();

            $cats = explode('|', $rule['cats']);
            $prods = explode('|', $rule['prods']);
            $cats_num  = !(isset($cats[0])  && $cats[0] != NULL)  ? '0' : count($cats);
            $prods_num = !(isset($prods[0]) && $prods[0] != NULL) ? '0' : count($prods);
            $cat_prod = $cats_num . '/' . $prods_num;

            $strict_cart_label = ($rule['dirty_cart'] == 1) ? getMsg("SCC", "NO_LABEL") : getMsg("SCC", "YES_LABEL");

            $template_contents = array(
                    "FsRule_id" => $rule['id']
                   ,"FsRule_count" => $i
                   ,"FsRuleHref" => $url
                   ,"RuleName"  => $rule['rule_name']
                   ,"MinSubtotal" => ($rule['min_subtotal'] > 0) ? modApiFunc("Localization", "format", $rule['min_subtotal'], "currency") : "-"
                   ,"Cat_Prod" => $cat_prod
                   ,"DirtyCart" => $strict_cart_label
                );

            $this->_Template_Contents=$template_contents;
            $application->registerAttributes($this->_Template_Contents);

            $output .= $this->mTmplFiller->fill("shipping_cost_calculator/", "one_rule.tpl.html",array());
            $i++;
        }
        $output .= "<input type=hidden id='delete_ids_count_id' value='$i'>";

        return $output;
    }

    function outputEmptyPlaceHolders()
    {
        $output = '';
        $num = count($this->rulesList);

        if ($num > 2)
            return $output;

        $i=0;
        if ($num == 0)
        {
            $output .= $this->mTmplFiller->fill("shipping_cost_calculator/", "empty_rule_w_message.tpl.html", array());
            $i=1;
        }
        $num = 3 - $num;
        for (; $i < $num; $i++)
        {
            $output .= $this->mTmplFiller->fill("shipping_cost_calculator/", "empty_rule.tpl.html", array());
        }
        return $output;
    }

    function output()
    {
        global $application;

        if($this->ViewState["hasCloseScript"] == "true")
        {
            modApiFunc("application", "UpdateParent");
        }

        $request = new Request();
        $request->setView('AddFsRule');
        $AddFsRuleHref = $request->getURL();

        $request->setView(CURRENT_REQUEST_URL);
        $request->setAction("DeleteFsRule");
        $FsRuleDeleteActionUrl = $request->getURL();

        $template_contents = array(
                "HiddenArrayViewState"  => $this->outputViewState()
               ,"Errors"                => $this->outputErrors()
               ,"SSC_Header"      => $this->MessageResources->getMessage("SSC_HEADER")
               ,"LabelSettings"   => $this->MessageResources->getMessage("LABEL_SETTINGS")
               ,"AddFsRuleHref"   => $AddFsRuleHref
               ,"FsRuleDeleteActionUrl" => $FsRuleDeleteActionUrl

               ,"Alert_001" => $this->MessageResources->getMessage("ALERT_001")
               ,"Alert_002" => $this->MessageResources->getMessage("ALERT_002")
               ,"Alert_003" => $this->MessageResources->getMessage("ALERT_003")
               ,"Alert_004" => $this->MessageResources->getMessage("ALERT_004")
               ,"Alert_005" => $this->MessageResources->getMessage("ALERT_005")

               ,"Items" => $this->outputItems()
               ,"EmptyPlaceHolders" => $this->outputEmptyPlaceHolders()
        );

        $this->_Template_Contents=$template_contents;
        $application->registerAttributes($this->_Template_Contents);

        $mainMessageResources = &$application->getInstance('MessageResources');

        $output = modApiFunc('TmplFiller', 'fill',
                              './../../js/','validate.msgs.js.tpl',
                              array(
                                    "INTEGER" => $mainMessageResources->getMessage( new ActionMessage(array('PRDADD_001')) )
                                   ,"FLOAT"   => $mainMessageResources->getMessage( new ActionMessage(array('PRDADD_002')) )
                                   ,"STRING1024"=> $mainMessageResources->getMessage( new ActionMessage(array('CATADD_001')) )
                                   ,"STRING128"=> $mainMessageResources->getMessage( new ActionMessage(array('CATADD_002')) )
                                   ,"STRING256"=> $mainMessageResources->getMessage( new ActionMessage(array('CATADD_003')) )
                                   ,"STRING512"=> $mainMessageResources->getMessage( new ActionMessage(array('CATADD_004')) )
                                   ,"CURRENCY"=> addslashes($mainMessageResources->getMessage( new ActionMessage(array('CURRENCY_FIELD',
                                                         modApiFunc("Localization", "FloatToFormatStr", 12.35, "currency"),
                                                         modApiFunc("Localization", "FloatToFormatStr", 34, "currency"),
                                                         modApiFunc("Localization", "FloatToFormatStr", 99.00, "currency")))))
                                   ,"WEIGHT"   => addslashes($mainMessageResources->getMessage( new ActionMessage(array('WEIGHT_FIELD',
                                                         modApiFunc("Localization", "FloatToFormatStr", 23.325, "weight"),
                                                         modApiFunc("Localization", "FloatToFormatStr", 34, "weight"),
                                                         modApiFunc("Localization", "FloatToFormatStr", 99.2, "weight")))))
                                   ,"ITEM"     => $mainMessageResources->getMessage( new ActionMessage(array('ITEM_FIELD')))
                                   )
                            );
        return $output.$this->mTmplFiller->fill("shipping_cost_calculator/", "fs_rules_list/settings.tpl.html",array());
    }

    function getTag($tag)
    {
        global $application;
        $value = getKeyIgnoreCase($tag, $this->_Template_Contents);
        return $value;
    }

    var $rulesList;
    var $_Template_Contents;
    var $MessageResources;

    var $ViewState;

    var $ErrorsArray;
    var $ErrorMessages;

    var $_error_index;
    var $_error;

}


?>