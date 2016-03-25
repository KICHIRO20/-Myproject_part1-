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

define("MAX_EMPTY_ROWS_PER_TABLE", 3);

/**
 * Checkout Module, TaxSettings View.
 *
 * @package Checkout
 * @author Alexey Florinsky
 */
class TaxSettings
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
    function TaxSettings()
    {
        global $application;
        $this->MessageResources = &$application->getInstance('MessageResources');

        $this->TaxNamesList = modApiFunc("Taxes", "getTaxNamesList");
        $this->TaxNames = modApiFunc("Taxes", "getTaxNames");
        $this->TaxDisplayOptionsList = modApiFunc("Taxes", "getTaxDisplayOptionsList");
        $this->TaxRatesList = modApiFunc("Taxes", "getTaxRatesList");

        $this->maxRows = max(sizeof($this->TaxNamesList), sizeof($this->TaxDisplayOptionsList));
        if ($this->maxRows < MAX_EMPTY_ROWS_PER_TABLE)
        {
            $this->maxRows = MAX_EMPTY_ROWS_PER_TABLE;
        }
    }

    function outputTaxNamesList()
    {
        global $application;

        $retval = "";
        if ($this->TaxNamesList == NULL)
        {
            $retval.= modApiFunc('TmplFiller', 'fill', "taxes/tax-settings/","tax_name_item_na.tpl.html", array());
            for ($i=0; $i<($this->maxRows-1); $i++)
            {
                $retval.= modApiFunc('TmplFiller', 'fill', "taxes/tax-settings/","tax_name_item_empty.tpl.html", array());
            }
        }
        else
        {
            $n = sizeof($this->TaxNamesList);
            $i=1;
            foreach ($this->TaxNamesList as $TaxNameInfo)
            {
                $TaxNameInfo['included_into_price'] = $TaxNameInfo['included_into_price'] == "true" ? "<i>(". getMsg('SYS','TAX_NAMES_HEADER_003') .")</i>" : "";
                if($TaxNameInfo['NeedsAddress'] == DB_TRUE)
                {
                    $TaxNameInfo['Address'] = $this->MessageResources->getMessage($TaxNameInfo['Address']);
                }
                else
                {
                    $TaxNameInfo['Address'] = $this->MessageResources->getMessage('TAX_ADDRESS_NAME_1025');
                }
                $TaxNameInfo['I'] = $i;
                $TaxNameInfo['Name'] = prepareHTMLDisplay($TaxNameInfo['Name']);
                $this->_Template_Contents = $TaxNameInfo;
                $application->registerAttributes($this->_Template_Contents);
                $retval.= modApiFunc('TmplFiller', 'fill', "taxes/tax-settings/","tax_name_item.tpl.html", array());
                $i++;
            }
            if ($n<$this->maxRows)
            {
                for ($i=0; $i<($this->maxRows-$n); $i++)
                {
                    $retval.= modApiFunc('TmplFiller', 'fill', "taxes/tax-settings/","tax_name_item_empty.tpl.html", array());
                }
            }
        }
        return $retval;
    }

    function outputTaxDisplayOptionsList()
    {
        global $application;

        $retval = "";
        if ($this->TaxDisplayOptionsList == NULL)
        {
            $retval.= modApiFunc('TmplFiller', 'fill', "taxes/tax-settings/","tax_display_option_item_na.tpl.html", array());
            for ($i=0; $i<($this->maxRows-1); $i++)
            {
                $retval.= modApiFunc('TmplFiller', 'fill', "taxes/tax-settings/","tax_display_option_item_empty.tpl.html", array());
            }
        }
        else
        {
            $replace = array();
            foreach ($this->TaxNamesList as $taxNameInfo)
            {
                $replace['{'.$taxNameInfo['Id'].'}'] = prepareHTMLDisplay($taxNameInfo['Name']);
            }

            $n = sizeof($this->TaxDisplayOptionsList);
            $i=1;
            foreach ($this->TaxDisplayOptionsList as $TaxDisplayOptionInfo)
            {
                $TaxDisplayOptionInfo['Formula'] = strtr($TaxDisplayOptionInfo['Formula'], $replace);
                $TaxDisplayOptionInfo['OptionName'] = $this->MessageResources->getMessage($TaxDisplayOptionInfo['OptionName']);
                $TaxDisplayOptionInfo['I'] = $i;
                $TaxDisplayOptionInfo['View'] = prepareHTMLDisplay($TaxDisplayOptionInfo['View']);
                $this->_Template_Contents = $TaxDisplayOptionInfo;
                $application->registerAttributes($this->_Template_Contents);
                $retval.= modApiFunc('TmplFiller', 'fill', "taxes/tax-settings/","tax_display_option_item.tpl.html", array());
                $i++;
            }
            if ($n<$this->maxRows)
            {
                for ($i=0; $i<($this->maxRows-$n); $i++)
                {
                    $retval.= modApiFunc('TmplFiller', 'fill', "taxes/tax-settings/","tax_display_option_item_empty.tpl.html", array());
                }
            }
        }
        return $retval;
    }

    function outputTaxClassesList()
    {
        global $application;

        $retval = "";
        $ClassesList = modApiFunc("Taxes", "getClassesList");
        if ($ClassesList == NULL)
        {
            $retval.= modApiFunc('TmplFiller', 'fill', "taxes/tax-settings/","tax_class_item_na.tpl.html", array());
            for ($i=0; $i<MAX_EMPTY_ROWS_PER_TABLE; $i++)
            {
                $retval.= modApiFunc('TmplFiller', 'fill', "taxes/tax-settings/","tax_class_item_empty.tpl.html", array());
            }
        }
        else
        {
            $n = sizeof($ClassesList);
            $i=1;
            foreach ($ClassesList as $ClassInfo)
            {
                $ClassInfo["tc_i"] = $i;
                $ClassInfo["canDeleteTaxClass"] = $ClassInfo["Type"] == 'standard'? "0":"1";
                $ClassInfo['Name'] = prepareHTMLDisplay($ClassInfo['Name']);
                $ClassInfo['Descr'] = prepareHTMLDisplay($ClassInfo['Descr']);
                $this->_Template_Contents = $ClassInfo;
                $application->registerAttributes($this->_Template_Contents);
                $retval.= modApiFunc('TmplFiller', 'fill', "taxes/tax-settings/","tax_class_item.tpl.html", array());
                $i++;
            }
            if ($n<MAX_EMPTY_ROWS_PER_TABLE)
            {
                for ($i=0; $i<(MAX_EMPTY_ROWS_PER_TABLE-$n); $i++)
                {
                    $retval.= modApiFunc('TmplFiller', 'fill', "taxes/tax-settings/","tax_class_item_empty.tpl.html", array());
                }
            }
        }
        return $retval;
    }

    function outputTaxRatesList($TaxClass = null)
    {
        global $application;

        $this->TaxRateByZip_Sets = modApiFunc("TaxRateByZip", "getSetsList");

        if ($TaxClass)
        {
            $this->TaxRatesList = modApiFunc("Taxes", "getTaxRatesList", -1, -1, $TaxClass, -1);
        }

        $retval = "";
        if ($this->TaxRatesList == NULL)
        {
            $retval.= modApiFunc('TmplFiller', 'fill', "taxes/tax-settings/","tax_rate_item_na.tpl.html", array());
            for ($i=0; $i<(MAX_EMPTY_ROWS_PER_TABLE-1); $i++)
            {
                $retval.= modApiFunc('TmplFiller', 'fill', "taxes/tax-settings/","tax_rate_item_empty.tpl.html", array());
            }
        }
        else
        {
            foreach ($this->TaxRatesList as $TaxRateInfo)
            {
                $c_id = $TaxRateInfo['c_id'];
                $defined_all_other = false;
                foreach ($this->TaxRatesList as $_TaxRateInfo)
                {
                    if ($_TaxRateInfo['c_id'] == $c_id && $_TaxRateInfo['s_id'] == '0')
                    {
                        $defined_all_other = true;
                    }
                }
                if ($this->TaxNames[$TaxRateInfo["tax_name_id"]]["NeedsAddress"] == DB_TRUE &&
                    !$defined_all_other)
                {
                    //                  "       -          "         ,
                    //  ---                         &&
                    //  ---                        "                     "
                    $TaxRate = array(
                                     "Id"       => "&country_id=".$c_id
                                    ,"c_id"     => $c_id
                                    ,"s_id"     => "0"
                                    ,"tax_class_id" => $TaxClass
                                    ,"ProductTaxClass" => ""//$this->MessageResources->getMessage('TAX_RATE_NA_LABEL')
                                    ,"TaxName" => ""//$this->MessageResources->getMessage('TAX_RATE_NA_LABEL')
                                    ,"Rate" => "0"
                                    ,"Formula" => ""//$this->MessageResources->getMessage('TAX_RATE_NA_LABEL')
                                    ,"canDelete" => "0"
                                    );
                    $this->TaxRatesList[] = $TaxRate;
                }
            }

            $i = 0;
            foreach ($this->TaxRatesList as $TaxRateInfo)
            {
                if(isset($TaxRateInfo["tax_name_id"]) &&
                   $this->TaxNames[$TaxRateInfo["tax_name_id"]]["NeedsAddress"] == DB_FALSE)
                {
                    $this->TaxRatesList[$i]['Country'] = $this->MessageResources->getMessage('TAX_ADDRESS_NAME_1025');
                    $this->TaxRatesList[$i]['State'] = "";
                }
                else
                {
                    $this->TaxRatesList[$i]['Country'] = modApiFunc("Location", "getCountry", $TaxRateInfo['c_id']);
                    $state = modApiFunc("Location", "getState", $TaxRateInfo['s_id']);
                    $this->TaxRatesList[$i]['State'] = ($state == $this->MessageResources->getMessage('STATE_ALL_OTHER'))? "":$state;
                }
                if (!isset($this->TaxRatesList[$i]['canDelete']))
                {
//                    if ($TaxRateInfo["Applicable"] == "true") //: Cycle check for "Not Applicable"
//                    {
//                        $this->TaxRatesList[$i]['canDelete'] = (modApiFunc("Taxes", "doesDeletingTaxFormulaCreateCycle",     $this->TaxRatesList[$i]['Id']) === false)? "1":"0";
//                    }
                    $this->TaxRatesList[$i]['canDelete'] = "1";
                }
                $i++;
            }

            if (!function_exists("cmp"))
            {
                function cmp ($a, $b)
                {
                    return strcmp($a["Country"]." ".$a["State"], $b["Country"]." ".$b["State"]);
                }
            }
            usort($this->TaxRatesList, "cmp");

            $n = sizeof($this->TaxRatesList);
            $i=1;
            foreach ($this->TaxRatesList as $TaxRateInfo)
            {
                $specific_rate = "";
                if (isset($TaxRateInfo["rates_set"])
                    && isset($this->TaxRateByZip_Sets[$TaxRateInfo["rates_set"]])
                    && $TaxRateInfo["rates_set"] != 0)
                {
                    $specific_rate = $this->TaxRateByZip_Sets[$TaxRateInfo["rates_set"]];
                }
                $TaxRateInfo['CountryState'] = (!$TaxRateInfo['State'])? $TaxRateInfo['Country']:"&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$TaxRateInfo['State'];
                $TaxRateInfo['Formula'] = modApiFunc("Taxes", "getTaxFormulaViewFull", $TaxRateInfo['Id'], $specific_rate);
                $TaxRateInfo['Rate'] = modApiFunc("Localization", "num_format", $TaxRateInfo['Rate']);
                $TaxRateInfo['I'] = $TaxClass."_".$i;
                if ($TaxRateInfo["tax_class_id"] == 0)
                {
                    $TaxRateInfo["ProductTaxClass"] = $this->MessageResources->getMessage('PRODUCT_TAX_CLASS_ANY_LABEL');
                }
                $this->_Template_Contents = $TaxRateInfo;
                $application->registerAttributes($this->_Template_Contents);
                $retval.= modApiFunc('TmplFiller', 'fill', "taxes/tax-settings/","tax_rate_item.tpl.html", array());
                $i++;
            }
            if ($n<MAX_EMPTY_ROWS_PER_TABLE)
            {
                for ($i=0; $i<(MAX_EMPTY_ROWS_PER_TABLE-$n); $i++)
                {
                    $retval.= modApiFunc('TmplFiller', 'fill', "taxes/tax-settings/","tax_rate_item_empty.tpl.html", array());
                }
            }
            $retval.= modApiFunc('TmplFiller', 'fill', "taxes/tax-settings/","tax_rate_item_non_taxable.tpl.html", array());
        }
        return $retval;
    }

    function outputTaxRates()
    {
        global $application;

        $retval = '';
        $TaxClassesList = modApiFunc("Taxes", "getClassesList");
        foreach ($TaxClassesList as $TaxClassInfo)
        {
            $request = new Request();
            $request->setView  ('AddTaxRate');
            $request->setAction('SetTaxClassId');
            $request->setKey('tc_id', $TaxClassInfo['Id']);
            $TaxRateAddLink = $request->getURL();

            $request = new Request();
            $request->setView  ('EditTaxRate');
            $request->setAction('SetEditableTaxId');
            $request->setKey('tc_id', $TaxClassInfo['Id']);
            $request->setKey('Entity', 'TaxRate');
            $request->setKey('TaxId', '');
            $TaxRateEditLink = $request->getURL();

            $request = new Request();
            $request->setView  ('TaxSettings');
            $request->setAction('DeleteTaxRateAction');
            $request->setKey('TaxId', '');
            $TaxRateDeleteLink = $request->getURL();

            $this->_Template_Contents = array(
                                              'TaxClassTitle' => $this->MessageResources->getMessage(new ActionMessage(array('TAX_RATE_SUBTITLE', prepareHTMLDisplay($TaxClassInfo['Name']))))
                                             ,'TaxClass' => prepareHTMLDisplay($TaxClassInfo['Name'])
                                             ,'TaxClassId' => $TaxClassInfo['Id']
                                             ,'TaxRatesItems' => $this->outputTaxRatesList($TaxClassInfo['Id'])
                                             ,'TaxRateAddLink' => $TaxRateAddLink
                                             ,'TaxRateEditLink' => $TaxRateEditLink
                                             ,'TaxRateDeleteLink' => $TaxRateDeleteLink
                                             );
            $application->registerAttributes($this->_Template_Contents);
            $retval .= modApiFunc('TmplFiller', 'fill', "taxes/tax-settings/","tax_rates_list.tpl.html", array());
        }
        return $retval;
    }

    /**
     *
     */
    function output()
    {
        global $application;

        $request = new Request();
        $request->setView  ('EditTaxName');
        $request->setAction('SetEditableTaxId');
        $request->setKey('Entity', 'TaxName');
        $request->setKey('TaxId', '');
        $TaxNamesEditLink = $request->getURL();

        $request = new Request();
        $request->setView  ('TaxSettings');
        $request->setAction('DeleteTaxNameAction');
        $request->setKey('TaxId', '');
        $TaxNamesDeleteLink = $request->getURL();

        $request = new Request();
        $request->setView  ('EditTaxDisplayOption');
        $request->setAction('SetEditableTaxId');
        $request->setKey('Entity', 'TaxDisplayOption');
        $request->setKey('TaxId', '');
        $TaxDisplayOptionEditLink = $request->getURL();

        $request = new Request();
        $request->setView  ('TaxSettings');
        $request->setAction('DeleteTaxDisplayOptionAction');
        $request->setKey('TaxId', '');
        $TaxDisplayOptionDeleteLink = $request->getURL();

        $request = new Request();
        $request->setView  ('EditTaxClass');
        $request->setAction('SetEditableTaxId');
        $request->setKey('Entity', 'TaxClass');
        $request->setKey('TaxId', '');
        $TaxClassEditLink = $request->getURL();

        $request = new Request();
        $request->setView  ('TaxSettings');
        $request->setAction('DeleteProdTaxClass');
        $request->setKey('TaxId', '');
        $TaxClassDeleteLink = $request->getURL();

        $this->_Template_Contents = array(
                                          'TaxNamesItems' => $this->outputTaxNamesList()
                                         ,'TaxNamesEditLink' => $TaxNamesEditLink
                                         ,'TaxNamesDeleteLink' => $TaxNamesDeleteLink
                                         ,'TaxDisplayOptionsItems' => $this->outputTaxDisplayOptionsList()
                                         ,'TaxDisplayOptionEditLink' => $TaxDisplayOptionEditLink
                                         ,'TaxDisplayOptionDeleteLink' => $TaxDisplayOptionDeleteLink
                                         ,'TaxClassesItems' => $this->outputTaxClassesList()
                                         ,'TaxClassesEditLink' => $TaxClassEditLink
                                         ,'TaxClassesDeleteLink' => $TaxClassDeleteLink
                                         ,'ShippingModulesList' => prepareHTMLDisplay(implode(", ", modApiFunc("Taxes", "getShippingModulesList", false)))
                                         ,'CanAddTaxRate' => (sizeof($this->TaxNamesList)? "true":"false")
                                         ,'TaxRates' => $this->outputTaxRates()
                                         );
        $application->registerAttributes($this->_Template_Contents);
        return modApiFunc('TmplFiller', 'fill', "taxes/tax-settings/","container.tpl.html", array());
    }

    function getTag($tag)
    {
        global $application;
        $value = null;
        if (array_key_exists($tag, $this->_Template_Contents))
        {
            $value = $this->_Template_Contents[$tag];
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

    var $TaxRateByZip_Sets;


    /**#@-*/

}
?>