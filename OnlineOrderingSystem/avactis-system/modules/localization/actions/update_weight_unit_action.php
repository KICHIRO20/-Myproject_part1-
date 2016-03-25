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
 * Action handler on update weight unit.
 *
 * @package Localization
 * @access  public
 * @author Alexander Girin
 */
class UpdateWeightUnit extends AjaxAction
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * Action constructor.
     *
     * @ finish the functions on this page
     */
    function UpdateWeightUnit()
    {
    }

    /**
     * @
     */
    function onAction()
    {
        global $application;
        $request = $application->getInstance('Request');

        modApiFunc('Localization', 'setValue', "WEIGHT_UNIT", $request->getValueByKey("WeightUnit"));
        modApiFunc('Localization', 'setValue', "WEIGHT_COEFF", modApiFunc("Localization", "FormatStrToFloat", $request->getValueByKey("WeightCoeff"), "weight_coeff"));
        modApiFunc('Session','set','ResultMessage','MSG_WEIGHT_UPDATED');

        modApiFunc('EventsManager','throwEvent','WeightUnitUpdated');

        $request = new Request();
        $request->setView(CURRENT_REQUEST_URL);
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