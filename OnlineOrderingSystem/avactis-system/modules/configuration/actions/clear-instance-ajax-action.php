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
 * Action handler on AJAX clear cache.
 *
 * @package Configuration
 * @access  public
 * @author Alexey Astafyev
 */
class ClearInstanceAjax extends AjaxAction
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
    function ClearInstanceAjax()
    {
    }

    /**
     *
     */
    function onAction()
    {
        global $application;
        $request = $application->getInstance('Request');
        $instance = $request->getValueByKey('instance');
	$log_clear_type = $request->getValueByKey('log_clear_type');
        $value = '';
        switch($instance)
        {
            case 'cache':
				CCacheFactory::clearAll();
                $value = getMsg("SYS","MSG_CACHE_CLEARED");
                if (APC_EXTENSION_LOADED) apc_clear_cache("user");
            break;
            case 'timeline':
		modApiFunc('Timeline', 'clearTimeline',$log_clear_type);
               	$timeline = modApiFunc('Timeline','getTimelineRecsCount');
               	if($timeline == 0)
		$value = getMsg("SYS","ADMIN_PHP_FILES_NO_LOG_RECORDS");
                else
              $value = $timeline.getMsg("SYS","ADMIN_PHP_FILES_LOG_RECORDS");
            break;
            default: break;
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

    /**#@-*/
}

?>