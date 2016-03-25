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
class UpdateFsRuleInfo extends AjaxAction
{

    function UpdateFsRuleInfo()
    {
    }

    function saveSettings($SessionPost)
    {
        $params = array (
                "FsRule_id"         => $SessionPost["FsRule_id"]
               ,"FsRuleName"        => $SessionPost["FsRuleName"]
               ,"FsRuleMinSubtotal" => $SessionPost["FsRuleMinSubtotal"]
               ,"FsRuleStrictCart"  => $SessionPost["FsRuleStrictCart"]
            );

        return modApiFunc("Shipping_Cost_Calculator", "updateFsRuleInfo", $params);
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

        $fsr_id = $SessionPost["FsRule_id"] = intval($SessionPost["FsRule_id"]);
        $SessionPost["FsRuleName"] = trim($SessionPost["FsRuleName"]);
        $SessionPost["FsRuleMinSubtotal"] = floatval($SessionPost["FsRuleMinSubtotal"]);
        $SessionPost["FsRuleStrictCart"] = intval($SessionPost["StrictCart"]);

        if ($SessionPost["FsRuleName"] == "")
        {
            $SessionPost["ViewState"]["ErrorsArray"][] = "ERROR_EMPTY_RULE_NAME";
        }

        $is_unique = modApiFunc("Shipping_Cost_Calculator", "checkIfFsRuleIsUnique", $SessionPost["FsRuleName"], $fsr_id);
        if (!$is_unique)
        {
            $SessionPost["ViewState"]["ErrorsArray"][] = "ERROR_NOT_UNIQUE_RULE_NAME";
        }

        if($SessionPost["FormSubmitValue"] == "Save")
        {
            if (count($SessionPost["ViewState"]["ErrorsArray"]) == 0)
            {
                unset($SessionPost["ViewState"]["ErrorsArray"]);
                $this->saveSettings($SessionPost);
                $SessionPost["ViewState"]["hasCloseScript"] = "true";
            }
        }

        modApiFunc('Session', 'set', 'SessionPost', $SessionPost);
        $request = new Request();
        $request->setView(CURRENT_REQUEST_URL);
        $request->setKey('FsRule_id', $fsr_id);
        $application->redirect($request);
    }

};

?>