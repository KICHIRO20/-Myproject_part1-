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
 * MultiLang class
 *
 * Common API class for Multi Language support.
 *
 * @author Sergey Kulitsky
 * @version $Id: multilang_api.php xxxx 2009-05-28 11:33:47Z azrael $
 * @package MultiLang
 */
class MultiLang
{
    function MultiLang()
    {
        $this -> _language = '';
        $this -> _language_number = 0;
        $this -> _default_language = '';
        $this -> _default_language_number = 0;
        $this -> _resource_language = '';
        $this -> _resource_language_number = 0;
        $this -> _heap_table_name = 'multilang_data';

	if(modApiFunc('Modules_Manager','getModuleVersion','Shipping_Module_Flat_Shipping_Rates') !='0.0.0')
		$this -> _ml_fields['Shipping_Module_Flat_Shipping_Rates'] = array(
        	    'sm_flat_shipping_rates_settings' => array(
        	        'sm_flat_shipping_rates_setting_value' => 1101
        	)
		);

	if(modApiFunc('Modules_Manager','getModuleVersion','Shipping_Module_Flat_Shipping_Rates2') !='0.0.0')
		$this -> _ml_fields['Shipping_Module_Flat_Shipping_Rates2'] = array(
	            'sm_flat_shipping_rates2_settings' => array(
	                'sm_flat_shipping_rates_setting_value' => 1201
	        )
		);

	if(modApiFunc('Modules_Manager','getModuleVersion','Shipping_Module_Flat_Shipping_Rates3') !='0.0.0')
		$this -> _ml_fields['Shipping_Module_Flat_Shipping_Rates3'] = array(
            	'sm_flat_shipping_rates3_settings' => array(
            	    'sm_flat_shipping_rates_setting_value' => 1301
        	)
		);
    }

    function install()
    {
    	include_once(dirname(__FILE__) . '/includes/install.inc');
    }

    function getTables()
    {
        static $tables;

        if (is_array($tables))
        {
            return $tables;
        }

        $tables = array ();

        $tbl_info = 'multilang_data';
        $tables[$tbl_info] = array();
        $tables[$tbl_info]['columns'] = array
            (
                'ml_id'     => $tbl_info . '.ml_id',
                'label'     => $tbl_info . '.label',
                'label_key' => $tbl_info . '.label_key',
                'lng'       => $tbl_info . '.lng',
                'value'     => $tbl_info . '.value'
            );
        $tables[$tbl_info]['types'] = array
            (
                'ml_id'     => DBQUERY_FIELD_TYPE_INT . ' NOT NULL auto_increment',
                'label'     => DBQUERY_FIELD_TYPE_INT,
                'label_key' => DBQUERY_FIELD_TYPE_INT,
                'lng'       => DBQUERY_FIELD_TYPE_INT,
                'value'     => DBQUERY_FIELD_TYPE_LONGTEXT
            );
        $tables[$tbl_info]['primary'] = array
            (
                'ml_id'
            );
        $tables[$tbl_info]['indexes'] = array
            (
                'UNIQUE KEY label_lng' => 'label, label_key, lng',
                'label'                => 'label',
                'label_key'            => 'label_key',
                'lng'                  => 'lng'
            );

        $tbl_info = 'multilang_languages';
        $tables[$tbl_info] = array();
        $tables[$tbl_info]['columns'] = array
            (
                'lng'         => $tbl_info . '.lng',
                'lng_name'    => $tbl_info . '.lng_name',
                'codepage'    => $tbl_info . '.codepage',
            );
        $tables[$tbl_info]['types'] = array
            (
                'lng'         => DBQUERY_FIELD_TYPE_CHAR2,
                'lng_name'    => DBQUERY_FIELD_TYPE_CHAR255,
                'codepage'    => DBQUERY_FIELD_TYPE_CHAR255,
            );
        $tables[$tbl_info]['primary'] = array
            (
                'lng'
            );
        $tables[$tbl_info]['indexes'] = array
            (
                'lng_name'   => 'lng_name'
            );

        $tbl_info = 'multilang_active_languages';
        $tables[$tbl_info] = array();
        $tables[$tbl_info]['columns'] = array
            (
                'lng_number'  => $tbl_info . '.lng_number',
                'lng'         => $tbl_info . '.lng',
                'lng_name'    => $tbl_info . '.lng_name',
                'lng_name_mb' => $tbl_info . '.lng_name_mb',
                'codepage'    => $tbl_info . '.codepage',
                'is_active'   => $tbl_info . '.is_active',
                'is_default'  => $tbl_info . '.is_default'
            );
        $tables[$tbl_info]['types'] = array
            (
                'lng_number'  => DBQUERY_FIELD_TYPE_INT . ' NOT NULL auto_increment',
                'lng'         => DBQUERY_FIELD_TYPE_CHAR2,
                'lng_name'    => DBQUERY_FIELD_TYPE_CHAR255,
                'lng_name_mb' => DBQUERY_FIELD_TYPE_CHAR255,
                'codepage'    => DBQUERY_FIELD_TYPE_CHAR255,
                'is_active'   => DBQUERY_FIELD_TYPE_CHAR1,
                'is_default'  => DBQUERY_FIELD_TYPE_CHAR1
            );
        $tables[$tbl_info]['primary'] = array
            (
                'lng_number'
            );
        $tables[$tbl_info]['indexes'] = array
            (
                'lng'        => 'lng',
                'lng_name'   => 'lng_name',
                'is_active'  => 'is_active',
                'is_default' => 'is_default',
            );

        global $application;
        return $application->addTablePrefix($tables);
    }

