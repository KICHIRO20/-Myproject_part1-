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

_use(dirname(__FILE__).'/number_settings_az.php');

/**
 * Localization Module, CurrencySettings View.
 *
 * @package Localization
 * @author Alexey Florinsky
 */
class CurrencySettings extends NumberSettings
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * CurrencySettings constructor.
     */
    function CurrencySettings()
    {
        global $application;
        $this->MessageResources = &$application->getInstance('MessageResources');
        modApiFunc("Localization", "loadCurrencyDisplaySettings", modApiFunc("Localization", "getCurrencyFormatEdited"));
        $this->CurrentCurrency = explode("|", modApiFunc("Localization", "getFormat", "currency"));
        $this->CurrenciesList = modApiFunc("Localization", "getFormatsList", "currency");
    }

    function getJS()
    {
        $CurrenciesList = modApiFunc("Localization", "getFormatsList", "currency");
        $LocalizationSettingsRaw = modApiFunc("Localization", "getSettingsRaw");
        $ret = "c_list = new Array();" . "\n";
        foreach($CurrenciesList as $info)
        {
        	$id = $info['id'];
        	if(isset($LocalizationSettingsRaw['CURRENCY_'. $id]))
        	{
	        	$ret .= "c_list['". $info['id'] ."'] = new Array();" . "\n";
	            $ret .= "c_list['". $info['id'] ."']['CURRENCY'] = '".addslashes($LocalizationSettingsRaw['CURRENCY_'. $id])."';" . "\n";
	            $ret .= "c_list['". $info['id'] ."']['CURRENCY_FORMAT'] = '".addslashes($LocalizationSettingsRaw['CURRENCY_FORMAT_'. $id])."';" . "\n";
	            $ret .= "c_list['". $info['id'] ."']['CURRENCY_POSITIVE_FORMAT'] = '".addslashes($LocalizationSettingsRaw['CURRENCY_POSITIVE_FORMAT_'. $id])."';" . "\n";
	            $ret .= "c_list['". $info['id'] ."']['CURRENCY_NEGATIVE_FORMAT'] = '".addslashes($LocalizationSettingsRaw['CURRENCY_NEGATIVE_FORMAT_'. $id])."';" . "\n";
        	}
        	else
        	{
                $ret .= "c_list['". $info['id'] ."'] = new Array();" . "\n";
                $ret .= "c_list['". $info['id'] ."']['CURRENCY'] = '". addslashes($id . "|" . $info['sign']) ."';" . "\n";
                $ret .= "c_list['". $info['id'] ."']['CURRENCY_FORMAT'] = '".addslashes(DEFAULT_CURRENCY_FORMAT)."';" . "\n";
                $ret .= "c_list['". $info['id'] ."']['CURRENCY_POSITIVE_FORMAT'] = '".addslashes(DEFAULT_CURRENCY_POSITIVE_FORMAT)."';" . "\n";
                $ret .= "c_list['". $info['id'] ."']['CURRENCY_NEGATIVE_FORMAT'] = '".addslashes(DEFAULT_CURRENCY_NEGATIVE_FORMAT)."';" . "\n";
        	}
        }
        return $ret;
    }

    /**
     *
     */
    function outputCurrenciesList()
    {
        $retval = "";

        //
        $this->CurrenciesListSorted = array();
        foreach($this->CurrenciesList as $value)
        {
        	$this->CurrenciesListSorted[$value["code"]] = $value;
        }
        ksort($this->CurrenciesListSorted);

        foreach ($this->CurrenciesListSorted as $CurrencyInfo)
        {
        	if($CurrencyInfo['active'] == DB_TRUE)
            {
	            $retval.= "<option value=\"".$CurrencyInfo["id"]."\"";
	            $retval.= (($this->CurrentCurrency[0] == $CurrencyInfo["id"])? " SELECTED ":"");
	            $retval.= ">".$CurrencyInfo["name"]."</option>";
            }
        }
        return $retval;
    }

    /**
     *
     */
    function outputCurrencySignsArray()
    {
        $retval = "";

        foreach ($this->CurrenciesList as $CurrencyInfo)
        {
            $retval.= "CurrencySigns[".$CurrencyInfo["id"]."] = '".$CurrencyInfo["sign"]."';\n";
        }
        return $retval;
    }

    function outputResultMessage()
    {
        global $application;
        if(modApiFunc("Session","is_set","ResultMessage"))
        {
            $msg=modApiFunc("Session","get","ResultMessage");
            modApiFunc("Session","un_set","ResultMessage");
            $template_contents=array(
                "ResultMessage" => getMsg('SYS',$msg)
            );
            $this->_Template_Contents=$template_contents;
            $application->registerAttributes($this->_Template_Contents);
            $this->mTmplFiller = &$application->getInstance('TmplFiller');
            return $this->mTmplFiller->fill("localization/currency_settings/", "result-message.tpl.html",array());
        }
        else
        {
            return "";
        }
    }

    /**
     *
     */
    function output()
    {
        global $application;
        loadCoreFile('html_form.php');
        $HtmlForm = new HtmlForm();

        $request = new Request();
        $request->setView  ('CurrencyFormat');
        $request->setAction('UpdateCurrencyFormat');
        $formAction = $request->getURL();

        $template_contents = array(
                                   "FORM"               => $HtmlForm->genForm($formAction, "POST", "CurrencyForm")
                                  ,"CurrenciesList"     => $this->outputCurrenciesList()
                                  ,"CurrencySign"       => $this->CurrentCurrency[1]
                                  ,"DecimalSeparators"  => $this->outputSelect("separators", 1, "currency_format")
                                  ,"DigitSeparators"    => $this->outputSelect("separators", 2, "currency_format")
                                  ,"Digits"             => $this->outputSelect("currency_digits", "", "currency_format")
                                  ,"PositiveCurrency"   => $this->outputSelect("positive_currency", "", "currency_format")
                                  ,"NegativeCurrency"   => $this->outputSelect("negative_currency", "", "currency_format")
                                  ,"CurrencySignsArray" => $this->outputCurrencySignsArray()
                                  ,"ResultMessage"      => $this->outputResultMessage()
                                  ,"JSData"             => $this->getJS()
                                  );
        $this->_Template_Contents = $template_contents;
        $application->registerAttributes($this->_Template_Contents);
        $res = modApiFunc('TmplFiller', 'fill', "localization/currency_settings/","container.tpl.html", array());
        modApiFunc('Localization', 'unsetCurrencyFormatEdited');
        return $res;
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


    /**#@-*/

}
?>