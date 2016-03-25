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
class SettingGroupList
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
    function SettingGroupList()
    {
    }

    /**
     *
     */
    function output()
    {
        global $application;

        $application->registerAttributes(array('GroupList','GroupName','GroupLinkName','GroupDescription','GroupVisibility'));

        return modApiFunc('TmplFiller', 'fill', "configuration/setting-group-list/","container.tpl.html", array());
    }

    function getGroupList()
    {
        $group_list = modApiFunc('Settings','getGroupList', SETTINGS_WITH_DESCRIPTION);
        $html = '';
        foreach ($group_list as $group)
        {
            $this->__groupInfo = $group;
            $html .= modApiFunc('TmplFiller', 'fill', "configuration/setting-group-list/","group-item.tpl.html", array());

            // dirty hack for customer reviews to show rate list window
	    //        if ($group['group_name'] == 'CUSTOMER_REVIEWS')
            //    $html .= modApiFunc('TmplFiller', 'fill', "configuration/setting-group-list/","customer-reviews-rates.tpl.html", array());
        }
        return $html;
    }

    function getTag($tag)
	{
		global $application;
		$value = null;

		switch ($tag)
		{
            case 'GroupList':
                $value = $this->getGroupList();
                break;

            case 'GroupName':
                $value = $this->__groupInfo['group_name'];
                break;

            case 'GroupLinkName':
                $value = $this->__groupInfo['name'];
                break;

            case 'GroupDescription':
                $value = $this->__groupInfo['description'];
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

    var $__groupInfo;

    /**#@-*/

}
?>