    function uninstall()
    {
        global $application;
        $query = new DB_Table_Delete(MultiLang :: getTables());
        $application -> db -> getDB_Result($query);
    }

    /**
     * Initializes the languages
     * Note: cannot be executed in constructor
     *       since the class is created before creating the factory
     */
    function initLanguages()
    {
        global $application;
        global $zone;

        // if no mb_string then no language selection is available
        if (!$application -> multilang_core -> _mb_enabled)
            return;

        $active_only = ($zone == 'CustomerZone');

        // setting the default language
        $this -> _default_language = $this -> _readDefaultLanguage();
		CTrace::inf('Default language is '.CTrace::var2str($this -> _default_language));

        // selecting the current language
        $this -> _language = $this -> _default_language;
		CTrace::inf('Current language is '.CTrace::var2str($this -> _language));

        // if customer is registered
        if ($zone == 'CustomerZone' &&
            $customer = modApiFunc('Customer_Account', 'getCurrentSignedCustomer'))
        {
            $lng = modApiFunc('Customer_Account', 'getAccountLanguage', $customer);
            if ($lng && $this -> checkLanguage($lng, $active_only))
            {
                $this -> _language = $lng;
                CTrace::inf('Customer is signed in, change current language to '.$this -> _language.' because of the account settings.');
            }
        }

        // if admin is registered
        if ($zone == 'AdminZone' &&
            $adminID = modApiFunc('Users', 'getCurrentUserID'))
        {
            $lng = modApiFunc('Users', 'getAccountLanguageById', $adminID);
            if ($lng && $this -> checkLanguage($lng, $active_only))
            {
                $this -> _language = $lng;
                CTrace::inf('Admin is signed in, change current language to '.$this -> _language.' because of the account settings.');
            }
        }

        // if cookie is set
        if (isset($_COOKIE['current_language_az']) && $zone == 'AdminZone')
        {
            $lng = $_COOKIE['current_language_az'];
            if ($this -> checkLanguage($lng, $active_only))
            {
                $this -> _language = $lng;
                CTrace::inf('Change current language to '.$this -> _language.' because of cookies.');
            }
        }
        if (isset($_COOKIE['current_language']) && $zone == 'CustomerZone')
        {
            $lng = $_COOKIE['current_language'];
            if ($this -> checkLanguage($lng, $active_only))
            {
                $this -> _language = $lng;
                CTrace::inf('Change current language to '.$this -> _language.' because of cookies.');
            }
        }

        // if language is set in the query string
        if (isset($_GET['current_language'])
            || isset($_POST['current_language']))
        {
            if (isset($_GET['current_language']))
                $lng = $_GET['current_language'];
            else
                $lng = $_POST['current_language'];
            if ($this -> checkLanguage($lng, $active_only))
            {
                $this -> _language = $lng;
                CTrace::inf('Change current language to '.$this -> _language.' because of GET params.');
            }
        }

        // checking the language
        if (!$this -> checkLanguage($this -> _language, $active_only))
        {
            $this -> _language = $this -> _getAnyLanguage($active_only);
            if (!$this -> _language)
            {
                $this -> _default_language = '';
                CTrace::inf('Set default language to "".');
            }
        }

        // saving the current language
        // (only in case if the language was not provided in URL)
        if ($this -> _language
            && !isset($_GET['current_language'])
            && !isset($_POST['current_language']))
        {
            // setting the cookie for 30 days
            if ($zone == 'CustomerZone')
                setcookie('current_language', $this -> _language, time() + 2592000);
            else
                setcookie('current_language_az', $this -> _language, time() + 2592000);

            // updating the customer_account if needed
            if ($zone == 'CustomerZone' && $customer)
                modApiFunc('Customer_Account', 'setAccountLanguage', $customer, $this -> _language);

            // updating the admin_account if needed
            if ($zone == 'AdminZone' && $adminID)
                modApiFunc('Users', 'updateAccountLanguage', $adminID, $this -> _language);
        }

        $this -> _default_language_number = $this -> _readLanguageNumber($this -> _default_language);
        $this -> _language_number = $this -> _readLanguageNumber($this -> _language);
        $this -> _resource_language = $this -> _language;
        $this -> _resource_language_number = $this -> _language_number;

        // getting the page language if set
        if ($zone == 'AdminZone' &&
            modApiFunc('Session', 'is_set', 'PageLanguages'))
        {
            $PageLanguages = modApiFunc('Session', 'get', 'PageLanguages');
            $request = &$application -> getInstance('Request');
            $view = $request -> getValueByKey('page_view');
            $action = $request -> getCurrentAction();
            $pagelanguage = '';

            $script_name = array_pop(explode('/', $_SERVER['SCRIPT_NAME']));
            if (isset($PageLanguages['pages'][$script_name]))
                $pagelanguage = $PageLanguages['pages'][$script_name];
            elseif (isset($PageLanguages['views'][$view]))
                $pagelanguage = $PageLanguages['views'][$view];
            elseif (isset($PageLanguages['actions'][$action]))
                $pagelanguage = $PageLanguages['actions'][$action];

            if ($pagelanguage &&
                $this -> checkLanguage($pagelanguage, $active_only))
            {
                $this -> _language = $pagelanguage;
                $this -> _language_number = $this -> _readLanguageNumber($this -> _language);
                CTrace::inf('Change current language to '.$this -> _language.' because it is page language.');
            }
        }
    }

