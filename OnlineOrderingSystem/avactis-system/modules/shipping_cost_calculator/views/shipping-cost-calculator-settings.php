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
class ShippingCostCalculatorSettings
{
    function ShippingCostCalculatorSettings()
    {
        global $application;
        $this->MessageResources = &$application->getInstance('MessageResources',"shipping-cost-calculator-messages", "AdminZone");
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

        loadCoreFile('html_form.php');
    }

    function initFormData()
    {
        $this->POST = modApiFunc("Shipping_Cost_Calculator", "getSettings");
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

        $this->POST =
            array(
                "PO_SC"      => $SessionPost["PO_SC"],
                "PO_SC_TYPE" => $SessionPost["PO_SC_TYPE"],
                "PO_HC"      => $SessionPost["PO_HC"],
                "MIN_SC"     => $SessionPost["MIN_SC"],
                "FS_OO"      => $SessionPost["FS_OO"],
                "FH_OO"      => $SessionPost["FH_OO"],
                'FS_METHOD_LABEL_VALUE' => $SessionPost["FS_METHOD_LABEL_VALUE"],
                'FS_MODE'    => $SessionPost["FS_MODE"],
                'FS_PLACING' => $SessionPost["FS_PLACING"]
                ,"FS_COUNTRY_HIDE" => $SessionPost["FS_COUNTRY_HIDE"]
                ,"FS_COUNTRY_ASSUME" => $SessionPost["FS_COUNTRY_ASSUME"]
                ,"FS_STATE_HIDE" => $SessionPost["FS_STATE_HIDE"]
                ,"FS_STATE_ASSUME" => $SessionPost["FS_STATE_ASSUME"]
                ,"FS_ZIP_HIDE" => $SessionPost["FS_ZIP_HIDE"]
                ,"FS_ZIP_ASSUME" => $SessionPost["FS_ZIP_ASSUME"]
            );
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

    function outputFsModeOptions()
    {
        $selected_zero = '';
        $selected_add = ' selected';
        if ($this->POST['FS_MODE'] == FS_MODE_ZERO)
        {
            $selected_zero = ' selected';
            $selected_add = '';
        }
        return "<option $selected_zero value='".FS_MODE_ZERO."'>".getMsg('SCC', 'FS_MODE_OPTION_0')."<option $selected_add value='".FS_MODE_ADD."'>".getMsg('SCC', 'FS_MODE_OPTION_1');
    }

    function outputFsPlacingOptions()
    {
        $selected_top = '';
        $selected_bottom = ' selected';
        if ($this->POST['FS_PLACING'] == FS_PLACING_TOP)
        {
            $selected_top = ' selected';
            $selected_bottom = '';
        }
        return "<option $selected_top value='".FS_PLACING_TOP."'>".getMsg('SCC', 'FS_PLACING_OPTION_TOP')."<option $selected_bottom value='".FS_PLACING_BOTTOM."'>".getMsg('SCC', 'FS_PLACING_OPTION_BOTTOM');
    }

    function output()
    {
        global $application;

        if($this->ViewState["hasCloseScript"] == "true")
        {
            modApiFunc("application", "UpdateParent");
        }

        # Per order shipping fee types
        $po_sc_types = array (
            "select_name" => "PO_SC_TYPE",
            "selected_value" => $this->POST["PO_SC_TYPE"],
            "values" => array(
                array (
                    "value" => "A",
                    "contents" => modApiFunc("Localization","getCurrencySign")
                ),
                array (
                    "value" => "P",
                    "contents" => "%"
                ),
            )
        );

        $request = new Request();
        $request->setView('AddFsRule');
        $AddFsRuleHref = $request->getURL();

        $request->setView(CURRENT_REQUEST_URL);
        $request->setAction("DeleteFsRule");
        $FsRuleDeleteActionUrl = $request->getURL();

	$request->setView(CURRENT_REQUEST_URL);
	$request->setAction('update_scc_settings');
	$updateSccSettingsActionUrl = $request->getURL();

        $currentCountry = isset($this->POST["FS_COUNTRY_ASSUME"]) ? $this->POST["FS_COUNTRY_ASSUME"] : '';
        $currentCountry = ($this->POST['FS_COUNTRY_HIDE'] == '1') ? $currentCountry : '-3';
        $currentState = isset($this->POST["FS_STATE_ASSUME"]) ? $this->POST["FS_STATE_ASSUME"] : '';

        $cOpts = modApiFunc("Checkout", "genCountrySelectList", $currentCountry, false, true);
        $sOpts = modApiFunc("Checkout", "genStateSelectList",   $currentState,   $currentCountry, true);

        $template_contents = array(
                "HiddenArrayViewState"  => $this->outputViewState()
               ,"Errors"                => $this->outputErrors()
               ,"SSC_Header"      => $this->MessageResources->getMessage("SSC_HEADER")
               ,"LabelSettings"   => $this->MessageResources->getMessage("LABEL_SETTINGS")

               ,"POSC_FieldName"  => $this->MessageResources->getMessage("PER_ORDER_SHIPPING_COST")
               ,"POSC_FieldValue" => $this->POST["PO_SC"]
               ,"POSC_Type"       => HtmlForm::genDropdownSingleChoice($po_sc_types)
               ,"POHC_FieldName"  => $this->MessageResources->getMessage("PER_ORDER_HANDLING_COST")
               ,"POHC_FieldValue" => $this->POST["PO_HC"]
               ,"MINSC_FieldName"  => $this->MessageResources->getMessage("MINIMUM_SHIPPING_COST")
               ,"MINSC_FieldValue" => $this->POST["MIN_SC"]
               ,"FSOO_FieldName"  => $this->MessageResources->getMessage("FREE_SHIPPING")
               ,"FSOO_FieldValue" => $this->POST["FS_OO"]
               ,"FHOO_FieldName"  => $this->MessageResources->getMessage("FREE_HANDLING")
               ,"FHOO_FieldValue" => $this->POST["FH_OO"]

               ,"JavascriptSynchronizeCountriesAndStatesLists" => modApiFunc("Location", "getJavascriptCountriesStatesArrays", true, array(), array(), true, true) . modApiFunc("Checkout", "getJavascriptSynchronizeCountriesAndStatesLists") .
                    //Combine all the OnChange instructions and add them to body.onload()
                    "<SCRIPT LANGUAGE=\"JavaScript\">" . "\n" .
                    "<!--\n" . "\n" .
                    "var onload_bak = window.onload;" . "\n" .
                    "window.onload = function()" . "\n" .
                    "{" . "\n" .
                    "    if(onload_bak){onload_bak();}" . "\n" .
                    "    refreshStatesList('DstCountry', 'DstState_menu_select', 'stub_state_text_input');" . //$onChangeStatements
                    "}" . "\n" .
                    "//-->" . "\n" .
                    "</SCRIPT>" . "\n"
                ,"CountriesOptions" => $cOpts
                ,"StatesOptions"    => $sOpts
                ,"ZipValue"         => $this->POST["FS_ZIP_ASSUME"]

                ,"CountryHideSelected"      => ($this->POST['FS_COUNTRY_HIDE'] == '1') ? 'selected' : ''
                ,"CountryDoNotHideSelected" => ($this->POST['FS_COUNTRY_HIDE'] == '0') ? 'selected' : ''
                ,"StateHideSelected"        => ($this->POST['FS_STATE_HIDE'] == '1') ? 'selected' : ''
                ,"StateDoNotHideSelected"   => ($this->POST['FS_STATE_HIDE'] == '0') ? 'selected' : ''
                ,"ZipHideSelected"          => ($this->POST['FS_ZIP_HIDE'] == '1') ? 'selected' : ''
                ,"ZipDoNotHideSelected"     => ($this->POST['FS_ZIP_HIDE'] == '0') ? 'selected' : ''

                ,"TrCountryDisplay"  => ($this->POST['FS_COUNTRY_HIDE'] == '0') ? 'style="display: none"' : ''
                ,"TrStateDisplay"  => ($this->POST['FS_STATE_HIDE'] == '0') ? 'style="display: none"' : ''
                ,"TrZipDisplay"  => ($this->POST['FS_ZIP_HIDE'] == '0') ? 'style="display: none"' : ''

               ,'FS_METHOD_LABEL_VALUE' => $this->POST['FS_METHOD_LABEL_VALUE']
               ,'FS_MODE_OPTIONS'       => $this->outputFsModeOptions()
               ,'FS_PLACING_OPTIONS'    => $this->outputFsPlacingOptions()

               ,"Alert_001" => $this->MessageResources->getMessage("ALERT_001")
               ,"Alert_002" => $this->MessageResources->getMessage("ALERT_002")
               ,"Alert_003" => $this->MessageResources->getMessage("ALERT_003")
               ,"Alert_004" => $this->MessageResources->getMessage("ALERT_004")
               ,"Alert_005" => $this->MessageResources->getMessage("ALERT_005")

               ,"CurrecySign" => modApiFunc("Localization","getCurrencySign")
               ,"CostFormat" => modApiFunc("Localization", "format_settings_for_js", "currency")
	       ,"SaveSelectedShippingSettingsHref"=>$updateSccSettingsActionUrl
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
        return $output.$this->mTmplFiller->fill("shipping_cost_calculator/", "settings.tpl.html",array());
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

    var $ErrorsArray;
    var $ErrorMessages;

    var $_error_index;
    var $_error;

}


?>