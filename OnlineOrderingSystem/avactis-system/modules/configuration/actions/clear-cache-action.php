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
 * Action handler on clear cache.
 *
 * @package Configuration
 * @access  public
 * @author Alexey Florinsky
 */
class ClearCache extends AjaxAction
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
    function ClearCache()
    {
    }

    /**
     *
     */
    function onAction()
    {
        global $application;

        $getRebuildVal = $application->getInstance('Request');
        $rebuildval = $getRebuildVal->getValueByKey('rebuildval');

        if($rebuildval == 'yes')
        {
      	 	$lang = _ml_strtolower($application->getAppIni('LANGUAGE'));
			modApiFunc('Resources','deleteSYSLabels','SYS');
        	modApiFunc("Resources", "addResourceIniToDB", $application->getAppIni('PATH_ADMIN_RESOURCES').'system-messages-'.$lang.'.ini', 'SYS', 'system_messages', 'AZ');
        	  modApiFunc('Session','set','ResultMessage','MSG_CACHE_UPDATED');
        }
        else
        {

		    CCacheFactory::clearAll();

		    $request = new Request();
		    $request->setView(CURRENT_REQUEST_URL);
		    $application->redirect($request);
		      modApiFunc('Session','set','ResultMessage','MSG_CACHE_UPDATED');
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