    /**
     * Returns heap table name
     */
    function getHeapTableInfo()
    {
        $tables = $this -> getTables();
        return array($this -> _heap_table_name,
                     $tables[$this -> _heap_table_name]['columns']);
    }

    function setLanguage($lng)
    {
        $this -> _language = $lng;
        $this -> _language_number = $this -> _readLanguageNumber($lng);
    }

    function setResourceLanguage($lng)
    {
        $this -> _resource_language = $lng;
        $this -> _resource_language_number = $this -> _readLanguageNumber($lng);
    }

    function getLanguage()
    {
        return $this -> _language;
    }

    function setDefaultLanguage($lng)
    {
        $this -> _default_language = $lng;
        $this -> _default_language_number = $this -> _readLanguageNumber($lng);
    }

    function getDefaultLanguage()
    {
        return $this -> _default_language;
    }

    function getResourceLanguage()
    {
        return $this -> _resource_language;
    }

    function getLanguageNumber()
    {
        return $this -> _language_number;
    }

    function getDefaultLanguageNumber()
    {
        return $this -> _default_language_number;
    }

    function getResourceLanguageNumber()
    {
        return $this -> _resource_language_number;
    }

    function setClass($class_full_name)
    {
        $this -> _class = $class_full_name;
    }

    function getClass()
    {
        return $this -> _class;
    }

    function getMLValue($label, $label_key, $lng = '')
    {
        if (!$lng)
            $lng = $this -> _language_number;
        elseif (!is_numeric($lng))
            $lng = $this -> _readLanguageNumber($lng);

        $result = execQuery('SELECT_ML_LABEL_VALUE', array('label' => $label,
                                                           'label_key' => $label_key,
                                                           'lng' => $lng));

        if (is_array($result) && !empty($result))
            return $result[0]['value'];

        return false;
    }

    function getMLID($label, $label_key, $lng = '')
    {
        if (!$lng)
            $lng = $this -> _language_number;
        elseif (!is_numeric($lng))
            $lng = $this -> _readLanguageNumber($lng);

        $result = execQuery('SELECT_ML_LABEL_ID', array('label' => $label,
                                                        'label_key' => $label_key,
                                                        'lng' => $lng));

        if (is_array($result) && !empty($result))
            return $result[0]['ml_id'];

        return false;
    }

    function setMLValue($label, $label_key, $value, $lng = '')
    {
        if (!$lng)
            $lng = $this -> _language;

        if (!is_numeric($lng))
            $lng = $this -> _readLanguageNumber($lng);

        if ($lng == $this -> getDefaultLanguageNumber())
            return false;

        if (!$label || !$label_key)
            return false;

        $id = $this -> getMLID($label, $label_key, $lng);

        execQuery('REPLACE_ML_RECORD', array('label' => $label,
                                             'label_key' => $label_key,
                                             'lng' => $lng,
                                             'id' => $id,
                                             'value' => $value));
    }

