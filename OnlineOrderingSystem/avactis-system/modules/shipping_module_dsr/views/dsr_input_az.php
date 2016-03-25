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
 * dsr_input_az view
 *
 * @package ShippingModuleDSR
 * @author Egor V. Derevyankin
 */
class dsr_input_az  extends pm_sm_input_az
{
	//------------------------------------------------
	//               PUBLIC DECLARATION
	//------------------------------------------------

	/**#@+
	 * @access public
	 */

	/**
	 * Modules_Manager constructor.
	 */
	function dsr_input_az()
    {
		global $application;
        $request = $application->getInstance("Request");
		$this->MessageResources = &$application->getInstance('MessageResources',"shipping-module-dsr-messages", "AdminZone");
        $this->Hints = &$application->getInstance('Hint');
		$this->shipping_methods = modApiFunc("Shipping_Module_DSR", "getShippingMethods");

		$this->mTmplFiller = & $application->getInstance('TmplFiller');

		if (!$request->getValueByKey("rates_method_id", NULL))
		{
		    $this->viewType = "list";
		}
		else
		{
		    $this->viewType = "rate";
		}

		if (modApiFunc("Session", "is_Set", "SessionPost"))
        {
			$this->copyFormData();
			modApiFunc('Session', 'un_Set', 'SessionPost');
		}
        else
        {
			$this->initFormData();
		}
	}

	/**
         * Copies data from the global POST to the local POST array.
	 */
	function copyFormData()
    {
		// eliminate copying on construction
		$SessionPost = modApiFunc("Session", "get", "SessionPost");
        if ($this->viewType == "rate")
        {
            $settings = modApiFunc("Shipping_Module_DSR", "getSettings");
            $SessionPost["status"] = modApiFunc("Shipping_Module_DSR", "isActive");
            $SessionPost["RateUnit"] = $settings["RATE_UNIT"];
        }
		$this->ViewState = $SessionPost["ViewState"];
		//Remove some data, that should not be resent to action, from ViewState.
		if (isset ($this->ViewState["ErrorsArray"]) && count($this->ViewState["ErrorsArray"]) > 0)
        {
			$this->ErrorsArray = $this->ViewState["ErrorsArray"];
			unset ($this->ViewState["ErrorsArray"]);
		}

		$this->POST = array (
                "status" => ($SessionPost["status"] == "active") ? true : false,
		        "RateUnit" => $SessionPost["RateUnit"],
                "SMethodsAvailable" => array ()
           );

		if (isset ($SessionPost["DstCountry"]))
			$this->POST["DstCountry"] = $SessionPost["DstCountry"];
	    else
	        $this->POST["DstCountry"] = "";
		if (isset ($SessionPost["DstState_menu_select"]))
			$this->POST["DstState_menu_select"] = $SessionPost["DstState_menu_select"];
	    else
	        $this->POST["DstState_menu_select"] = "";
		if (isset ($SessionPost["DstState_text_div"]))
			$this->POST["DstState_text_div"] = $SessionPost["DstState_text_div"];

		if (isset ($SessionPost["NewRate"]))
			$this->POST["NewRate"] = $SessionPost["NewRate"];
		else
			$this->POST["NewRate"] = array (
                "wrange_from" => "0.00",
                "wrange_to" => "0.00",
                "bcharge_abs" => "0.00",
                "bcharge_perc" => "0.00",
                "acharge_pi_abs" => "0.00",
                "acharge_pwu_abs" => "0.00",
                "acharge_pi_perc" => "0.00",
                "acharge_pwu_perc" => "0.00",
             );

		if (is_array($this->shipping_methods) and !empty ($this->shipping_methods))
			foreach ($this->shipping_methods as $key => $method)
			{
			    if (isset ($SessionPost["SMethodsAvailable"][$method['id']]))
			    {
				    $this->POST["SMethodsAvailable"][$method['id']] = ($SessionPost["SMethodsAvailable"][$method['id']] == 'on') ? 'Y' : "N";
			    }
			    else
			    {
			        $this->POST["SMethodsAvailable"][$method['id']] = 'N';
			    }
			}

	}

