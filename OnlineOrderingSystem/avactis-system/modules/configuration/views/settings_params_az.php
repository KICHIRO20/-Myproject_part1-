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
 * Configuration Module, Cache Settings.
 *
 * @package Configuration
 * @author Alexey Florinsky
 */
class SettingParamList
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * CacheSettings constructor.
     */
    function SettingParamList()
    {
    }

    /**
     *
     */
    function output()
    {
        global $application;

        $application->registerAttributes(array(
                                                'GroupName',
                                                'GroupLinkName',
                                                'GroupDescription',
                                                'MessageBox',
                                                'Messages',
                                                'Items',
                                                'ParamLinkName',
                                                'ParamControl',
                                                'ParamDescription',
                                                'GroupAdditionalButton'
                                              ));

        global $application;
        $request = $application->getInstance('Request');
        if ( ($__group_name = $request->getValueByKey('group')) === null )
        {
            die('ERROR: Group Name undefined');
        }

        $this->__group_info = modApiFunc('Settings','getGroupInfo',$__group_name, SETTINGS_WITH_DESCRIPTION);


        return modApiFunc('TmplFiller', 'fill', "configuration/setting-param-list/","container.tpl.html", array());
    }

    function getParamList()
    {
        $param_list = modApiFunc('Settings','getParamListByGroup', $this->__group_info['group_name'], SETTINGS_WITH_DESCRIPTION);
        $html = '';
        foreach ($param_list as $param)
        {
            $this->__param_info = $param;
            $html .= modApiFunc('TmplFiller', 'fill', "configuration/setting-param-list/","item.tpl.html", array());
        }
        return $html;
    }

    function getMessageBox()
    {
        $html = '';
        if (modApiFunc('Session','is_set','AplicationSettingsMessages'))
        {
            $messages = modApiFunc('Session','get','AplicationSettingsMessages');
            modApiFunc('Session','un_set','AplicationSettingsMessages');

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
            $this->__msg .= $msg."<br>";
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

		    case 'ParamLinkName':
                $value = $this->__param_info['name'];
                break;

            case 'ParamControl':
                $value = modApiFunc('Settings','getParamHTMLControl',$this->__param_info['group_name'], $this->__param_info['param_name']);
                break;

            case 'ParamDescription':
                $value = $this->__param_info['description'];
                break;

            case 'Items':
                $value = $this->getParamList();
                break;

            case 'GroupName':
                $value = $this->__group_info['group_name'];
                break;

            case 'GroupLinkName':
                $value = $this->__group_info['name'];
                break;

            case 'GroupDescription':
                $value = $this->__group_info['description'];
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