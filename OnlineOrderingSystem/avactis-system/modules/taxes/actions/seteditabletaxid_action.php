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
class SetEditableTaxId extends AjaxAction
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * SetEditableTaxId constructor.
     */
    function SetEditableTaxId()
    {
    }

    /**
     * AddsProduct Tax Class.
     */
    function onAction()
    {
        global $application;
        $request = $application->getInstance('Request');
        modApiFunc('Taxes', 'setEditableTaxId', $request->getValueByKey('Entity'), $request->getValueByKey('TaxId'));
        if ($c_id = $request->getValueByKey('country_id'))
        {
            modApiFunc('Taxes', 'setCountryId', $c_id);
        }
        if ($tc_id = $request->getValueByKey('tc_id'))
        {
            modApiFunc('Taxes', 'setTaxClassId', $tc_id);
        }
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