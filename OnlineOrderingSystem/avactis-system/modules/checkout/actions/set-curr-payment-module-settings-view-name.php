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
 * Checkout module.
 * Action handler on SetCurrentPaymentModuleSettingsViewName.
 *
 * @package Checkout
 * @access  public
 */
class SetCurrentPaymentModuleSettingsViewName extends AjaxAction
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
    function SetCurrentPaymentModuleSettingsViewName()
    {
    }

    /**
     * Saves the module, chosen for setting up.
     * Module's particular view name should be saved due to technical
     * specification.
     * Action: SetCurrentShippingModuleSettingsViewName
     *
     * @ finish the functions on this page
     */
    function onAction()
    {
        global $application;
        $request = $application->getInstance('Request');

        $view_class_name = $request->getValueByKey( 'pm_viewclassname' );
        $uid = $request->getValueByKey( 'pm_uid' );

        if ($view_class_name != NULL)
        {
            modApiFunc('Checkout', 'setCurrentPaymentShippingModuleSettingsUID', $uid);
            $retval = modApiFunc('Checkout', 'setCurrentPaymentModuleSettingsViewName', $view_class_name);
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