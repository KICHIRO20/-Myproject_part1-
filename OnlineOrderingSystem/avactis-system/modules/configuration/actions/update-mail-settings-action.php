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
 * Action handler on update general settings.
 *
 * @package Configuration
 * @access  public
 * @author Alexey Kolesnikov
 */
class UpdateMailSettings extends AjaxAction
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
    function UpdateMailSettings()
    {
    }

    /**
     * @
     */
    function onAction()
    {
        global $application;
        $request = $application->getInstance('Request');

        $subaction = $request->getValueByKey("asc_subaction");
        if ($subaction != 'update' && $subaction != 'send')
            $subaction = 'update';

        if ($subaction == 'update')
        {
            $messages["ERRORS"] = array();
            $messages["MESSAGES"] = array();
            $type = intval($request->getValueByKey("mail_type_select"));
            $host = trim($request->getValueByKey("mail_host"));
            $port = trim($request->getValueByKey("mail_port"));
            $auth = $request->getValueByKey("mail_auth");
            $user = trim($request->getValueByKey("mail_user"));
            $pass = trim($request->getValueByKey("mail_pass"));

            $SessionPost = $_POST;

            if ($type == 2)
            {
                if (_ml_strlen($host) == 0)
                {
                    $messages["ERRORS"][] = 'MAIL_SETTINGS_WARNING_HOST';
                }
                if (_ml_strlen($port) == 0)
                {
                    $messages["ERRORS"][] = 'MAIL_SETTINGS_WARNING_PORT';
                }

                if ($auth == "on")
                {
                    if (_ml_strlen($user) == 0)
                    {
                        $messages["ERRORS"][] = 'MAIL_SETTINGS_WARNING_USER';
                    }
                    if (_ml_strlen($pass) == 0)
                    {
                        $messages["ERRORS"][] = 'MAIL_SETTINGS_WARNING_PASS';
                    }
                }
            }

            if (sizeof($messages["ERRORS"]))
            {
                modApiFunc('Session', 'set', 'ResultMessage', $messages);
            }
            else
            {
                $values = array(
                     "MAIL_TYPE" => $type
                    ,"MAIL_HOST" => $host
                    ,"MAIL_PORT" => $port
                    ,"MAIL_AUTH" => ($auth == "on") ? 1 : 0
                    ,"MAIL_USER" => $user
                    ,"MAIL_PASS" => $pass
                );
                modApiFunc('Configuration', 'setMailSettings', $values);
                $messages["MESSAGES"][] = 'MSG_MAIL_SETTINGS_UPDATED';
                modApiFunc('Session', 'set', 'ResultMessage', $messages);
            }
        }
        else // if ($subaction == 'send')
        {
            $body    = modApiFunc("Configuration", "getMailSettings");
            if ($body["MAIL_TYPE"] == 1)
            {
                $body = array("MAIL_TYPE" => getMsg("SYS", "MAIL_SETTINGS_SERVER_DEFAULTS"));
            }
            else
            {
                $body["MAIL_TYPE"] = getMsg("SYS", "MAIL_SETTINGS_SPECIFIC_SETTINGS");
                if ($body["MAIL_AUTH"] == 0)
                {
                    $body["MAIL_AUTH"] = "No";
                    unset($body["MAIL_USER"]);
                    unset($body["MAIL_PASS"]);
                }
                else
                {
                    $body["MAIL_AUTH"] = "Yes";
                }
            }

            $from_address = modApiFunc("Configuration", "getValue", SYSCONFIG_STORE_OWNER_SITE_ADMINISTRATOR_EMAIL);
            $subject      = getMsg("SYS", "MAIL_SETTINGS_AVACTIS_TEST_MAIL_SUBJECT");
            $body         = getMsg("SYS", "MAIL_SETTINGS_AVACTIS_TEST_MAIL_BODY")
                                . "\n\n"
                                . print_r($body, true);

            loadCoreFile('ascHtmlMimeMail.php');
            $mail = new ascHtmlMimeMail();
            $mail->setText($body);
            $mail->setSubject($subject);
            $mail->setFrom($from_address);
            $to = trim($request->getValueByKey("mail_subject"));

            $rlt = $mail->send(array($to));

            if ($rlt)
            {
                $messages["MESSAGES"][] = 'MSG_MAIL_SENT';
            }
            else
            {
                $messages["ERRORS"][] = 'MAIL_NOT_SENT';
            }
            modApiFunc('Session', 'set', 'ResultMessage', $messages);
        }

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