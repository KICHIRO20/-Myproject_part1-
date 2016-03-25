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
 * @package TaxRateByZip
 * @author Ravil Garafutdinov
 */
class TaxRatesByZipItemsAction extends AjaxAction
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * TaxRatesByZipItemsAction constructor.
     */
    function TaxRatesByZipItemsAction()
    {
    }

    /**
     * Deletes tax rate by zip set.
     */
    function onAction()
    {
        global $application;

        $SessionPost = array();
        $SessionPost = $_POST;
        $Errors = array();
        $Results = array();
        $sets = modApiFunc("TaxRateByZip", "getSetsList");

        if (isset($SessionPost["item_n"]))
        {
            $sid = $SessionPost["item_n"];
        }
        else
        {
            //  errors
        }

        if (isset($SessionPost["ViewState"]["FormSubmitValue"]))
        {
            switch($SessionPost["ViewState"]["FormSubmitValue"])
            {

                case "deleteSet":
                    // check if set is used in tax rates
                    if (modApiFunc("Taxes", "checkIfSetIsUsed", $sid))
                    {
                        $Errors[] = str_replace("%1%", $sets[$sid], getMsg("TAX_ZIP", "SETS_LIST_CANNOT_DELETE_SET_IN_USE"));
                        break;
                    }
                    else
                    {
                        modApiFunc("TaxRateByZip", "deleteSetFromDB", $sid);
                    }
                    break;

                case "checkRate":
                    $sid = $SessionPost['CheckRate_sid'];
                    $zip = $SessionPost['CheckRate_zip'];
                    $Results[] = str_replace("%1%", $sets[$sid], getMsg("TAX_ZIP", "CHECK_RATES_GOT_ARCHIVE"));
                    $Results[] = str_replace("%1%", prepareHTMLDisplay($zip), getMsg("TAX_ZIP", "CHECK_RATES_GOT_ZIP"));
                    modApiFunc('Session', 'set', 'CheckRateByZip', array("sid" => $sid, "zip" => $zip));

                    $zip = preg_replace("/[^0-9]/", '', $zip);

                    $len = _ml_strlen($zip);
                    if ($len != 5 && $len != 9)
                    {
                        $Results[] = getMsg("TAX_ZIP", "CHECK_RATES_NOT_5_9_ZIP_ERROR");
                        break;
                    }

                    if ($len == 5)
                    {
                        $zip5 = intval($zip);
                        $Results[] = str_replace("%1%", $zip5, getMsg("TAX_ZIP", "CHECK_RATES_GOT_ZIP5"));
                        $Results[] = getMsg("TAX_ZIP", "CHECK_RATES_NO_ZIP4");

                        $rlt5 = modApiFunc("TaxRateByZip", "getTaxRateByZip5Strict", $sid, $zip5);
                        $rlt5 = ($rlt5 === FALSE) ? 0.0 : $rlt5;
                        $Results[] = str_replace("%1%", $rlt5, getMsg("TAX_ZIP", "CHECK_RATES_STRICT_RLT"));

                        $rlt5i = modApiFunc("TaxRateByZip", "getTaxRateByZip5Interval", $sid, $zip5);
                        $rlt5i = ($rlt5i === FALSE) ? 0.0 : $rlt5i;
                        $Results[] = str_replace("%1%", $rlt5i, getMsg("TAX_ZIP", "CHECK_RATES_INTERVAL_RLT"));

                        $rlt5m = modApiFunc("TaxRateByZip", "getTaxRateByZip5Mask", $sid, $zip5);
                        $rlt5m = ($rlt5m === FALSE) ? 0.0 : $rlt5m;
                        $Results[] = str_replace("%1%", $rlt5m, getMsg("TAX_ZIP", "CHECK_RATES_MASK_RLT"));
                    }
                    else
                    {
                        $zip5 = intval(_ml_substr($zip, 0, 5));
                        $zip4 = intval(_ml_substr($zip, 5, 4));
                        $Results[] = str_replace("%1%", $zip5, getMsg("TAX_ZIP", "CHECK_RATES_GOT_ZIP5"));
                        $Results[] = str_replace("%1%", $zip4, getMsg("TAX_ZIP", "CHECK_RATES_GOT_ZIP4"));

                        $rlt5 = modApiFunc("TaxRateByZip", "getTaxRateByZip9Strict", $sid, $zip5, $zip4);
                        $rlt5 = ($rlt5 === FALSE) ? 0.0 : $rlt5;
                        $Results[] = str_replace("%1%", $rlt5, getMsg("TAX_ZIP", "CHECK_RATES_STRICT_RLT"));

                        $rlt5i = modApiFunc("TaxRateByZip", "getTaxRateByZip9Interval", $sid, $zip5, $zip4);
                        $rlt5i = ($rlt5i === FALSE) ? 0.0 : $rlt5i;
                        $Results[] = str_replace("%1%", $rlt5i, getMsg("TAX_ZIP", "CHECK_RATES_INTERVAL_RLT"));

                        $rlt5m = modApiFunc("TaxRateByZip", "getTaxRateByZip9Mask", $sid, $zip5, $zip4);
                        $rlt5m = ($rlt5m === FALSE) ? 0.0 : $rlt5m;
                        $Results[] = str_replace("%1%", $rlt5m, getMsg("TAX_ZIP", "CHECK_RATES_MASK_RLT"));

//                        if (!$rlt5 && !$rlt5i && !$rlt5m)
//                        {
//                            $Results[] = getMsg("TAX_ZIP", "CHECK_RATES_COULDNT_FIND_RATE_WITH_ZIP4");
//
//                            $rlt5 = modApiFunc("TaxRateByZip", "getTaxRateByZip5Strict", $sid, $zip5);
//                            $rlt5 = ($rlt5 === FALSE) ? 0.0 : $rlt5;
//                            $Results[] = str_replace("%1%", $rlt5, getMsg("TAX_ZIP", "CHECK_RATES_STRICT_RLT"));
//
//                            $rlt5i = modApiFunc("TaxRateByZip", "getTaxRateByZip5Interval", $sid, $zip5);
//                            $rlt5i = ($rlt5i === FALSE) ? 0.0 : $rlt5i;
//                            $Results[] = str_replace("%1%", $rlt5i, getMsg("TAX_ZIP", "CHECK_RATES_INTERVAL_RLT"));
//
//                            $rlt5m = modApiFunc("TaxRateByZip", "getTaxRateByZip5Mask", $sid, $zip5);
//                            $rlt5m = ($rlt5m === FALSE) ? 0.0 : $rlt5m;
//                            $Results[] = str_replace("%1%", $rlt5m, getMsg("TAX_ZIP", "CHECK_RATES_MASK_RLT"));
//                        }
                    }

                    if ($rlt5)
                    {
                        $Results[] = str_replace("%1%", $rlt5, getMsg("TAX_ZIP", "CHECK_RATES_STRICT_RLT_USED"));
                    }
                    else if ($rlt5i)
                    {
                        $Results[] = str_replace("%1%", $rlt5i, getMsg("TAX_ZIP", "CHECK_RATES_INTERVAL_RLT_USED"));
                    }
                    else if ($rlt5m)
                    {
                        $Results[] = str_replace("%1%", $rlt5m, getMsg("TAX_ZIP", "CHECK_RATES_MASK_RLT_USED"));
                    }
                    else
                    {
                        $Results[] = getMsg("TAX_ZIP", "CHECK_RATES_NO_RLT");
                    }

                    break;

                default:
                    _fatal(array( "CODE" => "CORE_051"), __CLASS__, __FUNCTION__, $SessionPost["ViewState"]["FormSubmitValue"]);
                    break;
            }
        }
        else
        {
            _fatal(array( "CODE" => "CORE_051"), __CLASS__, __FUNCTION__, $SessionPost["ViewState"]["FormSubmitValue"]);
        }

        if (!empty($Errors))
            modApiFunc('Session', 'set', 'Errors', $Errors);

        if (!empty($Results))
            modApiFunc('Session', 'set', 'Results', $Results);

//        modApiFunc('Session', 'set', 'SessionPost', $SessionPost);

        $request = new Request();
        $request->setView('TaxRateByZip_Sets');
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