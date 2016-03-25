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
class CalculateShippingCZ extends AjaxAction
{

    function CalculateShippingCZ()
    {
    }

    function onAction()
    {
        global $application;

        $result = array();
        $SessionPost = $_POST;
        $subaction = 'calculate';
        if (isset($_POST['subaction']))
            $subaction = $_POST['subaction'];

        switch ($subaction)
        {
            case 'calculate':
                $result["DstCountry"]           = trim(intval($SessionPost["DstCountry"]));
                $result["DstState_menu_select"] = trim(intval($SessionPost["DstState_menu_select"]));
                $result["DstZip"]               = trim($SessionPost["DstZip"]);
                modApiFunc('Session', 'set', 'ShippingCalculatorPost', $result);
                break;

            case 'remember':
                $choice = isset($_POST["ShippingCalculatorChoice"])
                    ?
                    preg_replace('/[^A-Z0-9-_]/', '', trim($_POST["ShippingCalculatorChoice"]))
                    :
                    null;

                if ($choice != null)
                    modApiFunc('Session', 'set', 'ShippingCalculatorChoice', $choice);

                break;
        }

        $request = new Request();
        $request->setView(CURRENT_REQUEST_URL);
        $application->redirect($request);
    }
};

?>