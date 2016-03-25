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
 * @package CurrencyConverter
 * @author Ravil Garafutdinov
 *
 */

class CurrencyRateEditor
{
    function CurrencyRateEditor()
    {
        loadCoreFile('html_form.php');
    }

    function outputResultMessage($type, $label)
    {
        global $application;

        $output = '';
        if(modApiFunc("Session", "is_set", $label))
        {
            $messages = modApiFunc("Session", "get", $label);
            modApiFunc("Session", "un_set", $label);
            $i = 0;
            foreach($messages as $ekey => $eval)
            {
                $i++;
                $msg = '';
                if (count($messages) > 1)
                    $msg .= "$i. ";

                $template_contents=array(
                    "UniMessage" => $msg . $eval
                );
                $this->_Template_Contents = $template_contents;
                $application->registerAttributes($this->_Template_Contents);
                $this->mTmplFiller = &$application->getInstance('TmplFiller');
                $output .= $this->mTmplFiller->fill("currency_converter/misc/", "$type-message.tpl.html", array());
            }

        }
        return $output;
    }

    function initCurrenciesList()
    {
        $currencies = modApiFunc('Localization','getFormatsList','currency');

        $this->main_store_currency = NULL;
        $this->accepted_currencies = array();
        $this->full_currencies_list = array();
        $this->used_currencies = array();
        $this->pm_sm_required_currencies = modApiFunc('Checkout', 'getPM_SM_RequiredCurrenciesList');

    //
        foreach ($currencies as $cur)
        {
            $this->full_currencies_list[$cur["id"]] = $cur;

            if ($cur["dflt"] == "true")
            {
                $this->main_store_currency = $cur;
                $this->used_currencies[$this->main_store_currency["id"]] = $this->main_store_currency["name"];
            }
            else if ($cur["active"] == "true")
            {
                $this->accepted_currencies[$cur["code"]] = $cur;
                $this->accepted_currencies[$cur["code"]]["rate"] = 1;
                $this->used_currencies[$cur["id"]] = $cur["name"];
            }
        }

    //            accepted currencies c
    //   system currencies
        foreach ($this->accepted_currencies as $cid => $ac)
        {
            if (isset($this->pm_sm_required_currencies[$cid]))
            {
                $this->accepted_currencies[$cid]['pm_sm_required'] = $this->pm_sm_required_currencies[$cid];
            }
            else
            {
                $this->accepted_currencies[$cid]['pm_sm_required'] = false;
            }
        }

    //
        $this->currency_rates = modApiFunc('Currency_Converter','getManualRates');
        foreach ($this->currency_rates as $id => $cr)
        {
            if ($this->currency_rates[$id]["_to"] == $this->main_store_currency["code"])
            {
                $this->currency_rates[$id]["_to"] = $this->currency_rates[$id]["_from"];
                $this->currency_rates[$id]["_from"] = $this->main_store_currency["code"];
                $this->currency_rates[$id]["rate"] =
                    ($this->currency_rates[$id]["rate"] != 0)
                    ? number_format(1 / $this->currency_rates[$id]["rate"], 5, '.', '')
                    : 1;
            }

            if (isset($this->accepted_currencies[$this->currency_rates[$id]["_to"]]))
            {
                $this->accepted_currencies[$this->currency_rates[$id]["_to"]]["rate"] = $cr["rate"];
            }
        }

    //
        $temp_co = array();
        foreach ($this->full_currencies_list as $key => $val)
        {
            $temp_co[$key] = $val["name"];
        }

    //
        $this->currency_options['4']   = $temp_co['4'];     // USD
        $this->currency_options['48']  = $temp_co['48'];    // CAD
        $this->currency_options['36']  = $temp_co['36'];    // GBP
        $this->currency_options['6']   = $temp_co['6'];     // EUR
        $this->currency_options['13']  = $temp_co['13'];    // AUD
        $this->currency_options['143'] = $temp_co['143'];   // JPY
        $this->currency_options['107'] = $temp_co['107'];   // ILS
        $this->currency_options['205'] = $temp_co['205'];   // NOK
        $this->currency_options['265'] = $temp_co['265'];   // SEK
        $this->currency_options['68']  = $temp_co['68'];    // NZD

        $this->common_currencies = $this->currency_options;

    //                                 ,
        $temp_co = array_diff_assoc($temp_co, $this->currency_options);
        asort($temp_co);
        $this->other_currencies = $temp_co;
        $this->currency_options += $temp_co;
//print prepareArrayDisplay($this->pm_sm_required_currencies, 'req', true, null, 10);
        return;
    }