    function deleteMLRecord($label, $label_key, $lng = '')
    {
        if (!$lng)
            $lng = $this -> _language;
        if ($lng == $this -> getDefaultLanguage())
            return false;

        if (!is_numeric($lng))
            $lng = $this -> _readLanguageNumber($lng);

        execQuery('DELETE_ML_RECORD', array('label' => $label,
                                            'label_key' => $label_key,
                                            'lng' => $lng));
    }

    /**
     * Maps provided field to the multilang table
     * $table - table name (from getTables() function)
     * $field - field name
     * $class_name - class name
     * $field_number - number of the field (set in Module::getTables array)
     * returns: the label field in multilang_data
     */
    function mapMLField($table, $field, $class_name = '', $field_number = 0)
    {
        if (!$class_name)
            $class_name = $this -> _class_name;

        // if field number is not specified retriving it
        if (!$field_number || !is_numeric($field_number))
        {
            if (isset($this -> _ml_fields[$class_name][$table][$field]))
                $field_number = $this -> _ml_fields[$class_name][$table][$field];
            else
                _fatal(array('CODE' => 'CORE_ML_006', 'MODULE' => 'MultiLang'),
                       $class_name, $table, $field);
        }

        return $field_number;
    }

    /**
     * Maps provided label (inverse function for mapMLField)
     */
    function mapMLLabel($label)
    {
        foreach($this -> _ml_fields as $class => $tables)
            foreach($tables as $table => $fields)
                foreach($fields as $field => $l)
                    if ($l == $label)
                        return array($class, $table, $field);

        return array('', '', '');
    }

    /**
     * Deletes all multilang records for a given class
     */
    function deleteModuleData($class_name)
    {
        if (isset($this -> _ml_fields[$class_name]))
            foreach($this -> _ml_fields[$class_name] as $table)
                foreach($table as $ml_field)
                    execQuery('DELETE_ML_FIELD_DATA',
                              array('field' => $ml_field));
    }

    /**
     * Returns the total number of records for a given language
     */
    function getTotalLanguageRecordNumber($lng = '', $filter = '')
    {
        if (!$lng)
            $lng = $this -> _language;

        if (!is_numeric($lng))
            $lng = $this -> _readLanguageNumber($lng);

        if ($lng == $this -> getDefaultLanguageNumber())
            return 0;

        if (!$filter)
            $filter = $this -> getAllLabelIDs();

        if (!is_array($filter))
            $filter = array($filter);

        $result = execQuery('SELECT_COUNT_OF_LANGUAGE_RECORDS',
                            array('lng' => $lng, 'labels' => $filter));

        if ($result)
            return $result[0]['count_id'];

        return 0;
    }

    /**
     * Returns the total number of records for a given language by classes
     */
    function getLanguageRecordNumbers($lng = '', $filter = '')
    {
        if (!$lng)
            $lng = $this -> _language;

        if (!is_numeric($lng))
            $lng = $this -> _readLanguageNumber($lng);

        if (!$filter)
            $filter = $this -> getAllLabelIDs();

        if (!is_array($filter))
            $filter = array($filter);

        $result = array();
        foreach($this -> _ml_fields as $class => $tables)
        {
            $result[$class] = array();
            foreach($tables as $table => $fields)
            {
                $result[$class][$table] = array();
                foreach($fields as $field => $label)
                {
                    // skip if $label in $filter
                    if (!in_array($label, $filter))
                        continue;

                    $tmp = execQuery('SELECT_COUNT_OF_LANGUAGE_RECORDS',
                                     array('lng' => $lng,
                                           'labels' => array($label)));
                    if (isset($tmp[0]) && $tmp[0]['count_id'] > 0)
                        $result[$class][$table][$field] = $tmp[0]['count_id'];
                }
                if (empty($result[$class][$table]))
                    unset($result[$class][$table]);
            }
            if (empty($result[$class]))
                unset($result[$class]);
        }

        return $result;
    }

    /**
     * Returns the portion of data for the given label
     */
    function getLanguageRecordPortion($lng, $label, $pos, $bulk)
    {
        if (!is_numeric($lng))
            $lng = $this -> _readLanguageNumber($lng);

        return execQuery('SELECT_LANGUAGE_RECORDS',
                         array('lng' => $lng, 'label' => $label,
                               'pos' => $pos, 'bulk' => $bulk));
    }

    /**
     * Functions to work with multilang_active_languages table
     */
    function getLanguageList($active_only = true, $lng = '')
    {
        global $application;

        // if no mb_string then no language selection is available
        if (!$application -> multilang_core -> _mb_enabled)
            return '';

        $params = array('active_only' => $active_only);
        if ($lng)
            $params['lng'] = $lng;

        return execQuery('SELECT_LIST_OF_LANGUAGES', $params);
    }

