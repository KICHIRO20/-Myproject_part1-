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
 * Action handler on EditCustomFieldAction.
 *
 * @package Checkout
 * @access  public
 */
class EditCustomField_action extends AjaxAction
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    function EditCustomField_action()
    {
    }

    function onAction()
    {
    	global $application;

        $request = $application->getInstance('Request');

        $attr_id = $request->getValueByKey("customFieldsList");

        if (!isset($_POST['customFieldVisible']))
            $_POST['customFieldVisible'] = 0;
        if (!isset($_POST['customFieldRequired']))
            $_POST['customFieldRequired'] = 0;

        if ($_POST['customFieldRequired'] == 1)
        {
            $_POST['customFieldVisible'] = 1;
        }
        if ($_POST['customFieldVisible'] == 0)
        {
            $_POST['customFieldRequired'] = 0;
        }

        #validation
        if (isset($_POST['params']))
            foreach ($_POST['params'] as $name=>$v)
            {
                if (!preg_match("/^\d+$/",$v)) // not number
                {
                    $_data = $_POST;
                    $_data['invalid_params'] = $_POST['customFieldType'];
                    modApiFunc("Session","set","FormData",$_data);
                    modApiFunc("Session","set","ResultMessage",'MSG_INVALID_DATA');

                    $request = new Request();
                    $request->setView(CURRENT_REQUEST_URL);
                    $request->setKey('page_view', 'ManageCustomFields');
                    $request->setKey('variant_id', $_POST['variant_id']);
                    $request->setKey('attribute_id', $attr_id);
                    $request->setKey('mode', 'edit');
                    $application->redirect($request);

                    return;
                }
            }

        $params = null;
        if (isset($_POST['params']))
            $params = serialize($_POST['params']);

        $values = array();
        if (isset($_POST['customFieldValues']))
        {
            $values = explode("\n",$_POST['customFieldValues']);
            foreach ($values as $i=>$v)
                $values[$i] = trim($v);
        }

        $r = modApiFunc('Checkout', 'updateCustomField', $_POST['variant_id'], $attr_id, $_POST['customFieldVisibleName'], $_POST['customFieldDescription'], $_POST['customFieldVisible'], $_POST['customFieldRequired'], $_POST['customFieldType'], $values, $params);

        modApiFunc('Checkout', 'updateCheckoutFormHash');

        modApiFunc("Session","set","UpdateParent",true);

        $request = new Request();
        $request->setView(CURRENT_REQUEST_URL);
        $request->setKey('page_view', 'ManageCustomFields');
        $request->setKey('variant_id', $_POST['variant_id']);
        $request->setKey('attribute_id', $attr_id);
        $request->setKey('mode', 'edit');
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