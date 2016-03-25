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
 * @package Taxes
 * @author Alexander Girin
 */
class SetShippingTaxes extends AjaxAction
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * SetShippingTaxes constructor.
     */
    function SetShippingTaxes()
    {
    }

    /**
     * Adds Product Tax Class.
     */
    function onAction()
    {
        global $application;
        $request = $application->getInstance('Request');
        $sm = $request->getValueByKey('sm');
        if (!$sm)
        {
            $sm = array();
        }
        modApiFunc("Taxes", "setTaxSetting", "SELECTED_SHIPPING_MODULES_LIST", serialize($sm));

        $SessionPost = $_POST;
        $SessionPost["ViewState"]["hasCloseScript"] = "true";
        modApiFunc('Session', 'set', 'SessionPost', $SessionPost);
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