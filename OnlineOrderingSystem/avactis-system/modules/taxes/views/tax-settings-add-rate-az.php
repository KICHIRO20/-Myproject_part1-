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
 * Taxes Module, AddTaxRate View.
 *
 * @package Taxes
 * @author Alexey Florinsky, Alexander Girin
 */
class AddTaxRate
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * A constructor.
     */
    function AddTaxRate ()
    {
        global $application;
        $this->MessageResources = &$application->getInstance('MessageResources');

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
        $this->ViewState = $SessionPost["ViewState"];

        //Remove some data, that should not be sent to action one more time, from ViewState.
        if(isset($this->ViewState["ErrorsArray"]) &&
           count($this->ViewState["ErrorsArray"]) > 0)
        {
            $this->ErrorsArray = $this->ViewState["ErrorsArray"];
            unset($this->ViewState["ErrorsArray"]);
        }
        $this->POST  =
            array(
                "Id"                    => isset($SessionPost["Id"])? $SessionPost["Id"]:""
               ,"CountryId"             => isset($SessionPost["CountryId"])? $SessionPost["CountryId"]:"0"
               ,"StateId"               => isset($SessionPost["StateId"])? $SessionPost["StateId"]:"-1"
               ,"ProductTaxClassId"     => isset($SessionPost["ProductTaxClassId"])? $SessionPost["ProductTaxClassId"]:"1"
               ,"ProductTaxClassName"   => isset($SessionPost["ProductTaxClassName"])? $SessionPost["ProductTaxClassName"]:""
               ,"TaxNameId"             => isset($SessionPost["TaxNameId"])? $SessionPost["TaxNameId"]:"1"
               ,"Rate"                  => isset($SessionPost["Rate"])? $SessionPost["Rate"]:""
               ,"FormulaView"           => isset($SessionPost["FormulaView"])? $SessionPost["FormulaView"]:"&nbsp;"
               ,"Formula"               => isset($SessionPost["Formula"])? $SessionPost["Formula"]:""
               ,"Applicable"            => isset($SessionPost["NotApplicable"])? "false":"true"
               ,"TaxRateByZipSet"       => isset($SessionPost["TaxRateByZipSet"]) ? $SessionPost["TaxRateByZipSet"] : 0
               );
    }
    function initFormData()
    {
        $this->ViewState =
            array(
                "hasCloseScript" => "false"
                 );
        $ProductTaxClassId = modApiFunc("Taxes", "getTaxClassId");
        $ProductTaxClassName = modApiFunc("Taxes", "getTaxClassInfo", $ProductTaxClassId);
        $ProductTaxClassName = $ProductTaxClassName["name"];
        $this->POST  =
            array(
                "Id"                    => ""
               ,"CountryId"             => "0"
               ,"StateId"               => "-1"
               ,"ProductTaxClassId"     => $ProductTaxClassId
               ,"ProductTaxClassName"   => $ProductTaxClassName
               ,"TaxNameId"             => "0"
               ,"Rate"                  => ""
               ,"FormulaView"           => "&nbsp;"
               ,"Formula"               => ""
               ,"Applicable"            => "true"
               ,"TaxRateByZipSet"       => 0
           );
    }

    function outputViewState()
    {
        $retval = "";
        foreach ($this->ViewState as $key => $value)
        {
            $retval .= "<input type=\"hidden\" name=\"ViewState[" .$key . "]\" value=\"" . $value . "\">";
        }
        return $retval;
    }

    function formAction()
    {
        global $application;
        $request = new Request();
        $request->setView  ('AddTaxRate');
        $request->setAction('AddTaxRateAction');
        return $request->getURL();
    }

    function outputSubtitle()
    {
        return $this->MessageResources->getMessage('ADD_TAX_RATE_PAGE_SUBTITLE');
    }

    function outputButton()
    {
        return $this->MessageResources->getMessage('BTN_ADD');
    }

    function outputCountriesList()
    {
        $retval = "";
        $countriesList = modApiFunc("Location", "getCountries");
        if (!$this->POST["CountryId"])
        {
            $countriesList[0] = $this->MessageResources->getMessage('SELECT_COUNTRY_LABEL');
        }
        ksort($countriesList);
        foreach ($countriesList as $id => $country)
        {
            $retval.= "<option value=\"".$id."\" ".(($id == $this->POST["CountryId"])? "SELECTED":"").">".$country."</option>";
        }
        return $retval;
    }

    function outputStatesList()
    {
        $retval = "";
        $states = modApiFunc("Location", "getStates", $this->POST["CountryId"]);
        if ($this->POST["StateId"]<0)
        {
            $states[-1] = $this->MessageResources->getMessage('SELECT_STATE_LABEL');
        }
        if (sizeof($states)>1)
        {
            $states[0] = $this->MessageResources->getMessage('STATE_ALL_LABEL');
        }
        ksort($states);
        foreach ($states as $stateId => $stateName)
        {
            $retval.= "<option value=\"".$stateId."\" ".(($stateId == $this->POST["StateId"])? "SELECTED":"").">".$stateName."</option>";
        }

        return $retval;
    }

    function outputProductTaxClassesList()
    {
        $retval = "";
//        $retval = "<option value=\"0\" ".(($this->POST["ProductTaxClassId"] == 0)? "SELECTED":"").">".$this->MessageResources->getMessage("PRODUCT_TAX_CLASS_ANY_LABEL")."</option>";
        $classes = modApiFunc('Taxes', 'getProductTaxClasses');
        if (sizeof($classes)>2 && $this->POST["ProductTaxClassId"] == 0)
        {
            $retval = "<option value=\"0\" ".(($this->POST["ProductTaxClassId"] == 0)? "SELECTED":"").">".$this->MessageResources->getMessage("SELECT_PRODUCT_TAX_CLASS_LABEL")."</option>";
        }
        foreach ($classes as $classInfo)
        {
            if ($classInfo["id"] == TAX_CLASS_ID_NOT_TAXABLE)
            {
                continue;
            }
            $retval.= "<option value=\"".$classInfo["id"]."\" ".(($classInfo["id"] == $this->POST["ProductTaxClassId"])? "SELECTED":"").">".prepareHTMLDisplay($classInfo["value"])."</option>";
        }
        return $retval;
    }

    function outputTaxNamesList($for_condition=false, $for_included_tax_name = false)
    {
        $retval = "";
        $names = modApiFunc('Taxes', 'getTaxNamesList');
        if ($for_condition)
        {
            foreach ($names as $nameInfo)
            {
                if($for_included_tax_name === true &&
                   $nameInfo['included_into_price'] == DB_FALSE)
                {
                    //
                    continue;
                }
                else
                {

                    $retval.= "<option value=\"".$nameInfo["Id"]."\" str_value=\"".$nameInfo["Name"]."\">".prepareHTMLDisplay($nameInfo["Name"])."</option>";
                }
            }
        }
        else
        {
            if (sizeof($names)>1 && $this->POST["TaxNameId"] == 0)
            {
                $retval = "<option value=\"0\" ".(($this->POST["TaxNameId"] == 0)? "SELECTED":"").">".$this->MessageResources->getMessage("SELECT_TAX_NAME_LABEL")."</option>";
            }
            foreach ($names as $nameInfo)
            {
                $retval.= "<option value=\"".$nameInfo["Id"]."\" ".(($nameInfo["Id"] == $this->POST["TaxNameId"])? "SELECTED":"").">".prepareHTMLDisplay($nameInfo["Name"])."</option>";
            }
        }
        return $retval;
    }

    function outputTaxCostsList($for_included_tax_name = false)
    {
        $retval = "";
        $costs = modApiFunc('Taxes', 'getTaxCostsList');
        foreach ($costs as $cost)
        {
            if($for_included_tax_name === true &&
              ($cost["id"] == TAX_COST_DISCOUNT ||
               $cost["id"] == TAX_COST_SHIPPING))
            {
                continue;
            }
            else
            {
                $retval.= "<option value=\"".$cost["id"]."\" str_value=\"".$this->MessageResources->getMessage($cost["name"])."\">".$this->MessageResources->getMessage($cost["name"])."</option>";
            }
        }
        return $retval;
    }

    function outputErrorHeader($ErrorHeaderText)
    {
        global $application;

        $application->registerAttributes(array("ErrorHeader" => ""));
        $this->_ErrorHeader = $ErrorHeaderText;
        $retval = modApiFunc('TmplFiller', 'fill', "taxes/tax-settings-add-rate/","error_header_item.tpl.html", array());
        return $retval;
    }

    function outputTaxRateFormula($tax_rate_id, $bHighlightTaxNames = false, $LeftTaxNameColor = NULL, $RightTaxNameId = NULL, $RightTaxNameColor = NULL)
    {
        global $application;

        $TaxNameInfo = modApiFunc("Taxes", "getTaxNameInfo", $this->POST['TaxNameId']);
        if($tax_rate_id == TAX_FORMULA_ID_UNKNOWN)
        {
            //This is a new formula. It doesn't exist in the database. Data for
            // template need to be filled manually.
            $ProductTaxClassInfo = ($this->POST['ProductTaxClassId'] == TAX_CLASS_ID_ANY) ? array('value' => $this->MessageResources->getMessage('PRODUCT_TAX_CLASS_ANY_LABEL')) : modApiFunc("Taxes", "getProductTaxClassInfo", $this->POST['ProductTaxClassId']);

            $TaxRateInfo = array('Id'   => TAX_FORMULA_ID_UNKNOWN,
                                 'c_id' => $this->POST['CountryId'],
                                 'Country' => modApiFunc("Location", "getCountry", $this->POST['CountryId']),
                                 's_id' => $this->POST['StateId'],
                                 'State' => ($this->POST['StateId'] == STATE_ID_ALL) ? $this->MessageResources->getMessage('STATE_ALL_LABEL') : modApiFunc("Location", "getState", $this->POST['StateId']),
                                 'ProductTaxClass' => $ProductTaxClassInfo['value'],
                                 'tax_class_id'   => $this->POST['ProductTaxClassId'],
                                 'TaxName' => $TaxNameInfo['Name'], //$tax_name_id
                                 'Rate' => $this->POST['Rate'],
                                 'Formula' => $this->POST['Formula'],
                                );
        }
        else
        {
            $TaxRateInfo = modApiFunc("Taxes", "getTaxRateInfo", $tax_rate_id);
            $ProductTaxClassInfo = ($TaxRateInfo['ProductTaxClassId'] == TAX_CLASS_ID_ANY) ? array('value' => $this->MessageResources->getMessage('PRODUCT_TAX_CLASS_ANY_LABEL')) : modApiFunc("Taxes", "getProductTaxClassInfo", $TaxRateInfo['ProductTaxClassId']);
            $TaxRateInfo['Country'] = modApiFunc("Location", "getCountry", $TaxRateInfo['CountryId']);
            $TaxRateInfo['State'] = ($TaxRateInfo['StateId'] == STATE_ID_ALL) ? $this->MessageResources->getMessage('STATE_ALL_LABEL') : modApiFunc("Location", "getState", $TaxRateInfo['StateId']);
            $TaxRateInfo['ProductTaxClass'] = $ProductTaxClassInfo['value'];
        }

        $TaxRateInfo['Formula'] = str_replace("{p_1}", getMsg('SYS','TAX_COST_NAME_001'), $TaxRateInfo['Formula']);
        $TaxRateInfo['CountryState'] = ($TaxRateInfo['Country'] . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $TaxRateInfo['State']);

        $TaxNamesList = modApiFunc("Taxes", "getTaxNamesList");
        $replace = array();
        foreach ($TaxNamesList as $taxNameInfo)
        {
            $replace['{t_'.$taxNameInfo['Id'].'}'] = $taxNameInfo['Name'];
        }

        if($bHighlightTaxNames == true)
        {
            $TaxRateInfo['TaxNameColor'] = $LeftTaxNameColor;

            if($RightTaxNameId != NULL)
            {
                $replace_color = array('{t_'. $RightTaxNameId .'}' => '<font color="' . $RightTaxNameColor . '">{t_'. $RightTaxNameId .'}</font>');
                $TaxRateInfo['Formula'] = strtr($TaxRateInfo['Formula'], $replace_color);
            }
        }
        else
        {
            $TaxNameDefaultColor = "black";
            $TaxRateInfo['TaxNameColor'] = $TaxNameDefaultColor;
        }

        $TaxRateInfo['Formula'] = strtr($TaxRateInfo['Formula'], $replace);
        $TaxRateInfo['Rate'] = modApiFunc("Localization", "num_format", $TaxRateInfo['Rate']);
        $this->_TaxRate_Template_Contents = $TaxRateInfo;
        $application->registerAttributes($this->_TaxRate_Template_Contents);
        $retval = modApiFunc('TmplFiller', 'fill', "taxes/tax-settings-add-rate/","tax_rate_item.tpl.html", array());
        return $retval;
    }

    function outputErrors()
    {
        global $application;

        $retval = "";
        if(!empty($this->ErrorsArray['ContradictoryFormula']))
        {
            //Reveal both formulae:
            //  the one to be added and
            //  the one to contradict
            $retval .= $this->outputErrorHeader($this->MessageResources->getMessage('ADD_TAX_RATE_ERR_001'));

            $retval .= $this->outputTaxRateFormula($this->ErrorsArray['ContradictoryFormula']['formula_id']);
            //Formula being added
            $retval .= $this->outputTaxRateFormula(TAX_FORMULA_ID_UNKNOWN);
        }

        if(!empty($this->ErrorsArray['CyclicFormula']))
        {
            //Give an example of a formulae cycle.
            //  All the formulae in the cycle are
            //  in right order.
            $retval .= $this->outputErrorHeader($this->MessageResources->getMessage('ADD_TAX_RATE_ERR_002'));

            $color_first_last = "green";

            $color_first_from_two = "blue";
            $color_second_from_two = "red";

            $LeftTaxNameColor = $color_first_last;
            $RightTaxNameColor = $color_first_from_two = $color_first_from_two;
            for(;!empty($this->ErrorsArray['CyclicFormula']['cycle']);)
            {
                $vertex = array_pop($this->ErrorsArray['CyclicFormula']['cycle']);
//                $tax_name_id = $vertex['tax_name_id'];
                $formula_id = $vertex['formula_id'];
                $child_tax_name_id = $vertex['child_tax_name_id'];

                if(empty($this->ErrorsArray['CyclicFormula']['cycle']))
                {
                    $RightTaxNameColor = $color_first_last;
                }
                else
                {
                    $RightTaxNameColor = $RightTaxNameColor == $color_first_from_two  ? $color_second_from_two : $color_first_from_two ;
                }

                $retval .= $this->outputTaxRateFormula($formula_id, true, $LeftTaxNameColor, $child_tax_name_id, $RightTaxNameColor);

                $LeftTaxNameColor = $RightTaxNameColor;
            }
        }
        return $retval;
    }

    function outputPrevOperand()
    {
        return '_';
    }

    function outputTaxNameAddressArray()
    {
        $value  = "var tax_name_address_required_array = new Array();";
        $value .= "var tax_name_included_array = new Array();";
        $tax_names = modApiFunc("Taxes", "getTaxNames");
        foreach($tax_names as $id => $info)
        {
            if($info["NeedsAddress"] == DB_FALSE)
            {
                $value .= "tax_name_address_required_array[".$id."] = false;";
            }
            else
            {
                $value .= "tax_name_address_required_array[".$id."] = true;";
            }

            if($info['included_into_price'] == DB_FALSE)
            {
                $value .= "tax_name_included_array[".$id."] = false;";
            }
            else
            {
                $value .= "tax_name_included_array[".$id."] = true;";
            }
        }
        return $value;
    }


    function outputTaxRateByZipSetsList($list)
    {
        $output = '';
        if (!empty($list))
        {
            foreach ($list as $id => $name)
            {
                $selected = ($this->POST["TaxRateByZipSet"] == $id) ? " selected" : '';
                $output .= "<option value='$id'$selected>".$name;
            }
        }
        else
        {
            $output .= "<option selected disabled>No ZIP based tax rates available.";
        }
        return $output;
    }


    /**
     *
     */
    function output()
    {
        global $application;

        if($this->ViewState["hasCloseScript"] == "true")
        {
            modApiFunc("application", "closeChild_UpdateParent");
            return;
        }

        $list = modApiFunc("TaxRateByZip", 'getSetsList');

        $this->_Template_Contents = array(
                                          'FormAction'      => $this->formAction()
                                         ,'HiddenArrayViewState' => $this->outputViewState()
                                         ,'Subtitle'        => $this->outputSubtitle()
                                         ,'Button'          => $this->outputButton()
                                         ,'CountriesList'   => $this->outputCountriesList()
                                         ,'StatesList'      => $this->outputStatesList()
                                         ,'CountriesStatesArrays' => modApiFunc("Location", "getJavascriptCountriesStatesArrays")//, false, modApiFunc("Shipping_Module_Flat_Shipping_Rates", "getSelectedCountries"), modApiFunc("Shipping_Module_Flat_Shipping_Rates", "getSelectedStates"))
                                         ,"TaxRateByZipSetsList" => $this->outputTaxRateByZipSetsList($list)
                                         ,'ProductTaxClassId' => $this->POST["ProductTaxClassId"]
                                         ,'ProductTaxClassName' => prepareHTMLDisplay($this->POST["ProductTaxClassName"])
//                                         ,'ProductTaxClassesList' => $this->outputProductTaxClassesList()
                                         ,'TaxNamesList'    => $this->outputTaxNamesList()
                                         ,'TaxNamesListForCondition' => $this->outputTaxNamesList(true)
                                         ,'TaxNamesListForConditionForIncludedTaxName' => $this->outputTaxNamesList(true, true)
                                         ,'TaxCostsList'    => $this->outputTaxCostsList()
                                         ,'TaxCostsListForIncludedTaxName' => $this->outputTaxCostsList(true)
                                         ,"Format"          => 'decimals = "3" dec_point = "."'//modApiFunc("Localization", "format_settings_for_js", "weight")
                                         ,'Id'              => $this->POST["Id"]
                                         ,'Rate'            => $this->POST["Rate"]
                                         ,'FormulaView'     => $this->POST["FormulaView"]
                                         ,'Formula'         => $this->POST["Formula"]
                                         ,'Applicable'      => ($this->POST["Applicable"] == "true")? "":"CHECKED"
                                         ,'Errors'          => $this->outputErrors()
                                         ,'PrevOperand'     => $this->outputPrevOperand()
                                         ,'TaxNameAddressArray' => $this->outputTaxNameAddressArray()
                                         ,"ManualChecked"   => ($this->POST["TaxRateByZipSet"] == 0) ? "checked" : ''
                                         ,"ZipSetChecked"   => ($this->POST["TaxRateByZipSet"] != 0) ? "checked" : ''
                                         ,"areTaxZipSetsAvailable" => (empty($list)) ? 'false' : 'true'
                                         );
        $application->registerAttributes($this->_Template_Contents);
        $output = modApiFunc('TmplFiller', 'fill', './../../js/','validate.msgs.js.tpl', array("WEIGHT" => addslashes($this->MessageResources->getMessage( new ActionMessage(array('WEIGHT_FIELD',
                                                         modApiFunc("Localization", "FloatToFormatStr", 23.325, "weight"),
                                                         modApiFunc("Localization", "FloatToFormatStr", 34, "weight"),
                                                         modApiFunc("Localization", "FloatToFormatStr", 99.2, "weight")))))));
        return $output.modApiFunc('TmplFiller', 'fill', "taxes/tax-settings-add-rate/","container.tpl.html", array());
    }

    function getTag($tag)
    {
        global $application;
        $value = null;
        if (!empty($this->_Template_Contents) && array_key_exists($tag, $this->_Template_Contents))
        {
            $value = $this->_Template_Contents[$tag];
        }
        else if (!empty($this->_TaxRate_Template_Contents) && array_key_exists($tag, $this->_TaxRate_Template_Contents))
        {
            $value = $this->_TaxRate_Template_Contents[$tag];
        }
        else if($tag == "ErrorHeader")
        {
            $value = $this->_ErrorHeader;
        }

        return $value;
    }
    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */


    /**#@-*/

}
?>