	/**
	 * Fills the local POST array by data from the DB.
	 */
	function initFormData()
    {
		$this->POST = array ();
        $settings = modApiFunc("Shipping_Module_DSR", "getSettings");
        foreach ($settings as $key => $value)
        {
            switch($key)
            {
                case "RATE_UNIT": $this->POST["RateUnit"] = $value; break;
            }
        }
		$this->POST["status"] = modApiFunc("Shipping_Module_DSR", "isActive");
		$this->ViewState = array (
                "hasCloseScript" => "false",
                "FormSubmitValue" => "save"
               );

        $this->POST["DstCountry"] = "";
        $this->POST["DstState_menu_select"] = "";

		$this->POST["NewRate"] = array (
                "wrange_from" => "0.00",
                "wrange_to" => "0.00",
                "bcharge_abs" => "0.00",
                "bcharge_perc" => "0.00",
                "acharge_pi_abs" => "0.00",
                "acharge_pwu_abs" => "0.00",
                "acharge_pi_perc" => "0.00",
                "acharge_pwu_perc" => "0.00",
              );

		$this->POST["SMethodsAvailable"] = array ();
		if (is_array($this->shipping_methods) and !empty ($this->shipping_methods))
			foreach ($this->shipping_methods as $key => $method)
				$this->POST["SMethodsAvailable"][$method["id"]] = $method["available"];

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
			$retval .= "<input type=\"hidden\" name=\"ViewState[".$key."]\" value=\"".$value."\">";
		}
		return $retval;
	}

	/**
	 * @return HTML code for the errors
	 */
	function outputErrors()
    {
		global $application;

		$return_html_code = "";

		foreach ($this->ErrorsArray as $index => $value)
        {
			$this->_Template_Contents = array (
                    "ErrorIndex" => $index +1,
                    "Error" => $this->MessageResources->getMessage($value)
                  );
			$application->registerAttributes($this->_Template_Contents);
			$return_html_code .= $this->mTmplFiller->fill("shipping_module_dsr/", "error.tpl.html", array ());
		};

		return $return_html_code;
	}

	function outputTopErrors()
    {
		if (!is_array($this->ErrorsArray) || sizeof($this->ErrorsArray) == 0)
        {
			return;
		}

		global $application;

		$return_html_code = "";

		$this->_Template_Contents = array ("Errors" => $this->outputErrors());
		$application->registerAttributes($this->_Template_Contents);
		$return_html_code .= $this->mTmplFiller->fill("shipping_module_dsr/", "errors.tpl.html", array ());

		return $return_html_code;
	}

	/**
	 * @return HTML code for the module status
	 */
	function outputStatus()
    {
		global $application;
		$retval = "";
		$status = $this->POST["status"];
		$this->_Template_Contents = array (
                "Active" => ($status) ? "checked" : "",
                "ActiveMessage" => $this->MessageResources->getMessage('MODULE_STATUS_ACTIVE'),
                "Inactive" => ($status) ? "" : "checked",
                "InactiveMessage" => $this->MessageResources->getMessage('MODULE_STATUS_INACTIVE')
              );
		$application->registerAttributes($this->_Template_Contents);
		$retval .= $this->mTmplFiller->fill("shipping_module_dsr/", "status.tpl.html", array ());
		return $retval;

	}

	function outputJSmethodsIDs()
    {
		$return_html_code = "var MethodsIDs = new Array();\n";
		for ($i = 0; $i < count($this->shipping_methods); $i ++)
			$return_html_code .= "MethodsIDs[$i]=".$this->shipping_methods[$i]['id'].";\n";

		return $return_html_code;
	}

	/**
	 * @return HTML code for the 'available' checkbox for the shipping method
	 */
	function outputAvailableCheckBox($checkbox_id, $available)
    {
        // not a checkbox since Ticket #1846
//		$is_checked = ($available == "Y" ? "checked" : "");
//		$return_html_code = "<input type=\"checkbox\" id=\"MA-$checkbox_id\" name=\"SMethodsAvailable[$checkbox_id]\" $is_checked>";
        $selected_yes = "";
        $selected_no = "selected";
        if ($available == "Y")
        {
            $selected_yes = "selected";
            $selected_no = "";
        }
        $return_html_code = "<select class='form-control input-sm input-xsmall' id='MA-$checkbox_id' name=\"SMethodsAvailable[$checkbox_id]\">"
            ."<option value='on' $selected_yes>".$this->MessageResources->getMessage('LBL_YES')."</option>"
            ."<option value='off' $selected_no>".$this->MessageResources->getMessage('LBL_NO')."</option>"
            ."</select>";
		return $return_html_code;
	}

	function outputDeleteCheckbox($checkbox_id)
    {
		$return_html_code = "<input class='form-control input-sm' type=\"checkbox\" id=\"MD-$checkbox_id\" name=\"SMethodsDelete[$checkbox_id]\">";
		return $return_html_code;
	}

	/**
	 * @return HTML code for the list of the shipping methods
	 */
	function outputShippingMethods()
    {
		global $application;

		if (empty ($this->shipping_methods) or !is_array($this->shipping_methods))
			return "<tr><td colspan='3' height='25px'>".$this->MessageResources->getMessage('LABEL_NO_SHIPPING_METHODS')."</td></tr>";

		$return_html_code = "";

		$request = new Request();
        $request->setView(CURRENT_REQUEST_URL);

		foreach ($this->shipping_methods as $method_key => $method_info)
        {
			$request->setKey("rates_method_id", $method_info['id']);
			$method_rates_link = $request->getURL();

			$this->_Template_Contents = array (
                "CycleColor" => ($method_key % 2 ? "EEEEEE" : "FFFFFF"),
			    "lbl_Rates"  => $this->MessageResources->getMessage('LABEL_VIEW_RATES'),
                "SMethodRatesLink" => $method_rates_link,
                "SMethodName" => HtmlForm :: genInputTextField(70, "method_name[{$method_info['id']}]", 50, prepareHTMLDisplay($method_info['method_name']), "class='form-control input-sm input-large' style='display:inline;'"),
                "SMethodAvailableCheckBox" => $this->outputAvailableCheckBox($method_info['id'], $this->POST["SMethodsAvailable"][$method_info['id']]),
                "SMethodDeleteCheckbox" => $this->outputDeleteCheckbox($method_info['id'])
              );
			$application->registerAttributes($this->_Template_Contents);
			$return_html_code .= $this->mTmplFiller->fill("shipping_module_dsr/", "methods_list_item.tpl.html", array ());
		}

		unset ($request);
		return $return_html_code;
	}

	function out_JS_CS()
    {
		return modApiFunc("Checkout", "getJavascriptSynchronizeCountriesAndStatesLists").
               modApiFunc("Location", "getJavascriptCountriesStatesArrays").
               "<SCRIPT LANGUAGE=\"JavaScript\">"."\n"."<!--\n"."\n".
               "var onload_bak = window.onload;"."\n".
               "window.onload = function()"."\n".
               "{"."\n"."    if(onload_bak){onload_bak();}"."\n".
               "refreshStatesList('DstCountry', 'DstState_menu_select', 'DstState_text_div');"."}"."\n".
               "//-->"."\n"."</SCRIPT>"."\n";
	}

	function outCountrySelect()
    {
		$countries = modApiFunc("Location", "getCountries");

		$countries_select = array (
                "select_name" => "DstCountry",
                "class" => "form-control input-sm input-small",
                "onChange" => "refreshStatesList('DstCountry', 'DstState_menu_select', 'DstState_text_div');",
                "id" => "DstCountry",
                "values" => array ()
             );

		if (isset ($this->POST["DstCountry"]))
			$countries_select["selected_value"] = $this->POST["DstCountry"];

		foreach ($countries as $cid => $cname)
			$countries_select["values"][] = array ("value" => $cid, "contents" => $cname);

		return HtmlForm :: genDropdownSingleChoice($countries_select);

	}

	function outStatesSelect()
    {
		if (isset ($this->POST["DstCountry"]))
			$states = modApiFunc("Location", "getStates", $this->POST["DstCountry"]);

		$states_select = array (
                "select_name" => "DstState_menu_select",
                "id" => "DstState_menu_select",
                "values" => array ()
             );

		if (isset ($this->POST["DstState_menu_select"]))
			$states_select["selected_value"] = $this->POST["DstState_menu_select"];

		if (isset ($states) and !empty ($states))
			foreach ($states as $sid => $sname)
				$states_select["values"][] = array ("value" => $sid, "contents" => $sname);

		$state_name = (!empty ($this->POST["DstState_text_div"]) ? $this->POST["DstState"] : "");

		$html_ss = HtmlForm :: genDropdownSingleChoice($states_select);
		$html_div = "<div id=\"DstState_test_div\"><input class='form-control input-sm input-large' type=\"text\" id=\"DstState_text_div\" ".
                    HtmlForm :: genInputTextField("125", "DstState_text_div", "55", $state_name)."></div>";

		return $html_ss.$html_div;
	}

	function outputRatesList($method_id)
    {
		global $application;

		$return_html_code = "";

		$rates = modApiFunc("Shipping_Module_DSR", "getShippingRates", $method_id);

		if (!empty ($rates))
        {
			foreach ($rates as $rate_key => $rate_info)
            {
				$this->_Template_Contents = array (
                        "CycleColor" => ($rate_key % 2 ? "EEEEEE" : "FFFFFF"),
                        "SRDeleteCheckbox" => "<input class='form-control input-sm' type=\"checkbox\" name=\"RatesDelete[".$rate_info["rate_id"]."]\" id=\"RD-$rate_key\">",
                        "SRDstCountry" => modApiFunc("Location", "getCountry", $rate_info["dst_country"]),
                        "SRDstState" => modApiFunc("Location", "getState", $rate_info["dst_state"]),
                        "WeightSymbol" => $this->CurrentRateUnitSign,
                        "SRWrangeFrom" => sprintf("%.2f",$rate_info["wrange_from"]),
                        "SRWrangeTo" => sprintf("%.2f",$rate_info["wrange_to"]),
                        "SRBaseChargeAbs" => modApiFunc("Localization","currency_format",$rate_info["bcharge_abs"]),
                        "SRBaseChargePerc" => ($rate_info["bcharge_perc"] > 0) ? "+ " . sprintf("%.2f",$rate_info["bcharge_perc"]) . "% * Subtotal" : '&nbsp;',
                        "lbl_PerItem" => $this->MessageResources->getMessage('LBL_PER')." ".$this->MessageResources->getMessage('LBL_ITEM'),
                        "lbl_PerWeightUnit" => $this->MessageResources->getMessage('LBL_PER')." ".modApiFunc('Localization', 'getUnitTypeValue', 'weight'),
                        "SRAddChargePIabs" => ($rate_info["acharge_pi_abs"] > 0) ? modApiFunc("Localization","currency_format",$rate_info["acharge_pi_abs"]) . " * Items qty" : '&nbsp;',
                        "SRAddChargePIperc" => sprintf("%.2f",$rate_info["acharge_pi_perc"]),
                        "SRAddChargePWUabs" => ($rate_info["acharge_pwu_abs"] > 0) ? "+ " . modApiFunc("Localization","currency_format",$rate_info["acharge_pwu_abs"]) . " * Pack weight" : '&nbsp;',
                        "SRAddChargePWUperc" => sprintf("%.2f",$rate_info["acharge_pwu_perc"]),
                 );
				$application->registerAttributes($this->_Template_Contents);
				$return_html_code .= $this->mTmplFiller->fill("shipping_module_dsr/", "one_rate.tpl.html", array ());
                $return_html_code.="<script language='JavaScript'> RatesIDs[$rate_key]=$rate_info[rate_id] </script>";
			};
		}
        else
			$return_html_code .= $this->MessageResources->getMessage('LABEL_NO_SHIPPING_RATES');

		return $return_html_code;
	}

	function getFormula()
	{
	    $ret = str_replace("{WEIGHT_UNIT}",
	       modApiFunc("Localization", "getUnitTypeValue", "weight"),
	       $this->MessageResources->getMessage('MODULE_DSR_FORMULA_SHORT'));

	    return $ret;
	}

	function outputRatesTable()
    {
		global $application;

		if (!isset ($_REQUEST["rates_method_id"]))
			return "";

		if (!modApiFunc("Shipping_Module_DSR", "isValidShippingMethodId", $_REQUEST["rates_method_id"]))
			return "";

		$method_id = $_REQUEST["rates_method_id"];

		$return_html_code = "";
		$method_info = modApiFunc("Shipping_Module_DSR", "getShippingMethodInfo", $method_id);

		$this->_Template_Contents = array (
            "RatesHeader" => str_replace("%METHOD_NAME%", $method_info["method_name"], $this->MessageResources->getMessage('RATES_HEADER')),
            "lbl_Destination" => $this->MessageResources->getMessage('LBL_DESTINATION'),
            "lbl_WeightRange" => $this->CurrentRateUnit . $this->MessageResources->getMessage('LBL_RANGE'),
            "lbl_BaseCharge" => $this->MessageResources->getMessage('LBL_BASE_CHARGE'),
            "lbl_AdditionalCharge" => $this->MessageResources->getMessage('LBL_ADDITIONAL_CHARGE'),
            "lbl_PerItem" => $this->MessageResources->getMessage('LBL_PER')." ".$this->MessageResources->getMessage('LBL_ITEM'),
            "lbl_PerWeightUnit" => $this->MessageResources->getMessage('LBL_PER')." ".modApiFunc('Localization', 'getUnitTypeValue', 'weight'),
            "RatesList" => $this->outputRatesList($method_id),
            "WRangeFromField" => HtmlForm :: genInputTextField(10, "NewRate[wrange_from]", 5, ($this->POST["NewRate"]["wrange_from"])),
            "WRangeToField" => HtmlForm :: genInputTextField(10, "NewRate[wrange_to]", 5, ($this->POST["NewRate"]["wrange_to"])),
            "BChargeAbsField" => HtmlForm :: genInputTextField(10, "NewRate[bcharge_abs]", 5, ($this->POST["NewRate"]["bcharge_abs"])),
            "BChargePercField" => HtmlForm :: genInputTextField(10, "NewRate[bcharge_perc]", 5, ($this->POST["NewRate"]["bcharge_perc"])),
            "AChargePerItemAbsField" => HtmlForm :: genInputTextField(10, "NewRate[acharge_pi_abs]", 5, ($this->POST["NewRate"]["acharge_pi_abs"])),
            "AChargePerWUAbsField" => HtmlForm :: genInputTextField(10, "NewRate[acharge_pwu_abs]", 5, ($this->POST["NewRate"]["acharge_pwu_abs"])),
//            "AChargePerItemPercField" => HtmlForm :: genInputTextField(10, "NewRate[acharge_pi_perc]", 5, ($this->POST["NewRate"]["acharge_pi_perc"])),
//            "AChargePerWUPercField" => HtmlForm :: genInputTextField(10, "NewRate[acharge_pwu_perc]", 5, ($this->POST["NewRate"]["acharge_pwu_perc"])),
            "MethodID" => $method_id,
            "WeightSymbol" => $this->CurrentRateUnitSign,
            "CurrencySymbol" => modApiFunc('Localization', 'getUnitTypeValue', 'currency'),
//            "JSCountriesAndStates" => $this->out_JS_CS(),
           "JavascriptSynchronizeCountriesAndStatesLists" => modApiFunc("Location", "getJavascriptCountriesStatesArrays", true, array(), array(), true, true) . modApiFunc("Checkout", "getJavascriptSynchronizeCountriesAndStatesLists") .
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
                                    "</SCRIPT>" . "\n",
            "CountriesOptions"      => modApiFunc("Checkout", "genCountrySelectList", $this->POST["DstCountry"], false, true),
            "StatesOptions"         => modApiFunc("Checkout", "genStateSelectList", $this->POST["DstState_menu_select"], $this->POST["DstCountry"], true),
//            "DstCountrySelect" => $this->outCountrySelect(),
//            "DstStateSelect" => $this->outStatesSelect(),
            "lbl_CloneSelected" => $this->MessageResources->getMessage('LBL_DELETE_SELECTED'),
		    "lbl_RenameSelected" => $this->MessageResources->getMessage('LBL_RENAME_SELECTED'),
            "lbl_DeleteSelected" => $this->MessageResources->getMessage('LBL_DELETE_SELECTED')

		    ,"DSR_Formula" => $this->getFormula()
		    ,"RatesLink"  => "onClick=\"javascript: window.location='<?php  EditLink(); ?>'\">"
		);

		$application->registerAttributes($this->_Template_Contents);
		$return_html_code .= $this->mTmplFiller->fill("shipping_module_dsr/", "rates_table.tpl.html", array ());

		return $return_html_code;
	}

    /**
     * Output a list of rate units.
     */
    function outputRateUnits()
    {
        $retval = "";
        $rate_units = array(
                 "weight" => "Weight"
                ,"currency" => "Subtotal"
                ,"item" => "Items Qty"
            );
        foreach ($rate_units as $key => $value)
        {
            $retval .= "<option value=\"" . $key . "\"";
            if ($key == $this->POST["RateUnit"])
            {
                $retval.= "SELECTED";
                $this->CurrentRateUnit = $value;
                $this->CurrentRateUnitValue = $key;
                $this->CurrentRateUnitValueId = $key;
                $this->CurrentRateUnitSign = modApiFunc("Localization", "getUnitTypeValue", $this->CurrentRateUnitValue);
            }
            $retval.= ">". $value ."</option>";
        }
        return $retval;
    }

	/**
	 * @return HTML code for this view
	 */
	function output()
    {
        global $application;
        loadCoreFile('html_form.php');
        $HtmlForm1 = new HtmlForm();
        if ($this->ViewState["hasCloseScript"] == "true")
        {
        	modApiFunc("application", "closeChild_UpdateParent");
        	return;
        }
        $outputed_rate_units = $this->outputRateUnits();

        $request = new Request();
        $request->setView('CheckoutShippingModuleSettings');
        $request->setAction("update_dsr_settings");
        $form_action = $request->getURL();

        $request = new Request();
        $request->setView(CURRENT_REQUEST_URL);
        $return_link = $request->getURL();

        $template_contents = array (
            "EditSMForm" => $HtmlForm1->genForm($form_action, "POST", "EditSMForm"),
            "HiddenArrayViewState" => $this->outputViewState(),
            "ModuleType" => $this->MessageResources->getMessage('MODULE_TYPE'),
            "ModuleName" => $this->MessageResources->getMessage('MODULE_NAME'),
            "Subtitle" => $this->MessageResources->getMessage('FORM_SUBTITLE'),
            "TopErrors" => $this->outputTopErrors(),
            "ModuleStatusFieldName" => $this->MessageResources->getMessage('MODULE_STATUS_FIELD_NAME'),
            "ModuleStatusField" => $this->outputStatus(),
            "ModuleNameFieldName" => $this->MessageResources->getMessage('MODULE_NAME_FIELD_NAME'),
            "ModuleNameField" => $this->MessageResources->getMessage('MODULE_NAME'),
            "ModuleDescrFieldName" => $this->MessageResources->getMessage('MODULE_DESCR_FIELD_NAME'),
            "ModuleDescrFieldValue" => $this->MessageResources->getMessage('MODULE_DESCR'),
            "ModuleRateUnitFieldName" => $this->MessageResources->getMessage('MODULE_RATE_UNIT_FIELD_NAME'),
            "ModuleRateUnitDescrFieldName" => $this->Hints->getHintLink(array('MODULE_RATE_UNIT_FIELD_NAME','shipping-module-dsr-messages')),
            "RateUnits" => $outputed_rate_units,
            "RateUnitUnitsValue"    => $this->CurrentRateUnitSign,
            "UnitType"              => $this->CurrentRateUnitValue,
            "JSmethodsIDs" => $this->outputJSmethodsIDs(),
            "MethodsHeader" => $this->MessageResources->getMessage('METHODS_HEADER'),
            "MethodsList" => $this->outputShippingMethods(),
            "LabelSMName" => $this->MessageResources->getMessage('LABEL_SHIPPING_METHOD_NAME'),
            "LabelSMDestination" => $this->MessageResources->getMessage('LABEL_SHIPPING_METHOD_DESTINATION'),
            "LabelSMAvailable" => $this->MessageResources->getMessage('LABEL_SHIPPING_METHOD_AVAILABLE'),
            "MethodsCount" => count($this->shipping_methods),
            "lbl_DeleteSelected" => $this->MessageResources->getMessage('LBL_DELETE_SELECTED'),
            "lbl_CloneSelected" => $this->MessageResources->getMessage('LBL_CLONE_SELECTED'),
            "lbl_RenameSelected" => $this->MessageResources->getMessage('LBL_RENAME_SELECTED'),
            "lbl_NewMethodName" => $this->MessageResources->getMessage('LBL_NEW_METHOD_NAME'),
            "RatesTable" => $this->outputRatesTable(),
            "Alert_001" => $this->MessageResources->getMessage('ALERT_001'),
            "Alert_002" => $this->MessageResources->getMessage('ALERT_002'),
            "Alert_003" => $this->MessageResources->getMessage('ALERT_003'),
            "Alert_004" => $this->MessageResources->getMessage('ALERT_004'),
            "Alert_005" => $this->MessageResources->getMessage('ALERT_005'),
            "HintLink_MDESCR" => $this->Hints->getHintLink(array('MODULE_DESCR_FIELD_NAME','shipping-module-dsr-messages'))
             ,"DSR_Return_Link" => "location.href='{$return_link}'"
        );

        $this->_Template_Contents = $template_contents;
        $application->registerAttributes($this->_Template_Contents);

        if (!$request->getValueByKey("rates_method_id", NULL))
        {
            return $this->mTmplFiller->fill("shipping_module_dsr/", "list.tpl.html", array ());
        }
        else
        {
            return $this->mTmplFiller->fill("shipping_module_dsr/", "rates-list.tpl.html", array ());
        }

	}

	/**
	 * @param $tag name of the requested tag
	 * @return value of the tag
	 */
	function getTag($tag)
    {
		global $application;
		$value = getKeyIgnoreCase($tag, $this->_Template_Contents);
		return $value;
	}

	/**#@-*/

	//------------------------------------------------
	//              PRIVATE DECLARATION
	//------------------------------------------------

	/**#@+
	 * @access private
	 */

	var $POST;
	var $ViewState;
    var $CurrentRateUnit;
    var $CurrentRateUnitValue;
    var $CurrentRateUnitValueId;
    var $CurrentRateUnitSign;

	/**
	 * List of error ids. Comes from action.
	 */
	var $ErrorsArray;
	var $ErrorMessages;

	var $_Template_Contents;

	var $MessageResources;
	var $_error_index;
	var $_error;

	var $shipping_methods;
	var $viewType;

	/**#@-*/
}
?>