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

class SELECT_ENTITIES_WHICH_USE_INVENTORY_TRACKING extends DB_Select
{
    function initQuery($params)
    {
        $parent_entity = $params['parent_entity'];

        $tables = Product_Options::getTables();
        $c = $tables['po_options']['columns'];

        $this->addSelectField($c['entity_id'], 'entity_id');
        $this->WhereValue($c['parent_entity'], DB_EQ, $parent_entity);
        $this->WhereAND();
        $this->WhereValue($c['use_for_it'], DB_EQ, 'Y');
    }
}

class SELECT_ENTITY_OPTIONS_LIST extends DB_Select
{
    function initQuery($params)
    {
        $parent_entity = $params['parent_entity'];
        $entity_id = $params['entity_id'];
        $flags = $params['flags'];

        $tables = Product_Options::getTables();
        $options = $tables['po_options']['columns'];

        $this->addSelectTable('po_options');
        foreach($options as $k => $v)
        if (in_array($k, array('option_name', 'display_name', 'display_descr')))
        {
            $this->setMultiLangAlias('_' . $k, 'po_options', $v, $options['option_id'], 'Product_Options');
            $this->addSelectField($this->getMultiLangAlias('_' . $k), $k);
        }
        else
        {
            $this->addSelectField($v);
        }
        $this->WhereValue($options['parent_entity'], DB_EQ, $parent_entity);
        $this->WhereAND();
        $this->WhereValue($options['entity_id'], DB_EQ, $entity_id);
        if(($flags & USED_FOR_INV) != 0)
        {
            $this->WhereAND();
            $this->WhereValue($options['use_for_it'], DB_EQ, 'Y');
        };
        if(($flags & NOT_CUSTOM_INPUT) != 0)
        {
            $this->WhereAND();
            $this->WhereValue($options['option_type'], DB_NEQ, 'CI');
        };
        if(($flags & NOT_UPLOAD_FILE) != 0)
        {
            $this->WhereAND();
            $this->WhereValue($options['option_type'], DB_NEQ, 'UF');
        };
        if(($flags & NOT_USED_FOR_INV) != 0)
        {
            $this->WhereAND();
            $this->WhereValue($options['use_for_it'], DB_NEQ, 'Y');
        };
        $this->SelectOrder($options['sort_order'], 'ASC');
    }
}

class SELECT_PRODUCT_OPTION_VALUES extends DB_Select
{
     function initQuery($params)
     {
        $tables=Product_Options::getTables();
        $values=$tables['po_options_values']['columns'];

        $option_id = $params['option_id'];

        $this->addSelectTable('po_options_values');
        foreach($values as $k => $v)
        if ($k == 'value_name')
        {
            $this->setMultiLangAlias('_value_name', 'po_options_values', $v, $values['value_id'], 'Product_Options');
            $this->addSelectField($this->getMultiLangAlias('_value_name'), 'value_name');
        }
        else
        {
            $this->addSelectField($v);
        }
        $this->WhereValue($values['option_id'], DB_EQ, $option_id);
        $this->SelectOrder($values['sort_order'], 'ASC');

     }

}

class SELECT_PRODUCT_OPTIONS_INVENTORY extends DB_Select
{
    function initQuery($params)
    {
        $tables = Product_Options::getTables();
        $it_table = $tables['po_inventory']['columns'];

        $this->addSelectTable('po_inventory');
        $this->addSelectField('*');
        $this->WhereValue($it_table['parent_entity'], DB_EQ, $params['parent_entity']);
        $this->WhereAND();
        $this->WhereValue($it_table['entity_id'], DB_EQ, $params['entity_id']);

    }
}

class SELECT_OPTIONS_SETTINGS_FOR_ENTITY extends DB_Select
{
    function initQuery($params)
    {
        $tables = Product_Options::getTables();
        $settings = $tables['po_settings']['columns'];

        $this->addSelectTable('po_settings');
        $this->addSelectField('*');
        $this->WhereValue($settings['parent_entity'], DB_EQ, $params['parent_entity']);
        $this->WhereAND();
        $this->WhereValue($settings['entity_id'], DB_EQ, $params['entity_id']);
    }
}

?>