    function output_CurrenciesJSList()
    {
        $output = "var currencies = new Array();\n";
        foreach ($this->currency_options as $key => $val)
        {
            $val = str_replace("'", "\'", $val);
            $output .= "currencies[$key] = '$val';\n";
        }
        return $output;
    }

    function output_AdditionalCurrenciesList()
    {
        global $application;
        $output = "";

        if (count($this->accepted_currencies) < 1)
        {
            return "";
        }
        else
        {
            $i=0;
            $mTmplFiller = &$application->getInstance('TmplFiller');

            foreach ($this->accepted_currencies as $ac)
            {
                if ($i)
                    $output .= '<tr style="height: 1px;"><td style="padding: 0px 4px 0px 4px;" colspan=3><hr size="1" noshade></td></tr>';

                if ($ac['pm_sm_required'] !== false)
                {
                    $delete_link_action = "flipDiv({$ac["id"]});return false;";
                    $message = str_replace('{PM_SM_NUMBER}', count($ac['pm_sm_required']), getMsg('CC', "SYSTEM_CURRENCY_LINK_LABEL"));
                    $delete_link_label = '<font style="text-decoration: none; font-weight: bold;" color=blue>'.$message.'</font>';
                    $required_list = getMsg('CC', 'SYSTEM_CURRENCY_WARNING').'<br />';
                    foreach ($ac['pm_sm_required'] as $pmsm)
                    {
                    	$required_list .= $pmsm['module_class_name'].'<br />';
                    }
                }
                else
                {
                    $required_list = '';
                    $delete_link_action = "return DeleteAC({$ac["id"]});";
                    $delete_link_label = getMsg('CC', 'DELETE_LINK_LABEL');
                }

            	$template_contents = array(
            	    "AdditionalCurrencyId"   => $ac["id"]
            	   ,"AdditionalCurrencyName" => $ac["name"]
            	   ,"AdditionalCurrencyCode" => $ac["code"]
            	   ,"AdditionalCurrencyRate" => $ac["rate"]//modApiFunc("Localization", "num_format", $ac["rate"], $this->main_store_currency["id"])
            	   ,"MainStoreCurrencyCode"  => $this->main_store_currency["code"]
            	   ,"RateTo" =>   modApiFunc("Localization", "currency_round", 100 * $ac["rate"], $this->main_store_currency["id"])
            	   ,"RateFrom" => modApiFunc("Localization", "currency_round", 100 / $ac["rate"], $ac["id"])
            	   ,"VisibleChecked" => ($ac['visible'] == 'true') ? ' checked' : ''
            	   ,"DeleteLinkAction" => $delete_link_action
            	   ,"DeleteLinkLabel" => $delete_link_label
            	   ,"PM_SM_SC_RequiredList" => $required_list
            	);

            	$this->_Template_Contents = $template_contents;
            	$application->registerAttributes($this->_Template_Contents);
            	$output .= $mTmplFiller->fill("currency_converter/rate_editor/", "one_rate.tpl.html",array());

            	$i++;
            }
        }
        return $output;
    }

