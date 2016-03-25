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
 * Action handler on SaveSelectedPaymentModulesList.
 *
 * @package Checkout
 * @access  public
 */
class SaveSelectedPaymentModulesList extends AjaxAction
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
    function SaveSelectedPaymentModulesList()
    {
    }

    /**
     * Saves selected (for CZ) payment modules list.
     *
     * Action: SaveSelectedPaymentModulesList.
     *
     * @ finish the functions on this page
     */
    function onAction()
    {
    	global $application;

        $modulesType = "payment";
        $request = $application->getInstance('Request');
        $SelectedModulesIDList = $request->getValueByKey('SelModules');
	$SelectedModules = $request->getValueByKey('SelectedModules');
        if(empty($SelectedModulesIDList))
        {
            //To prevent [0] => null entry from appearing.
            $NewSelectedModules = array();
        }
        else
        {
            $NewSelectedModules = explode(',', $SelectedModulesIDList);
        }

	$selected_array = array_unique(array_merge($SelectedModules,$NewSelectedModules));
	$NewSelectedModules = array_intersect($selected_array,$NewSelectedModules);

        //"All Inactive" Payment Module should be always selected to appear in CZ.
        array_unshift($NewSelectedModules, modApiFunc("Checkout", "getGiftCertificatePaymentModuleId", $modulesType));
        array_unshift($NewSelectedModules, modApiFunc("Checkout", "getAllInactiveModuleId", $modulesType));

        $set_selected_modules_data = array();
        $sort_id = 0;
        foreach($NewSelectedModules as $SelectedModuleId)
        {
            $set_selected_modules_data[$SelectedModuleId] = array
            (
                "module_id"              => $SelectedModuleId
               ,"module_group"           => $modulesType
               ,"b_is_selected"          => true
               ,"sort_order"             => $sort_id++
            );
        }

        $retval = modApiFunc('Checkout', 'setSelectedModules', $set_selected_modules_data, $modulesType);
        //$res = modApiFunc('Checkout', 'updateAllInactiveModuleStatus', 'payment');

        modApiFunc('Session','set','ResultMessage','PAYM_METH_SAVED_MSG');
        $request = new Request();
        $request->setView('CheckoutPaymentModulesList');
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