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
 * Action handler on "update transaction tracking settings".
 *
 * @package TransactionTracking
 * @access  public
 * @author Vadim Lyalikov
 */
class UpdateTransactionTrackingSettings extends AjaxAction
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
    function UpdateTransactionTrackingSettings()
    {
    }

    /**
     * Update GoogleAnalytics Data
     */
    function updateGA()
    {
        global $application;
        $request = $application->getInstance('Request');
        $settings = $request->getValueByKey(MODULE_GOOGLE_ANALYTICS_UID);
        if(isset($settings['GA_ACCOUNT_NUMBER']))
        {
        	modApiStaticFunc("TransactionTracking", "updateModuleSettings", MODULE_GOOGLE_ANALYTICS_UID, 'GA_ACCOUNT_NUMBER', $settings['GA_ACCOUNT_NUMBER']);
        }
    }

    /**
     * Update ClixGalore Data
     */
    function updateClixGalore()
    {
        global $application;
        $request = $application->getInstance('Request');
        $settings = $request->getValueByKey(MODULE_CLIXGALORE_UID);

        if(isset($settings['CLIXGALORE_AD_ID']))
        {
            modApiStaticFunc("TransactionTracking", "updateModuleSettings", MODULE_CLIXGALORE_UID, 'CLIXGALORE_AD_ID', $settings['CLIXGALORE_AD_ID']);
        }

    }

    /**
     * @
     */
    function onAction()
    {
        global $application;

        $this->updateGA();
        $this->updateClixGalore();

        //Set modules active
        $request = $application->getInstance('Request');
        $ActiveModules = $request->getValueByKey('ModuleActive');
        $InstalledModules = modApiStaticFunc("TransactionTracking", "getInstalledModules");

        foreach($InstalledModules as $uid => $info)
        {
        	$new_status = isset($ActiveModules[$uid]) ? DB_TRUE : DB_FALSE;
        	TransactionTracking::updateModuleStatus($uid, $new_status);
        }
        modApiFunc('Session','set','ResultMessage','MSG_TRANSACTION_TRACKING_SETTINGS_UPDATED');
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