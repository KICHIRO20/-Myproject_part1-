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
class SELECT_LAYOUT_HTTPS_SETTINGS extends DB_Select
{
    function initQuery($params)
    {
        $tables = Configuration::getTables();
        $columns = $tables['layout_https_settings']['columns'];

        $this->addSelectField($columns['id'], 'id');
        $this->addSelectField($columns['layout_full_file_name'], 'layout_full_file_name');

        $this->addSelectField($columns['catalog'], 'catalog');
        $this->addSelectField($columns['cart'], 'cart');
        $this->addSelectField($columns['checkout'], 'checkout');
        $this->addSelectField($columns['download'], 'download');
        $this->addSelectField($columns['customer_data'], 'customer_data');
        $this->addSelectField($columns['customer_login'], 'customer_login');

        $this->addSelectField($columns['whole_cz'], 'whole_cz');
    }
}

class SELECT_SETTINGS_GROUP_BASE_INFO extends DB_Select
{
    function initQuery($params)
    {
        $group_name = $params['group_name'];

        $tables = Configuration::getTables();
        $columns = $tables['settings_groups']['columns'];

        $this->addSelectField($columns['group_name'], 'group_name');
        $this->addSelectField($columns['group_description_id'], 'group_description_id');
        $this->addSelectField($columns['group_visibility'], 'group_visibility');
        $this->WhereValue($columns['group_name'], DB_EQ, $group_name);
    }
}

class SELECT_SETTINGS_GROUP_FULL_INFO extends DB_Select
{
    function initQuery($params)
    {
        $group_name = $params['group_name'];

        $tables = Configuration::getTables();
        $group_columns = $tables['settings_groups']['columns'];
        $descr_columns = $tables['settings_descriptions']['columns'];

        $this->addSelectField($group_columns['group_name'],                   'group_name');
        $this->addSelectField($group_columns['group_visibility'],             'group_visibility');
        $this->addSelectField($descr_columns['name_module_short_name'],       'name_module_short_name');
        $this->addSelectField($descr_columns['name_resource_name'],           'name_resource_name');
        $this->addSelectField($descr_columns['description_module_short_name'],'description_module_short_name');
        $this->addSelectField($descr_columns['description_resource_name'],    'description_resource_name');

        $this->WhereField($group_columns['group_description_id'], DB_EQ, $descr_columns['description_id']);
        $this->WhereAND();
        $this->WhereValue($group_columns['group_name'], DB_EQ, $group_name);
    }
}

class SELECT_SETTINGS_GROUP_LIST extends DB_Select
{
    function initQuery($params)
    {
        $tables = Configuration::getTables();
        $group_columns = $tables['settings_groups']['columns'];
        $descr_columns = $tables['settings_descriptions']['columns'];

        $this->addSelectField($group_columns['group_name'],                   'group_name');
        $this->addSelectField($group_columns['group_visibility'],             'group_visibility');
        $this->addSelectField($descr_columns['name_module_short_name'],       'name_module_short_name');
        $this->addSelectField($descr_columns['name_resource_name'],           'name_resource_name');
        $this->addSelectField($descr_columns['description_module_short_name'],'description_module_short_name');
        $this->addSelectField($descr_columns['description_resource_name'],    'description_resource_name');

        $this->WhereField($group_columns['group_description_id'], DB_EQ, $descr_columns['description_id']);
        $this->WhereAND();
        $this->WhereValue($group_columns['group_visibility'], DB_EQ, "1");
    }
}

class SELECT_SETTINGS_PARAM_VALUE_LIST extends DB_Select
{
    function initQuery($params)
    {
        $group_name = $params['group_name'];
        $param_name = $params['param_name'];

        $tables = Configuration::getTables();
        $columns = $tables['settings_list_values']['columns'];

        $this->addSelectField($columns['param_list_value'],                 'param_list_value');
        $this->addSelectField($columns['param_list_value_description_id'],  'param_list_value_description_id');

        $this->WhereValue($columns['group_name'], DB_EQ, $group_name);
        $this->WhereAND();
        $this->WhereValue($columns['param_name'], DB_EQ, $param_name);
    }
}