    function output_AdditionalCurrenciesPart()
    {
        global $application;
        $output = "";

        $r = new Request();
        $r->setView('PopupWindow');
        $r->setKey("page_view", "CurrencyRateEditor");
        $r->setAction('UpdateAcceptedCurrencies');

        if (count($this->accepted_currencies) < 1
            && !modApiFunc("Session", "is_set", "AC_Result")
            && !modApiFunc("Session", "is_set", "AC_Errors"))
        {
            return "";
        }
        else
        {
            $mTmplFiller = &$application->getInstance('TmplFiller');

            $template_contents = array(
                 "AdditionalCurrenciesList"  => $this->output_AdditionalCurrenciesList()
                ,"FormActionUrl"             => $r->getURL()
                ,'AC_ResultMessage'         => $this->outputResultMessage("result", "AC_Result")
                ,'AC_ErrorsMessage'         => $this->outputResultMessage("error", "AC_Errors")
            );

            $this->_Template_Contents = $template_contents;
            $application->registerAttributes($this->_Template_Contents);
            $output .= $mTmplFiller->fill("currency_converter/rate_editor/", "additional_currencies.tpl.html",array());
        }
        return $output;
    }

    function output_CurrenciesSelect($msc = false, $no_repeat = false)
    {
        $flag = 0;
        $output = "";
        if ($no_repeat)
        {
            $common = array_diff_assoc($this->common_currencies, $this->used_currencies);
            $other = array_diff_assoc($this->other_currencies, $this->used_currencies);

            foreach ($common as $key => $cur)
            {
                if ($msc == $key)
                    $selected = "selected";
                else
                    $selected = '';

                $output .= "<option value='{$key}' $selected>" . $cur;
            }

            if (!empty($common) && !empty($other))
                $output .= "<option value='separator' disabled>-----------------------</option>";

            foreach ($other as $key => $cur)
            {
                if ($msc == $key)
                    $selected = "selected";
                else
                    $selected = '';

                $output .= "<option value='{$key}' $selected>" . $cur;
            }
        }
        else
        {
            foreach ($this->currency_options as $key => $cur)
            {
                if ($msc == $key)
                    $selected = "selected";
                else
                    $selected = '';

                $output .= "<option value='{$key}' $selected>" . $cur;

                if ($flag == 9)
                    $output .= "<option value='separator' disabled>-----------------------</option>";

                $flag++;
            }
        }
        return $output;
    }

    function output()
    {
        global $application;

        $this->initCurrenciesList();

        $r = new Request();
        $r->setView('PopupWindow');
        $r->setKey("page_view", "CurrencyRateEditor");
        $r->setAction('UpdateAcceptedCurrencies');

        $template_contents = array(
            'MSC_ResultMessage'         => $this->outputResultMessage("result", "MSC_Result")
           ,'MSC_ErrorsMessage'         => $this->outputResultMessage("error", "MSC_Errors")
           ,'NewAC_ResultMessage'       => $this->outputResultMessage("result", "NewAC_Result")
           ,'NewAC_ErrorsMessage'       => $this->outputResultMessage("error", "NewAC_Errors")
           ,"MainStoreCurrencyId"       => $this->main_store_currency["id"]
           ,"MainStoreCurrencyName"     => $this->main_store_currency["name"]
           ,"MainStoreCurrencyCode"     => $this->main_store_currency["code"]
           ,"CC_AdditionalCurrenciesPart" => $this->output_AdditionalCurrenciesPart()
           ,"CurrenciesSelect"          => $this->output_CurrenciesSelect(false, true)
           ,"CurrenciesSelectMSC"       => $this->output_CurrenciesSelect($this->main_store_currency["id"])
           ,"CurrenciesJSList"          => $this->output_CurrenciesJSList()
           ,"FormActionUrl"             => $r->getURL()
        );

        $this->_Template_Contents=$template_contents;
        $application->registerAttributes($this->_Template_Contents);
        $this->mTmplFiller = &$application->getInstance('TmplFiller');
        return $this->mTmplFiller->fill("currency_converter/rate_editor/", "container.tpl.html",array());
    }

    function getTag($tag)
    {
        return getKeyIgnoreCase($tag, $this->_Template_Contents);
    }

    var $_Template_Contents;
    var $main_store_currency;
    var $accepted_currencies;
    var $full_currencies_list;
    var $currency_rates;
    var $currency_options;
    var $common_currencies;
    var $other_currencies;
    var $used_currencies;
    var $pm_sm_required_currencies;
};

?>