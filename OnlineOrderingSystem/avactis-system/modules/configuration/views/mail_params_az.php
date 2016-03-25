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
 * Configuration Module, Mail Settings.
 *
 * @package Configuration
 * @author Ravil Garafutdinov
 */
class MailParamList
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * MailParamList constructor.
     */
    function MailParamList()
    {
        $this->settings = modApiFunc("Configuration", "getMailSettings");
    }

    /**
     *
     */
    function output()
    {
        global $application;

        $application->registerAttributes(array(
                'MessageBox',
                'Messages',
                'Errors'
                ,"MailSettings_Type1"
                ,"MailSettings_Type2"
                ,"MailSettings_HostValue"
                ,"MailSettings_PortValue"
                ,"MailSettings_AuthValue"
                ,"MailSettings_UserValue"
                ,"MailSettings_PassValue"
            ));

        global $application;
        $request = $application->getInstance('Request');

        return modApiFunc('TmplFiller', 'fill', "configuration/mail_settings/","container.tpl.html", array());
    }

    function getMessageBox()
    {
        $html = '';
        if (modApiFunc('Session','is_set','ResultMessage'))
        {
            $messages = modApiFunc('Session','get','ResultMessage');
            modApiFunc('Session','un_set','ResultMessage');

            if (isset($messages['ERRORS']))
            {
                $html .= $this->renderMessages($messages['ERRORS'], "errors.tpl.html");
            }

            if (isset($messages['MESSAGES']))
            {
                $html .= $this->renderMessages($messages['MESSAGES'], "messages.tpl.html");
            }
        }
        return $html;
    }

    function renderMessages($messages, $tpl)
    {
        $this->__msg = '';
        foreach ($messages as $msg)
        {
            $this->__msg .= getMsg("SYS", $msg)."<br>";
        }
        $html = modApiFunc('TmplFiller', 'fill', "configuration/setting-param-list/",$tpl, array());
        $this->__msg = '';
        return $html;
    }

    function getTag($tag)
	{
		global $application;
		$value = null;
		switch ($tag)
		{
		    case 'MessageBox':
		        $value = $this->getMessageBox();
		        break;

            case 'Messages':
                $value = $this->__msg;
                break;

            case 'Errors':
                $value = $this->__msg;
                break;

            case "MailSettings_Type1":
                if ($this->settings["MAIL_TYPE"] == '1')
                    $value = "selected";
                else
                    $value = '';
                break;

            case "MailSettings_Type2":
                if ($this->settings["MAIL_TYPE"] == '2')
                    $value = "selected";
                else
                    $value = '';
                break;

            case "MailSettings_HostValue":
                $value = $this->settings["MAIL_HOST"];
                break;

            case "MailSettings_PortValue":
                $value = $this->settings["MAIL_PORT"];
                break;

            case "MailSettings_AuthValue":
                if ($this->settings["MAIL_AUTH"] == 0)
                    $value = '';
                else
                    $value = "checked";
                break;

            case "MailSettings_UserValue":
                $value = $this->settings["MAIL_USER"];
                break;

            case "MailSettings_PassValue":
                $value = $this->settings["MAIL_PASS"];
                break;
		}
		return $value;
	}


    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */

    var $__group_info;
    var $__param_info;
    var $__msg;

    /**#@-*/

}
?>