class SELECT_SETTINGS_PARAM_BASE_INFO extends DB_Select
{
    function initQuery($params)
    {
        $group_name = $params['group_name'];
        $param_name = $params['param_name'];

        $tables = Configuration::getTables();
        $columns = $tables['settings']['columns'];

        $this->addSelectField($columns['group_name'],           'group_name');
        $this->addSelectField($columns['param_name'],           'param_name');
        $this->addSelectField($columns['param_description_id'], 'param_description_id');
        $this->addSelectField($columns['param_type'],           'param_type');
        $this->addSelectField($columns['param_validator_class'],'param_validator_class');
        $this->addSelectField($columns['param_validator_method'],'param_validator_method');
        $this->addSelectField($columns['param_current_value'],  'param_current_value');
        $this->addSelectField($columns['param_default_value'],  'param_default_value');

        $this->WhereValue($columns['group_name'], DB_EQ, $group_name);
        $this->WhereAND();
        $this->WhereValue($columns['param_name'], DB_EQ, $param_name);
    }
}

class SELECT_ALL_SETTINGS_PARAMS_BASE_INFO extends DB_Select
{
    function initQuery($params)
    {
        $tables = Configuration::getTables();
        $columns = $tables['settings']['columns'];

        $this->addSelectField($columns['group_name'],           'group_name');
        $this->addSelectField($columns['param_name'],           'param_name');
        $this->addSelectField($columns['param_description_id'], 'param_description_id');
        $this->addSelectField($columns['param_type'],           'param_type');
        $this->addSelectField($columns['param_validator_class'],'param_validator_class');
        $this->addSelectField($columns['param_validator_method'],'param_validator_method');
        $this->addSelectField($columns['param_current_value'],  'param_current_value');
        $this->addSelectField($columns['param_default_value'],  'param_default_value');
    }
}

class SELECT_SETTINGS_PARAM_FULL_INFO_LIST_BY_GROUP extends DB_Select
{
    function initQuery($params)
    {
        $group_name = $params['group_name'];

        $tables = Configuration::getTables();
        $settings_columns = $tables['settings']['columns'];
        $descr_columns = $tables['settings_descriptions']['columns'];

        $this->addSelectField($settings_columns['group_name'],            'group_name');
        $this->addSelectField($settings_columns['param_name'],            'param_name');
        $this->addSelectField($settings_columns['param_description_id'],  'param_description_id');
        $this->addSelectField($settings_columns['param_type'],            'param_type');
        $this->addSelectField($settings_columns['param_validator_class'], 'param_validator_class');
        $this->addSelectField($settings_columns['param_validator_method'],'param_validator_method');
        $this->addSelectField($settings_columns['param_current_value'],   'param_current_value');
        $this->addSelectField($settings_columns['param_default_value'],   'param_default_value');

        $this->addSelectField($descr_columns['name_module_short_name'],       'name_module_short_name');
        $this->addSelectField($descr_columns['name_resource_name'],           'name_resource_name');
        $this->addSelectField($descr_columns['description_module_short_name'],'description_module_short_name');
        $this->addSelectField($descr_columns['description_resource_name'],    'description_resource_name');

        $this->WhereValue($settings_columns['group_name'], DB_EQ, $group_name);
        $this->WhereAND();
        $this->WhereField($settings_columns['param_description_id'], DB_EQ, $descr_columns['description_id']);
    }
}


class SELECT_SETTINGS_DESCRIPTION extends DB_Select
{
    function initQuery($params)
    {
        $description_id = $params['description_id'];

        $tables = Configuration::getTables();
        $columns = $tables['settings_descriptions']['columns'];

        $this->addSelectField($columns['description_id'],                   'description_id');
        $this->addSelectField($columns['name_module_short_name'],           'name_module_short_name');
        $this->addSelectField($columns['name_resource_name'],               'name_resource_name');
        $this->addSelectField($columns['description_module_short_name'],    'description_module_short_name');
        $this->addSelectField($columns['description_resource_name'],        'description_resource_name');

        $this->WhereValue($columns['description_id'], DB_EQ, $description_id);
    }
}

class UPDATE_LAYOUT_HTTPS_SETTINGS extends DB_Update
{
    function UPDATE_LAYOUT_HTTPS_SETTINGS()
    {
        parent::DB_Update('layout_https_settings');
    }

