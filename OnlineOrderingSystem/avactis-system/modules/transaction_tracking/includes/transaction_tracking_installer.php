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
 * TransactionTrackingInstaller.
 *                transaction tracking        .

 * @package TransactionTracking
 * @author  Vadim Lyalikov
 * @access  public
*/
class TransactionTrackingInstaller
{
    function TransactionTrackingInstaller()
    {
    }

    function getModuleDefaultSettings($uuid)
    {
    	switch($uuid)
    	{
    		case MODULE_GOOGLE_ANALYTICS_UID:
    			return array
    			(
    			    "GA_ACCOUNT_NUMBER" => "UA-xxxxxx-x"
    			);
    			break;
    		case MODULE_CLIXGALORE_UID:
    			return array
    			(
    			    "CLIXGALORE_AD_ID" => "xxxx"
    			);
    			break;
    		default:
    			return array();
    	}
    }

    /**
     *
     * "          ".
     *
     *           .
     *
     * @param unknown_type $uid
     */
	function intstallModule($uid)
	{
        global $application;
        $tables =  TransactionTracking::getTables();

        //                                             .
        $table = 'transaction_tracking_modules';
        $columns = $tables[$table]['columns'];

        $obj = &$application->getInstance('MessageResources', modApiFunc("Modules_Manager","getResFileByShortName", 'TT'), 'AdminZone');
        $module_name = $obj->getMessage($uid . '_name');

        $query = new DB_Insert($table);
        $query->addInsertValue($uid, $columns['module_id']);
        $query->addInsertValue($module_name, $columns['module_name']);
        $query->addInsertValue(DB_FALSE, $columns['status_active']);
        $application->db->getDB_Result($query);

        //
        $module_default_settings = TransactionTrackingInstaller::getModuleDefaultSettings($uid);
        $table = 'transaction_tracking_modules_settings';
        $columns = $tables[$table]['columns'];

        foreach($module_default_settings as $key_name => $value)
        {
	        $query = new DB_Insert($table);
	        $query->addInsertValue($uid, $columns['module_id']);
	        $query->addInsertValue($key_name, $columns['key_name']);
	        $query->addInsertValue($value, $columns['value']);
	        $application->db->getDB_Result($query);
        }
	}
}

?>