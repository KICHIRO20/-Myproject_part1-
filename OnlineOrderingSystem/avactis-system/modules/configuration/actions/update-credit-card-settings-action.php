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
 * Action handler on update credit card.
 *
 * @package Configuration
 * @access  public
 * @author Alexey Kolesnikov
 */
class UpdateCreditCardSettings extends AjaxAction
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
    function UpdateCreditCardSettings()
    {
    }

    /**
     * @
     */
    function onAction()
    {
        global $application;
        $request = $application->getInstance('Request');

        $SessionPost = $_POST;

        switch($request->getValueByKey('FormSubmitValue'))
        {
            case "UpdateRows" :
            {
                $new_names = $request->getValueByKey("cc_type_name");
                $new_visible_status = $request->getValueByKey("cc_type_status");

                $values = array();

                if(is_array($new_names) && sizeof($new_names) > 0)
                {
                    foreach($new_names as $id => $name)
                    {
                        $name = trim($name);
                        if(is_numeric($id) && !empty($name))
                        {
                            $values[$id] = array
                            (
                                "name" => $name
                               ,"visible" => (isset($new_visible_status[$id]) && $new_visible_status[$id] == 2) ? DB_FALSE : DB_TRUE
                            );
                        }
                    }

                    modApiFunc('Configuration', 'setCreditCardSettings', $values);
                    modApiFunc('Session','set','ResultMessage','MSG_CC_LIST_UPDATED');
                }
                $request = new Request();
                $request->setView('CreditCardSettings');
                $application->redirect($request);
                break;
            }
            case "AddRow":
            {
                $new_credit_card_type_name = $request->getValueByKey("add_cc_type_name");
                $new_credit_card_type_name = trim($new_credit_card_type_name);
                if(empty($new_credit_card_type_name))
                {
                }
                else
                {
                    modApiFunc('Configuration', 'addCreditCardType', $new_credit_card_type_name);
                    $request = new Request();
                    $request->setView('CreditCardSettings');
                    $application->redirect($request);
                }
                break;
            }
            case "SaveSortOrder":
            {
                $ccTypesSortOrder = $request->getValueByKey( 'ObjectList_hidden' );
                $ccTypesSortOrderArray = explode('|', $ccTypesSortOrder);

                if ($ccTypesSortOrderArray != NULL)
                {
                    modApiFunc('Configuration', 'setCreditCardTypesSortOrder', $ccTypesSortOrderArray);
                }
                modApiFunc('Session', 'set', 'SessionPost', $SessionPost);
                break;
            }
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