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
 * Location module.
  *
 * @package Localization
 * @access  public
 * @author  Ravil Garafutdinov
 */
class UpdateAcceptedCurrencies extends AjaxAction
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * Action constructor.
     */
    function UpdateAcceptedCurrencies()
    {
    }


    /**
     * Action: UpdateCurrencies.
     *
     */
    function onAction()
    {
        global $application;

        $SessionPost = array();

        $SessionPost = $_POST;
        $Errors = array();
        $Result = array();

        $current_msc = modApiFunc("Localization", "getMainStoreCurrency");
        $current_msc_code = modApiFunc("Localization", "getCurrencyCodeById", $current_msc);

        switch($SessionPost["ViewState"]["FormSubmitValue"])
        {
            case "setMSC":

                $new_msc = (isset($SessionPost["new_msc"])) ? $SessionPost["new_msc"] : $current_msc;
                $cur_code = modApiFunc("Localization", "getCurrencyCodeById", $new_msc);
                $cur_name = (isset($SessionPost["new_msc_name"])) ? trim(prepareHTMLDisplay($SessionPost["new_msc_name"])) : "";

                if ($cur_name == '')
                {
                    //
                    $Errors["MSC"][] = str_replace("{CURRENCY_CODE}", $cur_code, getMsg('CC', "STORE_CURRENCIES_EMPTY_MSC_NAME"));
                }

                //
                if ($new_msc != $current_msc)
                {
                    //
                    modApiFunc("Localization", "clearActiveAndDefaultCurrenciesList");

                    //
                    modApiFunc("Localization", "updateCurrency", $new_msc, $cur_name, "true", "true","true");

                    //
                    modApiFunc("Currency_Converter", "delAllManualRates");

                    //                 PM/SM required currencies
                    modApiFunc("Checkout", "setPM_SM_RequiredCurrencieslist");

                    //
                    $Result["MSC"][0] = str_replace("{CURRENCY_CODE}", $cur_code, getMsg('CC', "CC_RESULT_MSC_CHANGED"));
                    break;
                }
                else if ($cur_name != '')
                {
                    //
                    modApiFunc("Localization", "updateCurrency", $new_msc, $cur_name, "true", "true");

                    //
                    $Result["MSC"][0] = str_replace("{CURRENCY_CODE}", $cur_code, getMsg('CC', "CC_RESULT_MSC_NAME_CHANGED"));
                    break;
                }
                break;

            case "deleteAC":

                $cur_id = (isset($SessionPost["currency_id"])) ? $SessionPost["currency_id"] : 0;
                $cur_code = modApiFunc("Localization", "getCurrencyCodeById", $cur_id);

                $req = modApiFunc("Checkout", "getPM_SM_RequiredCurrenciesList");
                if (isset($req[$cur_code]))
                {
                    // cannot delete technical currency
                    $Errors["MSC"][] = str_replace("{CURRENCY_CODE}", $cur_code, getMsg('CC', "STORE_CURRENCIES_CANNOT_DELETE_TECHNICAL"));
                    break;
                }

                //
                modApiFunc("Localization", "updateCurrency", $cur_id, "", "false", "false", "false");

                //
                $cur_code = modApiFunc("Localization", "getCurrencyCodeById", $cur_id);
                modApiFunc("Currency_Converter", "delManualRateByCode", $cur_code);

                //
                $Result["AC"][0] = str_replace("{CURRENCY_CODE}", $cur_code, getMsg('CC', "CC_RESULT_AC_DELETED"));

                break;

            case "updateRateAC":

                $cur_id = (isset($SessionPost["currency_id"])) ? $SessionPost["currency_id"] : 0;
                $cur_code = modApiFunc("Localization", "getCurrencyCodeById", $cur_id);

                if ($cur_code == NULL)
                    break;

                //
                $rate = modApiFunc("Currency_Converter", "getRateFromWeb", $cur_code, $current_msc_code);
                if ($rate == false)
                {
                    //                 ,
                    $Errors["AC"][] = str_replace("{CURRENCY_CODE}", $cur_code, getMsg('CC', "STORE_CURRENCIES_CANNOT_OBTAIN_RATE_FROM_WEB"));
                    break;
                }

                //
                $rate = number_format($rate, 4, '.', '');
                modApiFunc("Currency_Converter", "delManualRateByCode", $cur_code);
                modApiFunc("Currency_Converter", "addManualRate", $current_msc_code, $cur_code, $rate);

                //
                $Result["AC"][0] = str_replace("{CURRENCY_CODE}", $cur_code, getMsg('CC', "CC_RESULT_AC_RATE_UPDATED"));

                break;

            case "newAC":

                $cur_id = (isset($SessionPost["new_currency_select"])) ? $SessionPost["new_currency_select"] : 0;
                $cur_code = modApiFunc("Localization", "getCurrencyCodeById", $cur_id);
                $method = (isset($SessionPost["rate_method"])) ? $SessionPost["rate_method"] : 1;
                $rate = (isset($SessionPost["new_rate"])) ? $SessionPost["new_rate"] : null;
                $visibility = (isset($SessionPost["new_currency_visible"]) && $SessionPost["new_currency_visible"] == 'on') ? 'true' : 'false';

                $rlt = modApiFunc("Localization", "addNewAdditionalCurrency", $cur_id, $method, $rate, $visibility);

                if ($rlt === true)
                {
                    $Result["NewAC"][0] = str_replace("{CURRENCY_CODE}", $cur_code, getMsg('CC', "CC_RESULT_NEWAC_CURRENCY_ADDED"));
                }
                else switch ($rlt)
                {
                    case STORE_CURRENCIES_CANNOT_ADD_MAIN_AS_ADDITIONAL:
                        $Errors["NewAC"][] = str_replace("{CURRENCY_CODE}", $cur_code, getMsg('CC', "STORE_CURRENCIES_CANNOT_ADD_MAIN_AS_ADDITIONAL"));
                        break;

                    case STORE_CURRENCIES_CANNOT_ADD_DUPLICATE:
                        $Errors["NewAC"][] = str_replace("{CURRENCY_CODE}", $cur_code, getMsg('CC', "STORE_CURRENCIES_CANNOT_ADD_DUPLICATE"));
                        break;

                    case STORE_CURRENCIES_INVALID_MANUAL_RATE_ERROR:
                        $Errors["NewAC"][] = str_replace("{CURRENCY_CODE}", $cur_code, getMsg('CC', "STORE_CURRENCIES_INVALID_MANUAL_RATE_ERROR"));
                        break;

                    case STORE_CURRENCIES_CANNOT_OBTAIN_NEW_RATE_FROM_WEB:
                        $Errors["NewAC"][] = str_replace("{CURRENCY_CODE}", $cur_code, getMsg('CC', "STORE_CURRENCIES_CANNOT_OBTAIN_NEW_RATE_FROM_WEB"));
                        break;
                }

                break;

            case "updateAC":

                //                               ,             -
                $currencies = (isset($SessionPost["ac"])) ? $SessionPost["ac"] : array();
                if (empty($currencies))
                    break;

                //
                foreach ($currencies as $cur_id)
                {
                    $cur_code = modApiFunc("Localization", "getCurrencyCodeById", $cur_id);
                    $visibility = (isset($SessionPost["visible_$cur_id"]) && $SessionPost["visible_$cur_id"] == 'on') ? 'true' : 'false';

                    //
                    $cur_name = (isset($SessionPost["cname_$cur_id"])) ? trim(prepareHTMLDisplay($SessionPost["cname_$cur_id"])) : '';
                    if ($cur_name == '')
                    {
                        $Errors["AC"][] = str_replace("{CURRENCY_CODE}", $cur_code, getMsg('CC', "STORE_CURRENCIES_EMPTY_NAME"));
                        continue;
                    }

                    //
                    $rate = (isset($SessionPost["crate_$cur_id"])) ? $SessionPost["crate_$cur_id"] : 0;
                    $rate = number_format($rate, 4, '.', '');
                    if ($rate == 0)
                    {
                        $Errors["AC"][] = str_replace("{CURRENCY_CODE}", $cur_code, getMsg('CC', "STORE_CURRENCIES_INVALID_RATE"));
                        continue;
                    }

                    //
                    modApiFunc("Localization", "updateCurrency", $cur_id, $cur_name, "true", "false", $visibility);

                    //             ,          ,
                    $cur_code = modApiFunc("Localization", "getCurrencyCodeById", $cur_id);
                    modApiFunc("Currency_Converter", "delManualRateByCode", $cur_code);
                    modApiFunc("Currency_Converter", "addManualRate", $current_msc_code, $cur_code, $rate);

                    //
                    $Result["AC"][0] = getMsg('CC', "CC_RESULT_AC_CURRENCIES_UPDATED");
                }

                break;

            default :
                _fatal(array( "CODE" => "CORE_051"), __CLASS__, __FUNCTION__, $SessionPost["ViewState"]["FormSubmitValue"]);
                break;
        }

        if (isset($Errors["MSC"]) && !empty($Errors["MSC"]))
            modApiFunc('Session', 'set', 'MSC_Errors', $Errors["MSC"]);
        if (isset($Errors["AC"]) && !empty($Errors["AC"]))
            modApiFunc('Session', 'set', 'AC_Errors', $Errors["AC"]);
        if (isset($Errors["NewAC"]) && !empty($Errors["NewAC"]))
            modApiFunc('Session', 'set', 'NewAC_Errors', $Errors["NewAC"]);

        if (isset($Result["MSC"]) && !empty($Result["MSC"]))
            modApiFunc('Session', 'set', 'MSC_Result', $Result["MSC"]);
        if (isset($Result["AC"]) && !empty($Result["AC"]))
            modApiFunc('Session', 'set', 'AC_Result', $Result["AC"]);
        if (isset($Result["NewAC"]) && !empty($Result["NewAC"]))
            modApiFunc('Session', 'set', 'NewAC_Result', $Result["NewAC"]);

        $request = new Request();
        $request->setView('PopupWindow');
        $request->setKey("page_view", "CurrencyRateEditor");
        $application->redirect($request);
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