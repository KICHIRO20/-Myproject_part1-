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
 * TransactionTracking module.
 *                                        html     ,         ,
 * Checkout CZ.       : Google Analytics e-commerce transactions tracker.

 * @package TransactionTracking
 * @author  Vadim Lyalikov
 * @access  public
*/
class TransactionTracking
{
	/**
	 *            .                                               .
	 *                       (                 transaction tracking
	 *       ) -                                        :
	 *                            .
	 */
	function TransactionTracking()
	{
		$BundledModules = TransactionTracking::getBundledModules();
		$InstalledModules = TransactionTracking::getInstalledModules();
		foreach($BundledModules as $bundled_module_id)
		{
			if(!isset($InstalledModules[$bundled_module_id]))
			{
				//                        .
				loadClass('TransactionTrackingInstaller');
				TransactionTrackingInstaller::intstallModule($bundled_module_id);
			}
		}
	}

    function install()
    {
    	//               .
    	//                                      .
        global $application;
        $obj = &$application->getInstance('MessageResources');

        $tables = TransactionTracking::getTables();
        $query = new DB_Table_Create($tables);
    }

    function getIncludedFileContents($short_fname)
    {
    	$file = new CFile(dirname(__FILE__) . '/includes/' .$short_fname);
    	return $file->getContent();
    }

	/**
	 *                                   ,
	 *                .
	 * (                                  TransactionTracking),
	 *         ,                   TransactionTracking
	 *                                      tracking       .
	 */
	/*static*/ function getBundledModules()
	{
		return array
		(
		    MODULE_GOOGLE_ANALYTICS_UID
		   ,MODULE_CLIXGALORE_UID
		);
	}

    /**
     * Returns a meta description of database tables, defined for storing data
     * of the TransactionTracking module.
     *
     * @return array table meta info
     */
    /*static*/ function getTables ()
    {
        global $application;
        static $tables;
        if (is_array($tables))
        {
            return $tables;
        }
        $tables = array ();

        $modules = 'transaction_tracking_modules';
        $tables[$modules] = array();
        $tables[$modules]['columns'] = array
            (
                'module_id'                  => $modules.'.module_id'
               ,'module_name'                => $modules.'.module_name'
               ,'status_active'              => $modules.'.status_active'

            );
        $tables[$modules]['types'] = array
            (
                'module_id'                  => DBQUERY_FIELD_TYPE_CHAR50 . ' NOT NULL UNIQUE '
               ,'module_name'                => DBQUERY_FIELD_TYPE_CHAR255 . ' NOT NULL DEFAULT \'\''
               ,'status_active'              => DBQUERY_FIELD_BOOLEAN_DEFAULT_FALSE
            );
        $tables[$modules]['primary'] = array
            (
                'module_id'
            );

        $modules_settings = 'transaction_tracking_modules_settings';
        $tables[$modules_settings] = array();
        $tables[$modules_settings]['columns'] = array
            (
                'id'                         => $modules_settings.'.id'
               ,'module_id'                  => $modules_settings.'.module_id'
               ,'key_name'                   => $modules_settings.'.key_name'
               ,'value'                      => $modules_settings.'.value'
            );
        $tables[$modules_settings]['types'] = array
            (
                'id'                         => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
               ,'module_id'                  => DBQUERY_FIELD_TYPE_CHAR50 . ' NOT NULL  DEFAULT \'\''
               ,'key_name'                   => DBQUERY_FIELD_TYPE_CHAR50 . ' NOT NULL  DEFAULT \'\''
               ,'value'                      => DBQUERY_FIELD_TYPE_LONGTEXT . ' NOT NULL'
            );
        $tables[$modules_settings]['primary'] = array
            (
                'id'
            );
        $tables[$modules_settings]['indexes'] = array
            (
                'UNIQUE IDX_module_key' => 'module_id, key_name'
            );

        return $application->addTablePrefix($tables);
    }

    /**
     *                   "                "        .
     *                          AZ
     *          .
     *
     * @return unknown
     */
    function getInstalledModules()
    {
        $result = execQuery('SELECT_TRANSACTION_TRACKING_MODULES', array());

        $InstalledModules = array();
        if(!empty($result))
        {
        	foreach($result as $module)
        	{
        		$InstalledModules[$module['module_id']] = $module;
        	}
        }
        return $InstalledModules;
    }

    function updateModuleSettings($uid, $key_name, $value)
    {
        global $application;
        $tables = TransactionTracking::getTables();
        $columns = $tables['transaction_tracking_modules_settings']['columns'];

        $query = new DB_Update('transaction_tracking_modules_settings');
        $query->addUpdateValue($columns["value"], $value);
        $query->WhereValue($columns["key_name"], DB_EQ, $key_name);
        $query->WhereAnd();
        $query->WhereValue($columns["module_id"], DB_EQ, $uid);
        $result = $application->db->getDB_Result($query);
    }

    function updateModuleStatus($uid, $new_status)
    {
        global $application;
        $tables = TransactionTracking::getTables();
        $columns = $tables['transaction_tracking_modules']['columns'];

        $query = new DB_Update('transaction_tracking_modules');
        $query->addUpdateValue($columns["status_active"], $new_status);
        $query->WhereValue($columns["module_id"], DB_EQ, $uid);
        $result = $application->db->getDB_Result($query);
    }

    /**
     *           transaction        .
     *
     * @return unknown
     */
    function getModulesSettings()
    {
        global $application;
        static $ModulesSettings = NULL;
//        if($ModulesSettings === NULL)
        {
            $ModulesSettings = array();
            $tables = TransactionTracking::getTables();
            $ttm = $tables['transaction_tracking_modules_settings']['columns'];

            $query = new DB_Select();
            $query->addSelectField($ttm['id'], 'id');
            $query->addSelectField($ttm['module_id'], 'module_id');
            $query->addSelectField($ttm['key_name'], 'key_name');
            $query->addSelectField($ttm['value'], 'value');
            $result = $application->db->getDB_Result($query);
            if(!empty($result))
            {
                foreach($result as $entry)
                {
                	$m_uid = $entry['module_id'];
                	if(!isset($ModulesSettings[$m_uid]))
                	{
                		$ModulesSettings[$m_uid] = array();
                	}
                    $ModulesSettings[$m_uid][$entry['key_name']] = $entry['value'];
                }
            }
        }
        return $ModulesSettings;
    }
}

?>