    function checkLanguage($lng, $active_only = true)
    {
        $res = execQuery('SELECT_LIST_OF_LANGUAGES', array('lng' => $lng, 'active_only' => $active_only));
        if ($res)
            return true;

        return false;
    }

    function addLanguage($lng_data)
    {
        execQuery('INSERT_NEW_LANGUAGE', $lng_data);
    }

    function updateLanguage($data)
    {
        if (!isset($data['lng']) || !isset($data['lng_name']) || !$data['lng'])
            return;

        if (!isset($data['is_active']) || $data['is_active'] != 'Y')
            $data['is_active'] = 'N';

        execQuery('UPDATE_LANGUAGE_RECORD', $data);
    }

    function deleteLanguage($lng, $remove_records = false)
    {
        $lng_number = $this -> _readLanguageNumber($lng);
        execQuery('DELETE_LANGUAGE', array('lng' => $lng));
        if ($remove_records)
            $this -> deleteLanguageRecords($lng_number);
    }

    function deleteLanguageRecords($lng)
    {
        if (!is_numeric($lng))
            $lng = $this -> _readLanguageNumber($lng);

        execQuery('DELETE_ML_LANGUAGE_DATA', array('lng' => $lng));
    }

    function _readDefaultLanguage()
    {
        $res = execQuery('SELECT_DEFAULT_LANGUAGE', array());
        if ($res)
            return $res[0]['lng'];

        return '';
    }

    function _readLanguageNumber($lng)
    {
        $res = execQuery('SELECT_LANGUAGE_NUMBER', array('lng' => $lng));
        if ($res)
            return $res[0]['number'];

        return 0;
    }

    function _changeDefaultLanguage($lng)
    {
        execQuery('UPDATE_LANGUAGES_CLEAR_DEFAULT_LANGUAGE', array());
        execQuery('UPDATE_LANGUAGES_SET_DEFAULT_LANGUAGE', array('lng' => $lng));
    }

    function _getAnyLanguage($active_only = true)
    {
        $res = $this -> getLanguageList($active_only);
        if ($res)
            return $res[0]['lng'];

        return '';
    }
    /**
     * End of Functions to work with multilang_active_languages table
     */

    /**
     * Functions to work with multilang_languages table
     */
    function getLanguageData($lng_codes)
    {
        if (!is_array($lng_codes))
            $lng_codes = array($lng_codes);

        return execQuery('SELECT_ALL_LANGUAGES', array('lng_codes' => $lng_codes));
    }

    function getAllLanguages($exception = '')
    {
        return execQuery('SELECT_ALL_LANGUAGES', array('exception' => $exception));
    }
    /**
     * End of Functions to work with multilang_languages table
     */

    /**
     * Functions to work with the resource labels
     */

    /**
     * Counts the number of labels (using Resource module as well)
     */
    function getLabelCount($type, $lng = '')
    {
        $result = 0;

        if (!$lng)
            $lng = $this -> _language_number;
        elseif (!is_numeric($lng))
            $lng = $this -> _readLanguageNumber($lng);

        switch($type)
        {
            case 'all':
                $result = execQuery('SELECT_COUNT_OF_ALL_LABELS', array());
                $result = $result[0]['count_id'];
                break;

            case 'storefront':
                $result = execQuery('SELECT_COUNT_OF_ALL_LABELS',
                                    array('prefix' => 'CZ'));
                $result = $result[0]['count_id'];
                break;

            case 'custom':
                $result = execQuery('SELECT_COUNT_OF_ALL_LABELS',
                                    array('prefix' => 'CZ',
                                          'begin_with' => 'CUSTOM_'));
                $result = $result[0]['count_id'];
                break;

            case 'nontranslated':
                $result = execQuery('SELECT_COUNT_OF_TRANSLATED_LABELS',
                                    array('lng' => $lng, 'translated' => 'N'));
                $result = $result[0]['count_id'];
                break;

            case 'translated':
                $result = execQuery('SELECT_COUNT_OF_TRANSLATED_LABELS',
                                    array('lng' => $lng, 'translated' => 'Y'));
                $result = $result[0]['count_id'];
                break;
        }

        return $result;
    }

    /**
     * Gets the list of modules
     */
    function getResourceModuleList()
    {
        $result = array();
        $temp = execQuery('SELECT_RESOURCE_MODULES', array());
        if (is_array($temp))
            foreach($temp as $v)
                $result[$v['shortname']] = $v;

        return $result;
    }

    /**
     * Builds paginator data for the query to search labels
     */
    function searchPgLabels($search_filter, $pg_enable)
    {
        if ($pg_enable == PAGINATOR_ENABLE)
            $search_filter['use_paginator'] = true;

        return execQueryPaginator('SELECT_LABELS_BY_FILTER', $search_filter);
    }

