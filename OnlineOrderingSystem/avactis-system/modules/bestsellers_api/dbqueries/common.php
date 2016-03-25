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

class SELECT_BESTSELLERS_SETTINGS extends DB_Select
{
    function initQuery($params)
    {
        $tables = Bestsellers_API::getTables();
        $sets_table = $tables['bs_settings']['columns'];

        $this->addSelectTable('bs_settings');
        $this->addSelectField($sets_table['setting_key'], 'setting_key');
        $this->addSelectField($sets_table['setting_value'], 'setting_value');
        $this->WhereValue($sets_table['category_id'], DB_EQ, $params['category_id']);
    }
}

class SELECT_HARD_BESTSELLERS_LINKS_FOR_CATEGORIES extends DB_Select
{
    function initQuery($params)
    {
        $categories_ids = $params['categories_ids'];

        $tables = Bestsellers_API::getTables();
        $bs_table = $tables['bs_links']['columns'];

        $this->addSelectTable('bs_links');
        $this->addSelectField($bs_table['bs_id'], 'bs_id');
        $this->Where($bs_table['category_id'], DB_IN, "(".implode(", ",$categories_ids).")");
        $this->SelectGroup($bs_table['bs_id']);
        $this->SelectOrder($bs_table['sort_order']);
    }
}

class SELECT_MAX_BESTSELLERS_SORT_ORDER_FOR_CATEGORY extends DB_Select
{
    function initQuery($params)
    {
        $category_id = $params['category_id'];

        $tables = Bestsellers_API::getTables();
        $bs_table = $tables['bs_links']['columns'];

        $this->addSelectTable('bs_links');
        $this->addSelectField($this->fMax($bs_table['sort_order']), 'max_so');
        $this->WhereValue($bs_table['category_id'], DB_EQ, $category_id);
    }
}

class REPLACE_BESTSELLERS_SETTINGS extends DB_Replace
{
    function REPLACE_BESTSELLERS_SETTINGS()
    {
        parent::DB_Replace('bs_settings');
    }

    function initQuery($params)
    {
        $category_id = $params['category_id'];
        $setting_key = $params['setting_key'];
        $setting_value = $params['setting_value'];

        $tables = Bestsellers_API::getTables();
        $sets_table = $tables['bs_settings']['columns'];

        $this->addReplaceValue($category_id, $sets_table['category_id']);
        $this->addReplaceValue($setting_key, $sets_table['setting_key']);
        $this->addReplaceValue($setting_value, $sets_table['setting_value']);
    }
}


class DELETE_BESTSELLER_LINKS_BY_CATEGORIES_ID extends DB_Delete
{
    function DELETE_BESTSELLER_LINKS_BY_CATEGORIES_ID()
    {
        parent::DB_Delete('bs_links');
    }

    function initQuery($params)
    {
        $categories_ids = $params['categories_ids'];

        $tables = Bestsellers_API::getTables();
        $bs_table = $tables['bs_links']['columns'];

        $this->Where($bs_table['category_id'], DB_IN, "(".implode(", ",$categories_ids).")");
    }
}

class DELETE_BESTSELLER_SETTINGS_BY_CATEGORIES_ID extends DB_Delete
{
    function DELETE_BESTSELLER_SETTINGS_BY_CATEGORIES_ID()
    {
        parent::DB_Delete('bs_settings');
    }

    function initQuery($params)
    {
        $categories_ids = $params['categories_ids'];

        $tables = Bestsellers_API::getTables();
        $sets_table = $tables['bs_settings']['columns'];

        $this->Where($sets_table['category_id'], DB_IN, "(".implode(", ",$categories_ids).")");
    }
}

class DELETE_BESTSELLER_LINKS_BY_PRODUCTS_ID extends DB_Delete
{
    function DELETE_BESTSELLER_LINKS_BY_PRODUCTS_ID()
    {
        parent::DB_Delete('bs_links');
    }

    function initQuery($params)
    {
        $products_ids = $params['products_ids'];

        $tables = Bestsellers_API::getTables();
        $bs_table = $tables['bs_links']['columns'];

        $this->Where($bs_table['bs_id'], DB_IN, "(".implode(", ",$products_ids).")");
    }
}

class DELETE_BESTSELLER_LINKS_FROM_CATEGORY extends DB_Delete
{
    function DELETE_BESTSELLER_LINKS_FROM_CATEGORY()
    {
        parent::DB_Delete('bs_links');
    }

    function initQuery($params)
    {
        $category_id = $params['category_id'];
        $bs_ids = $params['bs_ids'];

        $tables = Bestsellers_API::getTables();
        $bs_table = $tables['bs_links']['columns'];

        $this->WhereValue($bs_table['category_id'], DB_EQ, $category_id);
        $this->WhereAND();
        $this->Where($bs_table['bs_id'], DB_IN, "(".implode(", ",$bs_ids).")");
    }
}

//
//                       ,
//
class MULTIPLE_INSERT_BESTSELLERS_LINKS_TO_CATEGORY extends DB_Multiple_Insert
{
    function MULTIPLE_INSERT_BESTSELLERS_LINKS_TO_CATEGORY()
    {
        parent::DB_Multiple_Insert('bs_links');
    }

    function initQuery($params)
    {
        $bs_ids = $params['bs_ids'];
        $category_id = $params['category_id'];
        $so = $params['so'];

        $this->setInsertFields(array('category_id','bs_id','sort_order'));
        foreach($bs_ids as $bs_id)
        {
            $i_arr = array(
                'category_id' => $category_id
               ,'bs_id'       => $bs_id
               ,'sort_order'  => ++$so
            );
            $this->addInsertValuesArray($i_arr);
        };
    }
}

?>