    function initQuery($params)
    {
        $tables = Configuration::getTables();
        $columns = $tables['layout_https_settings']['columns'];

        $this->addUpdateValue($columns['catalog'], $params['catalog']);
        $this->addUpdateValue($columns['cart'], $params['cart']);
        $this->addUpdateValue($columns['checkout'], $params['checkout']);
        $this->addUpdateValue($columns['download'], $params['download']);
        $this->addUpdateValue($columns['customer_data'], $params['customer_data']);
        $this->addUpdateValue($columns['customer_login'], $params['customer_login']);

        $this->addUpdateValue($columns['whole_cz'], $params['whole_cz']);

        $this->WhereValue($columns['id'], DB_EQ, $params['id']);
    }
}

class INSERT_LAYOUT_HTTPS_SETTINGS extends DB_Insert
{
    function INSERT_LAYOUT_HTTPS_SETTINGS()
    {
        parent::DB_Insert('layout_https_settings');
    }

    function initQuery($params)
    {
        $tables = Configuration::getTables();
        $columns = $tables['layout_https_settings']['columns'];

        $this->addInsertValue($params['layout_full_file_name'], $columns['layout_full_file_name']);
    }
}

class INSERT_SETTING extends DB_Insert
{
    function INSERT_SETTING()
    {
        parent::DB_Insert('settings');
    }

    function initQuery($params)
    {
        $group_name             = $params['group_name'];
        $param_name             = $params['param_name'];
        $param_description_id   = $params['param_description_id'];
        $param_type             = $params['param_type'];
        $param_validator_class  = $params['param_validator_class'];
        $param_validator_method = $params['param_validator_method'];
        $param_current_value    = $params['param_current_value'];
        $param_default_value    = $params['param_default_value'];

        $tables = Configuration::getTables();
        $columns = $tables['settings']['columns'];

        $this->addInsertValue($group_name,              $columns['group_name']);
        $this->addInsertValue($param_type,              $columns['param_type']);
        $this->addInsertValue($param_description_id,    $columns['param_description_id']);
        $this->addInsertValue($param_name,              $columns['param_name']);
        $this->addInsertValue($param_validator_class,   $columns['param_validator_class']);
        $this->addInsertValue($param_validator_method,  $columns['param_validator_method']);
        $this->addInsertValue($param_current_value,     $columns['param_current_value']);
        $this->addInsertValue($param_default_value,     $columns['param_default_value']);
    }
}

class INSERT_SETTINGS_DESCRIPTION extends DB_Insert
{
    function INSERT_SETTINGS_DESCRIPTION()
    {
        parent::DB_Insert('settings_descriptions');
    }

    function initQuery($params)
    {
        $name_module_short_name          = $params['name_module_short_name'];
        $name_resource_name              = $params['name_resource_name'];
        $description_module_short_name   = $params['description_module_short_name'];
        $description_resource_name       = $params['description_resource_name'];

        $tables = Configuration::getTables();
        $columns = $tables['settings_descriptions']['columns'];

        $this->addInsertValue($name_module_short_name,           $columns['name_module_short_name']);
        $this->addInsertValue($name_resource_name,               $columns['name_resource_name']);
        $this->addInsertValue($description_module_short_name,    $columns['description_module_short_name']);
        $this->addInsertValue($description_resource_name,        $columns['description_resource_name']);
    }
}

class INSERT_SETTINGS_GROUP extends DB_Insert
{
    function INSERT_SETTINGS_GROUP()
    {
        parent::DB_Insert('settings_groups');
    }

    function initQuery($params)
    {
        $group_name             = $params['group_name'];
        $group_description_id   = $params['group_description_id'];
        $group_visibility       = $params['group_visibility'];

        $tables = Configuration::getTables();
        $columns = $tables['settings_groups']['columns'];

        $this->addInsertValue($group_name,              $columns['group_name']);
        $this->addInsertValue($group_description_id,    $columns['group_description_id']);
        $this->addInsertValue($group_visibility,        $columns['group_visibility']);
    }
}

class INSERT_SETTINGS_LIST_VALUE extends DB_Insert
{
    function INSERT_SETTINGS_LIST_VALUE()
    {
        parent::DB_Insert('settings_list_values');
    }

