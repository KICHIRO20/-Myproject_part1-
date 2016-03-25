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

class ShippingTesterWindow
{

    function ShippingTesterWindow()
    {
        global $application;
        $this->MessageResources = &$application->getInstance('MessageResources',"shipping-tester-messages", "AdminZone");
        $this->mTmplFiller = &$application->getInstance('TmplFiller');

        if(modApiFunc("Session", "is_set", "SessionPost"))
        {
            $this->copyFormData();
            modApiFunc('Session', 'un_set', 'SessionPost');
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

        $this->POST=array(
            "DstCity" => "",
            "DstCountry" => modApiFunc('Configuration', 'getValue', SYSCONFIG_STORE_OWNER_COUNTRY),
            "DstState" => modApiFunc('Configuration', 'getValue', SYSCONFIG_STORE_OWNER_STATE),
            "DstZip" => "",
            "Cart" => array(
                    "products" => array(),
                    "subtotal" => 0,
                    "total_weight" => 0,
                ),
        );
    }

    /**
     * Copies data from the global POST to the local POST array
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

        $this->POST =
            array(
                "DstCity" => $SessionPost["DstCity"],
                "DstCountry" => $SessionPost["DstCountry"],
                "DstState" => $SessionPost["DstState"],
                "DstZip" => $SessionPost["DstZip"],
                "Cart" => $SessionPost["Cart"],
            );

        if(isset($SessionPost["results"]))
            $this->test_results=unserialize(gzinflate($SessionPost["results"]));

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

    function out_JS_CS()
    {
        return   modApiFunc("Checkout", "getJavascriptSynchronizeCountriesAndStatesLists") .
                 modApiFunc("Location", "getJavascriptCountriesStatesArrays") .
                 "<SCRIPT LANGUAGE=\"JavaScript\">" . "\n" .
                 "<!--\n" . "\n" .
                 "var onload_bak = window.onload;" . "\n" .
                 "window.onload = function()" . "\n" .
                 "{" . "\n" .
                 "    if(onload_bak){onload_bak();}" . "\n" .
                 "refreshStatesList('DstCountry', 'DstState_menu_select', 'DstState_text_div');" .
                 "}" . "\n" .
                 "//-->" . "\n" .
                 "</SCRIPT>" . "\n";
    }

    function outCountrySelect()
    {
        $countries = modApiFunc("Location", "getCountries");

        $countries_select = array(
            "select_name" => "DstCountry",
            "selected_value" => $this->POST["DstCountry"],
            "onChange" => "refreshStatesList('DstCountry', 'DstState_menu_select', 'DstState_text_div');",
            "id" => "DstCountry",
            "values" => array()
        );
        foreach($countries as $cid => $cname)
            $countries_select["values"][]=array(
                    "value" => $cid,
                    "contents" => $cname
                );

       return HtmlForm::genDropdownSingleChoice($countries_select);

    }

    function outStatesSelect()
    {
        $cid = $this->POST["DstCountry"];
        $states = modApiFunc("Location", "getStates", $cid);

        $states_select = array(
            "select_name" => "DstState_menu_select",
            "selected_value" => (!empty($states)?$this->POST["DstState"]:""),
            "id" => "DstState_menu_select",
            "values"  => array()
        );
        if(!empty($states))
            foreach($states as $sid => $sname)
                $states_select["values"][]=array(
                        "value" => $sid,
                        "contents" => $sname
                    );

        $state_name=(!empty($states)?"":$this->POST["DstState"]);

        $html_ss = HtmlForm::genDropdownSingleChoice($states_select);
        $html_div = "<div id=\"DstState_test_div\"><input type=\"text\" id=\"DstState_text_div\" ".HtmlForm::genInputTextField("125","DstState_text_div","55",$state_name)."></div>";

        return $html_ss.$html_div;
    }

    function outJSproducts()
    {
        $js_code="";
        for($i=0;$i<count($this->POST["Cart"]["products"]);$i++)
            $js_code.="cart_products[products_count]=new Array();\n" .
                      "cart_products[products_count][0]='Product #'+(products_count+1);\n" .
                      "cart_products[products_count][1]=".$this->POST["Cart"]["products"][$i]["qty"].";\n" .
                      "cart_products[products_count][2]=".$this->POST["Cart"]["products"][$i]["weight"].";\n" .
                      "cart_products[products_count][3]=".$this->POST["Cart"]["products"][$i]["cost"].";\n" .
                      "cart_products[products_count][4]=".$this->POST["Cart"]["products"][$i]["ship_charge"].";\n" .
                      "cart_products[products_count][5]=".$this->POST["Cart"]["products"][$i]["hand_charge"].";\n" .
                      "cart_products[products_count][6]=".((isset($this->POST["Cart"]["products"][$i]["free_ship"]) and $this->POST["Cart"]["products"][$i]["free_ship"]!="") ? "'checked'" : "''" ).";\n" .
                      "products_count++;\n";
        return $js_code;
    }

    function outDebugInfo($debug_info)
    {
        global $application;
        $return_html_code = "";
        foreach($debug_info as $key => $value)
        {
            if(strstr(_ml_strtolower($value[0]),"xml"))
            {
                $value[1]=preg_replace("/(>)(<[^\/])/", "\\1\n\\2", $value[1]);
                $value[1]=preg_replace("/(<\/[^>]+>)([^\n])/", "\\1\n\\2", $value[1]);
                $value[1]=nl2br(_ml_htmlentities($value[1]));
            };
            if(preg_match("/(post|get) params/",_ml_strtolower($value[0])))
            {
                $out="";
                foreach($value[1] as $_k => $_v)
                    $out.=$_k." =&gt; ".$_v."<br>";
                $value[1]=$out;
            };

            $this->_Template_Contents = array(
                "VarName"  => $value[0]
               ,"VarValue" => $value[1]
            );
            $application->registerAttributes($this->_Template_Contents);
            $return_html_code.=$this->mTmplFiller->fill("shipping_tester/", "debug_info_var.tpl.html", array());
        };

        return $return_html_code;
    }

    function outDays($days)
    {
        if(is_numeric($days) and $days>0)
            return $days." ".$this->MessageResources->getMessage('LABEL_DAYS');
        elseif(is_string($days) and _ml_strlen($days)>2)
            return $days;
        else
            return "";
    }

    function outMethods($methods)
    {
        global $application;
        $return_html_code="";

        if(!empty($methods))
            foreach($methods as $key => $value)
            {
                $this->_Template_Contents = array(
                    "MethodName"    => isset($value["method_name"]) ? $value["method_name"] : ''
                   ,"MethodDays"    => isset($value["days"]) ? $this->outDays($value["days"]) : ''
                   ,"MethodCost"    => modApiFunc('Localization','currency_format',$value["shipping_cost"]["ShippingMethodCost"])
                   ,"ShipCharge"    => modApiFunc('Localization','currency_format',$value["shipping_cost"]["TotalShippingCharge"])
                   ,"DisplayCost"   => modApiFunc('Localization','currency_format',$value["shipping_cost"]["TotalShippingAndHandlingCost"])
                );
                $application->registerAttributes($this->_Template_Contents);
                $return_html_code.=$this->mTmplFiller->fill("shipping_tester/", "method.tpl.html", array());
            };

        return $return_html_code;
    }

    function outApiResults()
    {
        global $application;

        $return_html_code="";

        foreach($this->test_results as $api_name => $result)
        {
            $modInf = modApiFunc($api_name,"getInfo");
            $this->_Template_Contents = array(
                "ModuleName"         => $modInf["Name"]
               ,"ApiID"              => $api_name
               ,"DebugInfo_Header"   => $this->MessageResources->getMessage('DEBUG_INFO_HEADER')
               ,"DebugInfo"          => (isset($result["debug_info"])) ? $this->outDebugInfo($result["debug_info"]) : ''
               ,"Methods_Header"     => $this->MessageResources->getMessage('METHODS_HEADER')
               ,"Methods"            => $this->outMethods($result["methods"])
               ,"lbl_MethodName"     => $this->MessageResources->getMessage('LABEL_METHOD_NAME')
               ,"lbl_TransitTime"    => $this->MessageResources->getMessage('LABEL_TRANSIT_TIME')
               ,"lbl_DeliveryCost"   => $this->MessageResources->getMessage('LABEL_DELIVERY_COST')
               ,"lbl_ShippingCharge" => $this->MessageResources->getMessage('LABEL_SHIPPING_CHARGE')
               ,"lbl_HandlingCharge" => $this->MessageResources->getMessage('LABEL_HANDLING_CHARGE')
               ,"lbl_ShippingCost"   => $this->MessageResources->getMessage('LABEL_SHIPPING_COST')
               ,"lbl_TotalShippingCharge" => $this->MessageResources->getMessage('TOTAL_SHIPPING_CHARGE')
            );
            $application->registerAttributes($this->_Template_Contents);
            $return_html_code.=$this->mTmplFiller->fill("shipping_tester/", "api_result.tpl.html", array());
        };

        return $return_html_code;

    }

    function outResults()
    {
        global $application;

        $return_html_code="";

        if(!empty($this->test_results))
        {
            $this->_Template_Contents = array(
                "LabelResults" => $this->MessageResources->getMessage('LABEL_RESLUTS')
               ,"ApiResults"   => $this->outApiResults()
            );

            $application->registerAttributes($this->_Template_Contents);
            $return_html_code.=$this->mTmplFiller->fill("shipping_tester/", "results.tpl.html", array());
        };

        return $return_html_code;

    }

    function output()
    {
        global $application;

        if($this->ViewState["hasCloseScript"] == "true")
        {
            modApiFunc("application", "closeChild_UpdateParent");
            return;
        }

        if(isset($this->test_results))
        {
            foreach($this->test_results as $api_name => $api_res)
            {
                if(!empty($api_res['methods']))
                {
                    $per_item_shipping_cost_sum = $api_res['methods'][0]['shipping_cost']['PerItemShippingCostSum'];
                    $per_item_handling_cost_sum = $api_res['methods'][0]['shipping_cost']['PerItemHandlingCostSum'];
                    $total_handling_charge = $api_res['methods'][0]['shipping_cost']['TotalHandlingCharge'];
                    break;
                };
            };
        };

        $GSS = modApiFunc("Shipping_Cost_Calculator","getSettings");

        $template_contents = array(
                "HiddenArrayViewState"  => $this->outputViewState()
               ,"OrigAddress"   => $this->MessageResources->getMessage("LABEL_ORIG_ADDRESS")
               ,"OrigCity" => modApiFunc("Configuration","getTagValue","StoreOwnerCity")
               ,"OrigCountry" => modApiFunc("Configuration","getTagValue","StoreOwnerCountry")
               ,"OrigState" => modApiFunc("Configuration","getTagValue","StoreOwnerState")
               ,"OrigZip" => modApiFunc("Configuration","getTagValue","StoreOwnerPostcode")
               ,"DstAddress"      => $this->MessageResources->getMessage("LABEL_DEST_ADDRESS")
               ,"JavascriptCountriesAndStates" => $this->out_JS_CS()
               ,"DstCity" => HtmlForm::genInputTextField("125","DstCity","55",$this->POST["DstCity"])
               ,"DstCountry" => $this->outCountrySelect()
               ,"DstState" => $this->outStatesSelect()
               ,"DstZip" => HtmlForm::genInputTextField("125","DstZip","55",$this->POST["DstZip"])

               ,"LBL_ShoppingCart" => $this->MessageResources->getMessage('LABEL_CART')
               ,"LBL_Product" => $this->MessageResources->getMessage('LABEL_PRODUCT')
               ,"LBL_Qty" => $this->MessageResources->getMessage('LABEL_QTY')
               ,"LBL_Weight" => $this->MessageResources->getMessage('LABEL_WEIGHT')
               ,"LBL_Cost" => $this->MessageResources->getMessage('LABEL_COST')
               ,"LBL_Shipping_Charge" => $this->MessageResources->getMessage('LABEL_SHIPPING_CHARGE')
               ,"LBL_Handling_Charge" => $this->MessageResources->getMessage('LABEL_HANDLING_CHARGE')
               ,"LBL_Free_Shipping" => $this->MessageResources->getMessage('LABEL_FREE_SHIPPING')
               ,"LBL_Subtotal" => $this->MessageResources->getMessage('LABEL_SUBTOTAL')
               ,"LBL_TotalWeight" => $this->MessageResources->getMessage('LABEL_TOTAL_WEIGHT')
               ,"LBL_EmptyCart" => $this->MessageResources->getMessage('LABEL_EMPTY_CART')

               ,"LBL_FHFOO" => $this->MessageResources->getMessage('FREE_HANDLING')
               ,"LBL_FSFOO" => $this->MessageResources->getMessage('FREE_SHIPPING')
               ,"LBL_MinSC" => $this->MessageResources->getMessage('MINIMUM_SHIPPING_COST')
               ,"LBL_PerISCS" => $this->MessageResources->getMessage('PER_ITEM_SHIPPING_COST_SUM')
               ,"LBL_PerOSF" => $this->MessageResources->getMessage('PER_ORDER_SHIPPING_COST')
               ,"LBL_PerIHCS" => $this->MessageResources->getMessage('PER_ITEM_HANDLING_COST_SUM')
               ,"LBL_PerOHF" => $this->MessageResources->getMessage('PER_ORDER_HANDLING_COST')
               ,"LBL_TotalHC" => $this->MessageResources->getMessage('TOTAL_HANDLING_CHARGE')

               ,"GSS_FHFOO" => ($GSS["FH_OO"]!=""?modApiFunc("Localization","currency_format",$GSS["FH_OO"]):$this->MessageResources->getMessage('LBL_NA'))
               ,"GSS_FSFOO" => ($GSS["FS_OO"]!=""?modApiFunc("Localization","currency_format",$GSS["FS_OO"]):$this->MessageResources->getMessage('LBL_NA'))
               ,"GSS_MinSC" => modApiFunc("Localization","currency_format",$GSS["MIN_SC"])
               ,"Cart_PerISCS" => (isset($per_item_shipping_cost_sum)?modApiFunc("Localization","currency_format",$per_item_shipping_cost_sum):$this->MessageResources->getMessage('LBL_NA'))
               ,"GSS_PerOSF" => ($GSS["PO_SC_TYPE"]=="A"?modApiFunc("Localization","currency_format",$GSS["PO_SC"]):$GSS["PO_SC"]."%")
               ,"Cart_PerIHCS" => (isset($per_item_handling_cost_sum)?modApiFunc("Localization","currency_format",$per_item_handling_cost_sum):$this->MessageResources->getMessage('LBL_NA'))
               ,"GSS_PerOHF" => modApiFunc("Localization","currency_format",$GSS["PO_HC"])
               ,"Cart_TotalHC" => (isset($total_handling_charge)?modApiFunc("Localization","currency_format",$total_handling_charge):$this->MessageResources->getMessage('LBL_NA'))

               ,"Cart_Subtotal" => modApiFunc("Localization","currency_format",$this->POST["Cart"]["subtotal"])
               ,"Cart_TotalWeight" => $this->POST["Cart"]["total_weight"]

               ,"genJSproducts" => $this->outJSproducts()

               ,"WeightSymbol" => modApiFunc('Localization','getUnitTypeValue','weight')

               ,"Results" => $this->outResults()
        );

        $this->_Template_Contents=$template_contents;
        $application->registerAttributes($this->_Template_Contents);
        return $this->mTmplFiller->fill("shipping_tester/", "window.tpl.html",array());
    }

    function getTag($tag)
    {
        global $application;
        $value = getKeyIgnoreCase($tag, $this->_Template_Contents);
        return $value;
    }

    var $_Template_Contents;
    var $MessageResources;
    var $POST;
    var $ViewState;

    var $test_results;

}

?>