    /**
     * Returns the list of labels by the filter
     */
    function searchLabels($search_filter)
    {
        return execQuery('SELECT_LABELS_BY_FILTER', $search_filter);
    }

    /**
     * Returns the number of labels by the filter
     */
    function searchLabelCount($search_filter)
    {
        return execQueryCount('SELECT_LABELS_BY_FILTER', $search_filter);
    }

    /**
     * Returns array of labels by their id in the specified language
     * @param ids - array of label id
     *              (should be eigher plain array
     *               or associative array with "id" key)
     * Usage: main usage - export labels
     */
    function getLabelTranslationByIDs($ids, $lng = '')
    {
        if (!$lng)
            $lng = $this -> _language_number;

        if (!is_numeric($lng))
            $lng = $this -> _readLanguageNumber($lng);

        if (!is_array($ids))
            return array();

        if (!is_numeric($ids[0]))
        {
            // assuming $ids is associative array
            // label id should be in "id" field of each element
            $tmp = array();
            foreach($ids as $v)
                $tmp[] = @$v['id'];

            $ids = $tmp;
        }

        $labels = execQuery(
            "SELECT_LABEL_TRANSLATION_BY_IDS",
            array(
                'label' => $this -> mapMLField('resource_labels',
                                               'res_text', 'Resources'),
                'ids'   => $ids,
                'lng'   => $lng
            )
        );

        if (!is_array($labels))
            return array();

        $result = array();
        foreach($labels as $v)
            $result[$v['id']] = $v['value'];

        return $result;
    }

    /**
     * Returns the label information
     * @param record: initial information from the DB
     */
    function getLabelInformation($record, $modules = '', $lng = '')
    {
        if (!$modules)
            $modules = $this -> getResourceModuleList();

        if (!$lng)
            $lng = $this -> _language;

        $record['sh_label'] = $record['label'];
        $record['module'] = $record['prefix'];

        if ($record['module'] == 'CZ')
            $record['zone'] = getMsg('ML', 'ML_STOREFRONT_LABEL');
        elseif ($record['module'] == '')
            $record['zone'] = getMsg('ML', 'ML_NA');
        else
            $record['zone'] = getMsg('ML', 'ML_ADMIN_LABEL');

        if ($record['module'] == 'CZ'
            && _ml_substr($record['label'], 0, 7) == 'CUSTOM_')
            $record['module_name'] = getMsg('ML', 'ML_CUSTOM_LABELS');
        elseif ($record['module'] == 'CZ')
            $record['module_name'] = getMsg('ML', 'ML_AVACTIS_LABELS');
        elseif (isset($modules[$record['module']]['module']))
            $record['module_name'] = $modules[$record['module']]['module'];
        else
            $record['module_name'] = getMsg('ML', 'ML_NA');

        $record['usage'] = getMsg('ML', 'ML_NA');
        if ($record['module'] == 'CZ')
            $record['usage'] = '<?php Label(\'' . $record['sh_label'] .
                               '\'); ?>';
        elseif ($record['module'] == 'SYS')
            $record['usage'] = '<?php Msg(\'' . $record['sh_label'] .
                               '\'); ?>';
        elseif ($record['module'])
            $record['usage'] = '<?php xMsg(\'' . $record['module'] .
                               '\', \'' . $record['sh_label'] . '\'); ?>';

        $record['status'] = ((@$record['value'] !== NULL)
                             ? getMsg('ML', 'ML_TRANSLATED_LABELS')
                             : getMsg('ML', 'ML_NON_TRANSLATED_LABELS'));
        if ($lng == $this -> _default_language)
            $record['status'] = '';

        return $record;
    }

    /**
     * End of Functions to work with the resource labels
     */

    /**
     * Functions to work with multilang records throughout the Avactis
     */
    function getAllMLRecordNumbers($filter = '')
    {
        if (!$filter)
            $filter = $this -> getAllLabelIDs();

        if (!is_array($filter))
            $filter = array($filter);

        $result = array();

        foreach($this -> _ml_fields as $class => $tables)
        {
            $result[$class] = array();
            foreach($tables as $table => $fields)
            {
                $result[$class][$table] = array();
                foreach($fields as $field => $label)
                {
                    // skip if $label in $filter
                    if (!in_array($label, $filter))
                        continue;

                    $filter_values = array();
                    $filter_field = '';

                    // for catalog -> product_attributes
                    // we need additional filter
                    if ($class == 'Catalog'
                        && $table == 'product_attributes'
                        && $field == 'product_attr_value')
                    {
                        $filter_values = modApiFunc('Catalog',
                                                    'getAllMLAttributeIDs');
                        $filter_field = 'attribute_id';
                    }

                    $tmp = execQuery('SELECT_COUNT_OF_ML_RECORDS',
                                     array(
                                         'table'        => $table,
                                         'field'        => $field,
                                         'filter_field' => $filter_field,
                                         'filter'       => $filter_values
                                     ));
                    if (isset($tmp[0]))
                        $result[$class][$table][$field] = $tmp[0]['num'];
                }
                if (empty($result[$class][$table]))
                    unset($result[$class][$table]);
            }
            if (empty($result[$class]))
                unset($result[$class]);
        }

        return $result;
    }