    function initQuery($params)
    {
        $param_name                         = $params['param_name'];
        $group_name                         = $params['group_name'];
        $param_list_value                   = $params['param_list_value'];
        $param_list_value_description_id    = $params['param_list_value_description_id'];

        $tables = Configuration::getTables();
        $columns = $tables['settings_list_values']['columns'];

        $this->addInsertValue($param_name,                          $columns['param_name']);
        $this->addInsertValue($group_name,                          $columns['group_name']);
        $this->addInsertValue($param_list_value,                    $columns['param_list_value']);
        $this->addInsertValue($param_list_value_description_id,     $columns['param_list_value_description_id']);
    }
}

class UPDATE_SETTINGS_PARAM_VALUE extends DB_Update
{
    function UPDATE_SETTINGS_PARAM_VALUE()
    {
        parent::DB_Update('settings');
    }

    function initQuery($params)
    {
        $param_name = $params['param_name'];
        $group_name = $params['group_name'];
        $value      = $params['value'];

        $tables = Configuration::getTables();
        $columns = $tables['settings']['columns'];

        $this->addUpdateValue($columns['param_current_value'], $value);

        $this->WhereValue($columns['param_name'], DB_EQ, $param_name);
        $this->WhereAND();
        $this->WhereValue($columns['group_name'], DB_EQ, $group_name);
    }
}

class SELECT_STORE_SETTINGS extends DB_Select
{
    function initQuery()
    {
        $tables = Configuration::getTables();
        $columns = $tables['store_settings']['columns'];

        $this->addSelectField($columns['type'], 'type');
        $this->addSelectField($columns['name'], 'name');
        $this->addSelectField($columns['value'],'value');
    }
}

class SELECT_MAIL_SETTINGS extends DB_Select
{
    function initQuery()
    {
        $tables = Configuration::getTables();
        $columns = $tables['mail_settings']['columns'];

        $this->addSelectField($columns['name'], 'name');
        $this->addSelectField($columns['value'],'value');
    }
}

class UPDATE_MAIL_SETTINGS extends DB_Update
{
    function UPDATE_MAIL_SETTINGS()
    {
        parent::DB_Update('mail_settings');
    }

    function initQuery($params)
    {
        $name  = $params['name'];
        $value = $params['value'];

        $tables = Configuration::getTables();
        $columns = $tables['mail_settings']['columns'];

        $this->addUpdateValue($columns['value'], $value);

        $this->WhereValue($columns['name'], DB_EQ, $name);
    }
}

class SELECT_CREDIT_CARD_ATTRIBUTES_BY_CC_TYPE extends DB_Select
{
    function initQuery($params)
    {
        $tables = Configuration::getTables();
        $columns = $tables['credit_card_attributes_to_types']['columns'];

        $this->addSelectField($columns['type'], 'type');
        $this->addSelectField($columns['attr'], 'attr');
        $this->addSelectField($columns['visible'], 'visible');
        $this->addSelectField($columns['required'], 'required');

        $this->WhereValue($columns['type'], DB_EQ, $params['type']);
    }
}


class INSERT_CREDIT_CARD_ATTRIBUTE extends DB_Insert
{
    function INSERT_CREDIT_CARD_ATTRIBUTE()
    {
        parent::DB_Insert('credit_card_attributes_to_types');
    }

    function initQuery($params)
    {
        $tables = Configuration::getTables();
        $columns = $tables['credit_card_attributes_to_types']['columns'];

        $this->addInsertValue($params['type'], $columns['type']);
        $this->addInsertValue($params['attr'], $columns['attr']);
        $this->addInsertValue($params['visible'], $columns['visible']);
        $this->addInsertValue($params['required'], $columns['required']);
    }
}

// Option Table Query

class SELECT_OPTION_VALUE extends DB_Select
{
    function initQuery($params)
    {
       $option = $params['opt_name'];

       $tables = Configuration::getTables();
       $tr = $tables['options']['columns'];
       $this->addSelectTable('options');
       $this->addSelectField('option_value');
       $this->WhereValue($tr['option_name'], DB_EQ, $option);

    }
}
class SELECT_ALL_OPTIONS extends DB_Select
{

  function initQuery()
    {

       $tables = Configuration::getTables();
       $tr = $tables['options']['columns'];
       $this->addSelectTable('options');
       $this->addSelectField('option_name');
       $this->addSelectField('option_value');

    }
}
?>