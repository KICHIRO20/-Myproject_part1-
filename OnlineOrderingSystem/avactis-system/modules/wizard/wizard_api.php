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
 * Wizard class
 *
 * Common API class for Wizard functionality.
 *
 * @author HBWSL
 * @package Wizard
 */
class Wizard
{
    function Wizard()
    {
    }

	function install()
	{
		$group_info = array('GROUP_NAME'        => 'WIZARD_SETTINGS',
                            'GROUP_DESCRIPTION' => array(   'NAME'          => array('WZ', 'STORE_SETUP_GUIDE_NAME'),
                                                            'DESCRIPTION'   => array('WZ', 'STORE_SETUP_GUIDE_GROUP_DESCR')),
                            'GROUP_VISIBILITY'    => 'SHOW'); /*@ add to constants */

        modApiFunc('Settings','createGroup', $group_info);

		$param_info = array(
                         'GROUP_NAME'        => $group_info['GROUP_NAME'],
                         'PARAM_NAME'        => 'STORE_SETUP_GUIDE_HIDE',
                         'PARAM_DESCRIPTION' => array( 'NAME'        => array('WZ', 'STORE_SETUP_GUIDE_SHOW_HIDE_NAME'),
                                                       'DESCRIPTION' => array('WZ', 'STORE_SETUP_GUIDE_SHOW_HIDE_DESCR') ),
                         'PARAM_TYPE'          => PARAM_TYPE_LIST,
                         'PARAM_VALUE_LIST'    => array(
                                 array(  'VALUE' => 'HIDE',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('WZ', 'STORE_SETUP_GUIDE_HIDE'),
                                                                       'DESCRIPTION' => array('WZ', 'STORE_SETUP_GUIDE_HIDE_DESCR') ),
                                       ),
                                 array(  'VALUE' => 'SHOW',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('WZ', 'STORE_SETUP_GUIDE_SHOW'),
                                                                       'DESCRIPTION' => array('WZ', 'STORE_SETUP_GUIDE_SHOW_DESCR') ),
                                       )),
                         'PARAM_CURRENT_VALUE' => 'SHOW',
                         'PARAM_DEFAULT_VALUE' => 'HIDE',
        );
        modApiFunc('Settings','createParam', $param_info);
	}
}
?>