    function getMLRecordPortion($label, $pos, $bulk)
    {
        $params = array();
        $params['pos'] = $pos;
        $params['bulk'] = $bulk;

        list($class, $table, $field) = $this -> mapMLLabel($label);
        if (!$class)
            return array();

        $params['table'] = $table;
        $params['value_field'] = $field;

        // for catalog -> product_attributes
        // we need additional filter
        if ($class == 'Catalog' && $table == 'product_attributes'
            && $field == 'product_attr_value')
        {
            $params['filter'] = modApiFunc('Catalog', 'getAllMLAttributeIDs');
            $params['filter_field'] = 'attribute_id';
        }

        $tables = modApiFunc($class, 'getTables');
        $params['key_field'] = $tables[$table]['columns'][$tables[$table]['primary'][0]];

        return execQuery('SELECT_ML_RECORDS', $params);
    }

    function processMLRecords($label, $data)
    {
        if (!is_array($data))
            return;

        list($class, $table, $field) = $this -> mapMLLabel($label);
        if (!$class)
            return;

        $params = array();
        $params['table'] = $table;
        $params['value_field'] = $field;

        $tables = modApiFunc($class, 'getTables');
        $params['key_field'] = $tables[$table]['columns'][$tables[$table]['primary'][0]];

        foreach($data as $v)
        {
            $params['key'] = $v['label_key'];
            $params['value'] = $v['value'];
            execQuery('UPDATE_ML_RECORD', $params);
        }
    }

    /**
     * End of Functions to work with multilang records throughout the Avactis
     */

    /**
     * Functions to work with tmp records in multilang_data table
     */

    function clearTmpMLRecords()
    {
        execQuery('DELETE_TMP_ML_RECORDS', array());
    }

    function processTmpMLRecords($lng)
    {
        if (!is_numeric($lng))
            $lng = $this -> _readLanguageNumber($lng);

        if (!$lng)
            return;

        execQuery('UPDATE_TMP_ML_RECORDS_LANGUAGE', array('lng' => $lng));
    }

    function addTmpMLRecords($label, $data)
    {
        $insert_data = array();

        if (!is_array($data))
            return;

        if (!$label)
            return;

        foreach($data as $v)
            if ($v['label_key'])
                $insert_data[] = array($label, $v['label_key'],
                                       '-1', $v['value']);

        execQuery('MULTIPLE_INSERT_TMP_ML_RECORDS',
                  array('data' => $insert_data));
    }

    /**
     * End of Functions to work with tmp records in multilang_data table
     */

    function getAllLabelIDs()
    {
        $result = array();
        foreach($this -> _ml_fields as $class => $tables)
            foreach($tables as $table => $fields)
                foreach($fields as $field => $label)
                    $result[] = $label;

        return $result;
    }

    function getLabelIDs($classname = null, $table = null)
    {
        $result = $this -> _ml_fields;
        if (isset($classname) && array_key_exists($classname, $result)) {
            $result = $result[$classname];
            if (isset($table) && array_key_exists($table, $result)) {
                $result = $result[$table];
            }
        }
        return $result;
    }

    /**
     * Returns multilang records for a given module, table and record
     * The result contains all the records for all multilang fields
     * found in multilang_data table
     * array(
     *    'field_1' => array(
     *                     'lang_1' => value
     *                     'lang_2' => value
     *                     ...
     *                 ),
     *    'field_2' => array(
     *                     'lang_1' => value
     *                     'lang_2' => value
     *                     ...
     *                 ),
     *    ...
     * )
     */
    function getMLTableData($module, $table, $record)
    {
        if (!isset($this -> _ml_fields[$module][$table])
            || !is_array($this -> _ml_fields[$module][$table]))
            return array();

        $result = array();
        foreach($this -> _ml_fields[$module][$table] as $k => $v)
        {
            $result[$k] = array();
            $tmp = execQuery('SELECT_ML_ALL_RECORD_VALUES',
                             array('label' => $v, 'label_key' => $record));
            if (is_array($tmp))
                foreach($tmp as $vv)
                    $result[$k][$vv['lng']] = $vv['value'];
        }

        return $result;
    }

