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
class update_scc_settings extends AjaxAction
{

    function update_scc_settings()
    {
    }

    function saveSettings($SessionPost)
    {
        $settings = array (
                "PO_SC"      => $SessionPost["PO_SC"]
               ,"PO_SC_TYPE" => $SessionPost["PO_SC_TYPE"]
               ,"PO_HC"      => $SessionPost["PO_HC"]
               ,"MIN_SC"     => $SessionPost["MIN_SC"]
               ,"FS_OO"      => $SessionPost["FS_OO"]
               ,"FH_OO"      => $SessionPost["FH_OO"]
               ,"FS_MODE"    => $SessionPost["FS_MODE"]
               ,"FS_PLACING" => $SessionPost["FS_PLACING"]
               ,"FS_METHOD_LABEL_VALUE" => $SessionPost["FS_METHOD_LABEL_VALUE"]
               ,"FS_COUNTRY_HIDE"   => $SessionPost["FS_COUNTRY_HIDE"]
               ,"FS_COUNTRY_ASSUME" => $SessionPost["FS_COUNTRY_ASSUME"]
               ,"FS_STATE_HIDE"     => $SessionPost["FS_STATE_HIDE"]
               ,"FS_STATE_ASSUME"   => $SessionPost["FS_STATE_ASSUME"]
               ,"FS_ZIP_HIDE"       => $SessionPost["FS_ZIP_HIDE"]
               ,"FS_ZIP_ASSUME"     => $SessionPost["FS_ZIP_ASSUME"]
        );

        modApiFunc("Shipping_Cost_Calculator", "setSettings", $settings);
        modApiFunc('Shipping_Module_Free_Shipping', 'setSettings', array('MODULE_NAME' => $SessionPost["FS_METHOD_LABEL_VALUE"]));
    }

    function onAction()
    {
        global $application;

        if(modApiFunc('Session', 'is_Set', 'SessionPost'))
        {
            _fatal(array( "CODE" => "CORE_050"), __CLASS__, __FUNCTION__);
        }

        $SessionPost = $_POST;

        $SessionPost["ViewState"]["ErrorsArray"] = array();

        $SessionPost["PO_SC"]  = trim(str_replace(",", ".", $SessionPost["PO_SC"]));
        $SessionPost["PO_HC"]  = trim(str_replace(",", ".", $SessionPost["PO_HC"]));
        $SessionPost["MIN_SC"] = trim(str_replace(",", ".", $SessionPost["MIN_SC"]));
        $SessionPost["FS_OO"]  = trim(str_replace(",", ".", $SessionPost["FS_OO"]));
        $SessionPost["FH_OO"]  = trim(str_replace(",", ".", $SessionPost["FH_OO"]));
        $SessionPost["FS_MODE"] = intval($SessionPost["FS_MODE"]);
        $SessionPost["FS_PLACING"] = intval($SessionPost["FS_PLACING"]);
        $SessionPost["FS_METHOD_LABEL_VALUE"] = trim(addslashes($SessionPost["FS_METHOD_LABEL_VALUE"]));

        $SessionPost["FS_COUNTRY_ASSUME"] = trim(intval($SessionPost["DstCountry"]));
        $SessionPost["FS_STATE_ASSUME"]   = trim(intval($SessionPost["DstState_menu_select"]));
        $SessionPost["FS_ZIP_ASSUME"]     = preg_replace('/[^a-zA-Z0-9]/', '', trim($SessionPost["zip_assume"]));

        $SessionPost["FS_COUNTRY_HIDE"] = trim(intval($SessionPost["sce_country_hide"]));
        $SessionPost["FS_STATE_HIDE"]   = trim(intval($SessionPost["sce_state_hide"]));
        $SessionPost["FS_ZIP_HIDE"]     = trim(intval($SessionPost["sce_zip_hide"]));

        if ($SessionPost["FS_COUNTRY_HIDE"] != FS_DO_NOT_HIDE)
            $SessionPost["FS_COUNTRY_HIDE"] = FS_HIDE;
        if ($SessionPost["FS_STATE_HIDE"] != FS_DO_NOT_HIDE)
            $SessionPost["FS_STATE_HIDE"] = FS_HIDE;
        if ($SessionPost["FS_ZIP_HIDE"] != FS_DO_NOT_HIDE)
            $SessionPost["FS_ZIP_HIDE"] = FS_HIDE;

        if ($SessionPost["PO_SC"] == "")
        {
            $SessionPost["PO_SC"] = "0";
        }
        else
        {
            if(!is_numeric($SessionPost["PO_SC"]) || $SessionPost["PO_SC"]<0)
                $SessionPost["ViewState"]["ErrorsArray"][] = "ERROR_001";
        }

        if ($SessionPost["PO_HC"] == "")
        {
            $SessionPost["PO_HC"] = "0";
        }
        else
        {
            if(!is_numeric($SessionPost["PO_HC"]) || $SessionPost["PO_HC"]<0)
                $SessionPost["ViewState"]["ErrorsArray"][] = "ERROR_002";
        }

        if ($SessionPost["MIN_SC"] == "")
        {
            $SessionPost["MIN_SC"] = "0";
        }
        else
        {
            if(!is_numeric($SessionPost["MIN_SC"]) || $SessionPost["MIN_SC"] < 0)
                $SessionPost["ViewState"]["ErrorsArray"][] = "ERROR_003";
        }

        if ($SessionPost["FS_OO"] != "" && (!is_numeric($SessionPost["FS_OO"]) || $SessionPost["FS_OO"] < 0))
                $SessionPost["ViewState"]["ErrorsArray"][] = "ERROR_004";

        if ($SessionPost["FH_OO"] != "" && (!is_numeric($SessionPost["FH_OO"]) || $SessionPost["FH_OO"] < 0))
                $SessionPost["ViewState"]["ErrorsArray"][] = "ERROR_005";

        if($SessionPost["ViewState"]["FormSubmitValue"] == "Save")
        {
            if(count($SessionPost["ViewState"]["ErrorsArray"]) == 0)
            {
                unset($SessionPost["ViewState"]["ErrorsArray"]);
                $this->saveSettings($SessionPost);
                $SessionPost["ViewState"]["hasCloseScript"] = "true";
            };
        };

        modApiFunc('Session', 'set', 'SessionPost', $SessionPost);
        $request = new Request();
        $request->setView(CURRENT_REQUEST_URL);
        $application->redirect($request);
    }
};

?>