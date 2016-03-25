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
 * @package Notifications
 * @author Alexander Girin
 */
class AddNotification extends AjaxAction
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * A constructor.
     */
    function AddNotification()
    {
    }

    /**
     *
     */
    function onAction()
    {
        global $application;

        $request = $application->getInstance('Request');
        $data = array();
        $data['Id'] = $request->getValueByKey('notification_id');
        $data['Name'] = $request->getValueByKey('notification_name');
        $data['Subject'] = $request->getValueByKey('notification_subject');
        $data['Active'] = $request->getValueByKey('notification_active')? 'checked':'';
        $data['Action'] = $request->getValueByKey('notification_action');
        $data['OptionsValues'] = $request->getValueByKey('notification_option_value_'.$data['Action']);
        $sendTo = explode("|", $request->getValueByKey('notification_send_to_hidden'));
        $data['SendTo'] = array();
        if ($sendTo[0] != "")
        {
            foreach ($sendTo as $emailInfo)
            {
                $email = explode("=", $emailInfo);
                $data['SendTo'][] = array($email[0] => $email[1]);
            }
        }

        switch($request->getValueByKey('notification_send_from_radio'))
        {
            case "select":
                {
                    $emailInfo = explode("=", $request->getValueByKey('notification_send_from_select'));
                    $data['SendFrom'] = array($emailInfo[0] => $emailInfo[1]);
                    break;
                }
            case "input_text":
                {
                    $emailInfo = explode("=", 'EMAIL_CUSTOM=' . $request->getValueByKey('notification_send_from_input_text'));
                    $data['SendFrom'] = array($emailInfo[0] => $emailInfo[1]);
                    break;
                    break;
                }
            default:
                {
                    //@ output error message
                }
        }

        $data['Body'] = $request->getValueByKey('notification_body');
        $data['BlockBodies'] = $request->getValueByKey('nonification_block_body_'.$data['Action']);

        modApiFunc("Notifications", "addNotification", $data);
		$request = new Request();
        $request->setView('Notifications');
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