    /**
     * Adds bulk of records for given module, table and record
     * format of values:
     * array(
     *    'field_1' => array(
     *                     'lang_1' => value
     *                     'lang_2' => value
     *                     ...
     *                 ),
     *    'field_2' => array(
     *                     'lang_1' => value
     *                     'lang_2' => value
     *                     ...
     *                 ),
     *    ...
     * )
     */
    function addMLTableData($module, $table, $record, $values)
    {
        if (!is_array($values))
            return;

        $data = array();
        foreach($values as $field => $records)
        {
            $label = $this -> mapMLField($table, $field, $module);
            if (is_array($records))
                foreach($records as $lng => $value)
                    $data[] = array($label, $record, $lng, $value);

            execQuery('DELETE_ML_RECORDS_BY_LABEL_AND_LABEL_KEY',
                      array('label' => $label, 'label_key' => $record));
        }

        if (!empty($data))
            execQuery('MULTIPLE_REPLACE_ML_RECORDS', array('data' => $data));
    }

    # default language
    var $_default_language;
    var $_default_language_number;

    # current language
    var $_language;
    var $_language_number;

    # resource language
    var $_resource_language;
    var $_resource_language_number;

    # current class (full name)
    var $_class_name;

    # heap table name
    var $_heap_table_name;

    # array of multilang fields
    # index 0: module name
    # index 1: table name
    # index 2: field name
    # value: 'number' => unique number for the pair (table, field)
    var $_ml_fields = array(
        'Catalog' => array(
            'categories_descr' => array(
                'category_name'             => 1,
                'category_descr'            => 2,
                'category_image_descr'      => 3,
                'category_page_title'       => 4,
                'category_meta_keywords'    => 5,
                'category_meta_descr'       => 6,
                'category_image_file'       => 7,
                'category_image_small_file' => 8,
                'category_seo_url_prefix'   => 9
            ),
            'products' => array(
                'product_name' => 11
            ),
            'product_types' => array(
                'product_type_name'  => 21,
                'product_type_descr' => 22
            ),
            'input_type_values' => array(
                'input_type_value' => 31
            ),
            'product_attributes' => array(
                'product_attr_value' => 41
            ),
            'product_type_attributes' => array(
                'product_type_attr_default_value' => 51
            )
        ),
        'Notifications' => array(
            'notifications' => array(
                'notification_name'                      => 101,
                'notification_subject'                   => 102,
                'notification_body'                      => 103,
                'notification_from_email_custom_address' => 104
            ),
            'notification_blocktag_bodies' => array(
                'blocktag_body' => 111
            )
        ),
        'Subscriptions' => array(
            'subscription_topic' => array(
                'topic_name' => 201
            )
        ),
        'Newsletter' => array(
            'newsletter_letters' => array(
                'letter_subject'    => 301,
                'letter_from_name'  => 302,
                'letter_from_email' => 303,
                'letter_html'       => 304,
                'letter_text'       => 305
            )
        ),
        'Resources' => array(
            'resource_labels' => array(
                'res_text' => 401
            )
        ),
        'Checkout' => array(
            'person_info_variants_to_attributes' => array(
                'person_attribute_visible_name' => 501,
                'person_attribute_description'  => 502
            )
        ),
        'Customer_Account' => array(
            'ca_attrs_to_groups' => array(
                'visible_name' => 601
            )
        ),
        'CMS' => array(
            'cms_pages' => array(
                'name'         => 701,
                'descr'        => 702,
                'seo_title'    => 703,
                'seo_descr'    => 704,
                'seo_keywords' => 705,
		'seo_prefix'   => 706
            ),
            'cms_menu' => array(
                'menu_name' => 711
            ),
            'cms_menu_items' => array(
                'item_name' => 721
            )
        ),
        'Product_Options' => array(
            'po_options' => array(
                'option_name'   => 801,
                'display_name'  => 802,
                'display_descr' => 803
            ),
            'po_options_values' => array(
                'value_name' => 811
            )
        ),
        'Location' => array(
            'countries' => array(
                'country_name' => 901
            ),
            'states' => array(
                'state_name' => 911
            )
        ),
        'Product_Images' => array(
            'pi_images' => array(
                'alt_text' => 1001
            )
        ),
        'Shipping_Module_DSR' => array(
            'sm_dsr_methods' => array(
                'sm_dsr_method_name' => 1401
            )
        ),
        'Taxes' => array(
            'tax_names' => array(
                 'tax_name' => 1501
            ),
            'tax_display' => array(
                 'tax_display_view' => 1502
            )
        )
    );
}

?>