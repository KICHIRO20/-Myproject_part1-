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
 * Product Options API
 *
 * @package ProductOptions
 * @author Egor V. Derevyankin
 */

class Product_Options
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    function Product_Options()
    {
    }

    function install()
    {
        $query = new DB_Table_Create(Product_Options::getTables());

        $group_info = array('GROUP_NAME'        => 'FILE_UPLOAD_SETTINGS',
                            'GROUP_DESCRIPTION' => array(   'NAME'          => array('PO', 'FILE_UPLOAD_SETTINGS_NAME'),
                                                            'DESCRIPTION'   => array('PO', 'FILE_UPLOAD_SETTINGS_DESC')),
                            'GROUP_VISIBILITY'    => 'SHOW'); /*@ add to constants */

        modApiFunc('Settings','createGroup', $group_info);

        $param_info = array(
                         'GROUP_NAME'        => $group_info['GROUP_NAME'],
                         'PARAM_NAME'        => 'ALLOW_IMAGES',
                         'PARAM_DESCRIPTION' => array( 'NAME'        => array('PO', 'ALLOW_IMAGES_NAME'),
                                                       'DESCRIPTION' => array('PO', 'ALLOW_IMAGES_DESC') ),
                         'PARAM_TYPE'          => PARAM_TYPE_LIST,
                         'PARAM_VALUE_LIST'    => array(
                                 array(  'VALUE' => 'NO',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('PO', 'PO_ADV_CFG_NO'),
                                                                       'DESCRIPTION' => array('PO', 'PO_ADV_CFG_NO') ),
                                       ),
                                 array(  'VALUE' => 'YES',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('PO', 'PO_ADV_CFG_YES'),
                                                                       'DESCRIPTION' => array('PO', 'PO_ADV_CFG_YES') ),
                                       )),
                         'PARAM_CURRENT_VALUE' => 'YES',
                         'PARAM_DEFAULT_VALUE' => 'YES',
        );
        modApiFunc('Settings','createParam', $param_info);

        $param_info = array(
                         'GROUP_NAME'        => $group_info['GROUP_NAME'],
                         'PARAM_NAME'        => 'ALLOW_DOCUMENTS',
                         'PARAM_DESCRIPTION' => array( 'NAME'        => array('PO', 'ALLOW_DOCUMENTS_NAME'),
                                                       'DESCRIPTION' => array('PO', 'ALLOW_DOCUMENTS_DESC') ),
                         'PARAM_TYPE'          => PARAM_TYPE_LIST,
                         'PARAM_VALUE_LIST'    => array(
                                 array(  'VALUE' => 'NO',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('PO', 'PO_ADV_CFG_NO'),
                                                                       'DESCRIPTION' => array('PO', 'PO_ADV_CFG_NO') ),
                                       ),
                                 array(  'VALUE' => 'YES',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('PO', 'PO_ADV_CFG_YES'),
                                                                       'DESCRIPTION' => array('PO', 'PO_ADV_CFG_YES') ),
                                       )),
                         'PARAM_CURRENT_VALUE' => 'YES',
                         'PARAM_DEFAULT_VALUE' => 'YES',
        );
        modApiFunc('Settings','createParam', $param_info);

        $param_info = array(
                         'GROUP_NAME'        => $group_info['GROUP_NAME'],
                         'PARAM_NAME'        => 'ALLOW_ARCHIVES',
                         'PARAM_DESCRIPTION' => array( 'NAME'        => array('PO', 'ALLOW_ARCHIVES_NAME'),
                                                       'DESCRIPTION' => array('PO', 'ALLOW_ARCHIVES_DESC') ),
                         'PARAM_TYPE'          => PARAM_TYPE_LIST,
                         'PARAM_VALUE_LIST'    => array(
                                 array(  'VALUE' => 'NO',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('PO', 'PO_ADV_CFG_NO'),
                                                                       'DESCRIPTION' => array('PO', 'PO_ADV_CFG_NO') ),
                                       ),
                                 array(  'VALUE' => 'YES',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('PO', 'PO_ADV_CFG_YES'),
                                                                       'DESCRIPTION' => array('PO', 'PO_ADV_CFG_YES') ),
                                       )),
                         'PARAM_CURRENT_VALUE' => 'YES',
                         'PARAM_DEFAULT_VALUE' => 'YES',
        );
        modApiFunc('Settings','createParam', $param_info);

        $param_info = array(
                         'GROUP_NAME'        => $group_info['GROUP_NAME'],
                         'PARAM_NAME'        => 'ALLOW_VECTOR',
                         'PARAM_DESCRIPTION' => array( 'NAME'        => array('PO', 'ALLOW_VECTOR_NAME'),
                                                       'DESCRIPTION' => array('PO', 'ALLOW_VECTOR_DESC') ),
                         'PARAM_TYPE'          => PARAM_TYPE_LIST,
                         'PARAM_VALUE_LIST'    => array(
                                 array(  'VALUE' => 'NO',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('PO', 'PO_ADV_CFG_NO'),
                                                                       'DESCRIPTION' => array('PO', 'PO_ADV_CFG_NO') ),
                                       ),
                                 array(  'VALUE' => 'YES',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('PO', 'PO_ADV_CFG_YES'),
                                                                       'DESCRIPTION' => array('PO', 'PO_ADV_CFG_YES') ),
                                       )),
                         'PARAM_CURRENT_VALUE' => 'YES',
                         'PARAM_DEFAULT_VALUE' => 'YES',
        );
        modApiFunc('Settings','createParam', $param_info);

        $param_info = array(
                         'GROUP_NAME'        => $group_info['GROUP_NAME'],
                         'PARAM_NAME'        => 'ALLOW_AUDIO',
                         'PARAM_DESCRIPTION' => array( 'NAME'        => array('PO', 'ALLOW_AUDIO_NAME'),
                                                       'DESCRIPTION' => array('PO', 'ALLOW_AUDIO_DESC') ),
                         'PARAM_TYPE'          => PARAM_TYPE_LIST,
                         'PARAM_VALUE_LIST'    => array(
                                 array(  'VALUE' => 'NO',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('PO', 'PO_ADV_CFG_NO'),
                                                                       'DESCRIPTION' => array('PO', 'PO_ADV_CFG_NO') ),
                                       ),
                                 array(  'VALUE' => 'YES',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('PO', 'PO_ADV_CFG_YES'),
                                                                       'DESCRIPTION' => array('PO', 'PO_ADV_CFG_YES') ),
                                       )),
                         'PARAM_CURRENT_VALUE' => 'YES',
                         'PARAM_DEFAULT_VALUE' => 'YES',
        );
        modApiFunc('Settings','createParam', $param_info);

        $param_info = array(
                         'GROUP_NAME'        => $group_info['GROUP_NAME'],
                         'PARAM_NAME'        => 'ALLOW_VIDEO',
                         'PARAM_DESCRIPTION' => array( 'NAME'        => array('PO', 'ALLOW_VIDEO_NAME'),
                                                       'DESCRIPTION' => array('PO', 'ALLOW_VIDEO_DESC') ),
                         'PARAM_TYPE'          => PARAM_TYPE_LIST,
                         'PARAM_VALUE_LIST'    => array(
                                 array(  'VALUE' => 'NO',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('PO', 'PO_ADV_CFG_NO'),
                                                                       'DESCRIPTION' => array('PO', 'PO_ADV_CFG_NO') ),
                                       ),
                                 array(  'VALUE' => 'YES',
                                         'VALUE_DESCRIPTION' => array( 'NAME'        => array('PO', 'PO_ADV_CFG_YES'),
                                                                       'DESCRIPTION' => array('PO', 'PO_ADV_CFG_YES') ),
                                       )),
                         'PARAM_CURRENT_VALUE' => 'YES',
                         'PARAM_DEFAULT_VALUE' => 'YES',
        );
        modApiFunc('Settings','createParam', $param_info);
    }

    function uninstall()
    {
        $query = new DB_Table_Delete(Product_Options::getTables());
    }

    function getTables()
    {
        static $tables;

        if (is_array($tables))
        {
            return $tables;
        }

        $tables=array();

        $table='po_settings';
        $tables[$table]=array();
        $tables[$table]['columns']=array(
            'setting_id'    => $table.'.setting_id'
           ,'parent_entity' => $table.'.parent_entity'
           ,'entity_id'     => $table.'.entity_id'
           ,'setting_key'   => $table.'.setting_key'
           ,'setting_value' => $table.'.setting_value'
        );
        $tables[$table]['types']=array(
            'setting_id'    => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
           ,'parent_entity' => 'ENUM(\''.implode("','",Product_Options::_getParentEntities()).'\')'
           ,'entity_id'     => DBQUERY_FIELD_TYPE_INT .' NOT NULL DEFAULT 0'
           ,'setting_key'   => DBQUERY_FIELD_TYPE_CHAR255.' NOT NULL DEFAULT \'\''
           ,'setting_value' => DBQUERY_FIELD_TYPE_LONGTEXT.' NOT NULL '
        );
        $tables[$table]['primary']=array(
            'setting_id'
        );
        $tables[$table]['indexes']=array(
            'UNIQUE KEY IDX_ent_eid_key'   => 'parent_entity,entity_id,setting_key'
        );

        $table='po_options';
        $tables[$table]=array();
        $tables[$table]['columns']=array(
            'option_id'     => $table.'.option_id'
           ,'parent_entity' => $table.'.parent_entity'
           ,'entity_id'     => $table.'.entity_id'
           ,'option_name'   => $table.'.option_name'
           ,'display_name'  => $table.'.display_name'
           ,'display_descr' => $table.'.display_descr'
           ,'option_type'   => $table.'.option_type'
           ,'show_type'     => $table.'.show_type'
           ,'discard_avail' => $table.'.discard_avail'
           ,'discard_value' => $table.'.discard_value'
           ,'checkbox_text' => $table.'.checkbox_text'
           ,'use_for_it'    => $table.'.use_for_it'
           ,'sort_order'    => $table.'.sort_order'
        );
        $tables[$table]['types']=array(
            'option_id'     => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
           ,'parent_entity' => 'ENUM(\''.implode("','",Product_Options::_getParentEntities()).'\')'
           ,'entity_id'     => DBQUERY_FIELD_TYPE_INT .' NOT NULL DEFAULT 0'
           ,'option_name'   => DBQUERY_FIELD_TYPE_CHAR255.' NOT NULL DEFAULT \'\''
           ,'display_name'  => DBQUERY_FIELD_TYPE_CHAR255.' NOT NULL DEFAULT \'\''
           ,'display_descr' => DBQUERY_FIELD_TYPE_TEXT
           ,'option_type'   => 'ENUM(\''.implode("','",Product_Options::__getInnerVar("_OPTION_TYPES")).'\')'
           ,'show_type'     => 'ENUM(\''.implode("','",Product_Options::__getInnerVar("_SHOW_TYPES")).'\')'
           ,'discard_avail' => 'ENUM(\'Y\',\'N\') DEFAULT \'N\''
           ,'discard_value' => DBQUERY_FIELD_TYPE_CHAR255.' NOT NULL DEFAULT \'\''
           ,'checkbox_text' => DBQUERY_FIELD_TYPE_CHAR255.' NOT NULL DEFAULT \'\''
           ,'use_for_it'    => 'ENUM(\'Y\',\'N\') DEFAULT \'N\''
           ,'sort_order'    => DBQUERY_FIELD_TYPE_INT .' NOT NULL DEFAULT 0'
        );
        $tables[$table]['primary']=array(
            'option_id'
        );
        $tables[$table]['indexes']=array(
            'IDX_ent_eid'       => 'parent_entity,entity_id'
        );

        $table='po_options_values';
        $tables[$table]=array();
        $tables[$table]['columns']=array(
            'value_id'                  => $table.'.value_id'
           ,'option_id'                 => $table.'.option_id'
           ,'value_name'                => $table.'.value_name'
           ,'is_default'                => $table.'.is_default'
           ,'sort_order'                => $table.'.sort_order'
        );
        $tables[$table]['types']=array(
            'value_id'                  => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
           ,'option_id'                 => DBQUERY_FIELD_TYPE_INT .' NOT NULL DEFAULT 0'
           ,'value_name'                => DBQUERY_FIELD_TYPE_CHAR255.' NOT NULL DEFAULT \'\''
           ,'is_default'                => 'ENUM(\'Y\',\'N\')'
           ,'sort_order'                => DBQUERY_FIELD_TYPE_INT .' NOT NULL DEFAULT 0'
        );
        foreach(Product_Options::__getInnerVar("_MODIFIERS") as $modifier)
        {
            $tables[$table]['columns'][$modifier.'_modifier']=$table.'.'.$modifier.'_modifier';
            $tables[$table]['types'][$modifier.'_modifier']=DBQUERY_FIELD_TYPE_DECIMAL20_5.' NOT NULL DEFAULT 0';
        }
        $tables[$table]['primary']=array(
            'value_id'
        );
        $tables[$table]['indexes']=array(
            'IDX_oid'       => 'option_id'
        );

        $table = 'po_inventory';
        $tables[$table]=array();
        $tables[$table]['columns']=array(
            'it_id'             => $table.'.it_id'
           ,'parent_entity'     => $table.'.parent_entity'
           ,'entity_id'         => $table.'.entity_id'
           ,'combination'       => $table.'.combination'
           ,'formula'           => $table.'.formula'
           ,'sku'               => $table.'.sku'
           ,'quantity'          => $table.'.quantity'
           ,'sort_order'        => $table.'.sort_order'
        );
        $tables[$table]['types']=array(
            'it_id'             => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
           ,'parent_entity'     => 'ENUM(\''.implode("','",Product_Options::_getParentEntities()).'\')'
           ,'entity_id'         => DBQUERY_FIELD_TYPE_INT .' NOT NULL DEFAULT 0'
           ,'combination'       => DBQUERY_FIELD_TYPE_LONGTEXT .' NOT NULL '
           ,'formula'           => DBQUERY_FIELD_TYPE_LONGTEXT .' NOT NULL '
           ,'sku'               => DBQUERY_FIELD_TYPE_CHAR255.' NOT NULL DEFAULT \'\''
           ,'quantity'          => DBQUERY_FIELD_TYPE_INT .' NOT NULL DEFAULT 0'
           ,'sort_order'        => DBQUERY_FIELD_TYPE_INT .' NOT NULL DEFAULT 0'
        );
        $tables[$table]['primary']=array(
            'it_id'
        );
        $tables[$table]['indexes']=array(
            'IDX_ent_eid'       => 'parent_entity,entity_id'
        );

        $table = 'po_crules';
        $tables[$table]=array();
        $tables[$table]['columns']=array(
            'crule_id'          => $table.'.crule_id'
           ,'parent_entity'     => $table.'.parent_entity'
           ,'entity_id'         => $table.'.entity_id'
           ,'tpl_index'         => $table.'.tpl_index'
           ,'sside'             => $table.'.sside'
           ,'lside'             => $table.'.lside'
           ,'rside'             => $table.'.rside'
           ,'crule_formula'     => $table.'.crule_formula'
           ,'sort_order'        => $table.'.sort_order'
        );
        $tables[$table]['types']=array(
            'crule_id'             => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
           ,'parent_entity'     => 'ENUM(\''.implode("','",Product_Options::_getParentEntities()).'\')'
           ,'entity_id'         => DBQUERY_FIELD_TYPE_INT .' NOT NULL DEFAULT 0'
           ,'tpl_index'         => DBQUERY_FIELD_TYPE_INT .' NOT NULL DEFAULT 0'
           ,'sside'             => DBQUERY_FIELD_TYPE_TEXT .' NOT NULL '
           ,'lside'             => DBQUERY_FIELD_TYPE_TEXT .' NOT NULL '
           ,'rside'             => DBQUERY_FIELD_TYPE_TEXT .' NOT NULL '
           ,'crule_formula'     => DBQUERY_FIELD_TYPE_LONGTEXT .' NOT NULL '
           ,'sort_order'        => DBQUERY_FIELD_TYPE_INT .' NOT NULL DEFAULT 0'
        );
        $tables[$table]['primary']=array(
            'crule_id'
        );
        $tables[$table]['indexes']=array(
            'IDX_ent_eid'       => 'parent_entity,entity_id'
        );

        global $application;
        return $application->addTablePrefix($tables);
    }


    /**
     * Checks data if it is correct to perform some action.
     *
     * @descr $inner_action - the name of the function, data is checked for.
     * The list of available data you can get this way:
     * Product_Options::__getInnerVar('_INNER_ACTIONS'),
     * but this list must be of no interest for anybody (that's why the method
     * __getInnerVar is private)
     * @descr  $data - data array, the format is different for each action,
     * for generation examples see actions of this module.
     *
     * @param string $inner_action - the name of action, which is performed after
     * data checking
     * @param array $data - data to check
     * @return error array, if it is empty, then data is correct
     */
    function checkDataFor($inner_action,$data)
    {
        $return=array();
        if(!in_array($inner_action,$this->__getInnerVar("_INNER_ACTIONS")))
        {
            $return[]="E_INVALID_INNER_ACTION";
            return $return;
        };
        $inner_method="__checkDataFor_".$inner_action;
        $return=array_merge($return,$this->$inner_method($data));

        return $return;
    }

    /**
     * Adds an option to the entity.
     *
     * @param $data = array(
     *  'parent_entity' => enum('product','ptype',...)
     * ,'entity_id' => int  - entity ID
     * ,'option_name' => string  - option name
     * ,'display_name' => string  - the name to be displayed in the storefront
     * ,'option_type' => enum('SS','MS','CI')  - option type
     * ,'show_type' => enum('DD','RG','MS','CBG','SI','TA','CBSI','CBTA')  -  storefront
     * ,'discard_avail' => enum('Y','N')  - optionally is used for option_type=SS
     * ,'discard_value' => string  - optionally is used for option_type=SS
     * ,'checkbox_text' => string  - optionally is used for option_type=CI
     * );
     *
     * for generation examples see action add_option_to_entity.
     *
     * WARNING: the call of this function with the data that hasn't been checked
     * by the function checkDataFor() can result in the fatal error in the
     * class DB_MySQL.
     *
     * @param array $data - data to add ,checked by the function checkDataFor()
     * @return new option ID if it is added, FALSE if an error occurred
     * while adding
     */
    function addOptionToEntity($data, $ml_all_langs = false)
    {
        global $application;
        $tables=$this->getTables();
        $options=$tables['po_options']['columns'];

        $max_sort_order=$this->__getMaxOptionsSortOrder($data['parent_entity'],$data['entity_id']);
        $data['sort_order']=$max_sort_order+1;

        $query = new DB_Insert('po_options');
        foreach($data as $key => $value)
            if (in_array($key, array('option_name', 'display_name', 'display_descr')))
                $query->addMultiLangInsertValue($value, $options[$key], $options['option_id'], 'Product_Options');
            elseif ($key != '_ml')
                $query->addInsertValue($value, $options[$key]);
        $application->db->getDB_Result($query);
        $res = $application->db->QueryResult;
        if($res==false)
            return false;
        else
            $option_id = $application->db->DB_Insert_Id();

        if (isset($data['_ml']))
            modApiFunc('MultiLang', 'addMLTableData',
                       'Product_Options', 'po_options', $option_id,
                       $data['_ml']);

        if($data['option_type']=='CI' and @func_get_arg(1)!='_copy_options')
        {
            $val_data=array(
                'option_id' => $option_id
               ,'value_name' => getMsg('PO', 'PO_CI_OPTION_NAME')
               ,'is_default' => 'N'
            );
            $this->addValueToOption($val_data);
        };

        if($data['option_type']=='UF' and @func_get_arg(1)!='_copy_options')
        {
            $val_data=array(
                'option_id' => $option_id
               ,'value_name' => getMsg('PO', 'PO_UF_OPTION_NAME')
               ,'is_default' => 'N'
            );
            $this->addValueToOption($val_data);
        };

        return $option_id;
    }


    /**
     * Deletes the option of the entity.
     *
     * On the level of checking data the array $data has the following structure:
     * array(
     *  'parent_entity' => enum('product','ptype',...)
     * ,'enity_id' => int  - the ID of the entity that has option to delete
     * ,'option_id' => int  - ID of the option to delete
     * );
     * if it is checked successfully the parameter entity_id is not used any more,
     * that's why it is not used in the function's body.
     *
     * @descr $data = array(
     *  'option_id' => int  - ID of the option to delete
     * );
     *
     * It can be added also any other parameters to this array, they will be skipped.
     *
     * @param array $data - data to delete, that is checked by the function checkDataFor()
     * @return TRUE - if data is deleted, FALSE otherwise
     */
    function delOptionFromEntity($data)
    {
        global $application;
        $tables=$this->getTables();
        $options=$tables['po_options']['columns'];
        $values=$tables['po_options_values']['columns'];

        $old_oinf = $this->getOptionInfo($data['option_id']);

        $query = new DB_Delete('po_options');
        $query->deleteMultiLangField($options['option_name'], $options['option_id'], 'Product_Options');
        $query->deleteMultiLangField($options['display_name'], $options['option_id'], 'Product_Options');
        $query->deleteMultiLangField($options['display_descr'], $options['option_id'], 'Product_Options');
        $query->WhereValue($options['option_id'], DB_EQ, $data['option_id']);
        $application->db->getDB_Result($query);
        if(!$application->db->QueryResult)
            return false;

        if($this->__delAllValuesFromOption($data['option_id']))
        {
            $this->__delCRulesBy('option_id',$data['option_id']);
            if($old_oinf['use_for_it']=='Y')
                $this->__clearITForEntity($old_oinf['parent_entity'],$old_oinf['entity_id']);
            return true;
        }
        else
            return false;
    }


    /**
     * Adds a value of the option.
     *
     * @descr $data = array(
     *  'option_id' => int  - ID of the option the value should be added to
     * ,'value_name' => string  - value name
     * ,'is_default' => enum('Y','N')  - if the added value is default for the option
     * ,'price_modifier' => float  - price modifier
     * ,'weight_modifier' => float  - weight modifier
     * ,'shipping_cost_modifier' => float  - shipping cost modifier
     * );
     *
     * for generation examples see action add_value_to_option.
     *
     * WARNING: the call of this function with the data that hasn't been checked
     * by the function checkDataFor() can result in the fatal error in the
     * class DB_MySQL.
     *
     * @param array $data - data to add ,checked by the function checkDataFor()
     * @return new value ID if it is added, FALSE if an error occurred
     * while adding
     */
    function addValueToOption($data)
    {
        global $application;
        $tables=$this->getTables();
        $values=$tables['po_options_values']['columns'];
        $options=$tables['po_options']['columns'];

        $max_sort_order=$this->__getMaxValuesSortOrder($data['option_id']);
        $data['sort_order']=$max_sort_order+1;

        $query = new DB_Insert('po_options_values');
        foreach($data as $key => $value)
            if ($key == 'value_name')
                $query->addMultiLangInsertValue($value, $values[$key], $values['value_id'], 'Product_Options');
            elseif ($key != '_ml')
                $query->addInsertValue($value, $values[$key]);
        $application->db->getDB_Result($query);
        $res=$application->db->QueryResult;
        if($res==false)
            return false;

        $vid=$application->db->DB_Insert_Id();

        if (isset($data['_ml']))
            modApiFunc('MultiLang', 'addMLTableData',
                       'Product_Options', 'po_options_values', $vid,
                       $data['_ml']);

        $query = new DB_Select();
        $query->addSelectTable('po_options');
        $query->addSelectField($options["option_type"], 'option_type');
        $query->WhereValue($options["option_id"], DB_EQ, $data["option_id"]);
        $res=$application->db->getDB_Result($query);
        $otype=$res[0]["option_type"];
        if($otype=="SS")
        {
            $this->__chooseAndSetDefaultValueForOption($data["option_id"],($data["is_default"]=='Y'?$vid:0));
        };

        return $vid;
    }

    /**
     * Deletes values of the option.
     *
     * @descr $data = array(
     *  'option_id' => int  - ID of the option that has values to delete
     * ,'values_ids' => array of int  - IDs of the values to delete
     * );
     *
     * @param array $data - data,checked by the function checkDataFor()
     * @return TRUE - if data is deleted, FALSE otherwise
     */
    function delValuesFromOption($data)
    {
        global $application;
        $tables=$this->getTables();
        $options=$tables['po_options']['columns'];
        $values=$tables['po_options_values']['columns'];

        $query = new DB_Delete('po_options_values');
        $query->deleteMultiLangField($values['value_name'], $values['value_id'], 'Product_Options');
        $query->WhereValue($values['option_id'], DB_EQ, $data['option_id']);
        $query->WhereAND();
        $query->Where($values['value_id'], DB_IN, '(\''.implode('\',\'',$data['values_ids']).'\')');
        $application->db->getDB_Result($query);
        if(!$application->db->QueryResult)
            return false;

        foreach($data['values_ids'] as $vid)
        {
            $this->__delCRulesBy('value_id',$vid);
        };

        $oinf = $this->getOptionInfo($data['option_id']);

        if($oinf['option_type']=="SS")
        {
            $this->__chooseAndSetDefaultValueForOption($data['option_id']);
        };

        if($oinf['use_for_it']=="Y")
            //$this->updateOptionsSettingsForEntity($oinf['parent_entity'],$oinf['entity_id'],array('IT_ACTUAL'=>'N'));
            foreach($data['values_ids'] as $vid)
                $this->__delInventoryBy('value_id',$vid);

        return true;
    }


    /**
     * Updates the option of the entity.
     *
     * For the $data array format see the comment of the function addOptionToEntity.
     *
     * @param array $data - data to update, checked by the function checkDataFor()
     * @return TRUE - if it is updated, FALSE if errors occurred while updating
     */
    function updateOptionOfEntity($data)
    {
        global $application;
        $tables=$this->getTables();
        $options=$tables['po_options']['columns'];

        list($pent, $pid, $oid) = array($data["parent_entity"], $data["entity_id"], $data["option_id"]);
        unset($data["parent_entity"], $data["entity_id"], $data["option_id"]);

        $oinf = $this->getOptionInfo($oid);

        $query = new DB_Update('po_options');
        foreach($data as $key => $data_value)
            if (in_array($key, array('option_name', 'display_name', 'display_descr')))
                $query->addMultiLangUpdateValue($options[$key], $data_value, $options['option_id'], $oid, 'Product_Options');
            else
                $query->addUpdateValue($options[$key], $data_value);
        $query->WhereValue($options['option_id'], DB_EQ, $oid);
        $application->db->getDB_Result($query);
        if($application->db->QueryResult)
        {
            if($data["option_type"]=="CI" or ($oinf["use_for_it"]!=$data["use_for_it"] and $data["use_for_it"]=="N"))
                //$this->updateOptionsSettingsForEntity($oinf['parent_entity'],$oinf['entity_id'],array('IT_ACTUAL'=>'N'));
                $this->__clearITForEntity($oinf['parent_entity'],$oinf['entity_id']);

            if($data['option_type']=="SS")
            {
                return $this->__chooseAndSetDefaultValueForOption($oid);
            }
            elseif($data['option_type']=="CI" || $data['option_type']=="UF")
            {
                $option_values=$this->getValuesList($oid);
                if(count($option_values)>0)
                {
                    $first_vid=$option_values[0]['value_id'];
                    $values_table=$tables['po_options_values']['columns'];

                    $query = new DB_Delete('po_options_values');
                    $query->deleteMultiLangField($values_table['value_name'], $values_table['value_id'], 'Product_Options');
                    $query->WhereValue($values_table['option_id'], DB_EQ, $oid);
                    $query->WhereAND();
                    $query->WhereValue($values_table['value_id'], DB_NEQ, $first_vid);
                    $application->db->getDB_Result($query);
                }
                else
                {
                    $val_data=array(
                        'option_id' => $oid
                       ,'value_name' => (($data['option_type']=="CI") ? getMsg('PO', 'PO_CI_OPTION_NAME') : getMsg('PO', 'PO_UF_OPTION_NAME'))
                       ,'is_default' => 'N'
                    );
                    $this->addValueToOption($val_data);
                }
            };
            return true;
        }
        else
            return false;
    }

    /**
     * Updates the values of the option.
     *
     * For the $data array format see the comment to the function addValueToOption,
     * for all that the parameter option_id shouldn't exist in this array.
     *
     * @param int $value_id - value ID
     * @param array $data - data to update, checked by the function checkDataFor()
     * @return TRUE - if it is updated, FALSE otherwise
     */
    function updateValueOfOption($value_id,$data)
    {
        global $application;
        $tables=$this->getTables();
        $values=$tables['po_options_values']['columns'];

        $query = new DB_Update('po_options_values');
        foreach($data as $key => $data_value)
            if ($key == 'value_name')
                $query->addMultiLangUpdateValue($values[$key], $data_value, $values['value_id'], $value_id, 'Product_Options');
            else
                $query->addUpdateValue($values[$key], $data_value);
        $query->WhereValue($values['value_id'], DB_EQ, $value_id);
        $application->db->getDB_Result($query);
        return $application->db->QueryResult;
    }


    /**
     * Updates values of the option.
     *
     * It is different in the aim: the previous method updates data of one value,
     * this method updates data of several values.
     * They are devided into two methods for easy usage.
     * One method instead of these two will result in creating cycles to update
     * several values of the option.
     *
     * @descr $data = array(
     *  'values' => array (
     *              '$value_id_01' => array ( see the comment to the previous function )
     *             ,'$value_id_02' => array ( see the comment to the previous function )
     *              ......
     *             ,'$value_id_NN' => array ( see the comment to the previous function )
     *          )
     * );
     * On the level of checking datathe array contains the parameter
     * 'option_id' = option ID, values of which is updated.
     *
     * @param array $data - data to update, checked by the function checkDataFor()
     * @return array with the update result for every updated value
     */
    function updateValuesOfOption($data)
    {
        $return=array();
        foreach($data["values"] as $vid => $vdata)
        {
            $return[$vid]=$this->updateValueOfOption($vid,$vdata);
        }
        return $return;
    }


    //: return errors ??
    //: dump with the next function
    function updateOptionsSortOrder($sort_array)
    {
        global $application;
        $tables=$this->getTables();
        $options=$tables['po_options']['columns'];

        foreach($sort_array as $key => $val)
        {
            $query = new DB_Update('po_options');
            $query->addUpdateValue($options['sort_order'],$key);
            $query->WhereValue($options['option_id'], DB_EQ, $val);
            $application->db->PrepareSQL($query);
            $application->db->DB_Exec();
        };

        return true;
    }


    //: return errors ??
    //: dump with the next function
    function updateValuesSortOrder($sort_array)
    {
        global $application;
        $tables=$this->getTables();
        $values=$tables['po_options_values']['columns'];

        foreach($sort_array as $key => $val)
        {
            $query = new DB_Update('po_options_values');
            $query->addUpdateValue($values['sort_order'],$key);
            $query->WhereValue($values['value_id'], DB_EQ, $val);
            $application->db->PrepareSQL($query);
            $application->db->DB_Exec();
        };

        return true;
    }


    /**
     * Deletes all options of the entities.
     *
     * @param string $parent_entity - entity name
     * @param array $entities_ids - array of entity IDs, all options of which should be deleted
     */
    function delAllOptionsFromEntities($parent_entity, $entities_ids)
    {
        if(empty($entities_ids))
            return true;

        global $application;
        $tables=$this->getTables();
        $options=$tables['po_options']['columns'];
        $values=$tables['po_options_values']['columns'];

        $query = new DB_Select();
        $query->addSelectTable('po_options');
        $query->addSelectField($options['option_id'], 'option_id');
        $query->WhereValue($options['parent_entity'], DB_EQ, $parent_entity);
        $query->WhereAND();
        $query->Where($options['entity_id'], DB_IN, "('".implode("','",$entities_ids)."')");
        $res=$application->db->getDB_Result($query);

        if(empty($res))
            return true;

        $options_ids=array();
        foreach($res as $k => $option)
            $options_ids[]=$option['option_id'];

        $query = new DB_Delete('po_options');
        $query->deleteMultiLangField($options['option_name'], $options['option_id'], 'Product_Options');
        $query->deleteMultiLangField($options['display_name'], $options['option_id'], 'Product_Options');
        $query->deleteMultiLangField($options['display_descr'], $options['option_id'], 'Product_Options');
        $query->Where($options['option_id'], DB_IN, "('".implode("','",$options_ids)."')");
        $application->db->getDB_Result($query);

        $query = new DB_Delete('po_options_values');
        $query->deleteMultiLangField($values['value_name'], $values['value_id'], 'Product_Options');
        $query->Where($values['option_id'], DB_IN, "('".implode("','",$options_ids)."')");
        $application->db->getDB_Result($query);

        return true;
    }


    /**
     * Deletes all Inventories of the entities.
     *
     * @param string $parent_entity - entity name
     * @param array $entities_ids - the array of entity IDs, all Inventories
     * of which should be deleted
     */
    function delAllInventoryFromEntities($parent_entity,$entities_ids)
    {
        if(empty($entities_ids))
            return true;

        global $application;
        $tables=$this->getTables();
        $exs_table=$tables['po_inventory']['columns'];

        $query = new DB_Delete('po_inventory');
        $query->WhereValue($exs_table['parent_entity'], DB_EQ, $parent_entity);
        $query->WhereAND();
        $query->Where($exs_table['entity_id'], DB_IN, "('".implode("','",$entities_ids)."')");
        $application->db->PrepareSQL($query);
        $application->db->DB_Exec();

        return true;
    }

    function delAllCRulesFromEntities($parent_entity,$entities_ids)
    {
        if(empty($entities_ids))
            return true;

        global $application;
        $tables=$this->getTables();
        $exs_table=$tables['po_crules']['columns'];

        $query = new DB_Delete('po_crules');
        $query->WhereValue($exs_table['parent_entity'], DB_EQ, $parent_entity);
        $query->WhereAND();
        $query->Where($exs_table['entity_id'], DB_IN, "('".implode("','",$entities_ids)."')");
        $application->db->PrepareSQL($query);
        $application->db->DB_Exec();

       return true;
    }


    /**
     * Deletes all settings of the entities.
     *
     * @param string $parent_entity - entity name
     * @param array $entities_ids - the array of entity IDs, all module setting of which
     * should be deleted.
     */
    function delAllOptionsSettingsFromEntities($parent_entity,$entities_ids)
    {
        if(empty($entities_ids))
            return true;

        global $application;
        $tables=$this->getTables();
        $sets_table=$tables['po_settings']['columns'];

       $query = new DB_Delete('po_settings');
       $query->WhereValue($sets_table['parent_entity'], DB_EQ, $parent_entity);
       $query->WhereAND();
       $query->Where($sets_table['entity_id'], DB_IN, "('".implode("','",$entities_ids)."')");
       $application->db->PrepareSQL($query);
       $application->db->DB_Exec();

       return true;
    }


    /**
     * Copies all options form one entity to another.
     *
     * @param string $from_parent_entity - source entity name
     * @param int $from_eid - source entity ID
     * @param string $to_parent_entity - destination entity name
     * @param int $to_eid - destination entity ID
     * @return transfer map of the option IDs and value IDs
     */
    function copyAllOptionsFromEntityToEntity($from_parent_entity,$from_eid,$to_parent_entity,$to_eid)
    {
        if(!$this->__hasEntityPrivilegesFor($from_parent_entity,'options') or !$this->__hasEntityPrivilegesFor($to_parent_entity,'options'))
            return true;

        $transfer_map = array('options'=>array(),'values'=>array());
        $options=$this->getOptionsWithValues($from_parent_entity,$from_eid,0,true);
        if(!empty($options))
        {
            foreach($options as $okey => $option_data)
            {
                $data=$option_data;
                $data['parent_entity']=$to_parent_entity;
                $data['entity_id']=$to_eid;
                unset($data['option_id'], $data['values']);

                $new_oid=$this->addOptionToEntity($data,'_copy_options');

                // add new element to transfer map
                $transfer_map['options'][$option_data['option_id']]=$new_oid;

                if($new_oid!=false)
                {
                    if(!empty($option_data['values']))
                    {
                        foreach($option_data['values'] as $vkey => $value_data)
                        {
                            $data=$value_data;
                            $data['option_id']=$new_oid;
                            unset($data['value_id']);

                            $new_vid = $this->addValueToOption($data);

                            // add new element to transfer map
                            $transfer_map['values'][$value_data['value_id']]=$new_vid;
                        }
                    }
                }
            }
        }
        return $transfer_map;
    }

    function copyAllCRulesFromEntityToEntity($from_parent_entity,$from_eid,$to_parent_entity,$to_eid,$transfer_map)
    {
        if(!$this->__hasEntityPrivilegesFor($from_parent_entity,'crules') or !$this->__hasEntityPrivilegesFor($to_parent_entity,'crules'))
            return true;

        if(empty($transfer_map['options']))
            return true;

        global $application;
        $tables = $this->getTables();
        $crules_table = $tables['po_crules']['columns'];

        $str_replacements = $this->__convTMtoSR($transfer_map);

        $crules = $this->getCRulesForEntity($from_parent_entity,$from_eid);

        for($i=0;$i<count($crules);$i++)
        {
            $query = new DB_Insert('po_crules');
            $query->addInsertValue($to_parent_entity,'parent_entity');
            $query->addInsertValue($to_eid,'entity_id');
            $query->addInsertValue($crules[$i]['tpl_index'],'tpl_index');
            $query->addInsertValue(str_replace(array_keys($str_replacements),array_values($str_replacements),$crules[$i]['sside']),'sside');
            $query->addInsertValue(str_replace(array_keys($str_replacements),array_values($str_replacements),$crules[$i]['lside']),'lside');
            $query->addInsertValue(str_replace(array_keys($str_replacements),array_values($str_replacements),$crules[$i]['rside']),'rside');
            $query->addInsertValue(str_replace(array_keys($str_replacements),array_values($str_replacements),$crules[$i]['crule_formula']),'crule_formula');
            $query->addInsertValue($crules[$i]['sort_order'],'sort_order');
            $application->db->PrepareSQL($query);
            $application->db->DB_Exec();
        };

        $this->__rebuildCRulesFormulaForEntity($to_parent_entity,$to_eid);

        return true;
    }


    /**
     * Copies all module settings form one entity to another.
     *
     * @param string $from_parent_entity - source entity name
     * @param int $from_eid - source entity ID
     * @param string $to_parent_entity - destination entity name
     * @param int $to_eid - destination entity ID
     * @return bool
     */
    function copyAllOptionsSettingsFromEntityToEntity($from_parent_entity,$from_eid,$to_parent_entity,$to_eid)
    {
        $sets = $this->getOptionsSettingsForEntity($from_parent_entity,$from_eid);
        if(!empty($sets))
        {
            return $this->updateOptionsSettingsForEntity($to_parent_entity,$to_eid,$sets);
        };
        return true;
    }


    /**
     * Copies all Inventories form one entity to another.
     *
     * @param string $from_parent_entity - source entity name
     * @param int $from_eid - source entity ID
     * @param string $to_parent_entity - destination entity name
     * @param int $to_eid - destination entity ID
     * @param array $transfer_map - transfer map of the option IDs and value IDs
     *                       copyAllOptionsFromEntityToEntity
     */
    function copyAllInventoryFromEntityToEntity($from_parent_entity,$from_eid,$to_parent_entity,$to_eid,$transfer_map)
    {
        if(!$this->__hasEntityPrivilegesFor($from_parent_entity,'inventory') or !$this->__hasEntityPrivilegesFor($to_parent_entity,'inventory'))
            return true;

        if(empty($transfer_map['options']))
            return true;

        $sets = $this->getOptionsSettingsForEntity($from_parent_entity,$from_eid);
/*        if($sets['IT_ACTUAL']!='Y')
            return true;*/

        global $application;
        $tables=$this->getTables();
        $it_table=$tables['po_inventory']['columns'];

        $str_replacements = $this->__convTMtoSR($transfer_map);

        $query = new DB_Select();
        $query->addSelectTable('po_inventory');
        $query->addSelectField('*');
        $query->WhereValue($it_table['parent_entity'], DB_EQ, $from_parent_entity);
        $query->WhereAND();
        $query->WhereValue($it_table['entity_id'], DB_EQ, $from_eid);

        $res = $application->db->getDB_Result($query);

        $steps_count = ceil(count($res)/INV_RECORDS_PER_QUERY);

        if(!empty($res))
        {
            $prefix=$application->getAppIni("DB_TABLE_PREFIX");

            $counter=0;
            reset($res);
            for($i=0;$i<$steps_count;$i++)
            {
                $added = 0;
                $direct_query = "insert into ".$prefix."po_inventory (parent_entity, entity_id, combination, formula, sku, quantity, sort_order) values ";
                do
                {
                    $inv_inf=current($res);
                    $direct_query.="('".$to_parent_entity."'".
                        ",'".$to_eid."'".
                        ",'".str_replace(array_keys($str_replacements),array_values($str_replacements),$inv_inf['combination'])."'".
                        ",'".str_replace(array_keys($str_replacements),array_values($str_replacements),$inv_inf['formula'])."'".
                        ",'".$inv_inf['sku']."'".
                        ",'".$inv_inf['quantity']."'".
                        ",'".$inv_inf['sort_order']."'), ";
                }
                while(next($res) and ((++$added) < INV_RECORDS_PER_QUERY));

                $direct_query=_ml_substr($direct_query,0,-2);
                $application->db->DB_Query($direct_query);
            };
//            $this->updateOptionsSettingsForEntity($to_parent_entity,$to_eid,array('IT_ACTUAL'=>'Y'));
        };

        return true;
    }


    /**
     * Checks combinations of option values. It is used in the CZ.
     *
     * @param $data = array(
     *  'parent_entity' => enum('product','ptype',...)
     * ,'entity_id' => int  - ID of the entity to which this combination of option values refers
     * ,'options' => array, describing the combination (for more details see wiki)
     * );
     * @return error array, if it is empty, no errors exist.
     */
    function checkCombination($data)
    {
        $options_array=$this->__getOptionsWithValuesAsIDsArray($data['parent_entity'],$data['entity_id']);
        $combination=$data['options'];
        $return=array();

        if(empty($options_array) and empty($combination))
            return array($return,$data);

        foreach($options_array as $k => $option)
        {
            if($option['type']=='SS' and !empty($option['values']))
            {
                if(!in_array($k,array_keys($combination)))
                    $return["not_set"][]=$k;
                elseif($combination[$k]==0 and $option['discard']!='Y')
                    $return["invalid_discard"][]=$k;
                elseif(!in_array($combination[$k],$option['values']) and $combination[$k]!=0)
                    $return["invalid_val"][]=$k;
            }
            elseif($option['type']=='MS')
            {
                if(isset($combination[$k]) and !is_array($combination[$k]))
                    $return["invalid_val_type"][]=$k;
                elseif(!empty($combination[$k]))
                {
                    $selected_items=array_keys($combination[$k]);
                    for($j=0;$j<count($selected_items);$j++)
                    {
                        if(!in_array($selected_items[$j],$option['values']))
                            $return["invalid_val"][]="$k-$j";
                    }
                }
            }
            elseif($option['type']=='CI')
            {
                if(!isset($combination[$k]['val']))
                    $return["not_set"][]=$k;
                else
                {
                    if(preg_match("/^CB/",$option['show']))
                    {
                        if(!isset($combination[$k]['cb']))
                            $combination[$k]['cb']='';
                        if($combination[$k]['cb']=='on' and $combination[$k]['val']=="")
                            $return["invalid_val"][]=$k;
                    }
                    else
                    {
                        if($combination[$k]['val']=="")
                            $return["invalid_val"][]=$k;
                    }
                }
            }
            elseif($option['type']=='UF')
            {
                if ((isset($data['options'][$k]) && file_exists($data['options'][$k]['val'])))
                {
                    $return=array();
                }
                elseif((!isset($_FILES['po']['name'][$k]) && (isset($_FILES['po']))) && $option['discard']!='Y')
                {
                	$return["not_set"][]=$k;
                }
                elseif((!isset($_FILES['po']['name'][$k]) || $_FILES['po']['error'][$k] == UPLOAD_ERR_NO_FILE) && $option['discard']=='Y')
                {
                	$data['options'][$k] = array(
                        'is_file' => true
                       ,'val' => ''
                    );
                }
                elseif((isset($_FILES['po'])) && $_FILES['po']['error'][$k]!=UPLOAD_ERR_OK)
                {
                	$return["not_set"][]=$k;
                }
                else
                {
                	$regular_path = $this->__moveUploadedFileToUploadsDir($k);

                    if($regular_path != null || ((file_exists($_FILES['po']['tmp_name'][$k]) == false) && (isset($data['options'][$k]) && file_exists($data['options'][$k]['val']))))
                    {
                    	$data['options'][$k] = array(
                            'is_file' => true
                           ,'val' => $regular_path
                        );
                    }
                    else
                    {
                    	$return["not_set"][]=$k;
                    }
                }
            }
        }
        return array($return,$data);
    }


    /**
     * Gets the module settings for the entity.
     *
     * @param string $parent_entity - entity name
     * @param int $entity_id - ID of the entity the module settings should be gotten for
     * @return array with the elements of the type 'setting_key' => 'setting_value'
     */
    function getOptionsSettingsForEntity($parent_entity,$entity_id)
    {
        $res = execQuery('SELECT_OPTIONS_SETTINGS_FOR_ENTITY', array(
            'parent_entity' => $parent_entity,
            'entity_id' => $entity_id,
        ));

        $return = array();
        foreach ($res as $row)
        {
            $return[$row['setting_key']] = $row['setting_value'];
        }
        $return=array_merge($this->_getDefaultSettings(),$return);

        return $return;
    }


    /**
     * Updates settings of this module for the entity.
     *
     * @param string $parent_entity - entity name
     * @param int $entity_id - ID of the entity, the settings of which should be updated
     * @param array $settings_array - array with the elements of the type'setting_key' => 'setting_value'
     * @return always true :)
     */
    function updateOptionsSettingsForEntity($parent_entity,$entity_id,$settings_array=array())
    {
        if(!empty($settings_array))
        {
            global $application;
            $tables=$this->getTables();
            $settings_table=$tables['po_settings']['columns'];

            foreach($settings_array as $skey => $sval)
            {
                $query = new DB_Replace('po_settings');
                $query->addReplaceValue($parent_entity, $settings_table['parent_entity']);
                $query->addReplaceValue($entity_id, $settings_table['entity_id']);
                $query->addReplaceValue($skey, $settings_table['setting_key']);
                $query->addReplaceValue($sval, $settings_table['setting_value']);
                $application->db->PrepareSQL($query);
                $application->db->DB_Exec();
            };
        };

        return true;
    }


    /**
     * Gets the list of options for the entity.
     *
     * @param string $parent_entity - entity name
     * @param int $entity_id - ID of the entity the list should be gotten for
     * @return index array of options
     * each element has the structure, described in the comment to the function addOptionToEntity
     */
    function getOptionsList($parent_entity, $entity_id, $flags = 0, $ml_all_langs = false)
    {
        if ($ml_all_langs)
        {
            $current_language = modApiFunc('MultiLang', 'getLanguage');
            modApiFunc('MultiLang', 'setLanguage',
                       modApiFunc('MultiLang', 'getDefaultLanguage'));
        }

        $params = array(
            'parent_entity' => $parent_entity,
            'entity_id' => $entity_id,
            'flags' => $flags,
        );
        $return = execQuery('SELECT_ENTITY_OPTIONS_LIST', $params);

        if ($ml_all_langs)
        {
            if ($return)
                foreach($return as $k => $v)
                    $return[$k]['_ml'] = modApiFunc('MultiLang',
                                                    'getMLTableData',
                                                    'Product_Options',
                                                    'po_options',
                                                    $v['option_id']);

            modApiFunc('MultiLang', 'setLanguage', $current_language);
        }

        return $return;
    }


    /**
     * Gets the list of values for the option.
     *
     * @param int $option_id - ID of the option the values should be gotten for
     * @return index array of values
     * each element has the structure, described in the comment to the function addValueToOption
     */
    function getValuesList($option_id, $ml_all_langs = false)
    {
        if ($ml_all_langs)
        {
            $current_language = modApiFunc('MultiLang', 'getLanguage');
            modApiFunc('MultiLang', 'setLanguage',
                       modApiFunc('MultiLang', 'getDefaultLanguage'));
        }

        $params = array("option_id" => $option_id);

        $return = execQuery("SELECT_PRODUCT_OPTION_VALUES", $params);

        if ($ml_all_langs)
        {
            if ($return)
                foreach($return as $k => $v)
                    $return[$k]['_ml'] = modApiFunc('MultiLang',
                                                    'getMLTableData',
                                                    'Product_Options',
                                                    'po_options_values',
                                                    $v['value_id']);

            modApiFunc('MultiLang', 'setLanguage', $current_language);
        }

        return $return;
    }


    /**
     * Gets the list of options together with their values for the entity.
     *
     * @param string $parent_entity - entity name
     * @param int $entity_id - ID of the entity the list of options with their values should be gotten for
     * @return idex array with options and their values
     * each element has the structure, described in the comment to the function addOptionToEntity
     *   + element 'values' - array, each element of which has the structure,
     *     described in the comment to the function addValueToOption
     */
    function getOptionsWithValues($parent_entity, $entity_id, $flags = 0, $ml_all_langs = false)
    {
        $options_list=$this->getOptionsList($parent_entity,$entity_id,$flags,$ml_all_langs);
        if($options_list!=false and is_array($options_list) and !empty($options_list))
        {
            foreach($options_list as $key => $option_info)
                $options_list[$key]["values"]=$this->getValuesList($option_info['option_id'],$ml_all_langs);
        }
        return $options_list;
    }


    /**
     * Gets the option info.
     *
     * @param int $option_id - ID of the option to get info for
     * @param bool $with_values_list - whethter option values are necessary or not
     * @return array of option info
     * the structure is described in the comment to the function addOptionToEntity,
     *  + 'values' - see the comment to the previous function
     */
    function getOptionInfo($option_id, $with_values_list=false)
    {
        global $application;
        $tables=$this->getTables();
        $options=$tables['po_options']['columns'];

        $query = new DB_Select();
        $query->addSelectTable('po_options');
        foreach($options as $k => $v)
        if (in_array($k, array('option_name', 'display_name', 'display_descr')))
        {
            $query->setMultiLangAlias('_' . $k, 'po_options', $v, $options['option_id'], 'Product_Options');
            $query->addSelectField($query->getMultiLangAlias('_' . $k), $k);
        }
        else
        {
            $query->addSelectField($v);
        }
        $query->WhereValue($options['option_id'], DB_EQ, $option_id);
        $res=$application->db->getDB_Result($query);
        if($res==false)
            return false;

        $return_info=array();
        if(is_array($res) and !empty($res))
        {
            $return_info=array_pop($res);
            if($with_values_list==true)
                $return_info["values"]=$this->getValuesList($option_id);
        };

        return $return_info;
    }


    /**
     * Gets option value info.
     *
     * @param int $value_id - ID of the value info should be gotten about
     * @return array of option value info
     * see the comment to the function addValueToOption
     */
    function getValueInfo($value_id)
    {
        global $application;
        $tables=$this->getTables();
        $values=$tables['po_options_values']['columns'];

        $query = new DB_Select();
        $query->addSelectTable('po_options_values');
        foreach($values as $k => $v)
        if ($k == 'value_name')
        {
            $query->setMultiLangAlias('_value_name', 'po_options_values', $v, $values['value_id'], 'Product_Options');
            $query->addSelectField($query->getMultiLangAlias('_value_name'), 'value_name');
        }
        else
        {
            $query->addSelectField($v);
        }
        $query->WhereValue($values['value_id'], DB_EQ, $value_id);
        return array_shift(array_values($application->db->getDB_Result($query)));
    }


    /**
     * Gets the default combination of option values for the entity.
     *
     * @param string $parent_entity - entity name
     * @param int $entity_id - ID of the entity
     * @return array describing the combination of option values
     * (for more details see wiki)
     */
    function getDefaultCombinationForEntity($parent_entity,$entity_id)
    {
        $return = array();

        $options_array = $this->getOptionsWithValues($parent_entity,$entity_id);

        for($i=0;$i<count($options_array);$i++)
        {
            $option_data=$options_array[$i];
            if($option_data['option_type']=='SS')
            {
                if($option_data['discard_avail']=='Y')
                    $return[$option_data['option_id']]=0;
                for($j=0;$j<count($option_data['values']);$j++)
                {
                    $value_data=$option_data['values'][$j];
                    if($value_data['is_default']=='Y')
                        $return[$option_data['option_id']]=$value_data['value_id'];
                };
            }
            elseif($option_data['option_type']=='MS')
            {
                $return[$option_data['option_id']]=array();
                for($j=0;$j<count($option_data['values']);$j++)
                {
                    $value_data=$option_data['values'][$j];
                    if($value_data['is_default']=='Y')
                        $return[$option_data['option_id']][$value_data['value_id']]="on";
                };
            }
            elseif($option_data['option_type']=='CI')
            {
                $default_val=array_shift(array_values($this->getValuesList($option_data['option_id'])));
                if($default_val['is_default']=='Y')
                {
                    if(preg_match("/^CB/",$option_data['show_type']))
                        $return[$option_data['option_id']]=array('cb'=>'on','val'=>$default_val['value_name']);
                    else
                        $return[$option_data['option_id']]=array('val'=>$default_val['value_name']);
                };
            };
        };

        return $return;
    }


    /**
     * Gets modifiers of default combination from the entity.
     *
     * @param string $parent_entity - entity name
     * @param int $entity_id - ID of the entity
     * @return array(
     *  'price' => sum of price modificators and values included in the combination
     * ,'weight' => sum of weight modificators and values included in the combination
     * ,'shipping_cost' => sum of shipping cost modificators and values included in the combination
     * ,'handling_cost' => sum of handling cost modificators and values included in the combination
     * )
     */
    function getModifiersOfDefaultCombination($parent_entity,$entity_id)
    {
        $modifiers=$this->__getInnerVar("_MODIFIERS");

        if(!empty($modifiers))
        {
            global $application;
            $tables=$this->getTables();
            $options_table=$tables['po_options']['columns'];
            $values_table=$tables['po_options_values']['columns'];
            $query = new DB_Select();
            $query->addSelectTable('po_options');
            $query->addSelectTable('po_options_values');
            foreach($modifiers as $mod)
                $query->addSelectField($query->fSum($values_table[$mod.'_modifier']), $mod);
            $query->WhereValue($values_table['is_default'], DB_EQ, 'Y');
            $query->WhereAND();
            $query->WhereValue($options_table['parent_entity'], DB_EQ, $parent_entity);
            $query->WhereAND();
            $query->WhereValue($options_table['entity_id'], DB_EQ, $entity_id);
            $query->WhereAND();
            $query->WhereField($values_table['option_id'], DB_EQ, $options_table['option_id']);
            return array_shift(array_values($application->db->getDB_Result($query)));
        }
        else
            return array();

    }


    /**
     * Gets combination modifiers.
     *
     * WARNING: it should be passed in the correct combination only.
     *
     * @param $combination - array describing combination (for details see wiki)
     * @return array(
     *  'price' => sum of price modificators and values included in the combination
     * ,'weight' => sum of weight modificators and values included in the combination
     * ,'shipping_cost' => sum of shipping cost modificators and values included in the combination
     * ,'handling_cost' => sum of handling cost modificators and values included in the combination
     * )
     */
    function getCombinationModifiers($combination)
    {
        $modifiers=$this->__getInnerVar("_MODIFIERS");
        $return=array();
        foreach($modifiers as $mod)
            $return[$mod]=0;

        if(empty($combination))
            return $return;

        foreach($modifiers as $mod)
            $return[$mod]=0;
        $cmb_options=array_keys($combination);

        for($i=0;$i<count($cmb_options);$i++)
        {
            if(is_numeric($combination[$cmb_options[$i]]))
            {
                $val_info=$this->getValueInfo($combination[$cmb_options[$i]]);
                foreach($modifiers as $mod)
                    $return[$mod]+=$val_info[$mod."_modifier"];
            }
            elseif(is_array($combination[$cmb_options[$i]]) and !isset($combination[$cmb_options[$i]]['val']))
            {
                $selected_items=array_keys($combination[$cmb_options[$i]]);
                for($j=0;$j<count($selected_items);$j++)
                {
                    $val_info=$this->getValueInfo($selected_items[$j]);
                    foreach($modifiers as $mod)
                        $return[$mod]+=$val_info[$mod."_modifier"];
                };
            }
            elseif(is_array($combination[$cmb_options[$i]]) and isset($combination[$cmb_options[$i]]['val']))
            {
                if($combination[$cmb_options[$i]]['val']!="")
                {
                    $val_info=array_shift(array_values($this->getValuesList($cmb_options[$i])));
                    foreach($modifiers as $mod)
                        $return[$mod]+=$val_info[$mod."_modifier"];
                };
            }
            elseif(is_array($combination[$cmb_options[$i]]) and isset($combination[$cmb_options[$i]]['is_file']))
            {
                if(file_exists($combination[$cmb_options[$i]]['file_path']))
                {
                    $val_info=array_shift(array_values($this->getValuesList($cmb_options[$i])));
                    foreach($modifiers as $mod)
                        $return[$mod]+=$val_info[$mod."_modifier"];
                };
            };
        };

        return $return;
    }


    /**
     * Calculate the combination hash.
     *
     * @param array $combination - array describing the combination
     * @return string
     */
    function getCombinationHash($combination)
    {
        return crc32(serialize($combination));
    }

    function convertModifierPrices($mods, $currency_code, $default_currency_code)
    {
    	//                        -
    	//"price"
        //"shipping_cost"
        //"handling_cost"
        if($currency_code == $default_currency_code)
        {
        	return $mods;
        }
        else
        {
            $converted_mods = array();
            foreach($mods as $key => $value)
            {
            	if($key == "price" ||
            	   $key == "shipping_cost" ||
            	   $key == "handling_cost")
            	{
            	    $converted_mods[$key] = modApiFunc("Currency_Converter", "convert", $value, $default_currency_code, $currency_code);
            	}
            	else
            	{
            		$converted_mods[$key] = $value;
            	}
            }
            return $converted_mods;
        }
    }

    /**
     * Prepares data to add order to the database.
     *
     * @param int $option_id - ID of the option, to prepare data for
     * @param mixed option_data - array element describing the combination
     * (for details see wiki)
     *
     * @return array(
     *  $option_name  - option name
     * ,$values_string - string with the name(names) of the option value (values)
     * in the combination
     * ,$modifiers = array(
     *   'price' => price modificator
     *  ,'weight' => weight modificator
     *  ,'shipping_cost' => shipping cost modificator
     *  ,'handling_cost' => handling cost modificator
     * )
     * )
     */
    function prepareDataForPlaceOrder($option_id, $option_data)
    {
        $modifiers=$this->__getInnerVar("_MODIFIERS");
        if(is_numeric($option_data))
        {
            //SS
            $option_info=$this->getOptionInfo($option_id);
            $value_info=$this->getValueInfo($option_data);
            $mods=array();
            for($i=0;$i<count($modifiers);$i++)
                $mods[$modifiers[$i]]=$value_info[$modifiers[$i]."_modifier"];
            return array($option_info['display_name'],$value_info['value_name'], $mods);//modApiFunc("Product_Options", "convertModifierPrices", $mods, $currency_code, $default_currency_code));
        }
        elseif(is_array($option_data) and !isset($option_data['val']))
        {
            //MS
            $option_info=$this->getOptionInfo($option_id);
            $values_list=array_keys($option_data);
            $values_string="";
            $mods_array=array();
            for($i=0;$i<count($modifiers);$i++)
                $mods_array[$modifiers[$i]]=0;
            if(!empty($values_list))
            {
                global $application;
                $tables=$this->getTables();
                $values_table=$tables['po_options_values']['columns'];

                $query = new DB_Select();
                $query->addSelectTable('po_options_values');
                $query->setMultiLangAlias('_value_name', 'po_options_values', $values_table['value_name'], $values_table['value_id'], 'Product_Options');
                $query->addSelectField($query->getMultiLangAlias('_value_name'), 'value_name');
                for($i=0;$i<count($modifiers);$i++)
                    $query->addSelectField($values_table[$modifiers[$i]."_modifier"], $modifiers[$i]."_modifier");
                $query->Where($values_table['value_id'], DB_IN, '(\''.implode("','",$values_list).'\')');
                $res=$application->db->getDB_Result($query);

                $vals=array();
                foreach($res as $k => $value_data)
                {
                    $vals[]=$value_data['value_name'];
                    for($i=0;$i<count($modifiers);$i++)
                        $mods_array[$modifiers[$i]]+=$value_data[$modifiers[$i]."_modifier"];
                }
                $values_string=implode(", ",$vals);
            }
            return array($option_info['display_name'], $values_string, $mods_array);//modApiFunc("Product_Options", "convertModifierPrices", $mods_array, $currency_code, $default_currency_code));
        }
        elseif(is_array($option_data) and isset($option_data['val']) and !isset($option_data['is_file']))
        {
            //CI
            $option_info=$this->getOptionInfo($option_id);
            if(preg_match("/^CB/",$option_info['show_type']) and (!isset($option_data['cb']) or $option_data['cb']!='on'))
            {
                $option_data['val']="";
            }

            $mods_array=array();
            for($i=0;$i<count($modifiers);$i++)
                $mods_array[$modifiers[$i]]=0;
            $value_data=array_shift(array_values($this->getValuesList($option_id)));
            for($i=0;$i<count($modifiers);$i++)
                $mods_array[$modifiers[$i]]+=$value_data[$modifiers[$i]."_modifier"];

            return array($option_info['display_name'], $option_data['val'], $mods_array);//modApiFunc("Product_Options", "convertModifierPrices", $mods_array, $currency_code, $default_currency_code));
        }
        elseif(is_array($option_data) and isset($option_data['val']) and isset($option_data['is_file']))
        {
            //UF
            $option_info=$this->getOptionInfo($option_id);

            $mods_array=array();
            for($i=0;$i<count($modifiers);$i++)
                $mods_array[$modifiers[$i]]=0;
            $value_data=array_shift(array_values($this->getValuesList($option_id)));
            for($i=0;$i<count($modifiers);$i++)
                $mods_array[$modifiers[$i]]+=$value_data[$modifiers[$i]."_modifier"];

            return array($option_info['display_name'], $option_data['val'], $mods_array);//modApiFunc("Product_Options", "convertModifierPrices", $mods_array, $currency_code, $default_currency_code));
        }
    }


    /**
     * Prepares the text for notifications.
     *
     * @param $options_array - array of options for the array product, taken
     * from the function Checkout::getOrderInfo()
     * @return string with option names and their values
     */
    function prepareTextForNotificaton($options_array)
    {
        $return_text="";

        $strings=array();
        for($i=0;$i<count($options_array);$i++)
        {
            $_val = $options_array[$i]['option_value'];

            if($_val != '')
            {
                $_val = ($options_array[$i]['is_file'] == 'Y' ? basename($_val) : $_val);
            };

            $strings[]=$options_array[$i]['option_name'].": ".$_val;
        };
        if(!empty($strings))
            $return_text.=implode("\n",$strings);

        return $return_text;
    }


    /**
     * Prepares the text to insert to the IIF-file when exporting orders.
     *
     * @param $options_array - array of options for the array product, taken
     * from the funtion Checkout::getOrderInfo()
     * @return string with option names and their values
     */
    function prepareTextForOrdersExport($options_array)
    {
        $return_text="";

        $strings=array();
        for($i=0;$i<count($options_array);$i++)
        {
            $strings[]=$options_array[$i]['option_name'].": ".$options_array[$i]['option_value'];
        };
        if(!empty($strings))
            $return_text.=implode('\n',$strings);

        return $return_text;
    }

    function convertCombinationToString($combination, $lines_delim="\n")
    {
        $return_text = '';

        $strings=array();

        foreach($combination as $option_id => $option_value)
        {
            $_tmp = $this->prepareDataForPlaceOrder($option_id, $option_value);
            $strings[] = $_tmp[0].': '.$_tmp[1];
        };

        if(!empty($strings))
            $return_text.=implode($lines_delim,$strings);

        return $return_text;
    }

    function addInvRecordToEntity($data)
    {
        global $application;
        $tables = $this->getTables();
        $inv_table = $tables['po_inventory']['columns'];

        $data['combination'] = $this->_serialize_combination($this->__normalize_combination($data['side']));
        $data['formula'] = $this->__convCmbs2CRFormula("5",array('sside'=>$data['side'])).";";
        unset($data['side']);

        $data['sort_order'] = $this->__getMaxInventorySortOrder($data['parent_entity'],$data['entity_id'])+1;

        $query = new DB_Insert('po_inventory');
        foreach($data as $key => $value)
            $query->addInsertValue($value, $inv_table[$key]);
        $application->db->PrepareSQL($query);
        if($application->db->DB_Exec())
            return $application->db->DB_Insert_Id();
        else
            return false;
    }

    function delInvRecordsFromEntity($parent_entity,$entity_id,$inv_records_ids)
    {
        if(!is_array($inv_records_ids) or empty($inv_records_ids))
            return true;

        global $application;
        $tables = $this->getTables();
        $inv_table = $tables['po_inventory']['columns'];

        $query = new DB_Delete('po_inventory');
        $query->Where($inv_table['it_id'], DB_IN, "('".implode("','",$inv_records_ids)."')");
        $application->db->PrepareSQL($query);
        return $application->db->DB_Exec();
    }


    /**
     * Rebuilds the Inventory table for the entity.
     *
     * @param string $parent_entity - entity name
     * @param int $entity_id - ID of the entity for which it should be rebuilded
     * @return bool;  true - if it is rebuilt, false otherwise
     */
    function rebuildInventoryForEntity($parent_entity,$entity_id)
    {
        if(!$this->__hasEntityPrivilegesFor($parent_entity,'inventory'))
            return true;

        global $application;
        $tables=$this->getTables();
        $options_table=$tables['po_options']['columns'];

        $sets=$this->getOptionsSettingsForEntity($parent_entity,$entity_id);

/*        //: rewrite!
        $query = new DB_Select();
        $query->addSelectField($options_table['option_id'], 'option_id');
        $query->WhereValue($options_table['parent_entity'], DB_EQ, $parent_entity);
        $query->WhereAND();
        $query->WhereValue($options_table['entity_id'], DB_EQ, $entity_id);
        $query->WhereAND();
        $query->WhereValue($options_table['use_for_it'], DB_EQ, 'Y');
        $query->WhereAND();
        $query->WhereValue($options_table['option_type'], DB_NEQ, 'CI');
        $query->SelectOrder($options_table['sort_order'], 'ASC');
*/

        $query = new DB_Select();
        $query->addSelectField($options_table['option_id'], 'option_id');
        $query->WhereValue($options_table['parent_entity'], DB_EQ, $parent_entity);
        $query->WhereAND();
        $query->WhereValue($options_table['entity_id'], DB_EQ, $entity_id);
        $query->WhereAND();
        $query->WhereValue($options_table['option_type'], DB_NEQ, 'CI');
        $query->SelectOrder($options_table['sort_order'], 'ASC');

        $res=$application->db->getDB_Result($query);
        $clear_res=$this->__clearITForEntity($parent_entity,$entity_id);
        if(empty($res))
        {
            if($clear_res)
            {
//                $this->updateOptionsSettingsForEntity($parent_entity,$entity_id,array('IT_ACTUAL'=>'Y'));
                return true;
            }
            else
                return false;
        };

        $options_ids=array();
        foreach($res as $k => $v)
            $options_ids[]=$v['option_id'];

        $all_combinations = $this->__genAllCombinationsByOptionsIDs($options_ids);

        $to_unset=array();

        foreach($all_combinations as $key => $cmb)
        {
            if(!$this->checkByCRules($parent_entity,$entity_id,$cmb,true))
                $to_unset[]=$key;
        };

        for($i=0;$i<count($to_unset);$i++)
            unset($all_combinations[$to_unset[$i]]);

        $all_combinations = array_filter(array_map(array(&$this,"__combination_filter_for_inv"),$all_combinations));

        $__cmbs_as_strings = array_map(array(&$this,"_serialize_combination"),$all_combinations);
        $__cmbs_as_strings = array_unique($__cmbs_as_strings);
        $all_combinations = array_map(array(&$this,"_unserialize_combination"),$__cmbs_as_strings);

/*        print_r($all_combinations);
        return;
*/

        $steps_count = ceil(count($all_combinations)/INV_RECORDS_PER_QUERY);

        if(!empty($all_combinations))
        {
            $prefix=$application->getAppIni("DB_TABLE_PREFIX");
            $base_sku="";
            /* commented by egor 07.05.2007 ASC-723(5)
            if($parent_entity=='product')
            {
                $prod_obj = &$application->getInstance('CProductInfo',$entity_id);
                $base_sku = $prod_obj->getProductTagValue('SKU');
            };
            if($base_sku=="")
                $base_sku="SKU-".$entity_id;
            */
            $counter=0;
            reset($all_combinations);
            for($i=0;$i<$steps_count;$i++)
            {
                $added = 0;
                $direct_query = "insert into ".$prefix."po_inventory (parent_entity, entity_id, combination, formula, sku, quantity, sort_order) values ";
                do
                {
                    $cmb=current($all_combinations);
                    $comb = $this->_serialize_combination($cmb);
                    if(empty($comb)) $comb = "[0]{{0}}";
                    $direct_query.="('".$parent_entity."'".
                        ",'".$entity_id."'".
                        ",'".$comb."'".
                        ",'".$this->__convCmbs2CRFormula("5",array('sside'=>$cmb)).";"."'".
                        ",'".$base_sku./*"-".(++$counter).*/"'".
                        ",'0'".
                        ",'".(++$counter)."'), ";
                }
                while(next($all_combinations) and ((++$added) < INV_RECORDS_PER_QUERY));

                $direct_query=_ml_substr($direct_query,0,-2);
                $application->db->DB_Query($direct_query);
            };
//            $this->updateOptionsSettingsForEntity($parent_entity,$entity_id,array('IT_ACTUAL'=>'Y'));
        }
        else
        {
//            $this->updateOptionsSettingsForEntity($parent_entity,$entity_id,array('IT_ACTUAL'=>'Y'));
            return true;
        }
    }


    /**
     * Gets a page from the Inventory table for the entity.
     *
     * @param string $parent_entity - entity name
     * @param int $entity_id - ID of the entity, the page should be gotten for
     * @param int $page_number - page number
     * @return array with the elements of the type
     * array(
     *  'it_id' => int - ID of table element
     * ,'parent_entity' => enum('product','ptype',...)
     * ,'entity_id' => int - ID of the entity
     * ,'combination' => string - serialize combination (see the method _serialize_combination)
     * ,'sku' => string - SKU of the combination
     * ,'quantity' => int - quantity of available products with this combination
     * )
     */
    function getInventoryPage($parent_entity,$entity_id,$page_number)
    {
        if($page_number!='last_page')
        {
            $page_number=intval($page_number);
            if($page_number<=0)
                $page_number=1;
        };

        global $application;
        $tables=$this->getTables();
        $it_table=$tables['po_inventory']['columns'];

        $query = new DB_Select();
        $query->addSelectTable('po_inventory');
        $query->addSelectField($query->fCount($it_table['it_id']), 'it_count');
        $query->WhereValue($it_table['parent_entity'], DB_EQ, $parent_entity);
        $query->WhereAND();
        $query->WhereValue($it_table['entity_id'], DB_EQ, $entity_id);
        $res=$application->db->getDB_Result($query);
        $it_count=$res[0]['it_count'];
        $sets = $this->getOptionsSettingsForEntity($parent_entity,$entity_id);
        $it_per_page = $sets["INV_PER_PAGE"];
        $full_pages_count = floor($it_count / $it_per_page);
        $on_last_page = $it_count % $it_per_page;
        if($on_last_page>0)
            $pages_count = $full_pages_count+1;
        else
            $pages_count = $full_pages_count;

        if($page_number=='last_page')
            $page_number=$pages_count;

        if($page_number>$pages_count)
            $page_number=$pages_count;

        $query = new DB_Select();
        $query->addSelectTable('po_inventory');
        $query->addSelectField('*');
        $query->WhereValue($it_table['parent_entity'], DB_EQ, $parent_entity);
        $query->WhereAND();
        $query->WhereValue($it_table['entity_id'], DB_EQ, $entity_id);
        $query->SelectOrder($it_table['sort_order'], 'ASC');
        $query->SelectLimit(($page_number-1)*$it_per_page, $it_per_page);
        $page_content = $application->db->getDB_Result($query);
        return array(
                "pages_count" => $pages_count
               ,"page_number" => $page_number
               ,"page_content" => $page_content
               ,"inv_per_page" => $it_per_page
               ,"inv_count" => $it_count
             );
    }


    /**
     * Updates Inventory data.
     *
     * @param string $parent_entity - entity name
     * @param int $entity_id - ID of the entity
     * @param $inv_data = array(
     *  '$it_id_01' => array(
     *          'sku' => string
     *          'quantity' => int
     *      )
     *   .......
     * );
     * @return bool; true if it is updated, false otherwise
     */
    function updateInventory($parent_entity,$entity_id,$inv_data)
    {
        if(!is_array($inv_data) or empty($inv_data))
            return true;

        global $application;
        $tables=$this->getTables();
        $it_table=$tables['po_inventory']['columns'];

        foreach($inv_data as $it_id => $it_data)
        {
            $query = new DB_Update('po_inventory');
            $query->addUpdateValue($it_table['sku'], $it_data['sku']);
            $query->addUpdateValue($it_table['quantity'], intval($it_data['quantity']));
            $query->WhereValue($it_table['parent_entity'], DB_EQ, $parent_entity);
            $query->WhereAND();
            $query->WhereValue($it_table['entity_id'], DB_EQ, $entity_id);
            $query->WhereAND();
            $query->WhereValue($it_table['it_id'], DB_EQ, $it_id);
            $application->db->PrepareSQL($query);
            if(!$application->db->DB_Exec())
                return false;
        };

        return true;
    }


    /**
     * Gets element ID of the Inventory table.
     *
     * @param string $parent_entity - entity name
     * @param int $entity_id - ID of the entity the element ID of the Inventory table should be gotten for
     * @param array $combination - array describing the combination
     * @return null it's impossible to get a correct ID, int - ID of the required element
     */
    function getInventoryIDByCombination($parent_entity, $entity_id, $combination)
    {
        $inv_id=null;

        $res = execQuery('SELECT_PRODUCT_OPTIONS_INVENTORY', array(
            'parent_entity' => $parent_entity,
            'entity_id' => $entity_id,
        ));

        foreach ($res as $row)
        {
            if($this->__logic_calc($row['formula'],$combination))
            {
                $inv_id=$row['it_id'];
                break;
            };

        };

        return $inv_id;
    }


    /**
     * Gets info about the element of the Inventory table.
     *
     * @param int $inv_id - ID of the table element
     * @return array (see the method getInventoryPage)
     */
    function getInventoryInfo($inv_id)
    {
        global $application;
        $tables=$this->getTables();
        $it_table=$tables['po_inventory']['columns'];

        $query = new DB_Select();
        $query->addSelectTable('po_inventory');
        $query->addSelectField('*');
        $query->WhereValue($it_table['it_id'], DB_EQ, $inv_id);
        $res=$application->db->getDB_Result($query);
        return array_shift(array_values($res));
    }

    function getEntityInventory($parent_entity, $entity_id)
    {
        global $application;
        $tables = $this->getTables();
        $it_table = $tables['po_inventory']['columns'];

        $query = new DB_Select();
        $query->addSelectTable('po_inventory');
        $query->addSelectField($it_table['formula']);
        $query->addSelectField($it_table['sku']);
        $query->addSelectField($it_table['quantity']);
        $query->WhereValue($it_table['parent_entity'], DB_EQ, $parent_entity);
        $query->WhereAND();
        $query->WhereValue($it_table['entity_id'], DB_EQ, $entity_id);
        return $application->db->getDB_Result($query);
    }


    /**
     * Updates the field qunatity in the Inventory table.
     *
     * @param int $inv_id - ID of the updated element
     * @param int $quantity_offset - quantity update (any integer number)
     * @return bool; true if it is updated, false otherwise
     */
    function updateInventoryQuantity($inv_id, $quantity_offset)
    {
        global $application;
        $tables=$this->getTables();
        $it_table=$tables['po_inventory']['columns'];

        $query = new DB_Update('po_inventory');
        $query->addUpdateExpression($it_table['quantity'], $it_table['quantity'].'+('.$quantity_offset.')');
        $query->WhereValue($it_table['it_id'], DB_EQ, $inv_id);
        $application->db->PrepareSQL($query);
        if($application->db->DB_Exec())
        {
            $inv_info = $this->getInventoryInfo($inv_id);
            $sets = $this->getOptionsSettingsForEntity($inv_info['parent_entity'], $inv_info['entity_id']);

            if($sets['LL_NTF'] != '' and $inv_info['quantity'] <= $sets['LL_NTF'])
            {
                modApiFunc('EventsManager','throwEvent','InventoryLowLevel',$inv_info);
            };
        };
    }


    /**
     * Gets a map of modificators to map them on product attributes.
     */
    function getModsMap()
    {
        return array(
                    "price" => "SalePrice"
                   ,"weight" => "Weight"
                   ,"shipping_cost" => "PerItemShippingCost"
                   ,"handling_cost" => "PerItemHandlingCost"
                );
    }

    function addCRuleToEntity($parent_entity,$entity_id,$tpl_index,$cmbs)
    {
        if(!$this->__hasEntityPrivilegesFor($parent_entity,'crules'))
            return;

        global $application;
        $tables=$this->getTables();
        $crules_table=$tables['po_crules']['columns'];

        $new_cr_form = $this->__convCmbs2CRFormula($tpl_index,$cmbs);

        $data=array(
            'parent_entity' => $parent_entity
           ,'entity_id' => $entity_id
           ,'tpl_index' => $tpl_index
           ,'sside' => (isset($cmbs['sside']))?$this->_serialize_combination($cmbs['sside']):''
           ,'lside' => (isset($cmbs['lside']))?$this->_serialize_combination($cmbs['lside']):''
           ,'rside' => (isset($cmbs['rside']))?$this->_serialize_combination($cmbs['rside']):''
           ,'crule_formula' => $new_cr_form
           ,'sort_order' => $this->__getMaxCRulesSortOrder($parent_entity,$entity_id)+1
        );

        $query = new DB_Insert('po_crules');
        foreach($data as $key => $value)
            $query->addInsertValue($value, $crules_table[$key]);
        $application->db->PrepareSQL($query);
        if($application->db->DB_Exec())
        {
            $new_crule_id = $application->db->DB_Insert_Id();
            $this->__rebuildCRulesFormulaForEntity($parent_entity,$entity_id);

/*            $oids = array();
            $sides = array('sside','lside','rside');
            foreach($sides as $side_name)
                if($data[$side_name]!='')
                    $oids=array_merge($oids,array_keys($this->_unserialize_combination($data[$side_name])));

            $oids = array_unique($oids);

            if(!empty($oids) and $this->__isUsedForIT($oids))
                $this->updateOptionsSettingsForEntity($parent_entity,$entity_id,array('IT_ACTUAL'=>'N'));*/

            return $new_crule_id;
        }
        else
            return false;
    }


    /**
     * Return the stock sum of all inventory table.
     * @return mixed Return int or null.
     */
    function getQuantityInStockByInventoryTable($parent_entity,$entity_id,$positive_only = false)
    {
        global $application;
        $tables=$this->getTables();
        $it_table=$tables['po_inventory']['columns'];

        $query = new DB_Select();
        $query->addSelectTable('po_inventory');
        if ($positive_only)
            $query->addSelectValue('SUM(IF(quantity>0,quantity,0))', 'quantity');
        else
            $query->addSelectValue('SUM(quantity)', 'quantity');
        $query->WhereValue($it_table['parent_entity'], DB_EQ, $parent_entity);
        $query->WhereAND();
        $query->WhereValue($it_table['entity_id'], DB_EQ, $entity_id);
        $res = $application->db->getDB_Result($query);

        $stock = null;
        if (!empty($res))
            $stock = $res[0]['quantity'];

        return $stock;
    }

    function delCRulesFromEntity($parent_entity,$entity_id,$crules_ids)
    {
        global $application;
        $tables=$this->getTables();
        $crules_table=$tables['po_crules']['columns'];

/*        $query = new DB_Select();
        $query->addSelectTable('po_crules');
        $query->addSelectField('*');
        $query->Where($crules_table['crule_id'], DB_IN, "('".implode("','",$crules_ids)."')");
        $res = $application->db->getDB_Result($query);
        $oids = array();
        for($i=0;$i<count($res);$i++)
        {
            $oids=array_merge($oids,array_keys($this->_unserialize_combination($res[$i]['sside'])));
            $oids=array_merge($oids,array_keys($this->_unserialize_combination($res[$i]['lside'])));
            $oids=array_merge($oids,array_keys($this->_unserialize_combination($res[$i]['rside'])));
        };
        if(!empty($oids) and $this->__isUsedForIT($oids))
            $this->updateOptionsSettingsForEntity($parent_entity,$entity_id,array('IT_ACTUAL'=>'N'));*/

        $query = new DB_Delete('po_crules');
        $query->Where($crules_table['crule_id'], DB_IN, "('".implode("','",$crules_ids)."')");
        $application->db->PrepareSQL($query);
        if($application->db->DB_Exec())
            $this->__rebuildCRulesFormulaForEntity($parent_entity,$entity_id);
        else
            return false;
    }

    function getCRulesForEntity($parent_entity,$entity_id)
    {
        global $application;
        $tables=$this->getTables();
        $crules_table=$tables['po_crules']['columns'];

        $query = new DB_Select();
        $query->addSelectTable('po_crules');
        $query->addSelectField('*');
        $query->WhereValue($crules_table['parent_entity'], DB_EQ, $parent_entity);
        $query->WhereAND();
        $query->WhereValue($crules_table['entity_id'], DB_EQ, $entity_id);
        $query->SelectOrder($crules_table['sort_order'], 'ASC');

        return $application->db->getDB_Result($query);
    }

    function checkByCRules($parent_entity,$entity_id,$combination,$for_inv=false)
    {
        $sets = $this->getOptionsSettingsForEntity($parent_entity,$entity_id);
        return $this->__logic_calc($sets['CR_FORMULA'],$combination);
    }

    function getInventoryStatsForEntity($parent_entity,$entity_id)
    {
        if(!$this->__hasEntityPrivilegesFor($parent_entity,'inventory'))
            return null;

        $return = array();

        global $application;
        $tables = $this->getTables();
        $inv_table = $tables['po_inventory']['columns'];

        $query = new DB_Select();
        $query->addSelectField($query->fCount($inv_table['it_id']),'it_count');
        $query->WhereValue($inv_table['parent_entity'], DB_EQ, $parent_entity);
        $query->WhereAND();
        $query->WhereValue($inv_table['entity_id'], DB_EQ, $entity_id);
        $res = $application->db->getDB_Result($query);
        $return['it_count']=$res[0]['it_count'];

        $sets = $this->getOptionsSettingsForEntity($parent_entity,$entity_id);
        $return['AANIC'] = $sets['AANIC'];
//        $return['IT_ACTUAL'] = $sets['IT_ACTUAL'];

        return $return;
    }

    function getEntitiesWhichUseInventoryTracking($parent_entity)
    {
        $params = array('parent_entity' => $parent_entity);
        $res = execQuery('SELECT_ENTITIES_WHICH_USE_INVENTORY_TRACKING', $params);
        $plain_array = array();
        foreach ($res as $item)
        {
            $plain_array[] = $item['entity_id'];
        }
        return $plain_array;
    }

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------


    /**
     * Checks data to add an option to the entity.
     * @param $data = array(
     *  'option_name' => string
     * ,'display_name' => string
     * ,'option_type' => enum('SS','MS','CI')
     * ,'show_type' => enum('DD','RG') or enum('MS','CBG') or enum('SI','TA','CBSI','CBTA')
     * ,'discard_avail' => enum('Y','N') (optional, only for 'SS')
     * ,'discard_value' => string (optional, only for 'SS' and discard_avail='Y')
     * ,'checkbox_text' => string (optional, only for 'CI' and show_type in ('CBSI','CBTA'))
     * );
     * @return array of errors - odd errors are not returned!
     */
    function __checkDataFor_addOptionToEntity($data)
    {
        $return=array();
        //check option_name
        if(!is_string($data["option_name"]) or $data["option_name"]=="")
            $return[]="E_INVALID_OPTION_NAME";
        //check display_name
        if(!is_string($data["display_name"]) or $data["display_name"]=="")
            $return[]="E_INVALID_DISPLAY_NAME";
        //check option_type
        $_otypes=$this->_getOptionsTypes(true);
        if(!in_array($data["option_type"],array_keys($_otypes)))
            $return[]="E_INVALID_OPTION_TYPE";
        //check show_type
        if(!in_array($data["show_type"],$_otypes[$data["option_type"]]))
            $return[]="E_INVALID_SHOW_TYPE";
        //check discacrd
        if($data["option_type"]=="SS")
        {
            //check discard_avail
            if(!in_array($data["discard_avail"],array('Y','N')))
                $return[]="E_INVALID_DISCARD_AVAIL";
            //check discard_value
            elseif($data["discard_avail"]=="Y")
            {
                if(!is_string($data["display_name"]) or $data["display_name"]=="")
                    $return[]="E_INVALID_DISCARD_VALUE";
            };
        };
        //check checkbox text
        if($data["option_type"]=="CI" and in_array($data["show_type"],array('CBSI','CBTA')))
        {
            if(!is_string($data["checkbox_text"]) or $data["checkbox_text"]=="")
                $return[]="E_INVALID_CHECKBOX_TEXT";
        }
        //check use for inventory tracking
        if($data["option_type"]!="CI" and !in_array($data["use_for_it"],array('Y','N')))
        {
            $return[]="E_INVALID_USE_FOR_IT";
        }
        elseif($data["option_type"]=="CI" and @$data["use_for_it"]=="Y")
        {
            $return[]="E_INVALID_USE_FOR_IT";
        }
        return $return;
    }


    /**
     * Checks data to delete the option of the entity.
     *
     * @param $data = array(
     *  'parent_entity' => enum('product','ptype',...)
     * ,'entity_id' => entity ID
     * ,'option_id' => option ID
     * );
     * @return array of errors
     */
    function __checkDataFor_delOptionFromEntity($data)
    {
        $return=array();
        if(!isset($data["parent_entity"]) or !isset($data["entity_id"]) or !isset($data["option_id"])
            or !$this->__isCorrectEntityIDandOptionID($data["parent_entity"],$data["entity_id"],$data["option_id"]))
        {
            $return[]="E_INVALID_ENTITY_ID_AND_OPTION_ID_COMBINATION";
        }
        return $return;
    }


  /**
     * Checks data to add the value to the option.
     *
     * @param $data = array(
     *  'value_name' => value name
     * ,'is_default' => enum('Y','N')
     * ,'price_modifier' => float price modifier
     * ,'weight_modifier' => float weight modifier
     * ,'shipping_cost_modifier' => float shipping cost modifier
     * );
     * @return array of errors
     */
    function __checkDataFor_addValueToOption($data)
    {
        $return=array();
        //check value_name
        if(!is_string($data["value_name"]) or $data["value_name"]=="")
            $return[]="E_INVALID_VALUE_NAME";
        //check is_default
        if(!in_array($data["is_default"],array('Y','N')))
            $return[]="E_INVALID_IS_DEFAULT";
        //check modifiers
        foreach($this->__getInnerVar("_MODIFIERS") as $modifier)
            if(!is_float($data[$modifier."_modifier"]))
                $return[]="E_INVALID_"._ml_strtoupper($modifier)."_MODIFIER";
        return $return;
    }


    /**
     * Checks data to delete option values.
     *
     * @param $data=array(
     *  'option_id' => option ID
     * ,'values_ids' => IDs of deleted values
     * );
     * @return array of errors
     */
    function __checkDataFor_delValuesFromOption($data)
    {
        $return=array();
        if(!isset($data["option_id"]) or !isset($data["values_ids"])
            or !is_array($data["values_ids"]) or empty($data["values_ids"])
            or !$this->__isCorrectOptionIDandValuesIDs($data["option_id"],$data["values_ids"]))
        {
            $return[]="E_INVALID_OPTION_ID_AND_VALUES_IDS_COMBINATION";
        }
        return $return;
    }


    /**
     * Checks data to update option data.
     * @param $data=array(
     *  'parent_entity'
     * ,'entity_id'
     * ,'option_id'
     * for the next see __checkDataFor_addOptionToEntity
     * );
     * @return errors array
     */
    function __checkDataFor_updateOptionOfEntity($data)
    {
        $return=array();
        if(!isset($data["parent_entity"]) or !isset($data["entity_id"]) or !isset($data["option_id"])
            or !$this->__isCorrectEntityIDandOptionID($data["parent_entity"],$data["entity_id"],$data["option_id"]))
        {
            $return[]="E_INVALID_ENTITY_ID_AND_OPTION_ID_COMBINATION";
        }
        else
        {
            unset($data["parent_entity"]);
            unset($data["entity_id"]);
            unset($data["option_id"]);
            $return=array_merge($return,$this->__checkDataFor_addOptionToEntity($data));
        };

        return $return;
    }


    /**
     * Checks data to update option values.
     *
     * @param $data = array(
     *   'option_id'
     *  ,'values' => array (
     *            '$value_id_01' => for the array see __checkDataFor_addValueToOption
     *            '$value_id_02' => for the array see __checkDataFor_addValueToOption
     *             .....
     *            '$value_id_NN' => for the array see __checkDataFor_addValueToOption
     *          )
     * );
     * @return errors array
     */
    function __checkDataFor_updateValuesOfOption($data)
    {
        $return=array();
        if(!isset($data["option_id"]) or !isset($data["values"])
            or !is_array($data["values"]) or empty($data["values"])
            or !$this->__isCorrectOptionIDandValuesIDs($data["option_id"],array_keys($data["values"])))
        {
            $return[]="E_INVALID_OPTION_ID_AND_VALUES_IDS_COMBINATION";
        }
        else
        {
            foreach($data["values"] as $vid => $vdata)
            {
                $check_result=$this->__checkDataFor_addValueToOption($vdata);
                if(!empty($check_result))
                    $return[$vid]=$check_result;
            }
        }

        return $return;
    }


    /**
     * Checks if the combination entity_id and option_id is correct.
     *
     * @param string $parent_entity - entity name
     * @param int $entity_id - ID of product
     * @param int $option_id - ID of option
     * @return true or false
     */
    function __isCorrectEntityIDandOptionID($parent_entity,$entity_id,$option_id)
    {
        global $application;

        $tables = $this->getTables();
        $options = $tables['po_options']['columns'];

        $query = new DB_Select();
        $query->addSelectField($query->fCount($options['entity_id']), 'count_eid_oid');
        $query->WhereValue($options['parent_entity'], DB_EQ, $parent_entity);
        $query->WhereAND();
        $query->WhereValue($options['entity_id'], DB_EQ, $entity_id);
        $query->WhereAND();
        $query->WhereValue($options['option_id'], DB_EQ, $option_id);

        $result=$application->db->getDB_Result($query);
        if($result[0]['count_eid_oid']!=1)
            return false;

        return true;
    }


    /**
     * Checks if the combination option_id and values_ids is correct.
     *
     * @param int $option_id - ID of option
     * @param array of int $values_ids - IDs of values
     * @return true or false
     */
    function __isCorrectOptionIDandValuesIDs($option_id,$values_ids)
    {
        global $application;

        $tables = $this->getTables();
        $values = $tables['po_options_values']['columns'];

        $query = new DB_Select();
        $query->addSelectField($query->fCount($values['option_id']), 'count_oid_vid');
        $query->WhereValue($values['option_id'], DB_EQ, $option_id);
        $query->WhereAND();
        $query->Where($values['value_id'], DB_IN, '(\''.implode('\',\'',$values_ids).'\')');

        $result=$application->db->getDB_Result($query);
        if($result[0]['count_oid_vid']!=count($values_ids))
            return false;

        return true;
    }


    /**
     * Reelects the default value for the option of the type 'SS'
     *
     * @param int $option_id - option ID
     * @param int $value_id - ID of the value, dominating in the reelection
     * (if it is null, no dominating value exists)
     *
     * @return true it was successfully reelected, false otherwise
     */
    function __chooseAndSetDefaultValueForOption($option_id,$value_id=0)
    {
        global $application;
        $tables=$this->getTables();
        $values=$tables['po_options_values']['columns'];

        $query = new DB_Select();
        $query->addSelectTable('po_options_values');
        $query->addSelectField($values['value_id'], 'value_id');
        $query->addSelectField($values['is_default'], 'is_default');
        $query->WhereValue($values['option_id'], DB_EQ, $option_id);
        $query->SelectOrder($values['sort_order'], 'ASC');

        $res=$application->db->getDB_Result($query);
        if(!is_array($res))
            return false;

        if(!empty($res))
        {
            $default_ids=array();
            foreach($res as $k => $v)
                if($v['is_default']=='Y')
                    $default_ids[]=$v['value_id'];

            if(empty($default_ids))
            {
                $new_default_id = $value_id > 0 ? $value_id : $res[0]['value_id'];

                $query = new DB_Update('po_options_values');
                $query->addUpdateValue($values['is_default'], 'Y');
                $query->WhereValue($values['value_id'], DB_EQ, $new_default_id);
                $application->db->PrepareSQL($query);
                return $application->db->DB_Exec();
            }
            elseif(count($default_ids)==1)
            {
                if($value_id > 0)
                {
                    $query = new DB_Update('po_options_values');
                    $query->addUpdateValue($values['is_default'], 'N');
                    $query->WhereValue($values['value_id'], DB_EQ, $default_ids[0]);
                    $application->db->PrepareSQL($query);
                    if($application->db->DB_Exec())
                    {
                        $query = new DB_Update('po_options_values');
                        $query->addUpdateValue($values['is_default'], 'Y');
                        $query->WhereValue($values['value_id'], DB_EQ, $value_id);
                        $application->db->PrepareSQL($query);
                        return $application->db->DB_Exec();
                    }
                    else
                        return false;
                }
                return true;
            }
            else
            {
                $query = new DB_Update('po_options_values');
                $query->addUpdateValue($values['is_default'], 'N');
                $query->WhereValue($values['option_id'], DB_EQ, $option_id);
                $application->db->PrepareSQL($query);
                if($application->db->DB_Exec())
                {
                    $new_default_id = $value_id > 0 ? $value_id : $default_ids[0];
                    $query = new DB_Update('po_options_values');
                    $query->addUpdateValue($values['is_default'], 'Y');
                    $query->WhereValue($values['value_id'], DB_EQ, $new_default_id);
                    $application->db->PrepareSQL($query);
                    return $application->db->DB_Exec();
                }
                else
                    return false;
            }
        }

        return true;
    }


    /**
     * Deletes all optoin values.
     *
     * @param int $option_id - option ID
     * @return true or false
     */
    function __delAllValuesFromOption($option_id)
    {
        global $application;
        $tables=$this->getTables();
        $values=$tables['po_options_values']['columns'];

        $query = new DB_Delete('po_options_values');
        $query->deleteMultiLangField($values['value_name'], $values['value_id'], 'Product_Options');
        $query->WhereValue($values['option_id'], DB_EQ, $option_id);
        $application->db->getDB_Result($query);
        return $application->db->QueryResult;
    }


    /**
     * Returns options and their values as a short array.
     *
     * @param string $parent_entity - entity name
     * @param int $entity_id - entity ID
     * @return array(
     *  '$option_id_01' => array(
     *                      'type' => enum('SS','MS','CI')
     *                     ,'show' => enum('DD','RG') or enum('MS','CBG') or enum('SI','TA','CBSI','CBTA')
     *                     ,'discard' => enum('Y','N')
     *                     ,'values' => array(
     *                                  '0' => '$value_id_00'
     *                                  '1' => '$value_id_01'
     *                                  .....
     *                                  'NN' => '$value_id_NN'
     *                              )
     *                  )
     *   ............
     * )
     */
    function __getOptionsWithValuesAsIDsArray($parent_entity,$entity_id)
    {
        $options=$this->getOptionsWithValues($parent_entity,$entity_id);
        $return=array();
        for($i=0;$i<count($options);$i++)
        {
            $return[$options[$i]['option_id']]=array(
                    'type'=>$options[$i]['option_type'],
                    'show'=>$options[$i]['show_type'],
                    'discard'=>$options[$i]['discard_avail'],
                    'values'=>array());
            for($j=0;$j<count($options[$i]['values']);$j++)
                $return[$options[$i]['option_id']]['values'][]=$options[$i]['values'][$j]['value_id'];
        }
        return $return;
    }


    /**
     * Gets max value of the field sort_order for entity options.
     *
     * @param string $parent_entity - entity name
     * @param int $entity_id - product ID
     * @return int $max_sort_order
     */
    function __getMaxOptionsSortOrder($parent_entity,$entity_id)
    {
        global $application;
        $tables = $this->getTables();
        $options_table = $tables['po_options']['columns'];

        $query = new DB_Select();
        $query->addSelectField($query->fMax($options_table['sort_order']), 'max_sort_order');
        $query->WhereValue($options_table['parent_entity'], DB_EQ, $parent_entity);
        $query->WhereAND();
        $query->WhereValue($options_table['entity_id'], DB_EQ, $entity_id);
        $res = $application->db->getDB_Result($query);
        return $res[0]['max_sort_order'];
    }


    /**
     * Gets the max value of the field sort_order for option values.
     *
     * @param int $option_id - option ID
     * @return int $max_sort_order
     */
    function __getMaxValuesSortOrder($option_id)
    {
        global $application;
        $tables = $this->getTables();
        $values_table = $tables['po_options_values']['columns'];

        $query = new DB_Select();
        $query->addSelectField($query->fMax($values_table['sort_order']), 'max_sort_order');
        $query->WhereValue($values_table['option_id'], DB_EQ, $option_id);
        $res = $application->db->getDB_Result($query);
        return $res[0]['max_sort_order'];
    }

    function __getMaxCRulesSortOrder($parent_entity,$entity_id)
    {
        global $application;
        $tables = $this->getTables();
        $ex_table = $tables['po_crules']['columns'];

        $query = new DB_Select();
        $query->addSelectField($query->fMax($ex_table['sort_order']), 'max_sort_order');
        $query->WhereValue($ex_table['parent_entity'], DB_EQ, $parent_entity);
        $query->WhereAND();
        $query->WhereValue($ex_table['entity_id'], DB_EQ, $entity_id);
        $res = $application->db->getDB_Result($query);
        return $res[0]['max_sort_order'];
    }

    function __getMaxInventorySortOrder($parent_entity,$entity_id)
    {
        global $application;
        $tables = $this->getTables();
        $ex_table = $tables['po_inventory']['columns'];

        $query = new DB_Select();
        $query->addSelectField($query->fMax($ex_table['sort_order']), 'max_sort_order');
        $query->WhereValue($ex_table['parent_entity'], DB_EQ, $parent_entity);
        $query->WhereAND();
        $query->WhereValue($ex_table['entity_id'], DB_EQ, $entity_id);
        $res = $application->db->getDB_Result($query);
        return $res[0]['max_sort_order'];
    }


    /**
     * Gets the inner variable.
     * This method can be called statically.
     *
     * @param string $var_name - name of required variable
     * @return mixed inner variable value or null
     */
    function __getInnerVar($var_name)
    {
        switch($var_name){
            case "_INNER_ACTIONS":
                return array(
                    "addOptionToEntity"
                   ,"delOptionFromEntity"
                   ,"addValueToOption"
                   ,"delValuesFromOption"
                   ,"updateOptionOfEntity"
                   ,"updateValuesOfOption"
                );
                break;
            case "_OPTION_TYPES":
                return array('SS','MS','CI','UF');
                break;
            case "_SHOW_TYPES":
                return array('DD','RG','MS','CBG','CBSI','CBTA','SI','TA','DFI');
                break;
            case "_MODIFIERS":
                return array('price','weight','shipping_cost','handling_cost');
                break;
            case "_INVENTORY_PER_PAGE":
                return 5;
                break;
            case "_ENTITIES_PRIVILEGES":
                return array(
                    "product" => explode("|","options|crules|inventory")
                   ,"ptype" => explode("|","options|crules")
                );
                break;
            case "_DEFAULT_SETTINGS":
                global $application;
                $message_resources = &$application->getInstance('MessageResources',"product-options-messages", "AdminZone");
                return array(
                    "AAWD" => "Y"
                   ,"CR_FORMULA" => "(true);"
                   ,"AANIC" => "Y"
                   ,"AANIS" => "N"
                   ,"LL_NTF" => 0
                   ,"INV_PER_PAGE" => 10
                   ,"WRN_ONS" => ""
                   ,"WRN_CI_CR" => $message_resources->getMessage("SETTING_WRN_CI_CR_DEFAULT")
                   ,"WRN_CI_INV" => $message_resources->getMessage("SETTING_WRN_CI_INV_DEFAULT")
                   ,"WRN_CI_EXT" => $message_resources->getMessage("SETTING_WRN_CI_EXT_DEFAULT")
                );
                break;
            default:
                return null;
        }
    }


    /**
     * Gets option types.
     * This method can be called statically.
     *
     * @param bool $with_show_types - whether to return with the corresponding show_types
     * @return array (see below for struct)
     */
    function _getOptionsTypes($with_show_types=false)
    {
        if($with_show_types)
        {
            return array(
                'SS' => array('DD','RG')
               ,'MS' => array('MS','CBG')
               ,'CI' => array('CBSI','CBTA','SI','TA')
               ,'UF' => array('DFI')
             );
        }
        else
        {
            return array('SS','MS','CI','UF');
        }
    }

    /**
     * Gets modificator names.
     *
     * @return array of modificator names
     */

    function _getModifiers()
    {
        return $this->__getInnerVar("_MODIFIERS");
    }


    /**
     * Gets the names of entities and their privileges (optionally).
     *
     * @param bool $with_privileges - whether to return privileges for entities or not
     * @return array of names of entities and their privileges
     */
    function _getParentEntities($with_privileges=false)
    {
        if($with_privileges)
            return Product_Options::__getInnerVar("_ENTITIES_PRIVILEGES");
        else
            return array_keys(Product_Options::__getInnerVar("_ENTITIES_PRIVILEGES"));
    }


    /**
     * Checks if the combination enters to the set of all correct partial
     * combiantions, created from the set of all correct combinations of entity
     * option values.
     *
     * @return array of checking result
     */
    function _checkPartialCombination($combination,$parent_entity,$entity_id)
    {
        $options_array=$this->__getOptionsWithValuesAsIDsArray($parent_entity,$entity_id);
        $return=array();

        if(empty($options_array) and empty($combination))
            return $return;

        if(!empty($options_array) and empty($combination))
        {
            $return["empty_combination"]="";
            return $return;
        }

        $checked = array();

        foreach($options_array as $k => $option)
        {
            if(isset($combination[$k]))
            {
                if($option['type']=='SS' and !empty($option['values']))
                {
                    if($combination[$k]==0 and $option['discard']!='Y')
                        $return["invalid_discard"][]=$k;
                    elseif(!in_array($combination[$k],$option['values']) and $combination[$k]!=0)
                        $return["invalid_val"][]=$k;
                }
                elseif($option['type']=='MS')
                {
                    if(!empty($combination[$k]))
                    {
                        $selected_items=array_keys($combination[$k]);
                        for($j=0;$j<count($selected_items);$j++)
                        {
                            if(!in_array($selected_items[$j],$option['values']))
                                $return["invalid_val"][]="$k-$j";
                        };
                    };
                }
                elseif($option['type']=='CI')
                {
                    if(preg_match("/^CB/",$option['show']))
                    {
                        if(!isset($combination[$k]['cb']))
                            $combination[$k]['cb']='';
                        if($combination[$k]['cb']=='on' and $combination[$k]['val']=="")
                            $return["invalid_val"][]=$k;
                    }
                    else
                    {
                        if($combination[$k]['val']=="")
                            $return["invalid_val"][]=$k;
                    };
                };
                $checked[]=$k;
            };
        };

        if($checked!=array_keys($combination))
            $return["unnecessary"]=array_diff(array_keys($combination),$checked);

        return $return;
    }


    /**
     * Serializes the combination.
     *
     * @example
     * <php_code>
     * $combination = array(
     *  11 => 3
     * ,12 => 2
     * ,13 => array(
     *          7 => on
     *         ,8 => on
     *      )
     * ,14 => array(
     *          9 => on
     *         ,6 => on
     *      )
     * );
     * echo "Serialized: ".modApiFunc("Product_Options","_serialize_combination",$combination);
     * </php_code>
     * <output>
     *  Serialized: [11]{3}[12]{2}[13]{{7}{8}}[14]{{9}{6}}
     * </output>
     *
     * @param array $combination - array describing the combination
     * @return string
     */
    function _serialize_combination($combination)
    {
        $string = "";
        foreach($combination as $oid => $odata)
        {
            if(is_numeric($odata))
            {
                $string.="[".$oid."]{".$odata."}";
            }
            elseif(is_array($odata) and !empty($odata) and !isset($odata['val']))
            {
                $string.="[".$oid."]{{".implode("}{",array_keys($odata))."}}";
            };
        };
        return $string;
    }

    /**
     * Unserializes the combination.
     *
     * @example
     * <php_code>
     * $serialized = "[11]{3}[12]{2}[13]{{7}{8}}[14]{{9}{6}}";
     * echo "Unserialized: ".print_r(modApiFunc("Product_Options","_serialize_combination",$combination),true);
     * </php_code>
     * <output>
     * Unserialized:
     * Array(
     *  11 => 3
     * ,12 => 2
     * ,13 => array(
     *          7 => on
     *         ,8 => on
     *      )
     * ,14 => array(
     *          9 => on
     *         ,6 => on
     *      )
     * );
     * </output>
     *
     * @param string $string - serialized combination
     * @return array describing the combination
     */
    function _unserialize_combination($string)
    {
        $combination = array();
        preg_match_all("/\[(\d+)\]{([\d{}]*)}/",$string,$matches,PREG_SET_ORDER);
        for($i=0;$i<count($matches);$i++)
        {
            if(!strstr($matches[$i][2],"{"))
                $combination[$matches[$i][1]]=$matches[$i][2];
            else
            {
                $matches[$i][2]=_ml_substr($matches[$i][2],1,-1);
                $combination[$matches[$i][1]]=array_map(array("Product_Options","__val2on"),array_flip(explode("}{",$matches[$i][2])));
            }
        };
        return $combination;
    }


    /**
     * Deletes combination rules by the content type.
     *
     * @param string $by_type - enum('option_id','value_id')
     * @param int $by_id - ID of the thing which is directed while deleting
     * @return true if it is deleted, false otherwise
     */
    function __delCRulesBy($by_type,$by_id)
    {
        if(!in_array($by_type,array('option_id','value_id')) or !is_numeric($by_id))
            return false;

        global $application;
        $tables=$this->getTables();
        $crs_table=$tables['po_crules']['columns'];

        $query = new DB_Delete('po_crules');
        switch($by_type)
        {
            case "option_id":
                $query->WhereValue($crs_table['sside'], DB_LIKE, '%['.$by_id.']%');
                $query->WhereOR();
                $query->WhereValue($crs_table['lside'], DB_LIKE, '%['.$by_id.']%');
                $query->WhereOR();
                $query->WhereValue($crs_table['rside'], DB_LIKE, '%['.$by_id.']%');
                break;
            case "value_id":
                $query->WhereValue($crs_table['sside'], DB_LIKE, '%{'.$by_id.'}%');
                $query->WhereOR();
                $query->WhereValue($crs_table['lside'], DB_LIKE, '%{'.$by_id.'}%');
                $query->WhereOR();
                $query->WhereValue($crs_table['rside'], DB_LIKE, '%{'.$by_id.'}%');
                break;
        };
        $application->db->PrepareSQL($query);
        return $application->db->DB_Exec();
    }

    function __delInventoryBy($by_type,$by_id)
    {
        if(!in_array($by_type,array('option_id','value_id')) or !is_numeric($by_id))
            return false;

        global $application;
        $tables=$this->getTables();
        $_table=$tables['po_inventory']['columns'];

        $query = new DB_Delete('po_inventory');
        switch($by_type)
        {
            case "option_id":
                $query->WhereValue($_table['combination'], DB_LIKE, '%['.$by_id.']%');
                break;
            case "value_id":
                $query->WhereValue($_table['combination'], DB_LIKE, '%{'.$by_id.'}%');
                break;
        };
        $application->db->PrepareSQL($query);
        return $application->db->DB_Exec();
    }


    /**
     * Returns the page "on".
     * This method is used as callback in the method _unserialize_combination.
     */
    function __val2on($a)
    {
        return "on";
    }

    /**
     * Converts the transfer map of option IDs and their values to the
     * arrays for str_replace.
     * It is used to copy exceptions/existents and Inventory from one product
     * to another after copying all options.
     *
     * @param array $transfer_map - array of the type
     * array(
     * 'options' => array (
     *  '$old_option_id_01' => '$new_option_id_01'
     *  .....
     *  '$old_option_id_NN' => '$new_option_id_NN'
     * ),
     * 'values' => array (
     *  '$old_value_id_01' => '$new_value_id_01'
     *  .....
     *  '$old_value_id_NN' => '$new_value_id_NN'
     * )
     * )
     * @return array(
     *  '[$old_option_id_01]' => '[$new_option_id_01]'
     * ....
     *  '[$old_option_id_NN]' => '[$new_option_id_NN]'
     *  '{$old_value_id_01}' => '{$new_value_id_01}'
     * ....
     *  '{$old_value_id_NN}' => '{$new_value_id_NN}'
     * )
     */
    function __convTMtoSR($transfer_map)
    {
        $from_opts = array_map(array($this,"__oid2cmbEl"),array_keys($transfer_map['options']));
        $to_opts = array_map(array($this,"__oid2cmbEl"),array_values($transfer_map['options']));

        $from_vals = array_map(array($this,"__vid2cmbEl"),array_keys($transfer_map['values']));
        $to_vals = array_map(array($this,"__vid2cmbEl"),array_values($transfer_map['values']));

        $sr = array(
            'options' => asc_array_combine($from_opts,$to_opts)
           ,'values' => asc_array_combine($from_vals,$to_vals)
        );

        return array_merge($sr['options'],$sr['values']);
    }


    /**
     * It is used as the callback in the method __convTMtoSR.
     */
    function __oid2cmbEl($a)
    {
        return "[".$a."]";
    }


    /**
     * It is used as the callback in the method __convTMtoSR.
     */
    function __vid2cmbEl($a)
    {
        return "{".$a."}";
    }


    /**
     * Checks if options are used during InventoryTracking.
     *
     * @param array $oids - index array of option IDs
     * @return bool; true if at least one option is used, false no option is used
     */
    function __isUsedForIT($oids)
    {
        global $application;
        $tables=$this->getTables();
        $options_table=$tables['po_options']['columns'];

        $query = new DB_Select();
        $query->addSelectField($options_table['use_for_it'], 'use_for_it');
        $query->addSelectField($query->fCount('*'), 'uit_cnt');
        $query->Where($options_table['option_id'], DB_IN, "('".implode("','",$oids)."')");
        $query->SelectGroup($options_table['use_for_it']);
        $res = $application->db->getDB_Result($query);
        for($i=0;$i<count($res);$i++)
        {
            if($res[$i]["use_for_it"]=="Y" and $res[$i]["uit_cnt"]>0)
            {
               return true;
            };
        };
        return false;
    }


    /**
     * Clears the table Inventory Tracking for one product.
     * (If necessary, it can be rewritten for clearing the table Inventory
     * Tracking for several products).
     *
     * @param string $parent_entity - entity name
     * @param int $entity_id - ID of the entity the table should be cleared for.
     * @return bool; true if the table is cleared, false otherwise
     */
    function __clearITForEntity($parent_entity,$entity_id)
    {
        global $application;
        $tables=$this->getTables();
        $it_table=$tables['po_inventory']['columns'];

        $query = new DB_Delete('po_inventory');
        $query->WhereValue($it_table['parent_entity'], DB_EQ, $parent_entity);
        $query->WhereAND();
        $query->WhereValue($it_table['entity_id'], DB_EQ, $entity_id);
        $application->db->PrepareSQL($query);
        if($application->db->DB_Exec())
        {
//            $this->updateOptionsSettingsForEntity($parent_entity,$entity_id,array('IT_ACTUAL'=>'Y'));
            return true;
        }
        else
            return false;
    }


    /**
     * Generates all possible combinations of option values.
     *
     * @param array of int $options_ids - IDs of options the combinations should be generated for
     * @return array of combinations
     */
    function __genAllCombinationsByOptionsIDs($options_ids)
    {
        global $application;
        $tables=$this->getTables();

        $options_with_info=array();
        for($i=0;$i<count($options_ids);$i++)
        {
            $oinf=$this->getOptionInfo($options_ids[$i],true);
            $options_with_info[$oinf['option_id']]=array(
                'type' => $oinf['option_type']
               ,'values' => array()
            );
            for($j=0;$j<count($oinf['values']);$j++)
                $options_with_info[$oinf['option_id']]['values'][]=$oinf['values'][$j]['value_id'];
        };

        $commands=array();
        foreach($options_with_info as $oid => $oinf)
        {
            $commands[]=array(
                'leader' => $oid
               ,'players' => $this->__getCmbPlayersByShortOptionInfo($oinf)
            );
        };

        $combinations=array();

        foreach($commands as $command_key => $command)
        {
            // command displays its players in turn.
            $command_combinations=array();
            for($ci=0;$ci<count($command['players']);$ci++)
            {
                if(is_array($command['players'][$ci]))
                    $command['players'][$ci]=array_map(array(&$this,"__val2on"),array_flip($command['players'][$ci]));

                $command_combinations[]=array($command['leader']=>$command['players'][$ci]);
            };

            if(empty($combinations))
            {
                $combinations=$command_combinations;
            }
            else
            {
                $new_combinations=array();
                for($k=0;$k<count($combinations);$k++)
                {
                    for($ki=0;$ki<count($command_combinations);$ki++)
                    {
                        $tmp_combination=$combinations[$k];
                        $l = array_shift(array_keys($command_combinations[$ki]));
                        $tmp_combination[$l]=$command_combinations[$ki][$l];
                        $new_combinations[]=$tmp_combination;
                    }
                };
                $combinations=$new_combinations;
            };
        };

        return $combinations;
    }


    /**
     * Returns all possible generation players of all possible combinations of
     * option values for one option by short option info.
     *
     * @param $inf - array(
     *  'type' => enum('SS','MS')
     * ,'values' => array of value IDs
     * )
     */
    function __getCmbPlayersByShortOptionInfo($oinf)
    {
        if($oinf['type']=='SS')
            return $oinf['values'];
        elseif($oinf['type']=='MS')
        {
            $players = array();
            $players_count = pow(2,count($oinf['values']));
            for($i=0;$i<$players_count;$i++)
            {
                $tmp_player=array();
                $msk = 1;
                for($j=0;$j<count($oinf['values']);$j++)
                {
                    if(($i & $msk)!=0)
                        $tmp_player[]=$oinf['values'][$j];
                    $msk = $msk << 1;
                };
                $players[]=$tmp_player;
            };
            return $players;
        };
    }

    function __hasEntityPrivilegesFor($entity_name,$privileges_space)
    {
        $entities_privileges = $this->_getParentEntities(true);
        return in_array($privileges_space,$entities_privileges[$entity_name]);
    }

    function _getDefaultSettings()
    {
        return $this->__getInnerVar("_DEFAULT_SETTINGS");
    }

    /**
     * @developers only!
     */
    function __tmp_logic_filter($formula,$parent_entity,$entity_id)
    {
        $options=$this->getOptionsList($parent_entity,$entity_id,NOT_CUSTOM_INPUT);

        $oids=array();
        for($i=0;$i<count($options);$i++)
            $oids[]=$options[$i]['option_id'];
        $all_cmbs=$this->__genAllCombinationsByOptionsIDs($oids);

        $logic_filter_result = array('correct'=>array(),'incorrect'=>array());

        foreach($all_cmbs as $cmb)
        {
            $vids = array();
            foreach($cmb as $vdata)
                if(is_numeric($vdata))
                    $vids[]=$vdata;
                elseif(is_array($vdata) and !empty($vdata))
                    $vids=array_merge($vids,array_keys($vdata));

            $sss = array_map(array($this,"__vid2cmbEl"),$vids);

            $for_eval = str_replace($sss,'true',$formula);
            $for_eval = preg_replace("/\{\d+\}/","false",$for_eval);

            //echo print_r(microtime(),true)."\n";
            eval('$eval_res='.$for_eval);
            //echo print_r(microtime(),true)."\n-----------\n";
            if($eval_res)
                $logic_filter_result['correct'][]=$cmb;
            else
                $logic_filter_result['incorrect'][]=$cmb;
        };

        return $logic_filter_result;
	}

    function __logic_calc($formula,$combination)
    {
        $vids = array();
        foreach($combination as $vdata)
            if(is_numeric($vdata))
                $vids[]=$vdata;
            elseif(is_array($vdata) and !empty($vdata))
                $vids=array_merge($vids,array_keys($vdata));

        $sss = array_map(array($this,"__vid2cmbEl"),$vids);

        $for_eval = str_replace($sss,'true',$formula);
        $for_eval = preg_replace("/\{\d+\}/","false",$for_eval);
        //echo print_r(microtime(),true)."\n";
        eval('$eval_res='.$for_eval);
        //echo print_r(microtime(),true)."\n-----------\n";
        return $eval_res;
    }

    function _convSides2Combinations($sides)
    {
        $combinations=array();
        foreach($sides as $side_name => $side_content)
        {
            $options=array();
            if($side_content == null)
            {
                $combinations[$side_name]=$options;
                continue;
            };

            for($i=0;$i<count($side_content);$i++)
                $options[$side_content[$i][0]][]=$side_content[$i][1];
            if(!empty($options))
            {
                foreach($options as $oid => $vdata)
                {
                    $options[$oid] = array_map(array(&$this,"__val2on"),array_flip($vdata));
                }
            };
            $combinations[$side_name]=$options;
        }
        return $combinations;
    }

    function __convCmbs2CRFormula($tpl_index,$cmbs)
    {
        switch($tpl_index)
        {
            case "1":
                if(empty($cmbs['sside']))
                {
                    $cr_frm = "!(true)";
                }
                else
                {
                    $els = array();
                    foreach($cmbs['sside'] as $oid => $vdata)
                        $els[]=$this->__vdata2crfEl(1,$vdata);
                    $cr_frm = "!(".implode(" and ",$els).")";
                };
                break;
            case "2":
                if(empty($cmbs['sside']))
                {
                    $cr_frm = "(true)";
                }
                else
                {
                    $els = array();
                    foreach($cmbs['sside'] as $oid => $vdata)
                        $els[]=$this->__vdata2crfEl(2,$vdata);
                    $cr_frm = "(".implode(" and ",$els).$this->__genHiddenCRFrm($cmbs['sside']).")";
                };
                break;
            case "3":
                $l_els = array();
                if(empty($cmbs['lside']))
                {
                    $l_els[]='true';
                }
                else
                {
                    foreach($cmbs['lside'] as $oid => $vdata)
                        $l_els[]=$this->__vdata2crfEl(3,$vdata);
                };
                $r_els = array();
                if(empty($cmbs['rside']))
                {
                    $r_els[]='true';
                }
                else
                {
                    foreach($cmbs['rside'] as $oid => $vdata)
                        $r_els[]=$this->__vdata2crfEl(3,$vdata);
                };
                $cr_frm = "!((".implode(" or ",$l_els).") and (".implode(" or ",$r_els)."))";
                break;
            case "4":
                $l_els = array();
                if(empty($cmbs['lside']))
                {
                    $l_els[]='true';
                }
                else
                {
                    foreach($cmbs['lside'] as $oid => $vdata)
                        $l_els[]=$this->__vdata2crfEl(4,$vdata);
                };
                $r_els = array();
                if(empty($cmbs['rside']))
                {
                    $r_els[]='true';
                }
                else
                {
                    foreach($cmbs['rside'] as $oid => $vdata)
                        $r_els[]=$this->__vdata2crfEl(4,$vdata);
                };
                $cr_frm = "((".implode(" or ",$l_els).") and (".implode(" or ",$r_els)."))";
                break;
           case "5":
                if(empty($cmbs['sside']))
                {
                    $cr_frm = "(true)";
                }
                else
                {
                    $els = array();
                    foreach($cmbs['sside'] as $oid => $vdata)
                    {
                        if(is_array($vdata) and empty($vdata))
                            continue;
                        $els[]=$this->__vdata2crfEl(2,$vdata);
                    }
                    $cr_frm = "(".implode(" and ",$els).$this->__genHiddenCRFrm($cmbs['sside']).$this->__genAdditionalHiddenCRFrmFor5Tpl($cmbs['sside']).")";
                };
                break;
        }
        return $cr_frm;
    }

    function __vdata2crfEl($tpl_index,$vdata)
    {
        if(!is_array($vdata))
            return $this->__vid2cmbEl($vdata);

        if($tpl_index==1 or $tpl_index==2)
            $impl_del = " and ";
        elseif($tpl_index==3 or $tpl_index==4)
            $impl_del = " or ";

        return implode($impl_del,array_map(array(&$this,"__vid2cmbEl"),array_keys($vdata)));
    }

    function __genHiddenCRFrm($cmb)
    {
        $hidden_frms = array();
        foreach($cmb as $oid => $vdata)
        {
            $oinf = $this->getOptionInfo($oid,true);
            if($oinf['option_type']!='MS')
                continue;

            $not_used_values=array();
            foreach($oinf['values'] as $vinfo)
                if(!array_key_exists($vinfo['value_id'],$vdata))
                    $not_used_values[]=$vinfo['value_id'];
            if(!empty($not_used_values))
                $hidden_frms[]="!(".implode(" or ",array_map(array(&$this,"__vid2cmbEl"),$not_used_values)).")";
        };

        if(!empty($hidden_frms))
            return " and ".implode(" and ",$hidden_frms);
        else
            return "";
    }

    function __genAdditionalHiddenCRFrmFor5Tpl($cmb)
    {
        $oinf = $this->getOptionInfo(array_shift(array_keys($cmb)));
        $pe = $oinf['parent_entity'];
        $eid = $oinf['entity_id'];

        $all_opts_for_inv = $this->getOptionsWithValues($pe,$eid,USED_FOR_INV);

        $oids1 = array_keys($cmb);

        $oids2 = array();
        foreach($all_opts_for_inv as $op_inf)
            $oids2[]=$op_inf['option_id'];

        sort($oids1,SORT_NUMERIC);
        sort($oids2,SORT_NUMERIC);

        if($oids1==$oids2)
            return "";

        $need_add_oids = array_diff($oids2,$oids1);

        $frms = array();
        foreach($need_add_oids as $oid)
        {
            $oinf = $this->getOptionInfo($oid,true);
            if($oinf['option_type']=='MS' and !empty($oinf['values']))
            {
                $vids = array();
                for($i=0;$i<count($oinf['values']);$i++)
                    $vids[]=$oinf['values'][$i]['value_id'];
                $frms[]="!(".implode(" or ",array_map(array(&$this,"__vid2cmbEl"),$vids)).")";
            };
        };

        if(!empty($frms))
            return " and (".implode(" and ",$frms).")";
        else
            return "";
    }

    function __rebuildCRulesFormulaForEntity($parent_entity,$entity_id)
    {
        $crules = $this->getCRulesForEntity($parent_entity,$entity_id);
        if(!empty($crules))
        {
            $frms = array('negative'=>array(),'positive'=>array());
            foreach($crules as $crule_info)
            {
                if($crule_info['tpl_index']==1 or $crule_info['tpl_index']==3)
                    $frms['negative'][]=$crule_info['crule_formula'];
                elseif($crule_info['tpl_index']==2 or $crule_info['tpl_index']==4)
                    $frms['positive'][]=$crule_info['crule_formula'];
            };

            $parts = array();
            if(!empty($frms['negative']))
                $parts[] = "(".implode(" and ",$frms['negative']).")";
            if(!empty($frms['positive']))
                $parts[] = "(".implode(" or ",$frms['positive']).")";

            $crules_formula = "(".implode(" and ",$parts).");";
        }
        else
        {
            $def_sets = $this->_getDefaultSettings();
            $crules_formula = $def_sets['CR_FORMULA'];
        }
        $this->updateOptionsSettingsForEntity($parent_entity,$entity_id,array('CR_FORMULA'=>$crules_formula));
    }

    function __normalize_combination($combination)
    {
        if(!is_array($combination) or empty($combination))
            return array();

        $normalized = array();
        foreach($combination as $oid => $vdata)
        {
            $oinf = $this->getOptionInfo($oid);
            if($oinf["option_type"]=="SS" and is_array($vdata))
                $normalized[$oid] = array_shift(array_keys($vdata));
            else
                $normalized[$oid] = $vdata;
        };

        return $normalized;
    }

    function __combination_filter_for_inv($combination)
    {
        if(!is_array($combination) or empty($combination))
            return array();

        global $application;
        $__ret = $combination;

        foreach($__ret as $oid => $vdata)
        {
            $__q = "select use_for_it from ".$application->getAppIni('DB_TABLE_PREFIX')."po_options where option_id='$oid'";
            $res = $application->db->DB_Query($__q);
            $arr = mysqli_fetch_array($res);
            if($arr['use_for_it']!='Y')
                unset($__ret[$oid]);
            unset($__q);
        };

        return $__ret;
    }

    function __moveUploadedFileToUploadsDir($option_id, $opt_path = null)
    {
        global $application;
        if (DIRECTORY_SEPARATOR === "/")
            $dst_dir = str_replace("\\","/",realpath($application->getAppIni('UPLOAD_FILES_DIR')));
        else
            $dst_dir = realpath($application->getAppIni('UPLOAD_FILES_DIR'));

        $i=0;
        do{
            $dst_fpath = $dst_dir.DIRECTORY_SEPARATOR.md5(microtime_float()).(++$i);
        }while(file_exists($dst_fpath));

        @mkdir($dst_fpath);

        if (empty($_FILES) && $opt_path != null) //                                                .                                                        .                                                                       checkOptions
            return $opt_path;

        if (!isset($_FILES['po']['name'][$option_id]))
            return null;

        $dst_result_path = $dst_fpath.DIRECTORY_SEPARATOR.$_FILES['po']['name'][$option_id];

        $upload_result = @copy($_FILES['po']['tmp_name'][$option_id], $dst_result_path);

        if ((file_exists($dst_result_path)) || ($upload_result != false))
		{
			return $dst_result_path;
		}
		else
		{
		    return null;
		}
    }

    function isUploadsDirNotWritable()
    {
        global $application;
        $dir = $application->getAppIni('UPLOAD_FILES_DIR');
        return (!is_dir($dir) or !is_writable($dir));
    }

    function getCombinationsNamesAndQty($pid)
    {
        global $application;

        $page_number = 0;
        $options = modApiFunc("Product_Options", "getOptionsWithValues", 'product', $pid);
        $res_arr = modApiFunc("Product_Options", "getInventoryPage", 'product', $pid, $page_number);
        $inventory_page = $res_arr["page_content"];

        $c = array('min' => 0, 'max' => 0, 'cmb' => array());
        $min = 0;
        $max = 0;
        for($i=0; $i < count($inventory_page); $i++)
        {
            if ($i == 0)
                $min = $inventory_page[$i]['quantity'];

            $cmb_arr = $this->_unserialize_combination($inventory_page[$i]['combination']);
            $c['cmb'][$i] = array('names' => array(), 'qty' => 0);

            foreach($cmb_arr as $oid => $vdata)
            {
                $strings = $this->_ids2strings($options, array($oid, $vdata));
                $c['cmb'][$i]['names'][] = $strings['value_name'];
            }

            $c['cmb'][$i]['qty'] = $inventory_page[$i]['quantity'];
            if ($c['cmb'][$i]['qty'] > $max)
                $max = $c['cmb'][$i]['qty'];
            if ($c['cmb'][$i]['qty'] < $min)
                $min = $c['cmb'][$i]['qty'];
        }

        if ($max == 0)
            $max = 1;

        $c['max'] = $max;
        $c['min'] = $min;
        return $c;
    }

    function _ids2strings($options, $var)
    {
        $return = array('option_name' => '','value_name' => array());
        $oid = $var[0];
        $vid = is_array($var[1]) ? array_keys($var[1]) : array($var[1]);
        foreach($options as $ok => $odata)
        {
            if($odata['option_id'] == $oid)
            {
                $return['option_name'] = $odata['option_name'];
                foreach ($odata['values'] as $vk => $vdata)
                    if (in_array($vdata['value_id'], $vid))
                        $return['value_name'][] = $vdata['value_name'];
                $return['value_name'] = implode(', ' ,$return['value_name']);
                break;
            }
        }
        return $return;
    }

};


?>