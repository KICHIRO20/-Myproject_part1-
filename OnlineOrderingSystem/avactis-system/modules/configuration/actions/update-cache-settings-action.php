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
 * Action handler on update cache settings.
 *
 * @package Configuration
 * @access  public
 * @author Alexey Florinsky
 */
class UpdateCacheSettings extends AjaxAction
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
     */
    function UpdateCacheSettings()
    {
    }

    /**
     */
    function onAction()
    {
        global $application;
        $request = $application->getInstance('Request');

    	$cache_level = $request->getValueByKey( SYSCONFIG_CACHE_LEVEL );
    	$values = array(
    	    SYSCONFIG_CACHE_LEVEL => $cache_level
    	);
    	modApiFunc('Configuration', 'setValue', $values);
        modApiFunc('Session','set','ResultMessage','MSG_CACHE_UPDATED');
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