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
class ShippingCostCalculatorSection
{
    function ShippingCostCalculatorSection()
    {
        global $application;

        $this->MessageResources = &$application->getInstance('MessageResources',"shipping-cost-calculator-messages", "AdminZone");

        $this->NoView = false;
        if ($application->issetBlockTagFatalErrors("ShippingCostCalculatorSection"))
        {
            $this->NoView = true;
        }
    }

    function outputFsRulesSmallTable()
    {
        global $application;
        $output = '';
        $rulesList = execQuery('SELECT_SCC_FS_RULES', array());
        $this->mTmplFiller = &$application->getInstance('TmplFiller');

        if (count($rulesList) < 1)
            return '<tr><td colspan=4 style="text-align: center;padding-top: 50px;">'.getMsg('SCC', 'FS_EMPTY_LIST_MESSAGE').'</td>';

        foreach ($rulesList as $rule)
        {
            $cats = explode('|', $rule['cats']);
            $prods = explode('|', $rule['prods']);
            $cats_num  = !(isset($cats[0])  && $cats[0] != NULL)  ? '0' : count($cats);
            $prods_num = !(isset($prods[0]) && $prods[0] != NULL) ? '0' : count($prods);
            $cat_prod = $cats_num . '/' . $prods_num;

            $strict_cart_label = ($rule['dirty_cart'] == 1) ? getMsg("SCC", "NO_LABEL") : getMsg("SCC", "YES_LABEL");

            $template_contents = array(
                    "RuleName"  => $rule['rule_name']
                   ,"MinSubtotal" => ($rule['min_subtotal'] > 0) ? modApiFunc("Localization", "format", $rule['min_subtotal'], "currency") : "-"
                   ,"Cat_Prod" => $cat_prod
                   ,"DirtyCart" => $strict_cart_label
                );

            $this->_Template_Contents=$template_contents;
            $application->registerAttributes($this->_Template_Contents);

            $output .= $this->mTmplFiller->fill("shipping_cost_calculator/", "one_rule_smt.tpl.html",array());
        }


        return $output;
    }

    function output()
    {
        global $application;

        $settings = modApiFunc("Shipping_Cost_Calculator","getSettings");


        $na = $this->MessageResources->getMessage("GSS_FREE_SHIPPING_OVER_NA");
        $fs_OO = $settings["FS_OO"] == "" ? $na : modApiFunc("Localization","currency_format",$settings["FS_OO"]);
        $fh_OO = $settings["FH_OO"] == "" ? $na : modApiFunc("Localization","currency_format",$settings["FH_OO"]);

        $template_contents = array(
                "SCC_SettingsLink" => "scc_settings.php"
               ,"SCC_RulesLink" => "scc_fs_rules.php"
               ,"SSC_Header"    => $this->MessageResources->getMessage("SSC_HEADER")
               ,"LabelEditSettings" => $this->MessageResources->getMessage("LABEL_EDIT_SETTINGS")
               ,"LabelEditRules" => $this->MessageResources->getMessage("LABEL_EDIT_RULES")

               ,"setsPOSClabel" => $this->MessageResources->getMessage("PER_ORDER_SHIPPING_COST")
               ,"setsPOSCvalue" => $settings["PO_SC_TYPE"]=="P" ? $settings["PO_SC"]."%" : modApiFunc("Localization","currency_format",$settings["PO_SC"])
               ,"setsPOHClabel" => $this->MessageResources->getMessage("PER_ORDER_HANDLING_COST")
               ,"setsPOHCvalue" => modApiFunc("Localization","currency_format",$settings["PO_HC"])
               ,"setsMINSClabel" => $this->MessageResources->getMessage("MINIMUM_SHIPPING_COST")
               ,"setsMINSCvalue" => modApiFunc("Localization","currency_format",$settings["MIN_SC"])
               ,"setsFSOOlabel" => $this->MessageResources->getMessage("FREE_SHIPPING")
               ,"setsFSOOvalue" => $fs_OO
               ,"setsFHOOlabel" => $this->MessageResources->getMessage("FREE_HANDLING")
               ,"setsFHOOvalue" => $fh_OO
               ,"ShippingTester_WindowLink" => "shipping_tester_window.php"
               ,"ShippingTester_Button"     => $this->MessageResources->getMessage("SHIPPING_TESTER_BUTTON")
               ,"setsFsRules" => $this->outputFsRulesSmallTable()
        );

        $this->_Template_Contents=$template_contents;
        $application->registerAttributes($this->_Template_Contents);
        $this->mTmplFiller = &$application->getInstance('TmplFiller');
        return $this->mTmplFiller->fill("shipping_cost_calculator/", "section.tpl.html",array());
    }

    function getTag($tag)
    {
        global $application;
        $value = getKeyIgnoreCase($tag, $this->_Template_Contents);
        return $value;
    }

    var $_Template_Contents;
    var $MessageResources;

};

?>