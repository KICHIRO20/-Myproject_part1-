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
 * Action handler for FlipPersonInfoTypeStatus.
 *
 * @package Checkout
 * @access  public
 * @author Vadim Lyalikov
 */
class FlipPersonInfoTypeStatus extends AjaxAction
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * Action constructor
     *
     * @ finish the functions on this page
     */
    function FlipPersonInfoTypeStatus()
    {
        global $application;
        $this->MessageResources = &$application->getInstance('MessageResources');
    }

    /**
     * @
     */
    function onAction()
    {
        global $application;
        $request = $application->getInstance('Request');

    	$person_info_type_id = $request->getValueByKey( 'person_info_type_id' );
        $SessionPost["ViewState"]["ErrorsArray"] = array();
        if(!is_numeric($person_info_type_id))
        {
            exit();
            //: report error.
        }
        else
        {
            //                       "On"    "Off",                       :
            //  payment   shipping                          BillingInfo, ShippingInfo,
            //  CreditCardInfo, BankAccountInfo                    .
            //         ,                                                                .
            $types = modApiFunc('Checkout', 'getPersonInfoTypeList');
            if(!array_key_exists($person_info_type_id, $types))
            {
                //: report error;
                exit();
            }
            else
            {
                if($types[$person_info_type_id]['active'] == DB_TRUE)
                //                  active    inactive
                {
                    $res_msg = "";
                    $results_array = modApiFunc('EventsManager','processEvent','RemovePersonInfoTypeEvent', $types[$person_info_type_id]['tag']);
                    foreach($results_array as $msg)
                    {
                        if($msg !== NULL)
                        {
                            //    -                          .                                       .
                            $res_msg .= $msg . "<br>";
                        }
                    }

                    if($res_msg == "")
                    {
                        //                         -             .
                        modApiFunc('Checkout', 'FlipPersonInfoTypeStatus', $person_info_type_id);
                    }
                    else
                    {
                        //             -        .                                                    .
                        $SessionPost["ViewState"]["ErrorsArray"][$person_info_type_id] =
                              $this->MessageResources->getMessage('CHECKOUT_FORM_EDITOR_ALERT_CANNOT_REMOVE_PERSON_INFO_TYPE')
                            . "<br>"
                            . $res_msg;
                        modApiFunc('Session', 'set', 'SessionPost', $SessionPost);
                    }
                }
                else
                {
                    modApiFunc('Checkout', 'FlipPersonInfoTypeStatus', $person_info_type_id);
                }
            }
        }
        $request = new Request();
        $request->setView('CheckoutInfoList');
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