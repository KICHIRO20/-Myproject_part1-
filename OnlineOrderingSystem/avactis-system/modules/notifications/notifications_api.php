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

loadModuleFile('notifications/abstract/notification_content.php');

/**
 * Notifications module.
 *
 * @package Notifications
 * @author Alexander Girin
 */
class Notifications
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * Letters  constructor.
     */
    function Notifications()
    {
    }

    /**
     * Load Module state.
     */
    function loadState()
    {
        if(modApiFunc('Session', 'is_Set', 'currentNotificationId'))
        {
            $this->currentNotificationId = modApiFunc('Session', 'get', 'currentNotificationId');
        }
        else
        {
            $this->currentNotificationId = NULL;
        }
    }

    /**
     * Saves a Module state.
     */
    function saveState()
    {
        if($this->currentNotificationId != NULL)
        {
            modApiFunc('Session', 'set', 'currentNotificationId', $this->currentNotificationId);
        }
        else
        {
            modApiFunc('Session', 'un_Set', 'currentNotificationId');
        }
    }

    function install()
    {
        $query = new DB_Table_Create(Notifications::getTables());
        loadClass('Notifications_Installer');
        $installer = new Notifications_Installer();
        $installer->doInstall();

        $param_info = array(
                         'GROUP_NAME'        => 'TIMELINE',
                         'PARAM_NAME'        => 'LOG_EMAIL_SEND',
                         'PARAM_DESCRIPTION' => array( 'NAME'        => array('NTFCTN', 'ADV_CFG_LOG_EMAIL_SEND_NAME'),
                                                       'DESCRIPTION' => array('NTFCTN', 'ADV_CFG_LOG_EMAIL_SEND_DESCR') ),
                         'PARAM_TYPE'          => PARAM_TYPE_LIST,
                         'PARAM_VALUE_LIST'    => array(
                                 array(  'VALUE' => 'NO',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('NTFCTN', 'ADV_CFG_LOG_EMAIL_SEND_NO'),
                                                                       'DESCRIPTION' => array('NTFCTN', 'ADV_CFG_LOG_EMAIL_SEND_NO') ),
                                       ),
                                 array(  'VALUE' => 'YES',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('NTFCTN', 'ADV_CFG_LOG_EMAIL_SEND_YES'),
                                                                       'DESCRIPTION' => array('NTFCTN', 'ADV_CFG_LOG_EMAIL_SEND_YES') ),
                                       )),
                         'PARAM_CURRENT_VALUE' => 'YES',
                         'PARAM_DEFAULT_VALUE' => 'YES',
        );
        modApiFunc('Settings','createParam', $param_info);
	$group_info = array('GROUP_NAME'        => 'EMAIL_NOTIFICATION_SETTINGS',
			'GROUP_DESCRIPTION' => array(   'NAME'          => array('NTFCTN', 'ADV_CFG_NTFCTN_SETTINGS_GROUP_NAME'),
				'DESCRIPTION'   => array('NTFCTN', 'ADV_CFG_NTFCTN_SETTINGS_GROUP_DESCR')),
			'GROUP_VISIBILITY'    => 'SHOW'); /*@ add to constants */

	modApiFunc('Settings','createGroup', $group_info);

	$param_info = array(
			'GROUP_NAME'        => $group_info['GROUP_NAME'],
			'PARAM_NAME'        => 'EMAIL_NOTIFICATION_FORMAT',
			'PARAM_DESCRIPTION' => array( 'NAME'        => array('NTFCTN', 'ADV_EMAIL_NOTIFICATION_NAME'),
				'DESCRIPTION' => array('NTFCTN', 'ADV_EMAIL_NOTIFICATION_DESCR') ),
			'PARAM_TYPE'          => PARAM_TYPE_LIST,
			'PARAM_VALUE_LIST'    => array(
				array(  'VALUE' => 'TEXT',
					'VALUE_DESCRIPTION' => array( 'NAME'        => array('NTFCTN', 'ADV_CFG_EMAIL_TEXT'),
                                                                       'DESCRIPTION' => array('NTFCTN', 'ADV_CFG_EMAIL_TEXT') ),
                                       ),
                                 array(  'VALUE' => 'HTML',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('NTFCTN', 'ADV_CFG_EMAIL_HTML'),
                                                                       'DESCRIPTION' => array('NTFCTN', 'ADV_CFG_EMAIL_HTML') ),
                                       )),
                         'PARAM_CURRENT_VALUE' => 'TEXT',
                         'PARAM_DEFAULT_VALUE' => 'TEXT',
        );
        modApiFunc('Settings','createParam', $param_info);
    }

    /**
     * Installs the specified module in the system.
     *
     * The uninstall() method is called statically.
     * To call other methods of this class from this method,
     * the static call is used, for example,
     * Configuration::getTables() instead of $this->getTables().
     */
    function uninstall()
    {
        $query = new DB_Table_Delete(Notifications::getTables());
    }

    /**
     * Gets the array of meta description of module tables.
     *
     * The array structure of meta description of the table:
     * <code>
     *      $tables = array ();
     *      $table_name = 'table_name';
     *      $tables[$table_name] = array();
     *      $tables[$table_name]['columns'] = array
     *      (
     *          'fn1'               => 'table_name.field_name_1'
     *         ,'fn2'               => 'table_name.field_name_2'
     *         ,'fn3'               => 'table_name.field_name_3'
     *         ,'fn4'               => 'table_name.field_name_4'
     *      );
     *      $tables[$table_name]['types'] = array
     *      (
     *          'fn1'               => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
     *         ,'fn2'               => DBQUERY_FIELD_TYPE_INT .' NOT NULL'
     *         ,'fn3'               => DBQUERY_FIELD_TYPE_CHAR255
     *         ,'fn4'               => DBQUERY_FIELD_TYPE_TEXT
     *      );
     *      $tables[$table_name]['primary'] = array
     *      (
     *          'fn1'       # several key fields may be used, e.g. - 'fn1', 'fn2'
     *      );
     *      $tables[$table_name]['indexes'] = array
     *      (
     *          'index_name1' => 'fn2'      # several fields can be used in one index, e.g. - 'fn2, fn3'
     *         ,'index_name2' => 'fn3'
     *      );
     * </code>
     *
     * @return array -  the meta description of module tables
     */
    function getTables()
    {
        static $tables;

        if (is_array($tables))
        {
            return $tables;
        }

        $tables = array ();

        $table = 'notifications';
        $tables[$table] = array();
        $tables[$table]['columns'] = array
            (
                'id'                        => $table.'.notification_id'
               ,'na_id'                     => $table.'.notification_action_id'
               ,'name'                      => $table.'.notification_name'
               ,'subject'                   => $table.'.notification_subject'
               ,'body'                      => $table.'.notification_body'
               ,'from_email_custom_address' => $table.'.notification_from_email_custom_address'
               ,'from_email_admin_id'       => $table.'.notification_from_email_admin_id'
               ,'from_email_code'           => $table.'.notification_from_email_code'
               ,'active'                    => $table.'.notification_active'
            );
        $tables[$table]['types'] = array
            (
                'id'                        => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
               ,'na_id'                     => DBQUERY_FIELD_TYPE_INT
               ,'name'                      => DBQUERY_FIELD_TYPE_CHAR255
               ,'subject'                   => DBQUERY_FIELD_TYPE_CHAR255
               ,'body'                      => DBQUERY_FIELD_TYPE_LONGTEXT
               ,'from_email_custom_address' => DBQUERY_FIELD_TYPE_CHAR50 ." DEFAULT ''"
               ,'from_email_admin_id'       => DBQUERY_FIELD_TYPE_INT ." DEFAULT NULL"
               ,'from_email_code'           => DBQUERY_FIELD_TYPE_CHAR50
               ,'active'                    => DBQUERY_FIELD_TYPE_CHAR10
          );
        $tables[$table]['primary'] = array
            (
                'id'
            );
        $tables[$table]['indexes'] = array
            (
                'IDX_na' => 'na_id'
            );

        $table = 'notification_actions';
        $tables[$table] = array();
        $tables[$table]['columns'] = array
            (
                'id'                => $table.'.notification_action_id'
               ,'code'              => $table.'.action_code'
               ,'name'              => $table.'.action_name'
            );
        $tables[$table]['types'] = array
            (
                'id'                => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
               ,'code'              => DBQUERY_FIELD_TYPE_CHAR255
               ,'name'              => DBQUERY_FIELD_TYPE_CHAR255
          );
        $tables[$table]['primary'] = array
            (
                'id'
            );

        $table = 'notification_action_options';
        $tables[$table] = array();
        $tables[$table]['columns'] = array
            (
                'id'                => $table.'.notification_action_option_id'
               ,'na_id'             => $table.'.notification_action_id'
               ,'name'              => $table.'.option_name'
               ,'input_type'        => $table.'.option_input_type'
            );
        $tables[$table]['types'] = array
            (
                'id'                => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
               ,'na_id'             => DBQUERY_FIELD_TYPE_INT
               ,'name'              => DBQUERY_FIELD_TYPE_CHAR255
               ,'input_type'        => DBQUERY_FIELD_TYPE_CHAR50
          );
        $tables[$table]['primary'] = array
            (
                'id'
            );
        $tables[$table]['indexes'] = array
            (
                'IDX_na' => 'na_id'
            );

        $table = 'notification_action_option_values';
        $tables[$table] = array();
        $tables[$table]['columns'] = array
            (
                'id'                => $table.'.notification_action_option_value_id'
               ,'nao_id'            => $table.'.notification_action_option_id'
               ,'name'              => $table.'.option_value_name'
               ,'key'               => $table.'.option_key'
            );
        $tables[$table]['types'] = array
            (
                'id'                => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
               ,'nao_id'            => DBQUERY_FIELD_TYPE_INT
               ,'name'              => DBQUERY_FIELD_TYPE_CHAR255
               ,'key'               => DBQUERY_FIELD_TYPE_CHAR20
          );
        $tables[$table]['primary'] = array
            (
                'id'
            );
        $tables[$table]['indexes'] = array
            (
                'IDX_nao' => 'nao_id'
            );

        $table = 'option_values_to_notification';
        $tables[$table] = array();
        $tables[$table]['columns'] = array
            (
                'naov_id'           => $table.'.notification_action_option_value_id'
               ,'n_id'              => $table.'.notification_id'
               ,'value'             => $table.'.value'
            );
        $tables[$table]['types'] = array
            (
                'naov_id'           => DBQUERY_FIELD_TYPE_INT
               ,'n_id'              => DBQUERY_FIELD_TYPE_INT
               ,'value'             => DBQUERY_FIELD_TYPE_CHAR20
          );
        $tables[$table]['primary'] = array
            (
            );
        $tables[$table]['indexes'] = array
            (
                'IDX_naov' => 'naov_id'
               ,'IDX_n'    => 'n_id'
            );

        $table = 'notification_send_to';
        $tables[$table] = array();
        $tables[$table]['columns'] = array
            (
                'n_id'              => $table.'.notification_id'
               ,'email'             => $table.'.email'
               ,'code'              => $table.'.email_code'
            );
        $tables[$table]['types'] = array
            (
                'n_id'              => DBQUERY_FIELD_TYPE_INT
               ,'email'             => DBQUERY_FIELD_TYPE_CHAR255
               ,'code'              => DBQUERY_FIELD_TYPE_CHAR50
          );
        $tables[$table]['primary'] = array
            (
            );
        $tables[$table]['indexes'] = array
            (
                'IDX_n'    => 'n_id'
            );

        $table = 'notification_infotags';
        $tables[$table] = array();
        $tables[$table]['columns'] = array
            (
                'id'                => $table.'.notification_infotag_id'
               ,'name'              => $table.'.infotag_name'
            );
        $tables[$table]['types'] = array
            (
                'id'                => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
               ,'name'              => DBQUERY_FIELD_TYPE_CHAR50
          );
        $tables[$table]['primary'] = array
            (
                'id'
            );

        $table = 'infotags_to_action';
        $tables[$table] = array();
        $tables[$table]['columns'] = array
            (
                'id'                => $table.'.infotag_to_action_id'
               ,'ni_id'             => $table.'.notification_infotag_id'
               ,'na_id'             => $table.'.notification_action_id'
            );
        $tables[$table]['types'] = array
            (
                'id'                => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
               ,'ni_id'             => DBQUERY_FIELD_TYPE_INT
               ,'na_id'             => DBQUERY_FIELD_TYPE_INT
          );
        $tables[$table]['primary'] = array
            (
                'id'
            );
        $tables[$table]['indexes'] = array
            (
                'IDX_ni'    => 'ni_id'
               ,'IDX_na'    => 'na_id'
            );

        $table = 'notification_blocktags';
        $tables[$table] = array();
        $tables[$table]['columns'] = array
            (
                'id'                => $table.'.notification_blocktag_id'
               ,'name'              => $table.'.blocktag_name'
            );
        $tables[$table]['types'] = array
            (
                'id'                => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
               ,'name'              => DBQUERY_FIELD_TYPE_CHAR50
          );
        $tables[$table]['primary'] = array
            (
                'id'
            );

        $table = 'blocktags_to_action';
        $tables[$table] = array();
        $tables[$table]['columns'] = array
            (
                'id'                => $table.'.blocktag_to_action_id'
               ,'nb_id'             => $table.'.notification_blocktag_id'
               ,'na_id'             => $table.'.notification_action_id'
            );
        $tables[$table]['types'] = array
            (
                'id'                => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
               ,'nb_id'             => DBQUERY_FIELD_TYPE_INT
               ,'na_id'             => DBQUERY_FIELD_TYPE_INT
          );
        $tables[$table]['primary'] = array
            (
                'id'
            );
        $tables[$table]['indexes'] = array
            (
                'IDX_nb'    => 'nb_id'
               ,'IDX_na'    => 'na_id'
            );

        $table = 'infotags_to_blocktag';
        $tables[$table] = array();
        $tables[$table]['columns'] = array
            (
                'id'                => $table.'.infotag_to_blocktag_id'
               ,'nb_id'             => $table.'.notification_blocktag_id'
               ,'ni_id'             => $table.'.notification_infotag_id'
            );
        $tables[$table]['types'] = array
            (
                'id'                => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
               ,'nb_id'             => DBQUERY_FIELD_TYPE_INT
               ,'ni_id'             => DBQUERY_FIELD_TYPE_INT
          );
        $tables[$table]['primary'] = array
            (
                'id'
            );
        $tables[$table]['indexes'] = array
            (
                'IDX_nb'    => 'nb_id'
               ,'IDX_ni'    => 'ni_id'
            );

        $table = 'notification_blocktag_bodies';
        $tables[$table] = array();
        $tables[$table]['columns'] = array
            (
                'id'                => $table.'.notification_blocktag_body_id'
               ,'nb_id'             => $table.'.notification_blocktag_id'
               ,'n_id'              => $table.'.notification_id'
               ,'body'              => $table.'.blocktag_body'
            );
        $tables[$table]['types'] = array
            (
                'id'                => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
               ,'nb_id'             => DBQUERY_FIELD_TYPE_INT
               ,'n_id'              => DBQUERY_FIELD_TYPE_INT
               ,'body'              => DBQUERY_FIELD_TYPE_LONGTEXT
          );
        $tables[$table]['primary'] = array
            (
                'id'
            );
        $tables[$table]['indexes'] = array
            (
                'IDX_nb'    => 'nb_id'
               ,'IDX_n'     => 'n_id'
            );

        global $application;
        return $application->addTablePrefix($tables);
    }

    /**
     *             -                                 email                         -
     *                            .       -              .
     */
    function OnRemoveAdmin($id)
    {
        global $application;

        $tables = $this->getTables();
        $n  = $tables['notifications']['columns'];

        $query = new DB_Select();
        $query->addSelectField($n['id'], 'Id');
        $query->WhereValue($n['from_email_admin_id'], DB_EQ, $id);
        $result = $application->db->getDB_Result($query);
        $MR = &$application->getInstance('MessageResources','notifications-messages','AdminZone');
        if(sizeof($result) > 0)
        {
        	$msg = $MR->getMessage('NTFCTN_EVENT_REMOVE_ADMIN_MSG', array($id));
        	return $msg;
        }
        else
        {
        	return NULL;
        }
    }

    /**
     * Gets a list of notification specified in the system.
     *
     * @param integer $na_id - action id, if parameter is defined, return a list of notifications for
     *
     *@return array - a list of notifications
     */
    function getNotificationsList($na_id=NULL)
    {
        global $application;

        $tables = $this->getTables();
        $n  = $tables['notifications']['columns'];
        $na = $tables['notification_actions']['columns'];

        $query = new DB_Select();

        $query->setMultiLangAlias('_ml_ntfctn_name', 'notifications', $n['name'], $n['id'], 'Notifications');
        $query->setMultiLangAlias('_ml_ntfctn_subject', 'notifications', $n['subject'], $n['id'], 'Notifications');
        $query->setMultiLangAlias('_ml_ntfctn_from_email_custom_address', 'notifications', $n['from_email_custom_address'], $n['id'], 'Notifications');

        $query->addSelectField($n['id'], 'Id');

        $query->addSelectField($query->getMultiLangAlias('_ml_ntfctn_name'), 'Name');
        $query->addSelectField($query->getMultiLangAlias('_ml_ntfctn_subject'), 'Subject');
        $query->addSelectField($query->getMultiLangAlias('_ml_ntfctn_from_email_custom_address'), 'From_addr');

        $query->addSelectField($n['from_email_admin_id'], 'Admin_ID');
        $query->addSelectField($n['from_email_code'], 'Email_Code');
        $query->addSelectField($n['active'], 'Active');
        $query->addSelectField($na['name'], 'Action');
        $query->WhereField($n['na_id'], DB_EQ, $na['id']);
        if ($na_id)
        {
            $query->WhereAnd();
            $query->WhereField($n['na_id'], DB_EQ, $na_id);
        }

        return $application->db->getDB_Result($query);
    }

    /**
     * Gets notification info.
     *
     *@param integer $id - notification id
     *
     *@return array - notification info list
     */
    function getNotificationInfo($id)
    {
        global $application;

        $tables = $this->getTables();
        $n  = $tables['notifications']['columns'];
        $na = $tables['notification_actions']['columns'];

        $query = new DB_Select();

        $query->setMultiLangAlias('_ml_ntfctn_name', 'notifications', $n['name'], $n['id'], 'Notifications');
        $query->setMultiLangAlias('_ml_ntfctn_subject', 'notifications', $n['subject'], $n['id'], 'Notifications');
        $query->setMultiLangAlias('_ml_ntfctn_body', 'notifications', $n['body'], $n['id'], 'Notifications');
        $query->setMultiLangAlias('_ml_ntfctn_from_email_custom_address', 'notifications', $n['from_email_custom_address'], $n['id'], 'Notifications');

        $query->addSelectField($n['id'], 'Id');

        $query->addSelectField($query->getMultiLangAlias('_ml_ntfctn_name'), 'Name');
        $query->addSelectField($query->getMultiLangAlias('_ml_ntfctn_subject'), 'Subject');
        $query->addSelectField($query->getMultiLangAlias('_ml_ntfctn_body'), 'Body');
        $query->addSelectField($query->getMultiLangAlias('_ml_ntfctn_from_email_custom_address'), 'From_addr');

        $query->addSelectField($n['from_email_admin_id'], 'Admin_ID');
        $query->addSelectField($n['from_email_code'], 'Email_Code');
        $query->addSelectField($n['active'], 'Active');
        $query->addSelectField($n['na_id'], 'Action_id');
        $query->WhereValue($n['id'], DB_EQ, $id);

        return $application->db->getDB_Result($query);
    }

    /**
     * Gets a body of notification block tag.
     *
     *@param integer $n_id - notification id
     *@param integer $b_id - block tag id
     *
     *@return string - block tag body
     */
    function getNotificationBlockBody($n_id, $b_id)
    {
        $body = "";
        global $application;

        $tables = $this->getTables();
        $nbb = $tables['notification_blocktag_bodies']['columns'];

        $query = new DB_Select();

        $query->setMultiLangAlias('_ml_ntfctn_body', 'notification_blocktag_bodies', $nbb['body'], $nbb['id'], 'Notifications');

        $query->addSelectField($query->getMultiLangAlias('_ml_ntfctn_body'), 'Body');
        $query->WhereValue($nbb['n_id'], DB_EQ, $n_id);
        $query->WhereAnd();
        $query->WhereValue($nbb['nb_id'], DB_EQ, $b_id);
        $result = $application->db->getDB_Result($query);

        if (sizeof($result) != 0)
        {
            $body = $result[0]['Body'];
        }

        return $body;
    }


    /**
     * Gets a list of actions
     *
     *@return array - a list of actions
     */
    function getActionsList()
    {
        global $application;

        $tables = $this->getTables();
        $na = $tables['notification_actions']['columns'];

        $query = new DB_Select();
        $query->addSelectField($na['id'], 'Id');
        $query->addSelectField($na['code'], 'Code');
        $query->addSelectField($na['name'], 'Name');

        return $application->db->getDB_Result($query);
    }

    /**
     * Gets a list of addresses.
     *
     *@param integer $n_id - notification id
     *
     *@return array - a list of addresses
     */
    function getSendToList($n_id)
    {
        global $application;

        $tables = $this->getTables();
        $nst = $tables['notification_send_to']['columns'];

        $query = new DB_Select();
        $query->addSelectField($nst['email'], 'Email');
        $query->addSelectField($nst['code'], 'Code');
        $query->WhereValue($nst['n_id'], DB_EQ, $n_id);

        return $application->db->getDB_Result($query);
    }

    /**
     * Gets an email address by its code or uses directly the sended address.
     *
     *@param string $email - address
     *@param string $code - address code
     *
     *@return string - email address
     */
    function getEmail($email, $code)
    {
        global $application;

        switch ($code)
        {
            case 'EMAIL_CUSTOM':
                    $retval = $email;
                    break;
            case 'EMAIL_CUSTOMER':
                    $retval = '';
                    break;
            case 'EMAIL_STORE_OWNER':
                    $retval = modApiFunc("Configuration", "getValue", SYSCONFIG_STORE_OWNER_EMAIL);
                    break;
            case 'EMAIL_SITE_ADMINISTRATOR':
                    $retval = modApiFunc("Configuration", "getValue", SYSCONFIG_STORE_OWNER_SITE_ADMINISTRATOR_EMAIL);
                    break;
            case 'EMAIL_ORDERS_DEPARTMENT':
                    $retval = modApiFunc("Configuration", "getValue", SYSCONFIG_STORE_OWNER_ORDERS_DEPARTMENT_EMAIL);
                    break;
            case 'EMAIL_ADMINISTRATOR':
                    $retval = $email;
                    break;
            default:
                    $retval = '';
                    break;
        }

        return $retval;
    }

    /**
     * Gets complete email address in the format of Name <email>.
     *
     *@param string $email - address or admin_id if $code == 'EMAIL_ADMINISTRATOR'
     *@param string $code - address code
     *@param bool $for_send - false on default, is used to substitute <> or &lt;&gt carachters;
     *
     *@return string - complete email address
     */
    function getExtendedEmail($email, $code, $for_send = false, $admin_id = NULL, $include_lng = false)
    {
        global $application;
        $MessageResources = &$application->getInstance('MessageResources','notifications-messages','AdminZone');

        if ($for_send)
        {
            $openBracket = "<";
            $closeBracket = ">";
        }
        else
        {
            $openBracket = "&lt;";
            $closeBracket = "&gt;";
        }

        $retlng = modApiFunc('MultiLang', 'getDefaultLanguage');

        switch ($code)
        {
            case 'EMAIL_CUSTOM':
                    $retval = $for_send? $email:prepareHTMLDisplay($email);
                    break;
            case 'EMAIL_CUSTOMER':
                    $retval = $MessageResources->getMessage('EMAIL_CUSTOMER');
                    break;
            case 'EMAIL_STORE_OWNER':
                    $retval = modApiFunc("Configuration", "getValue", SYSCONFIG_STORE_OWNER_EMAIL_FROM)." $openBracket".modApiFunc("Configuration", "getValue", SYSCONFIG_STORE_OWNER_EMAIL)."$closeBracket";
                    break;
            case 'EMAIL_SITE_ADMINISTRATOR':
                    $retval = modApiFunc("Configuration", "getValue", SYSCONFIG_STORE_OWNER_SITE_ADMINISTRATOR_EMAIL_FROM)." $openBracket".modApiFunc("Configuration", "getValue", SYSCONFIG_STORE_OWNER_SITE_ADMINISTRATOR_EMAIL)."$closeBracket";
                    break;
            case 'EMAIL_ORDERS_DEPARTMENT':
                    $retval = modApiFunc("Configuration", "getValue", SYSCONFIG_STORE_OWNER_ORDERS_DEPARTMENT_EMAIL_FROM)." $openBracket".modApiFunc("Configuration", "getValue", SYSCONFIG_STORE_OWNER_ORDERS_DEPARTMENT_EMAIL)."$closeBracket";
                    break;
            case 'EMAIL_ADMINISTRATOR':
                    if ($admin_id == NULL)
                    {
                        $admin_id = $email;
                    }
                    $admin_info = modApiFunc("Users", "getUserInfo", $admin_id);
                    if (!isset($admin_info["id"]))
                    {
                        $retval = '';
                    }
                    else
                    {
                        $retval = $admin_info['firstname']." ".$admin_info['lastname']." $openBracket".$admin_info['email']."$closeBracket";
                        $retlng = modApiFunc('Users', 'getAccountLanguageById', $admin_id);
                        if (!$retlng || !modApiFunc('MultiLang', 'checkLanguage', $retlng, false))
                            $retlng = modApiFunc('MultiLang', 'getDefaultLanguage');
                    }
                    break;
            default:
                    $retval = '';
                    break;
        }

        if ($include_lng)
            return array($retval, $retlng);

        return $retval;
    }

    /**
     * Gets a list of possible addresses of senders and receivers.
     *
     *@param string $dirrection - address type 'to' or 'from'
     *
     *@return array - a list of addresses
     */
    function getSendSourceList($dirrection)
    {
        global $application;
        $MessageResources = &$application->getInstance('MessageResources','notifications-messages','AdminZone');

        $retval = array();
        if ($dirrection == "to")
        {
            $value = $MessageResources->getMessage('EMAIL_CUSTOMER');
            $retval["EMAIL_CUSTOMER="] = $value;
        }
        $email = modApiFunc("Configuration", "getValue", SYSCONFIG_STORE_OWNER_EMAIL);
        $value = modApiFunc("Configuration", "getValue", SYSCONFIG_STORE_OWNER_EMAIL_FROM)." &lt;".$email."&gt;";
        $retval["EMAIL_STORE_OWNER=".$email] = $value;
        $email = modApiFunc("Configuration", "getValue", SYSCONFIG_STORE_OWNER_SITE_ADMINISTRATOR_EMAIL);
        $value = modApiFunc("Configuration", "getValue", SYSCONFIG_STORE_OWNER_SITE_ADMINISTRATOR_EMAIL_FROM)." &lt;".$email."&gt;";
        $retval["EMAIL_SITE_ADMINISTRATOR=".$email] = $value;
        $email = modApiFunc("Configuration", "getValue", SYSCONFIG_STORE_OWNER_ORDERS_DEPARTMENT_EMAIL);
        $value = modApiFunc("Configuration", "getValue", SYSCONFIG_STORE_OWNER_ORDERS_DEPARTMENT_EMAIL_FROM)." &lt;".$email."&gt;";
        $retval["EMAIL_ORDERS_DEPARTMENT=".$email] = $value;

        $adminsList = modApiFunc("Users", "getAdminMembersList");
        foreach ($adminsList as $adminInfo)
        {
            $value = $adminInfo['firstname']." ".$adminInfo['lastname']." &lt;".$adminInfo['email']."&gt;";
            $retval["EMAIL_ADMINISTRATOR=".$adminInfo['id']] = $value;
        }

        return $retval;
    }

    /**
     * Gets option info for action.
     *
     *@param integer $a_id - action id
     *
     *@return array - option info
     */
    function getActionOptionInfo($a_id)
    {
        global $application;

        $tables = $this->getTables();
        $nao = $tables['notification_action_options']['columns'];

        $query = new DB_Select();
        $query->addSelectField($nao['id'], 'Id');
        $query->addSelectField($nao['name'], 'Name');
        $query->addSelectField($nao['input_type'], 'InputType');
        $query->WhereValue($nao['na_id'], DB_EQ, $a_id);

        return $application->db->getDB_Result($query);
    }

    /**
     * Gets action option info.
     *
     *@param integer $a_id - action id
     *
     *@return array - option info
     */
    function getActionOptionValuesList($n_id, $o_id)
    {
        global $application;

        $tables = $this->getTables();
        $naov = $tables['notification_action_option_values']['columns'];
        $ov2n = $tables['option_values_to_notification']['columns'];

        $query = new DB_Select();
        $query->addSelectField($naov['id'], 'Id');
        $query->addSelectField($naov['name'], 'Name');
        $query->WhereValue($naov['nao_id'], DB_EQ, $o_id);

        $result = $application->db->getDB_Result($query);

        if ($result)
            foreach($result as $k => $v)
                $result[$k]['Name'] = getMsg('SYS', $v['Name']);

        return $result;
    }

    /**
     * Gets an option value for the action.
     *
     *@param integer $n_id - notification id
     *@param integer $o_id - option id
     *
     *@return a value
     */
    function getNotificationActionOptionValue($n_id, $o_id)
    {
        global $application;

        $tables = $this->getTables();
        $ov2n = $tables['option_values_to_notification']['columns'];

        $query = new DB_Select();
        $query->addSelectField($ov2n['value'], 'value');
        $query->WhereValue($ov2n['n_id'], DB_EQ, $n_id);
        $query->WhereAnd();
        $query->WhereValue($ov2n['naov_id'], DB_EQ, $o_id);
        $result = $application->db->getDB_Result($query);

        if (!sizeof($result))
        {
            $result = array('id' => '', 'value' => '');
        }
        else
        {
            $result = $result[0];
        }
        return $result;
    }

    /**
     * Gets a list of available info  block tags by all actions.
     *
     *@param array $actionsList - a list of all actions
     *
     *@return array - a list of tags associated with actions
     */
    function getAvailableTagsList($actionsList)
    {
        global $application;

        $tables = $this->getTables();
        $i2a = $tables['infotags_to_action']['columns'];
        $ni = $tables['notification_infotags']['columns'];
        $b2a = $tables['blocktags_to_action']['columns'];
        $nb = $tables['notification_blocktags']['columns'];
        $i2b = $tables['infotags_to_blocktag']['columns'];

        $tagsList = array();
        foreach ($actionsList as $actionInfo)
        {
            $tagsList[$actionInfo['Id']] = array();

            $query = new DB_Select();
            $query->addSelectField($ni['id'], 'Id');
            $query->addSelectField($ni['name'], 'InfoTag');
            $query->WhereField($ni['id'], DB_EQ, $i2a["ni_id"]);
            $query->WhereAnd();
            $query->WhereValue($i2a['na_id'], DB_EQ, $actionInfo['Id']);
            $result = $application->db->getDB_Result($query);

            $InfoTags = array();
            foreach ($result as $InfoTag)
            {
                $InfoTags[$InfoTag['Id']] = $InfoTag['InfoTag'];
            }
            $tagsList[$actionInfo['Id']]['InfoTags'] = $InfoTags;

            $query = new DB_Select();
            $query->addSelectField($nb['id'], 'Id');
            $query->addSelectField($nb['name'], 'BlockTag');
            $query->WhereField($nb['id'], DB_EQ, $b2a["nb_id"]);
            $query->WhereAnd();
            $query->WhereValue($b2a['na_id'], DB_EQ, $actionInfo['Id']);
            $result = $application->db->getDB_Result($query);

            $tagsList[$actionInfo['Id']]['BlockTags'] = array();
            foreach ($result as $BlockTag)
            {
                $query = new DB_Select();
                $query->addSelectField($ni['id'], 'Id');
                $query->addSelectField($ni['name'], 'InfoTag');
                $query->WhereField($ni['id'], DB_EQ, $i2b["ni_id"]);
                $query->WhereAnd();
                $query->WhereValue($i2b['nb_id'], DB_EQ, $BlockTag['Id']);
                $_result = $application->db->getDB_Result($query);

                $InfoTags = array();
                foreach ($_result as $InfoTag)
                {
                    $InfoTags[$InfoTag['Id']] = $InfoTag['InfoTag'];
                }

                $tagsList[$actionInfo['Id']]['BlockTags'][$BlockTag['Id']] = array("BlockTag" => $BlockTag['BlockTag'], "BlockInfoTags" => $InfoTags);
            }
        }
        return $tagsList;
    }

    /**
     * Delete absent administrators from notifications
     *
     * @param $uid user id
     */
    function deleteDeadAdminFromNotifications($uid)
    {
        global $application;
        $tables = $this->getTables();

        $nst = $tables['notification_send_to']['columns'];
        $query = new DB_Delete('notification_send_to');
        $query->WhereValue($nst['email'], DB_EQ, $uid);
        $query->WhereAnd();
        $query->WhereValue($nst['code'], DB_EQ, "EMAIL_ADMINISTRATOR");
        $application->db->getDB_Result($query);
    }

    /**
     * Update notification info in the database.
     *
     *@param array $data - the array of features taken from the form
     *
     *@return
     */
    function updateNotification($data)
    {
        global $application;
        $tables = $this->getTables();

        $n = $tables['notifications']['columns'];
        $query = new DB_Update('notifications');
        $query->addUpdateValue($n['na_id'], $data['Action']);

        $query->addMultiLangUpdateValue($n['name'],    $data['Name'],    $n['id'], '', 'Notifications');
        $query->addMultiLangUpdateValue($n['subject'], $data['Subject'], $n['id'], '', 'Notifications');
        $query->addMultiLangUpdateValue($n['body'],    $data['Body'],    $n['id'], '', 'Notifications');

        if(key($data['SendFrom']) === 'EMAIL_ADMINISTRATOR')
        {
            $query->addUpdateValue($n['from_email_admin_id'], $data['SendFrom'][key($data['SendFrom'])]);
            $query->addUpdateValue($n['from_email_custom_address'], "");
        }
        else
        {
            $query->addUpdateValue($n['from_email_admin_id'], DB_NULL);
            $query->addUpdateValue($n['from_email_custom_address'], $data['SendFrom'][key($data['SendFrom'])]);
        }

        $query->addUpdateValue($n['from_email_code'], key($data['SendFrom']));
        $query->addUpdateValue($n['active'], $data['Active']);
        $query->WhereValue($n['id'], DB_EQ, $data['Id']);
        $application->db->getDB_Result($query);

        $nst = $tables['notification_send_to']['columns'];

        $query = new DB_Delete('notification_send_to');
        $query->WhereValue($nst['n_id'], DB_EQ, $data['Id']);
        $application->db->getDB_Result($query);

        foreach ($data['SendTo'] as $email)
        {
            $query = new DB_Insert('notification_send_to');
            $query->addInsertValue($data['Id'], $nst['n_id']);
            $query->addInsertValue($email[key($email)], $nst['email']);
            $query->addInsertValue(key($email), $nst['code']);
            $application->db->getDB_Result($query);
        }

        if ($data['OptionsValues'])
        {
            $ov2n = $tables['option_values_to_notification']['columns'];

            $query = new DB_Delete('option_values_to_notification');
            $query->WhereValue($ov2n['n_id'], DB_EQ, $data['Id']);
            $application->db->getDB_Result($query);

            foreach ($data['OptionsValues'] as $key => $val)
            {
                $query = new DB_Insert('option_values_to_notification');
                $query->addInsertValue($key, $ov2n['naov_id']);
                $query->addInsertValue($data['Id'], $ov2n['n_id']);
                $query->addInsertValue('true', $ov2n['value']);
                $application->db->getDB_Result($query);
            }
        }

        if ($data['BlockBodies'])
        {
            $nbb = $tables['notification_blocktag_bodies']['columns'];
            foreach ($data['BlockBodies'] as $block_id => $body)
            {
                $query = new DB_Update('notification_blocktag_bodies');

                $query->addMultiLangUpdateValue($nbb['body'], $body, $nbb['id'], '', 'Notifications');

                $query->WhereValue($nbb['nb_id'], DB_EQ, $block_id);
                $query->WhereAnd();
                $query->WhereValue($nbb['n_id'], DB_EQ, $data['Id']);
                $application->db->getDB_Result($query);
            }
        }
    }

    /**
     * Adds the notification to the database.
     *
     *@param array $data - the array of features taken from the form
     *
     *@return
     */
    function addNotification($data)
    {
        global $application;
        $tables = $this->getTables();

        $n = $tables['notifications']['columns'];
        $query = new DB_Insert('notifications');
        $query->addInsertValue($data['Action'], $n['na_id']);

        $query->addMultiLangInsertValue($data['Name'],    $n['name'],    $n['id'], 'Notifications');
        $query->addMultiLangInsertValue($data['Subject'], $n['subject'], $n['id'], 'Notifications');
        $query->addMultiLangInsertValue($data['Body'],    $n['body'],    $n['id'], 'Notifications');

        if(key($data['SendFrom']) === 'EMAIL_ADMINISTRATOR')
        {
            $query->addInsertValue("", $n['from_email_custom_address']);
            $query->addInsertValue($data['SendFrom'][key($data['SendFrom'])], $n['from_email_admin_id']);
        }
        else
        {
            $query->addInsertValue($data['SendFrom'][key($data['SendFrom'])], $n['from_email_custom_address']);
            $query->addInsertValue(DB_NULL, $n['from_email_admin_id']);
        }

        $query->addInsertValue(key($data['SendFrom']), $n['from_email_code']);
        $query->addInsertValue($data['Active'], $n['active']);
        $application->db->getDB_Result($query);

        $n_id = $application->db->DB_Insert_Id();

        $nst = $tables['notification_send_to']['columns'];
        foreach ($data['SendTo'] as $email)
        {
            $query = new DB_Insert('notification_send_to');
            $query->addInsertValue($n_id, $nst['n_id']);
            $query->addInsertValue($email[key($email)], $nst['email']);
            $query->addInsertValue(key($email), $nst['code']);
            $application->db->getDB_Result($query);
        }

        if ($data['OptionsValues'])
        {
            $ov2n = $tables['option_values_to_notification']['columns'];
            foreach ($data['OptionsValues'] as $key => $val)
            {
                $query = new DB_Insert('option_values_to_notification');
                $query->addInsertValue($key, $ov2n['naov_id']);
                $query->addInsertValue($n_id, $ov2n['n_id']);
                $query->addInsertValue('true', $ov2n['value']);
                $application->db->getDB_Result($query);
            }
        }

        if ($data['BlockBodies'])
        {
            $nbb = $tables['notification_blocktag_bodies']['columns'];
            foreach ($data['BlockBodies'] as $block_id => $body)
            {
                $query = new DB_Insert('notification_blocktag_bodies');
                $query->addInsertValue($block_id, $nbb['nb_id']);
                $query->addInsertValue($n_id, $nbb['n_id']);

                $query->addMultiLangInsertValue($body, $nbb['body'], $nbb['id'], 'Notifications');

                $application->db->getDB_Result($query);
            }
        }
    }

    /**
     * Deletes the notification from the database.
     *
     *@return
     */
    function deleteNotification($n_id)
    {
        global $application;
        $tables = $this->getTables();

    //==========================================

        $n = $tables['notifications']['columns'];
        $query = new DB_Delete('notifications');
        $query->WhereValue($n['id'], DB_EQ, $n_id);

        $query->deleteMultiLangField($n['name'], $n['id'], 'Notifications');
        $query->deleteMultiLangField($n['subject'], $n['id'], 'Notifications');
        $query->deleteMultiLangField($n['from_email_custom_address'], $n['id'], 'Notifications');
        $query->deleteMultiLangField($n['body'], $n['id'], 'Notifications');

        $application->db->getDB_Result($query);

    //==========================================

        $ov2n = $tables['option_values_to_notification']['columns'];
        $query = new DB_Delete('option_values_to_notification');
        $query->WhereValue($ov2n['n_id'], DB_EQ, $n_id);
        $application->db->getDB_Result($query);

    //==========================================

        $nst = $tables['notification_send_to']['columns'];
        $query = new DB_Delete('notification_send_to');
        $query->WhereValue($nst['n_id'], DB_EQ, $n_id);
        $application->db->getDB_Result($query);

    //==========================================

        $nbb = $tables['notification_blocktag_bodies']['columns'];
        $query = new DB_Delete('notification_blocktag_bodies');
        $query->WhereValue($nbb['n_id'], DB_EQ, $n_id);

        $query->deleteMultiLangField($nbb['body'], $nbb['id'], 'Notifications');

        $application->db->getDB_Result($query);
    }

    function setCurrentNotificationId($id)
    {
        $this->currentNotificationId = $id;
    }

    function getCurrentNotificationId()
    {
        return $this->currentNotificationId;
    }

    function unsetCurrentNotificationId()
    {
        $this->currentNotificationId = NULL;
    }

    function OnCustomerRegistered($reg_data)
    {
        $notifications = modApiFunc("Notifications", "getNotificationsList", 6);
        foreach ($notifications as $notificationInfo)
        {
            $notification = new NotificationContent(
                array(
                    'notification_id' => $notificationInfo['Id']
                   ,'action_id' => 6
                   ,'reg_data' => $reg_data
                )
            );
            $notification->send();
        };
    }

    function OnCustomerShouldActivateSelf($account_name)
    {
        $this->__OnAccountAction(7, $account_name);
    }

    function OnAdminShouldActivateCustomer($account_name)
    {
        $this->__OnAccountAction(8, $account_name);
    }

    function OnCustomerActivateSelf($account_name)
    {
        $this->__OnAccountAction(9, $account_name);
    }

    function OnAdminActivateCustomer($account_name)
    {
        $this->__OnAccountAction(10, $account_name);
    }

    function OnAdminDropCustomerPassword($account_name)
    {
        $this->__OnAccountAction(11, $account_name);
    }

    function OnCustomerPasswordDroped($account_name)
    {
        $this->__OnAccountAction(12, $account_name);
    }

    function OnAccountWasAutoCreated($account_name)
    {
        $this->__OnAccountAction(13, $account_name);
    }

    function OnOrderStatusUpdated($order_statuses)
    {
        if (isset($order_statuses['order_status']) && is_array($order_statuses['order_status']) && !empty($order_statuses['order_status']))
        {
            foreach ($order_statuses['order_status'] as $order_id => $statuses)
            {
                if ($statuses != null)
                {
                    $order_id = intval($order_id, 10);
                    $notifications = modApiFunc("Notifications", "getNotificationsList", 2);
                    foreach ($notifications as $notificationInfo)
                    {
                        $notification = new NotificationContent(array('notification_id' => $notificationInfo['Id'], 'order_id' => $order_id, 'action_id' => 2, 'status' => 'order', 'statuses' => $statuses));
                        $notification->send();
                    }
                }
            }
        }
        if (isset($order_statuses['payment_status']) && is_array($order_statuses['payment_status']) && !empty($order_statuses['payment_status']))
        {
            foreach ($order_statuses['payment_status'] as $order_id => $statuses)
            {
                if ($statuses != null)
                {
                    $order_id = intval($order_id, 10);
                    $notifications = modApiFunc("Notifications", "getNotificationsList", 3);
                    foreach ($notifications as $notificationInfo)
                    {
                        $notification = new NotificationContent(array('notification_id' => $notificationInfo['Id'], 'order_id' => $order_id, 'action_id' => 3, 'status' => 'payment', 'statuses' => $statuses));
                        $notification->send();
                    }
                }
                if (($statuses['new_status'] == 2) && ($statuses['new_status'] != $statuses['old_status']))
                {
                     $notifications = modApiFunc("Notifications", "getNotificationsList", 5);
                     foreach ($notifications as $notificationInfo)
                     {
                         $notification = new NotificationContent(array('notification_id' => $notificationInfo['Id'], 'order_id' => $order_id, 'action_id' => 5));
                         $notification->send();
                     }
                }
            }
        }
    }

    function OnGiftCertificatePurchased($gc_obj)
    {
        $action_id = 16; // GCPurchased action_id = 16

        $order_id = $gc_obj->purchased_order_id;
        $notifications = modApiFunc("Notifications", "getNotificationsList", $action_id);
        foreach ($notifications as $notificationInfo)
        {
            $notification = new NotificationContent(
                array(
                    'notification_id' => $notificationInfo['Id']
                   ,'action_id' => $action_id
                   ,'order_id' => $order_id
                   ,'gc_obj' => $gc_obj
                )
            );

            $emails = array();
            if ($gc_obj->sendtype === GC_SENDTYPE_EMAIL && $gc_obj->status === GC_STATUS_ACTIVE)
                $emails[] = array($this->makeFullEmail($gc_obj->to, $gc_obj->email));

            $notification->thirdparty_emails = $emails;
            $notification->send();
        };
    }

    function OnGiftCertificateCreated($gc_obj)
    {
        $action_id = 17; // GCCreated action_id = 17

        //$order_id = $gc_obj->purchased_order_id;
        $notifications = modApiFunc("Notifications", "getNotificationsList", $action_id);
        foreach ($notifications as $notificationInfo)
        {
            $notification = new NotificationContent(
                array(
                    'notification_id' => $notificationInfo['Id']
                   ,'action_id' => $action_id
                   //,'order_id' => $order_id
                   ,'gc_obj' => $gc_obj
                )
            );

            if ($gc_obj->sendtype === GC_SENDTYPE_EMAIL && $gc_obj->status === GC_STATUS_ACTIVE)
                $emails[] = array($this->makeFullEmail($gc_obj->to, $gc_obj->email));

            $notification->thirdparty_emails = $emails;
            $notification->send();
        };

    }

    function addCustomInfoTag($tagname, $actionIDs = 'order')
    {
        global $application;

        if ($actionIDs == 'order')
            $actionIDs = array(1,2,3,4);
        if (!is_array($actionIDs))
            $actionIDs = array($actionIDs);

        $tagname = '{' . $tagname . '}';

        $tables = $this -> getTables();
        $ni = $tables['notification_infotags']['columns'];
        $i2a = $tables['infotags_to_action']['columns'];

        // checking if the infotag already exists
        $query = new DB_Select();
        $query -> addSelectField($ni['id'], 'id');
        $query -> WhereValue($ni['name'], DB_EQ, $tagname);
        $result = $application -> db -> getDB_Result($query);

        if (!empty($result))
        {
            $id = $result[0]['id'];
        }
        else
        {
            $query = new DB_Insert('notification_infotags');
            $query -> addInsertValue($tagname, $ni['name']);
            $application -> db -> getDB_Result($query);
            $id = $application -> db -> DB_Insert_Id();
        }

        foreach($actionIDs as $aid)
        {
            $query = new DB_Select();
            $query -> addSelectField($i2a['id'], 'id');
            $query -> WhereValue($i2a['ni_id'], DB_EQ, $id);
            $query -> WhereAND();
            $query -> WhereValue($i2a['na_id'], DB_EQ, $aid);
            $result = $application -> db -> getDB_Result($query);

            if (empty($result))
            {
                $query = new DB_Insert('infotags_to_action');
                $query -> addInsertValue($id, $i2a['ni_id']);
                $query -> addInsertValue($aid, $i2a['na_id']);
                $application -> db -> getDB_Result($query);
            }
        }
    }

    function removeCustomInfoTag($tagname)
    {
        global $application;

        $tagname = '{' . $tagname . '}';

        $tables = $this -> getTables();
        $ni = $tables['notification_infotags']['columns'];
        $i2a = $tables['infotags_to_action']['columns'];

        // checking if the infotag exists
        $query = new DB_Select();
        $query -> addSelectField($ni['id'], 'id');
        $query -> WhereValue($ni['name'], DB_EQ, $tagname);
        $result = $application -> db -> getDB_Result($query);

        if (empty($result))
            return;

        foreach ($result as $tag)
        {
            $id = $tag['id'];

            $query = new DB_Delete('infotags_to_action');
            $query -> WhereValue($i2a['ni_id'], DB_EQ, $id);
            $application -> db -> getDB_Result($query);

            $query = new DB_Delete('notification_infotags');
            $query -> WhereValue($ni['id'], DB_EQ, $id);
            $application -> db -> getDB_Result($query);
        }
    }

    function __OnAccountAction($action_id, $account_name)
    {
        $notifications = modApiFunc("Notifications", "getNotificationsList", $action_id);
        foreach ($notifications as $notificationInfo)
        {
            $notification = new NotificationContent(
                array(
                    'notification_id' => $notificationInfo['Id']
                   ,'action_id' => $action_id
                   ,'account_name' => $account_name
                )
            );
            $notification->send();
        };
    }

    function OnInventoryLowLevel($inventory_info)
    {
        $notifications = modApiFunc("Notifications", "getNotificationsList", 14); //: remove magic number
        foreach ($notifications as $notificationInfo)
        {
            $notification = new NotificationContent(
                array(
                    'notification_id' => $notificationInfo['Id']
                   ,'action_id' => 14
                   ,'inventory_info' => $inventory_info
                )
            );
            $notification->send();
        };
    }

    function __getActionIdByActionName($action_name)
    {
        global $application;
        $tables = $this->getTables();
        $actions_table = $tables['notification_actions']['columns'];

        $query = new DB_Select();
        $query->addSelectField($actions_table['id'], 'action_id');
        $query->addSelectTable('notification_actions');
        $query->WhereValue($actions_table['code'], DB_EQ, $action_name);

        $res = $application->db->getDB_Result($query);

        return (count($res) == 1 ? $res[0]['action_id'] : 0);
    }

    function __getNotificationNameById($n_id)
    {
        global $application;
        $tables = $this->getTables();
        $actions_table = $tables['notifications']['columns'];

        $query = new DB_Select();
        $query->addSelectField($actions_table['name'], 'notification_name');
        $query->addSelectTable('notifications');
        $query->WhereValue($actions_table['id'], DB_EQ, $n_id);

        $res = $application->db->getDB_Result($query);

        return $res[0]['notification_name'];
    }

    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */

    function makeFullEmail($name, $email)
    {
        $name = trim($name);
        $email = trim($email);
        return $name == '' || $name == $email ? $email : escapeEmailName($name).' <'.$email.'>';
    }

    var $currentNotificationId = NULL;

    /**#@-*/

}
?>