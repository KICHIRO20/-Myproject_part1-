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
 * Action handler on RemoveCustomFieldAction.
 *
 * @package Checkout
 * @access  public
 */
class RemoveCustomField_action extends AjaxAction
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    function RemoveCustomField_action()
    {
    }

    function onAction()
    {
    	global $application;

        $request = $application->getInstance('Request');

        $attr_id = $request->getValueByKey("customFieldsList");

        modApiFunc('Checkout', 'removeCustomField', $_POST['variant_id'], $attr_id);

        modApiFunc('Checkout', 'updateCheckoutFormHash');

        modApiFunc("Session","set","UpdateParent",true);

        $request = new Request();
        $request->setView(CURRENT_REQUEST_URL);
        $request->setKey('page_view', 'ManageCustomFields');
        $request->setKey('variant_id', $_POST['variant_id']);
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