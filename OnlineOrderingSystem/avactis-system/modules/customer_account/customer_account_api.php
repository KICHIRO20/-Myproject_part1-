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
 * @package CustomerAccount
 * @author Egor V. Derevyankin
 *
 */

class Customer_Account
{
    function Customer_Account()
    {
        $this->_co_attrs_ids = array();
        $this->_co_default_variant_attrs = array();
        $this->_co_pi_types_ids = array();
        $this->_lowcase_names = array();
        $this->_customers_search_filter = null;
        $this->_customers = null;
    }

    function install()
    {
        $query = new DB_Table_Create(Customer_Account::getTables());
        loadClass('Customer_Account_Installer');
        $installer = new Customer_Account_Installer();
        $installer->doInstall();

        $group_info = array('GROUP_NAME'        => 'CUSTOMER_ACCOUNT_SETTINGS',
                            'GROUP_DESCRIPTION' => array(   'NAME'          => array('CA', 'ADV_CFG_CA_SETTINGS_GROUP_NAME'),
                                                            'DESCRIPTION'   => array('CA', 'ADV_CFG_CA_SETTINGS_GROUP_DESCR')),
                            'GROUP_VISIBILITY'    => 'SHOW'); /*@ add to constants */

        modApiFunc('Settings','createGroup', $group_info);

        $param_info = array(
                         'GROUP_NAME'        => $group_info['GROUP_NAME'],
                         'PARAM_NAME'        => 'ENABLE_SAVE_SESSION',
                         'PARAM_DESCRIPTION' => array( 'NAME'        => array('CA', 'ADV_ENABLE_SAVE_SESSION_NAME'),
                                                       'DESCRIPTION' => array('CA', 'ADV_ENABLE_SAVE_SESSION_DESCR') ),
                         'PARAM_TYPE'          => PARAM_TYPE_LIST,
                         'PARAM_VALUE_LIST'    => array(
                                 array(  'VALUE' => 'NO',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CA', 'ADV_CFG_NO'),
                                                                       'DESCRIPTION' => array('CA', 'ADV_CFG_NO') ),
                                       ),
                                 array(  'VALUE' => 'YES',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CA', 'ADV_CFG_YES'),
                                                                       'DESCRIPTION' => array('CA', 'ADV_CFG_YES') ),
                                       )),
                         'PARAM_CURRENT_VALUE' => 'YES',
                         'PARAM_DEFAULT_VALUE' => 'YES',
        );
        modApiFunc('Settings','createParam', $param_info);

               $param_info = array(
                         'GROUP_NAME'        => $group_info['GROUP_NAME'],
                         'PARAM_NAME'        => 'CUSTOMER_SESSION_DURATION_VALUE',
                         'PARAM_DESCRIPTION' => array( 'NAME'        => array('CA', 'CUSTOMER_SESSION_CA_NAME'),
                                                       'DESCRIPTION' => array('CA', 'CUSTOMER_SESSION_CA_DESC') ),
                         'PARAM_TYPE'          => PARAM_TYPE_LIST,
                         'PARAM_VALUE_LIST'    => array(
                                 array(  'VALUE' => '3600',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CA', 'CSR_SESSION_1_NAME'),
                                                                       'DESCRIPTION' => array('CA', 'CSR_SESSION_1_DESC')),
                                       ),
                                 array(  'VALUE' => '43200',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CA', 'CSR_SESSION_2_NAME'),
                                                                       'DESCRIPTION' => array('CA', 'CSR_SESSION_2_DESC')),
                                       ),
                                 array(  'VALUE' => '86400',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CA', 'CSR_SESSION_3_NAME'),
                                                                       'DESCRIPTION' => array('CA', 'CSR_SESSION_3_DESC')),
                                       ),
                                 array(  'VALUE' => '604800',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CA', 'CSR_SESSION_4_NAME'),
                                                                       'DESCRIPTION' => array('CA', 'CSR_SESSION_4_DESC')),
                                       ),
                                 array(  'VALUE' => '1209600',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CA', 'CSR_SESSION_5_NAME'),
                                                                       'DESCRIPTION' => array('CA', 'CSR_SESSION_5_DESC')),
                                       ),
                                 array(  'VALUE' => '2592000',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CA', 'CSR_SESSION_6_NAME'),
                                                                       'DESCRIPTION' => array('CA', 'CSR_SESSION_6_DESC')),
                                       ),
                                 array(  'VALUE' => '7776000',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CA', 'CSR_SESSION_7_NAME'),
                                                                       'DESCRIPTION' => array('CA', 'CSR_SESSION_7_DESC')),
                                       )),
                         'PARAM_CURRENT_VALUE' => '2592000',
                         'PARAM_DEFAULT_VALUE' => '2592000',
        );
        modApiFunc('Settings','createParam', $param_info);

        // whether to clear QuickCheckout Customers' information
        $param_info = array(
                         'GROUP_NAME'        => $group_info['GROUP_NAME'],
                         'PARAM_NAME'        => 'CLEAR_QCC_PERSONAL_INFO',
                         'PARAM_DESCRIPTION' => array( 'NAME'        => array('CA', 'CLEAR_QCC_PERSONAL_INFO_NAME'),
                                                       'DESCRIPTION' => array('CA', 'CLEAR_QCC_PERSONAL_INFO_DESCR') ),
                         'PARAM_TYPE'          => PARAM_TYPE_LIST,
                         'PARAM_VALUE_LIST'    => array(
                                 array(  'VALUE' => 'NO',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CA', 'CLEAR_QCC_PERSONAL_INFO_NO_NAME'),
                                                                       'DESCRIPTION' => array('CA', 'CLEAR_QCC_PERSONAL_INFO_NO_NAME') ),
                                       ),
                                 array(  'VALUE' => 'YES',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('CA', 'CLEAR_QCC_PERSONAL_INFO_YES_NAME'),
                                                                       'DESCRIPTION' => array('CA', 'CLEAR_QCC_PERSONAL_INFO_YES_NAME') ),
                                       )),
                         'PARAM_CURRENT_VALUE' => 'YES',
                         'PARAM_DEFAULT_VALUE' => 'YES',
        );
        modApiFunc('Settings','createParam', $param_info);
        Customer_Account::addCustomerGroup(getMsg('CA','LBL_DEFAULT_GROUP_NAME'));
        Customer_Account::addCustomerGroup(getMsg('CA','LBL_UNSIGNED_GROUP_NAME'));
        execQuery('UPDATE_CUSTOMER_GROUP_ID', array('group_name' => getMsg('CA','LBL_UNSIGNED_GROUP_NAME'), 'group_id' => 0));
    }

    function uninstall()
    {
        $query = new DB_Table_Delete(Customer_Account::getTables());
    }

    function getTables()
    {
        static $tables;

        if (is_array($tables))
        {
            return $tables;
        }

        $tables = array();

        $table = 'ca_customers';
        $tables[$table] = array(
            'columns' => array(
                'customer_id'       => $table.'.customer_id'
               ,'customer_account'  => $table.'.customer_account'
               ,'customer_password' => $table.'.customer_password'
               ,'customer_status'   => $table.'.customer_status'
               ,'affiliate_id'      => $table.'.affiliate_id' // Affiliate id associated with customer. Association occurs after successful order with affilite ID
               ,'customer_lng'      => $table.'.customer_lng'
               ,'notification_lng'  => $table.'.notification_lng'
               ,'group_id'          => $table.'.group_id'
             )
           ,'types'   => array(
                'customer_id'       => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
               ,'customer_account'  => DBQUERY_FIELD_TYPE_CHAR255.' NOT NULL DEFAULT \'\''
               ,'customer_password' => DBQUERY_FIELD_TYPE_CHAR32.' NOT NULL DEFAULT \'\''
               ,'customer_status'   => "ENUM ('A','N','B','R') default 'N'"
               ,'affiliate_id'      => DBQUERY_FIELD_TYPE_CHAR255.' NOT NULL DEFAULT \'\''
               ,'customer_lng'      => DBQUERY_FIELD_TYPE_CHAR2.' NOT NULL DEFAULT \'\''
               ,'notification_lng'  => DBQUERY_FIELD_TYPE_CHAR2.' NOT NULL DEFAULT \'\''
               ,'group_id'          => DBQUERY_FIELD_TYPE_INT .' NOT NULL DEFAULT 1'
             )
           ,'primary' => array(
                'customer_id'
             )
           ,'indexes' => array(
                'UNIQUE KEY UNQ_account' => 'customer_account'
             )
        );

        $table = 'ca_person_info_groups';
        $tables[$table] = array(
            'columns' => array(
                'group_id'      => $table.'.group_id'
               ,'group_name'    => $table.'.group_name'
               ,'lang_code'     => $table.'.lang_code'
               ,'sort_order'    => $table.'.sort_order'
             )
           ,'types'   => array(
                'group_id'      => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
               ,'group_name'    => DBQUERY_FIELD_TYPE_CHAR255.' NOT NULL DEFAULT \'\''
               ,'lang_code'     => DBQUERY_FIELD_TYPE_CHAR255.' NOT NULL DEFAULT \'\''
               ,'sort_order'    => DBQUERY_FIELD_TYPE_INT .' NOT NULL DEFAULT 0'
             )
           ,'primary' => array(
                'group_id'
             )
           ,'indexes' => array(
                'UNIQUE KEY UNQ_gn' => 'group_name'
             )
        );

        $table = 'ca_person_info_attrs';
        $tables[$table] = array(
            'columns' => array(
                'attr_id'       => $table.'.attr_id'
               ,'attr_name'     => $table.'.attr_name'
               ,'lang_code'     => $table.'.lang_code'
             )
           ,'types'   => array(
                'attr_id'       => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
               ,'attr_name'     => DBQUERY_FIELD_TYPE_CHAR255.' NOT NULL DEFAULT \'\''
               ,'lang_code'     => DBQUERY_FIELD_TYPE_CHAR255.' NOT NULL DEFAULT \'\''
             )
           ,'primary' => array(
                'attr_id'
             )
           ,'indexes' => array(
                'UNIQUE KEY UNQ_an' => 'attr_name'
             )
        );

        $table = 'ca_attrs_to_groups';
        $tables[$table] = array(
            'columns' => array(
                'ag_id'         => $table.'.ag_id'
               ,'group_id'      => $table.'.group_id'
               ,'attr_id'       => $table.'.attr_id'
               ,'sort_order'    => $table.'.sort_order'
               ,'visible_name'  => $table.'.visible_name'
               ,'is_visible'    => $table.'.is_visible'
               ,'is_required'   => $table.'.is_required'
             )
           ,'types'   => array(
                'ag_id'         => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
               ,'group_id'      => DBQUERY_FIELD_TYPE_INT .' NOT NULL DEFAULT 0'
               ,'attr_id'       => DBQUERY_FIELD_TYPE_INT .' NOT NULL DEFAULT 0'
               ,'sort_order'    => DBQUERY_FIELD_TYPE_INT .' NOT NULL DEFAULT 0'
               ,'visible_name'  => DBQUERY_FIELD_TYPE_CHAR255.' NOT NULL DEFAULT \'\''
               ,'is_visible'    => "ENUM ('Y','N') DEFAULT 'Y'"
               ,'is_required'   => "ENUM ('Y','N') DEFAULT 'Y'"
             )
           ,'primary' => array(
                'ag_id'
             )
           ,'indexes' => array(
                'UNIQUE KEY UNQ_gid_aid' => 'group_id,attr_id',
                'attr_id'                => 'attr_id'
             )
        );

        $table = 'ca_person_info_data';
        $tables[$table] = array(
            'columns' => array(
                'data_id'       => $table.'.data_id'
               ,'customer_id'   => $table.'.customer_id'
               ,'ag_id'         => $table.'.ag_id'
               ,'data_value'    => $table.'.data_value'
             )
           ,'types'   => array(
                'data_id'       => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
               ,'customer_id'   => DBQUERY_FIELD_TYPE_INT .' NOT NULL DEFAULT 0'
               ,'ag_id'         => DBQUERY_FIELD_TYPE_INT .' NOT NULL DEFAULT 0'
               ,'data_value'    => DBQUERY_FIELD_TYPE_CHAR255.' NOT NULL DEFAULT \'\''
             )
           ,'primary' => array(
                'data_id'
             )
           ,'indexes' => array(
                'UNIQUE KEY UNQ_cid_agid' => 'customer_id,ag_id',
                'ag_id'                   => 'ag_id',
                'data_value'              => 'data_value'
             )
        );

        $table = 'ca_activation_keys';
        $tables[$table] = array(
            'columns' => array(
                'key_id'         => $table.'.key_id'
               ,'key_value'      => $table.'.key_value'
               ,'customer_account' => $table.'.customer_account'
             )
           ,'types'   => array(
                'key_id'         => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
               ,'key_value'      => DBQUERY_FIELD_TYPE_CHAR32.' NOT NULL DEFAULT \'\''
               ,'customer_account' => DBQUERY_FIELD_TYPE_CHAR255.' NOT NULL DEFAULT \'\''
             )
           ,'primary' => array(
                'key_id'
             )
           ,'indexes' => array(
                'UNIQUE KEY UNQ_account' => 'customer_account'
             )
        );

        $table = 'ca_settings';
        $tables[$table] = array(
            'columns' => array(
                'setting_id'    => $table.'.setting_id'
               ,'setting_key'   => $table.'.setting_key'
               ,'setting_value' => $table.'.setting_value'
             )
           ,'types'   => array(
                'setting_id'    => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
               ,'setting_key'   => DBQUERY_FIELD_TYPE_CHAR255.' NOT NULL DEFAULT \'\''
               ,'setting_value' => DBQUERY_FIELD_TYPE_CHAR255.' NOT NULL DEFAULT \'\''
             )
           ,'primary' => array(
                'setting_id'
             )
        );

        $table = 'ca_customer_groups';
        $tables[$table] = array(
            'columns' => array(
                'group_id'   => $table.'.group_id'
               ,'group_name' => $table.'.group_name'
            )
           ,'types'   => array(
                'group_id'    => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
               ,'group_name'  => DBQUERY_FIELD_TYPE_CHAR255.' NOT NULL DEFAULT \'\''
            )
           ,'primary' => array(
                'group_id'
            )
        );

        global $application;
        return $application->addTablePrefix($tables);
    }

    function getSettings()
    {
        $res=execQuery('SELECT_CUSTOMER_ACCOUNT_SETTINGS', array());
        $settings=array();
        foreach($res as $k => $sval)
            $settings[$sval['setting_key']]=$sval['setting_value'];

        return $settings;
    }

    function getGroups($exclude_unsigned=null)
    {
        static $groups;
        $exclude_unsigned = $exclude_unsigned ? 1 : 0;
        if (! isset($groups)) {
            $groups = array(array(getMsg('CA','LBL_UNSIGNED_GROUP_NAME')), array());
            $res = execQuery('SELECT_CUSTOMER_ACCOUNT_GROUPS', array());
            foreach($res as $k => $val)
            {
                if($val['group_id']=='1') $val['group_name'] = getMsg('CA', 'LBL_DEFAULT_GROUP_NAME');
                $groups[0][$val['group_id']] = $val['group_name'];
                $groups[1][$val['group_id']] = $val['group_name'];
            }
        }
        return $groups[$exclude_unsigned];
    }

    function getGroupsDropDown($customer_id=null, $customer_group=null)
    {
        $values = array();
        foreach($this->getGroups($customer_id) as $id=>$name)
        {
            array_push($values, array('value' => $id, 'contents' => $name));
        }

        $dropdown = array(
            'select_name' => 'membership['.$customer_id.']'
           ,'selected_value' => $customer_group
           ,'values' => $values
		   ,'class' => 'form-control form-filter input-small input-sm input-inline'
        );

        return HtmlForm::genDropdownSingleChoice($dropdown);
    }

    function visibilityStringToArray($str="")
    {
        return explode("|",$str);
    }

    function getVisibleExistingGroups($visibility_str="")
    {
        $all_vis_gr = array();
        $all_gr = $this->getGroups('exclude unsigned');
        $vis_gr = $this->visibilityStringToArray($visibility_str);

        foreach($all_gr as $id => $name)
        {
            if(in_array($id, $vis_gr))
            {
                $all_vis_gr[$id] = $name;
            }
        }

        return $all_vis_gr;
    }

    function updateSettings($settings)
    {
        global $application;
        $tables=$this->getTables();
        $stable=$tables['ca_settings']['columns'];

        foreach($settings as $skey => $sval)
        {
            $query = new DB_Update('ca_settings');
            $query->addUpdateValue($stable['setting_value'],$sval);
            $query->WhereValue($stable['setting_key'], DB_EQ, $skey);
            $application->db->PrepareSQL($query);
            $application->db->DB_Exec();
        };

        return;
    }

    function addAccount($account, $password, $aff_id="")
    {
        global $application;
        $tables = $this->getTables();
        $customers_table = $tables['ca_customers']['columns'];

        $query = new DB_Insert('ca_customers');
        $query->addInsertValue($account, $customers_table['customer_account']);
        $query->addInsertValue(md5($password), $customers_table['customer_password']);
        $query->addInsertValue($aff_id, $customers_table['affiliate_id']);
        $query->addInsertValue(modApiFunc('MultiLang', 'getLanguage'), $customers_table['customer_lng']);
        $query->addInsertValue(modApiFunc('MultiLang', 'getLanguage'), $customers_table['notification_lng']);
        $application->db->PrepareSQL($query);
        return $application->db->DB_Exec();
    }

    function updateCustomerAccountGroup($account, $group_id)
    {
        execQuery('UPDATE_CUSTOMER_ACCOUNT_GROUP', array('customer_account' => $account, 'group_id' => $group_id));
    }

    function updateCustomerAccountGroupToDefault($group_id)
    {
        execQuery('UPDATE_CUSTOMER_ACCOUNT_GROUP_TO_DEFAULT', array('group_id' => $group_id));
    }

    function deleteCustomerGroup($group_id)
    {
        execQuery('DELETE_CUSTOMER_GROUP', array('group_id' => $group_id));
        $this->updateCustomerAccountGroupToDefault($group_id);
        modApiFunc('Catalog', 'updateMembershipVisibilityAttr', array('delete_group'=>$group_id));
    }

    function addCustomerGroup($group_name)
    {
        execQuery('INSERT_CUSTOMER_GROUP', array('group_name' => $group_name));
    }

    function setAccountStatus($account, $status)
    {
        if(!in_array($status, array('A','N','B','R')))
            return false;

        global $application;
        $tables = $this->getTables();
        $customers_table = $tables['ca_customers']['columns'];

        $res = execQuery('SELECT_CUSTOMER_ACCOUNT_STATUS', array('customer_account' => $account));
        if(count($res) != 1)
        {
            return false;
        }
        else
        {
            $old_status = $res[0]['status'];
        };

        $query = new DB_Update('ca_customers');
        $query->addUpdateValue($customers_table['customer_status'], $status);
        $query->WhereValue($customers_table['customer_account'], DB_EQ, $account);
        $application->db->PrepareSQL($query);
        $application->db->DB_Exec();

        return true;
    }

    function getAccountStatus($account)
    {
        global $application;
        $res = execQuery('SELECT_CUSTOMER_ACCOUNT_STATUS', array('customer_account' => $account));
        if(count($res) != 1)
        {
            return null;
        };

        return $res[0]['status'];
    }

    function setAccountLanguage($account, $lng)
    {
        execQuery('UPDATE_CUSTOMER_ACCOUNT_LANGUAGE', array('customer_account' => $account, 'customer_lng' => $lng));
    }

    function setAccountNotificationLanguage($account, $lng)
    {
        execQuery('UPDATE_CUSTOMER_ACCOUNT_NOTIFICATION_LANGUAGE', array('customer_account' => $account, 'notification_lng' => $lng));
    }

    function getAccountLanguage($account)
    {
        $res = execQuery('SELECT_CUSTOMER_ACCOUNT_LANGUAGE', array('customer_account' => $account));
        if(!$res)
            return '';

        return $res[0]['language'];
    }

    function getAccountNotificationLanguage($account)
    {
        $res = execQuery('SELECT_CUSTOMER_ACCOUNT_NOTIFICATION_LANGUAGE', array('customer_account' => $account));
        if(!$res)
            return '';

        return $res[0]['language'];
    }

    function doesAccountExists($account)
    {
        global $application;

        $res = execQuery('SELECT_CUSTOMER_ACCOUNT_STATUS', array('customer_account' => $account));
        return (count($res) == 1);
    }

    function registerAccount($account, $password, $register_info = null)
    {
        global $application;

        if (modApiFunc("Session","is_set","AffiliateID")) //Affiliate id found in session
        {
            $aff_id = modApiFunc("Session","get","AffiliateID");
        }
        else
            $aff_id = "";

        if($this->addAccount($account, $password, $aff_id) and $this->replaceActivationKey($account))
        {
            if($register_info != null)
            {
                $settings = $this->getSettings();
                $obj = &$application->getInstance('CCustomerInfo',$account);

                $target_groups = $obj->getPersonInfoGroupsNames();

                foreach($register_info as $attr_name => $attr_value)
                {
                    reset($target_groups);
                    foreach($target_groups as $group_name)
                    {
                        $obj->setPersonInfo(array(array($attr_name, $attr_value, $group_name)));
                    };
                };

                if($settings['ACCOUNT_ACTIVATION_SCHEME'] == ACCOUNT_ACTIVATION_SCHEME_NONE)
                {
                    $obj->setPersonInfo(array(array('Status','A', 'base')));
                };
            };

            return true;
        };

        return false;
    }

    function replaceActivationKey($account)
    {
        global $application;
        $tables = $this->getTables();
        $keys_table = $tables['ca_activation_keys']['columns'];

        $query = new DB_Replace('ca_activation_keys');
        $query->addReplaceValue($this->__genActivationKey(), $keys_table['key_value']);
        $query->addReplaceValue($account, $keys_table['customer_account']);

        $application->db->PrepareSQL($query);
        return $application->db->DB_Exec();
    }

    function getActivationKey($account)
    {
        global $application;
        $tables = $this->getTables();
        $keys_table = $tables['ca_activation_keys']['columns'];

        $query = new DB_Select();
        $query->addSelectTable('ca_activation_keys');
        $query->addSelectField($keys_table['key_value']);
        $query->WhereValue($keys_table['customer_account'], DB_EQ, $account);

        $res = $application->db->getDB_Result($query);

        if(count($res) != 1)
        {
            return null;
        }
        else
        {
            return $res[0]['key_value'];
        };
    }

    function getAccountByActivationKey($key)
    {
        if(!is_string($key))
        {
            return null;
        };

        global $application;
        $tables = $this->getTables();
        $keys_table = $tables['ca_activation_keys']['columns'];

        $query = new DB_Select();
        $query->addSelectTable('ca_activation_keys');
        $query->addSelectField($keys_table['customer_account']);
        $query->WhereValue($keys_table['key_value'], DB_EQ, $key);

        $res = $application->db->getDB_Result($query);

        if(count($res) != 1)
        {
            return null;
        }
        else
        {
            return $res[0]['customer_account'];
        };
    }

    function dropActivationKey($val, $type='key_value')
    {
        if(!in_array($type, array('key_value','customer_account')) or !is_string($val))
        {
            return false;
        };

        global $application;
        $tables = $this->getTables();
        $keys_table = $tables['ca_activation_keys']['columns'];

        $query = new DB_Delete('ca_activation_keys');
        $query->WhereValue($keys_table[$type], DB_EQ, $val);

        $application->db->PrepareSQL($query);

        return $application->db->DB_Exec();
    }

    function isCorrectAccountAndPasswd($account, $passwd)
    {
        if(!is_string($account) or !is_string($passwd))
        {
            return false;
        };

        global $application;
        $tables = $this->getTables();
        $customers_table = $tables['ca_customers']['columns'];

        $query = new DB_Select();
        $query->addSelectTable('ca_customers');
        $query->addSelectField($customers_table['customer_id']);
        $query->WhereValue($customers_table['customer_account'], DB_EQ, $account);
        $query->WhereAND();
        $query->WhereValue($customers_table['customer_password'], DB_EQ, md5($passwd));

        $res = $application->db->getDB_Result($query);

        return (count($res) == 1);
    }

    function getCurrentSignedCustomer()
    {
        if(modApiFunc('Session','is_set','SignedCustomer'))
        {
            $signed_customer = modApiFunc('Session','get','SignedCustomer');
            if($this->doesAccountExists($signed_customer))
            {
                return $signed_customer;
            }
            else
            {
                return null;
            };
        }
        else
        {
            return null;
        };
    }

    function getCurrentSignedCustomerGroupID()
    {
        $current_account = $this->getCurrentSignedCustomer();
        if($current_account)
        {
            $current_group = execQuery('SELECT_CURRENT_GROUP_ID',array('customer_account' => $current_account));
            return (!empty($current_group) ? $current_group[0]['group_id'] : 0);
        }
        else
        {
            return 0;
        }
    }

    function detectCOAttrID($tag)
    {
        if(empty($this->_co_attrs_ids))
        {
            $this->__loadCOAttrsIDs();
        };

        if(isset($this->_co_attrs_ids[_ml_strtolower($tag)]))
        {
            return $this->_co_attrs_ids[_ml_strtolower($tag)];
        }
        else
        {
            return null;
        };
    }

    function getPersonInfoAttrNameByCOAttrID($co_attr_id)
    {
        if(empty($this->_co_attrs_ids))
        {
            $this->__loadCOAttrsIDs();
        };

        return array_search($co_attr_id, $this->_co_attrs_ids);
    }

    function detectCOPITypeID($tag)
    {
        if(empty($this->_co_pi_types_ids))
        {
            $this->__loadCOPITypesIDs();
        };

        if(isset($this->_co_pi_types_ids[_ml_strtolower($tag)]))
        {
            return $this->_co_pi_types_ids[_ml_strtolower($tag)];
        }
        else
        {
            return null;
        };
    }

    function getPersonInfoGroupsList()
    {
        global $application;
        $tables = $this->getTables();
        $pig_table = $tables['ca_person_info_groups']['columns'];

        $query = new DB_Select();
        $query->addSelectTable('ca_person_info_groups');
        $query->addSelectField('*');
        $query->SelectOrder($pig_table['sort_order']);
        $res = $application->db->getDB_Result($query);

        return $res;
    }

    function getPersonInfoGroupInfoByName($group_name)
    {
        global $application;
        $tables = $this->getTables();
        $pig_table = $tables['ca_person_info_groups']['columns'];

        $query = new DB_Select();
        $query->addSelectTable('ca_person_info_groups');
        $query->addSelectField('*');
        $query->WhereValue($pig_table['group_name'], DB_EQ, $group_name);
        $res = $application->db->getDB_Result($query);

        if(count($res) == 1)
        {
            return $res[0];
        }
        else
        {
            return null;
        };
    }

    function getPersonInfoGroupNameByID($group_id)
    {
        global $application;
        $tables = $this->getTables();
        $pig_table = $tables['ca_person_info_groups']['columns'];

        $query = new DB_Select();
        $query->addSelectTable('ca_person_info_groups');
        $query->addSelectField($pig_table['group_name'], 'group_name');
        $query->WhereValue($pig_table['group_id'], DB_EQ, $group_id);
        $res = $application->db->getDB_Result($query);

        if(count($res) == 1)
        {
            return $res[0]['group_name'];
        }
        else
        {
            return null;
        };
    }

    function getPersonInfoGroupAttrs($group_id, $attr_vis = PERSON_INFO_GROUP_ATTR_ALL)
    {
        global $application;
        $tables = $this->getTables();
        $pia_table = $tables['ca_person_info_attrs']['columns'];
        $atg_table = $tables['ca_attrs_to_groups']['columns'];

        $query = new DB_Select();
        $query->addSelectTable('ca_person_info_attrs');
        $query->addSelectTable('ca_attrs_to_groups');
        $query->addSelectField($pia_table['attr_id'], 'attr_id');
        $query->addSelectField($pia_table['attr_name'], 'attr_name');
        $query->addSelectField($pia_table['lang_code'], 'lang_code');

        $query->setMultiLangAlias('_ml_name', 'ca_attrs_to_groups', $atg_table['visible_name'], $atg_table['ag_id'], 'Customer_Account');
        $query->addSelectField($query->getMultiLangAlias('_ml_name'), 'visible_name');

        $query->addSelectField($atg_table['is_visible'], 'is_visible');
        $query->addSelectField($atg_table['is_required'], 'is_required');
        $query->Where($atg_table['group_id'], DB_EQ, $group_id);
        $query->WhereAND();
        $query->Where($pia_table['attr_id'], DB_EQ, $atg_table['attr_id']);

        if($attr_vis == PERSON_INFO_GROUP_ATTR_VISIBLE)
        {
            $query->WhereAND();
            $query->WhereValue($atg_table['is_visible'], DB_EQ, 'Y');
        }
        elseif($attr_vis == PERSON_INFO_GROUP_ATTR_HIDDEN)
        {
            $query->WhereAND();
            $query->WhereValue($atg_table['is_visible'], DB_EQ, 'N');
        };

        $query->SelectOrder($atg_table['sort_order']);

        $res = $application->db->getDB_Result($query);

        $group_name = $this->getPersonInfoGroupNameByID($group_id);

        if($group_name == 'Customer')
        {
            $attrs = $res;
            return $attrs;
        };

        foreach($res as $k => $attr_info)
        {
            if($attr_vis == PERSON_INFO_GROUP_ATTR_ALL)
            {
                $attrs[] = $attr_info;
            };
            if($attr_vis == PERSON_INFO_GROUP_ATTR_VISIBLE)
            {
                if($this->__isCOAttrVisible($this->detectCOPITypeID($group_name), $this->detectCOAttrID($attr_info['attr_name'])))
                {
                    $attrs[] = $attr_info;
                };
            };
            if($attr_vis == PERSON_INFO_GROUP_ATTR_HIDDEN)
            {
                if($this->__isCOAttrHidden($this->detectCOPITypeID($group_name), $this->detectCOAttrID($attr_info['attr_name'])))
                {
                    $attrs[] = $attr_info;
                };
            };
        };

        return $attrs;
    }

    function getPersionInfoAttrNameByLowcaseName($lowcase_name)
    {
        if(sizeof($this->_lowcase_names) < 1)
        {
            global $application;
            $tables = $this->getTables();
            $attrs_table = $tables['ca_person_info_attrs']['columns'];

            $query = new DB_Select();
            $query->addSelectTable('ca_person_info_attrs');
            $query->addSelectField($attrs_table['attr_name'], 'attr_name');
            $result = $application->db->getDB_Result($query);
            foreach ($result as $info)
            {
                $this->_lowcase_names[_ml_strtolower($info['attr_name'])] = $info['attr_name'];
            }
        };

        return isset($this->_lowcase_names[$lowcase_name]) ? $this->_lowcase_names[$lowcase_name] : null;
    }

    function updateGroupAttrsInfo($group_name, $attrs)
    {
        global $application;
        $tables = $this->getTables();
        $atg_table = $tables['ca_attrs_to_groups']['columns'];

        $group_info = $this->getPersonInfoGroupInfoByName($group_name);
        $group_attrs = $this->getPersonInfoGroupAttrs($group_info['group_id']);

        foreach($group_attrs as $group_attr_info)
        {
            if(array_key_exists($group_attr_info['attr_name'], $attrs))
            {
                $a_info = $attrs[$group_attr_info['attr_name']];

                if(array_diff(array_keys($a_info), array_keys($atg_table)) == array_keys($a_info))
                    continue;

                $query = new DB_Update('ca_attrs_to_groups');
                foreach($a_info as $prop => $val)
                {
                    if ($prop == 'visible_name')
                        $query->addMultiLangUpdateValue($atg_table[$prop], $val, $atg_table['ag_id'], '', 'Customer_Account');
                    else
                        $query->addUpdateValue($atg_table[$prop], $val);
                };
                $query->WhereValue($atg_table['group_id'], DB_EQ, $group_info['group_id']);
                $query->WhereAND();
                $query->WhereValue($atg_table['attr_id'], DB_EQ, $group_attr_info['attr_id']);

                $application->db->getDB_Result($query);
            };
        };

        return;
    }

    function updateGroupAttrsSortOrder($group_name, $attrs_sort_order)
    {
        global $application;
        $tables = $this->getTables();
        $atg_table = $tables['ca_attrs_to_groups']['columns'];

        $group_info = $this->getPersonInfoGroupInfoByName($group_name);

        for($i=0; $i<count($attrs_sort_order); $i++)
        {
            $query = new DB_Update('ca_attrs_to_groups');
            $query->addUpdateValue($atg_table['sort_order'], $i);
            $query->WhereValue($atg_table['group_id'], DB_EQ, $group_info['group_id']);
            $query->WhereAND();
            $query->WhereValue($atg_table['attr_id'], DB_EQ, $attrs_sort_order[$i]);

            $application->db->PrepareSQL($query);
            $application->db->DB_Exec();
        };

        return;
    }

    function isPersionInfoGroupActive($group_name)
    {
        if($group_name == 'Customer')
        {
            return true;
        };

        global $application;
        $co_tables = modApiStaticFunc('Checkout','getTables');
        $pit_table = $co_tables['person_info_types']['columns'];

        $query = new DB_Select();
        $query->addSelectTable('person_info_types');
        $query->addSelectField($pit_table['active'], 'active');
        $query->WhereValue($pit_table['tag'], DB_EQ, _ml_strtolower($group_name).'Info');
        $res = $application->db->getDB_Result($query);

        if(count($res) != 1)
        {
            return false;
        }
        else
        {
            return ($res[0]['active'] == 'true');
        };
    }

    function OnOrderCreated($order_id)
    {
        global $application;

        $account = $this->getCurrentSignedCustomer();

        $setting_first_order_only = modApiFunc("Settings","getParamValue","AFFILIATE_SETTINGS","FIRST_ORDER_ONLY");
        $setting_update_affiliate = modApiFunc("Settings","getParamValue","AFFILIATE_SETTINGS","UPDATE_AFFILIATE_ID");

        if (modApiFunc("Session","is_set","AffiliateID")) //Associate Affiliate ID with customer account
        {
            if ($setting_first_order_only == "YES") // Only first affiliate order will be tracked. Affilite Id will NOT be saved with customer account
            {
                modApiFunc("Session","un_set","AffiliateID");
                $aff_id = "";
            }
            else
            {
                $aff_id = modApiFunc("Session","get","AffiliateID");
            }
        }
        else
            $aff_id = "";

        if($account != null)
        {
            $obj = &$application->getInstance('CCustomerInfo',$account);

            # update affiliate id
            if ($setting_update_affiliate == "CURRENT" && !empty($obj->affiliate_id) && $setting_first_order_only != "YES")
                {} # nothing to do
            else
                $obj->setAffiliateID($aff_id);

        }
        else
        {
            $groups = array('billingInfo','shippingInfo');
            $email = null;
            reset($groups);

            loadCoreFile('aal.class.php');

            foreach($groups as $group_name)
            {
                $Info = new ArrayAccessLayer( modApiFunc("Checkout", "getPrerequisiteValidationResults", $group_name) );
		        $Info->setAccessMask("validatedData", AAL_CUSTOM_PARAM, "value");
                $em = $Info->getByMask("Email");

                if($em != '')
                {
                    $email = $em;
                    break;
                };
            };

            $settings = $this->getSettings();

            if($settings['CHECKOUT_TYPE'] == CHECKOUT_TYPE_QUICK or ($settings['CHECKOUT_TYPE'] == CHECKOUT_TYPE_AUTOACCOUNT and $settings['AUTO_CREATE_ACCOUNT'] == 'N'))
            {
                if ($settings['CHECKOUT_TYPE'] == CHECKOUT_TYPE_AUTOACCOUNT && $settings['AUTO_CREATE_ACCOUNT'] == 'N' && $settings['MERGE_ORDERS_BY_EMAIL'] == 'Y' && $email != null)
                {
                    $account_name = $email;
                    if ($this->doesAccountExists($account_name) == false || $this->getAccountStatus($account_name) != "A") #merge orders for existing registered customers
                    {
                        $account_name = (($email != null) ? $email : PSEUDO_NA_CUSTOMER_PERFIX.$order_id) . PSEUDO_CUSTOMER_SUFFIX;
                    }
                }
                else
                {
                	$account_name = (($email != null) ? $email : PSEUDO_NA_CUSTOMER_PERFIX.$order_id) . PSEUDO_CUSTOMER_SUFFIX;
                	if($this->doesAccountExists($account_name) && $settings['MERGE_ORDERS_BY_EMAIL'] == 'N') // if account is already registered AND orders must not be merged
                	{
                		$account_name = $this->__genAutoAccountName();
                	}
                }

                if($this->doesAccountExists($account_name))
                {
                    $obj = &$application->getInstance('CCustomerInfo',$account_name);
                }
                else
                {
                    $this->addAccount($account_name, time(),$aff_id);
                    $this->setAccountStatus($account_name, 'B');
                    $obj = &$application->getInstance('CCustomerInfo',$account_name);

                    $obj->loadPersonInfoFromCheckout();
                    if($email != null)
                        $obj->setPersonInfo(array(array('Email',$email,'Customer')));

                    if($this->isPersionInfoGroupActive('Billing'))
                        $obj->copyPersonInfo('Billing','Customer',array('Email'));
                    elseif($this->isPersionInfoGroupActive('Shipping'))
                        $obj->copyPersonInfo('Shipping','Customer',array('Email'));
                }
            }
            else # forced automatic account creation
            {
                #
                # here we should check if we can use email as login
                #
                $throwAutoAccCreated = false;
                if ($email != null)
                {
                    $account_name = $email;

                    if ($this->doesAccountExists($account_name))
                    {
                        # check settings if order should be merged or new account created.
                        if ($settings['MERGE_ORDERS_BY_EMAIL'] == 'Y')
                        {
                            # do nothing?
                        }
                        else # have to create separate account
                        {
                            $account_name = $this->__genAutoAccountName();
                            $this->addAccount($account_name, time(),$aff_id);
                            $this->setAccountStatus($account_name, 'R');
                            $this->replaceActivationKey($account_name);
                            $throwAutoAccCreated = true;
                        }
                    }
                    else
                    {
                        $this->addAccount($account_name, time(),$aff_id);
                        $this->setAccountStatus($account_name, 'R');
                        $this->replaceActivationKey($account_name);
                        $throwAutoAccCreated = true;
                    }
                }
                else # no email
                {
                    $account_name = $this->__genAutoAccountName();
                    # here the account name is unique and not duplicated for sure
                    # if no email provided then each user/order will be treated as unique
                    $this->addAccount($account_name, time(),$aff_id);
                    $this->setAccountStatus($account_name, 'R');
                    $this->replaceActivationKey($account_name);
                    $throwAutoAccCreated = true;
                }

                $obj = &$application->getInstance('CCustomerInfo',$account_name);

                if ($throwAutoAccCreated == true)
                {
                    $obj->setPersonInfo(array(array('AccountName',$account_name,'Customer')));
                    $obj->loadPersonInfoFromCheckout();
                    if($email != null)
                        $obj->setPersonInfo(array(array('Email',$email,'Customer')));
                    else if ($email == null && preg_match("/".ANONYMOUS_ACCOUNT_NAME."/",$account_name))
                    {
                        $obj->setPersonInfo(array(array('Email',$account_name,'Customer')));
                    }

                    if($this->isPersionInfoGroupActive('Billing'))
                        $obj->copyPersonInfo('Billing','Customer',array('Email'));
                    elseif($this->isPersionInfoGroupActive('Shipping'))
                        $obj->copyPersonInfo('Shipping','Customer',array('Email'));

                    modApiFunc('EventsManager','throwEvent','AccountWasAutoCreated',$account_name);
                }
            }
        };

        modApiFunc('Customer_Account', 'setAccountLanguage',
                   $obj -> getPersonInfo('Account'),
                   modApiFunc('MultiLang', 'getLanguage'));
        modApiFunc('Customer_Account', 'setAccountNotificationLanguage',
                   $obj -> getPersonInfo('Account'),
                   modApiFunc('MultiLang', 'getLanguage'));
        $obj -> __loadBaseInfo();

        $ca_tables = modApiStaticFunc('Checkout','getTables');
        $orders_table = $ca_tables['orders']['columns'];

        $query = new DB_Update('orders');
        $query->addUpdateValue($orders_table['person_id'], $obj->getPersonInfo('ID'));
        $query->WhereValue($orders_table['id'], DB_EQ, $order_id);

        $application->db->PrepareSQL($query);
        $application->db->DB_Exec();

        return;
    }

    function OnCheckoutPersonInfoFieldUpdated($field_info)
    {
        $co_variant_id = $field_info['variant_id'];
        $co_attr_id = $field_info['attribute_id'];
        $visible_name = $field_info['name'];
        $is_visible = $field_info['visible'] == 1 ? 'Y' : 'N';
        $is_required = $field_info['required'] == 1 ? 'Y' : 'N';

        if($is_required == 'Y')
            $is_visible = 'Y';

        $varinat_info = modApiFunc('Checkout','getPersonVariantInfo',$co_variant_id);

        if($varinat_info == false)
            return;

        $type_id = $varinat_info['type_id'];

        if(empty($this->_co_pi_types_ids))
        {
            $this->__loadCOPITypesIDs();
        };

        $group_name = array_search($type_id, $this->_co_pi_types_ids);

        if($group_name === false)
            return;

        $group_name = _ml_ucfirst(_ml_strtolower($group_name));

        if($this->getPersonInfoGroupInfoByName($group_name) == null)
            return;

        if(empty($this->_co_attrs_ids))
        {
            $this->__loadCOAttrsIDs();
        };

        $attr_name = array_search($co_attr_id, $this->_co_attrs_ids);

        if($attr_name === false)
            return;

        $attr_name = $this->getPersionInfoAttrNameByLowcaseName(_ml_strtolower($attr_name));

        $arr_for_update = array(
            $attr_name => array(
                    'visible_name' => $visible_name
                   ,'is_visible' => $is_visible
                   ,'is_required' => $is_required
                )
        );

        $this->updateGroupAttrsInfo($group_name, $arr_for_update);
    }

    function OnCheckoutAttributesSortOrderUpdated($attrSortOrderArray, $co_variant_id)
    {
        $varinat_info = modApiFunc('Checkout','getPersonVariantInfo',$co_variant_id);

        if($varinat_info == false)
            return;

        $type_id = $varinat_info['type_id'];

        if(empty($this->_co_pi_types_ids))
        {
            $this->__loadCOPITypesIDs();
        };

        $group_name = array_search($type_id, $this->_co_pi_types_ids);

        if($group_name === false)
            return;

        $group_name = _ml_ucfirst(_ml_strtolower($group_name));

        if($this->getPersonInfoGroupInfoByName($group_name) == null)
            return;

        if(empty($this->_co_attrs_ids))
        {
            $this->__loadCOAttrsIDs();
        };

        $arr_for_update = array();

        $so = 0;
        foreach($attrSortOrderArray as $co_attr_id)
        {
            $attr_name = array_search($co_attr_id, $this->_co_attrs_ids);

            if($attr_name === false)
                continue;

            $attr_name = $this->getPersionInfoAttrNameByLowcaseName(_ml_strtolower($attr_name));
            $arr_for_update[$attr_name] = array('sort_order' => ++$so);
        };

        if(empty($arr_for_update))
            return;

        $this->updateGroupAttrsInfo($group_name, $arr_for_update);
    }

    function setCustomersSearchFilter($filter)
    {
        if($this->_customers_search_filter !== $filter)
        {
            $this->_customers_search_filter = $filter;
            $this->_customers = null;
        };
    }

    function getSearchCustomersResult()
    {
        if($this->_customers == null)
        {
            // getting currency codes from orders
            $currency_codes = execQuery('SELECT_ORDER_CURRENCY_CODES', array());

            // update currency rates
            modApiFunc('Currency_Converter', 'updateTempCurrencyRates', $currency_codes);

            // search for customers
            $this -> __searchCustomers();
        };
        return $this->_customers;
    }

    function getPgSearchCustomersResult()
    {
        if ($this->_customers_search_filter === null)
            return '';

        if ($this->_customers_search_filter['type'] == 'custom' &&
            !preg_match("/^[0-9]+$/", $this->_customers_search_filter['search_string']))
        {
            $attrs_to_search = array('FirstName', 'LastName', 'Email', 'Phone');
            $attrs_ids = execQuery('SELECT_ATTRS_GROUP_IDS_BY_ATTR_NAMES', $attrs_to_search);
            $this->_customers_search_filter['attr_group_ids'] = array();
            foreach($attrs_ids as $v)
                $this->_customers_search_filter['attr_group_ids'][] = $v['ag_id'];
        }
        else
        {
            unset($this->_customers_search_filter['attr_group_ids']);
        }
        if (!isset($this->_customers_search_filter['currency_code']))
            $this->_customers_search_filter['currency_code'] = modApiFunc('Localization','getCurrencyCodeById',modApiFunc('Localization', 'getLocalMainCurrency'));

        $this->_customers_search_filter['main_currency_code'] = modApiFunc('Localization','getCurrencyCodeById',modApiFunc('Localization', 'getLocalMainCurrency'));

        return execQueryPaginator('SELECT_SEARCH_CUSTOMERS',
                                  $this -> _customers_search_filter);
    }

    function getCustomerAccountNameByCustomerID($customer_id)
    {
        $res = execQuery('SELECT_CUSTOMER_ACCOUNT_NAME_BY_CUSTOMER_ID',array('customer_id'=>$customer_id));
        if(count($res) != 1)
        {
            return null;
        }
        else
        {
            return $res[0]['customer_account'];
        };
    }

    function deleteCustomers($customers_ids)
    {
        if(!is_array($customers_ids) or empty($customers_ids))
        {
            return false;
        };

        global $application;
        $ca_tables = $this->getTables();
        $co_tables = modApiStaticFunc('Checkout','getTables');

        $query = new DB_Select();
        $query->addSelectTable('orders');
        $query->addSelectField($co_tables['orders']['columns']['id'], 'order_id');
        $query->Where($co_tables['orders']['columns']['person_id'], DB_IN, "(".implode(", ",$customers_ids).")");
        $res = $application->db->getDB_Result($query);

        $orders_ids = array();
        for($i=0; $i<count($res); $i++)
        {
            $orders_ids[] = $res[$i]['order_id'];
        };

        if(!empty($orders_ids))
        {
            modApiFunc('Checkout','DeleteOrders',$orders_ids);
        };

        $accounts = array();

        $query = new DB_Select();
        $query->addSelectField($ca_tables['ca_customers']['columns']['customer_account'], 'customer_account');
        $query->addSelectTable('ca_customers');
        $query->Where($ca_tables['ca_customers']['columns']['customer_id'], DB_IN, "(".implode(", ",$customers_ids).")");
        $res = $application->db->getDB_Result($query);

        for($i=0; $i<count($res); $i++)
        {
            $accounts[] = $res[$i]['customer_account'];
        };

        if(!empty($accounts))
        {
            $query = new DB_Delete('ca_activation_keys');
            $query->Where($ca_tables['ca_activation_keys']['columns']['customer_account'], DB_IN, "('".implode("','",$accounts)."')");
            $application->db->PrepareSQL($query);
            $application->db->DB_Exec();
        };

        $query = new DB_Delete('ca_person_info_data');
        $query->Where($ca_tables['ca_person_info_data']['columns']['customer_id'], DB_IN, "(".implode(", ",$customers_ids).")");
        $application->db->PrepareSQL($query);
        $application->db->DB_Exec();

        $query = new DB_Delete('ca_customers');
        $query->Where($ca_tables['ca_customers']['columns']['customer_id'], DB_IN, "(".implode(", ",$customers_ids).")");
        $application->db->PrepareSQL($query);
        $application->db->DB_Exec();

        return true;
    }

    function dropCustomerPassword($var, $var_type='customer_account')
    {
        if(!in_array($var_type, array('customer_account','customer_id')))
        {
            return false;
        };

        global $application;
        $tables = $this->getTables();
        $ca_table = $tables['ca_customers']['columns'];

        $query = new DB_Select();
        $query->addSelectTable('ca_customers');
        $query->addSelectField($ca_table['customer_id'],'customer_id');
        $query->addSelectField($ca_table['customer_account'],'customer_account');
        $query->WhereValue($ca_table[$var_type], DB_EQ, $var);
        $res = $application->db->getDB_Result($query);

        if(count($res) != 1)
        {
            return false;
        }
        else
        {
            $customer_id = $res[0]['customer_id'];
            $customer_account = $res[0]['customer_account'];
        };

        $query = new DB_Update('ca_customers');
        $query->addUpdateValue($ca_table['customer_password'], $this->__genPseudoPasswd());
        $query->addUpdateValue($ca_table['customer_status'], 'R');
        $query->WhereValue($ca_table['customer_id'], DB_EQ, $customer_id);
        $application->db->PrepareSQL($query);

        return ($application->db->DB_Exec() and $this->replaceActivationKey($customer_account));
    }

    function getAllCustomerEmails()
    {
        $res = execQuery('SELECT_ALL_CUSTOMERS_EMAILS', array());

        $emails = array();
        for($i=0; $i<count($res); $i++)
        {
            $emails[] = $res[$i]['value'];
        };

        return $emails;
    }

    function getAttributesForExport($group_name)
    {
        $attrs = array();

        switch($group_name)
        {
            case 'system':
                $attrs = array(
                    'id' => array('tag' => 'ID', 'visible' => 'ID')
                   ,'account' => array('tag' => 'AccountName', 'visible' => 'Account Name')
                   ,'status' => array('tag' => 'Status', 'visible' => 'Status')
                );
                break;
            case 'orders':
                $attrs = array(
                    'quantity' => array('tag' => 'OrdersQuantity', 'visible' => 'Orders Quantity')
                   ,'amount' => array('tag' => 'OrdersTotalAmount', 'visible' => 'Total Amount')
                   ,'paid_amount' => array('tag' => 'OrdersTotalFullyPaidAmount', 'visible' => 'Total Paid Amount')
                );
                break;
            default:
                $group_info = $this->getPersonInfoGroupInfoByName(_ml_ucfirst(_ml_strtolower($group_name)));
                $group_attrs = $this->getPersonInfoGroupAttrs($group_info['group_id'], PERSON_INFO_GROUP_ATTR_VISIBLE);
                foreach($group_attrs as $attr_info)
                {
                    if(preg_match("/password/i",$attr_info['attr_name']))
                        continue;

                    $tag_prefix = '';
                    if(_ml_strtolower($group_name) != 'customer')
                    {
                        $tag_prefix = _ml_ucfirst(_ml_strtolower($group_name));
                    };

                    $attrs[_ml_strtolower($attr_info['attr_name'])] = array('tag' => $tag_prefix.$attr_info['attr_name'], 'visible' => $attr_info['visible_name']);
                };
                break;
        };

        return $attrs;
    }

    function __genActivationKey()
    {
        return _ml_strrev(md5(mt_rand(0,1000).time().uniqid(mt_rand(435,971))));
    }

    function __genPseudoPasswd()
    {
        return _ml_strrev(md5(mt_rand(271,3141).time().uniqid(mt_rand(314,2718))));
    }

    function __genAutoAccountName()
    {
        $params = array('anon_name' => ANONYMOUS_ACCOUNT_NAME);

    	# searching for latest anonymous id
        $r = execQuery('SELECT_ANONYMOUS_USERS', $params);

    	$next_id = 0;
    	if (is_array($r))
    	{
    		foreach ($r as $i => $u)
    	    {
    	        $v = preg_match("/".ANONYMOUS_ACCOUNT_NAME."([0-9*])/",$u['customer_account'], $m);
    	        if ($m[1] > $next_id)
    	            $next_id = $m[1];
    	    }
    	}
    	$next_id++;

    	$account_name = ANONYMOUS_ACCOUNT_NAME.$next_id;

    	while ($this->doesAccountExists($account_name))
    	{
    		$next_id++;
    		$account_name = ANONYMOUS_ACCOUNT_NAME.$next_id;
    	}

        /*do{
            $account_name = _ml_substr(preg_replace('/[^0-9]/i','',md5(microtime_float())),0,AUTOACCOUNT_LENGTH);
        }while($this->doesAccountExists($account_name));*/

        return $account_name;
    }


    /**
     *                            -                ,                    id         ,
     *                .
     * : "Turn On/Turn Off"                      .                 ,
     *                  .
     */
     //:
    function __loadCODevaultVariantAttrs()
    {
        global $application;
        loadClass('Checkout');
        $co_tables = Checkout::getTables();
        $pit_table = $co_tables['person_info_types']['columns'];
        $piv_table = $co_tables['person_info_variants']['columns'];
        $piva_table = $co_tables['person_info_variants_to_attributes']['columns'];

        $query = new DB_Select();
        $query->addSelectField($piva_table['attribute_id'], 'attribute_id');
        $query->addSelectField($piva_table['visible'], 'visible');
        $query->addSelectField($pit_table['id'], 'type_id');

        $query->addInnerJoin('person_info_variants', $piv_table['id'], DB_EQ, $piva_table['variant_id']);
        $query->addInnerJoin('person_info_types', $pit_table['id'], DB_EQ, $piv_table['type_id'] . ' AND ' . $piv_table['tag'] . " = 'default' ");

        $query_res = $application->db->getDB_Result($query);

        $this->_co_default_variant_attrs = array();
        foreach($query_res as $attr_info)
        {
            if(!isset($this->_co_default_variant_attrs[$attr_info['type_id']]))
            {
                $this->_co_default_variant_attrs[$attr_info['type_id']] = array();
            }
            $this->_co_default_variant_attrs[$attr_info['type_id']][$attr_info['attribute_id']] = $attr_info;
        };
    }


    function __loadCOAttrsIDs()
    {
        global $application;
        loadClass('Checkout');
        $co_tables = Checkout::getTables();
        $pa_table = $co_tables['person_attributes']['columns'];

        $query = new DB_Select();
        $query->addSelectTable('person_attributes');
        $query->addSelectField($pa_table['id'], 'attr_id');
        $query->addSelectField($pa_table['tag'], 'attr_tag');

        $res = $application->db->getDB_Result($query);

        foreach($res as $k => $pa_info)
        {
            if(_ml_strtolower($pa_info['attr_tag']) == 'postcode')
                $pa_info['attr_tag'] = 'zipcode';

            $this->_co_attrs_ids[_ml_strtolower($pa_info['attr_tag'])] = $pa_info['attr_id'];
        };
    }

    function __loadCOPITypesIDs()
    {
        global $application;
        loadClass('Checkout');
        $co_tables = Checkout::getTables();
        $pa_table = $co_tables['person_info_types']['columns'];

        $query = new DB_Select();
        $query->addSelectTable('person_info_types');
        $query->addSelectField($pa_table['id'], 'type_id');
        $query->addSelectField($pa_table['tag'], 'type_tag');

        $res = $application->db->getDB_Result($query);

        foreach($res as $k => $pa_info)
        {
            $this->_co_pi_types_ids[_ml_strtolower(str_replace('Info','',$pa_info['type_tag']))] = $pa_info['type_id'];
        };
    }

    function __isCOAttrVisible($type_id, $attr_id)
    {
        global $application;
        if(empty($this->_co_default_variant_attrs))
        {
            $this->__loadCODevaultVariantAttrs();
        };

        if(isset($this->_co_default_variant_attrs[$type_id][$attr_id]))
        {
            return $this->_co_default_variant_attrs[$type_id][$attr_id]['visible'] == 1;
        }
        else
        {
            return false;
        }
    }

    function __isCOAttrHidden($type_id, $attr_id)
    {
        return !($this->__isCOAttrVisible($type_id, $attr_id));
    }

    function __fetch_customer_accounts($customer_ids = NULL)
    {
        $rows = execQuery("SELECT_CUSTOMER_ACCOUNTS", array("customer_ids" => $customer_ids));
        $statuses = array();
        foreach($rows as $info)
        {
            $statuses[$info['customer_id']] = $info['customer_account'];
        }
        return $statuses;
    }

    function __fetch_customer_statuses($customer_ids = NULL)
    {
        global $application;
        if($customer_ids !== NULL && empty($customer_ids))
        {
            return array();
        }
        else
        {
            $rows = execQuery("SELECT_CUSTOMER_STATUSES", array("customer_ids" => $customer_ids));

            $statuses = array();
            foreach($rows as $info)
            {
                $statuses[$info['customer_id']] = $info['customer_status'];
            }
            return $statuses;
        }
    }

    function __fetch_customer_names($customer_ids = NULL)
    {
        global $application;
        if($customer_ids !== NULL && empty($customer_ids))
        {
            return array();
        }
        else
        {
            $name_list = execQuery("SELECT_CUSTOMER_NAMES", array("customer_ids" => $customer_ids));
            $full_names = array();
            foreach($name_list as $name)
            {
                $full_names[$name['customer_id']] = 'N/A';
                if (isset($name['name_ci']) && !isempty(trim($name['name_ci'])))
                {
                    $full_names[$name['customer_id']] = trim($name['name_ci']);
                }
                if (isset($name['name_bi']) && !isempty(trim($name['name_bi'])))
                {
                    $full_names[$name['customer_id']] = trim($name['name_bi']);
                }
                if (isset($name['name_si']) && !isempty(trim($name['name_si'])))
                {
                    $full_names[$name['customer_id']] = trim($name['name_si']);
                }
            }
            return $full_names;
        }
    }

    function __searchCustomers()
    {
        if ($this->_customers_search_filter === null)
        {
            $this->_customers = array();
            return;
        }

        if ($this->_customers_search_filter['type'] == 'custom' &&
            !preg_match("/^[0-9]+$/", $this->_customers_search_filter['search_string']))
        {
            $attrs_to_search = array('FirstName', 'LastName', 'Email', 'Phone');
            $attrs_ids = execQuery('SELECT_ATTRS_GROUP_IDS_BY_ATTR_NAMES', $attrs_to_search);
            $this->_customers_search_filter['attr_group_ids'] = array();
            foreach($attrs_ids as $v)
                $this->_customers_search_filter['attr_group_ids'][] = $v['ag_id'];
        }
        else
        {
            unset($this->_customers_search_filter['attr_group_ids']);
        }
        if (!isset($this->_customers_search_filter['currency_code']))
            $this->_customers_search_filter['currency_code'] = modApiFunc('Localization','getCurrencyCodeById',modApiFunc('Localization', 'getLocalMainCurrency'));

        $this->_customers_search_filter['main_currency_code'] = modApiFunc('Localization','getCurrencyCodeById',modApiFunc('Localization', 'getLocalMainCurrency'));

        $this -> _customers = execQuery('SELECT_SEARCH_CUSTOMERS',
                                        $this -> _customers_search_filter);

        return;
    }

//============================================================================

    function getmicrotime()
    {
        list($usec, $sec) = explode(" ",microtime());
        return ((float)$usec + (float)$sec);
    }

//============================================================================

    var $_co_attrs_ids;
    var $_co_default_variant_attrs;
    var $_co_pi_types_ids;
    var $_lowcase_names;
    var $_customers_search_filter;
    var $_customers;
};

?>