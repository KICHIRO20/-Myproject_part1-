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
loadModuleFile('cms/cms_api.php');

// ---------------------------
// Select queries
// ---------------------------

class SELECT_CMS_PAGE_NAMES_BY_PARENT_ID extends DB_Select
{
    function initQuery($params)
    {
        $tables = CMS :: getTables();
        $pt = $tables['cms_pages']['columns'];

        $this -> addSelectField($pt['page_id']);
        $this -> addSelectField($pt['page_index']);
        $this -> setMultiLangAlias('_name', 'cms_pages', $pt['name'],
                                   $pt['page_id'], 'CMS');
        $this -> addSelectField($this -> getMultiLangAlias('_name'), 'name');

        $this -> WhereValue($pt['parent_id'], DB_EQ, $params['parent_id']);
        if (isset($params['zone']) && $params['zone'] == 'CZ')
        {
            $this -> WhereAND();
            $this -> WhereValue($pt['status'], DB_EQ, 'A');
            $this -> WhereAND();
            if (@$params['signed'] == 'Y')
                $this -> Where($pt['availability'], DB_IN,
                               '(\'' . implode('\',\'', array('C', 'R')) . '\')');
            else
                $this -> Where($pt['availability'], DB_IN,
                               '(\'' . implode('\',\'', array('C', 'A')) . '\')');
        }
        $this->SelectOrder( $pt['sort_order'] );
    }
}

class SELECT_CMS_PAGE_PARENT_ID extends DB_Select
{
    function initQuery($params)
    {
        $tables = CMS :: getTables();
        $pt = $tables['cms_pages']['columns'];

        $this -> addSelectField($pt['parent_id']);

        $this -> WhereValue($pt['page_id'], DB_EQ, $params['page_id']);
    }
}

class SELECT_CMS_PAGES_BY_FILTER extends DB_Select
{
    function initQuery($params)
    {
        $tables = CMS :: getTables();
        $pt = $tables['cms_pages']['columns'];

        $this -> addSelectField($pt['page_id']);
        $this -> addSelectField($pt['page_index']);
        $this -> addSelectField($pt['parent_id']);
        $this -> setMultiLangAlias('_name', 'cms_pages', $pt['name'],
                                   $pt['page_id'], 'CMS');
        $this -> addSelectField($this -> getMultiLangAlias('_name'), 'name');
        $this -> setMultiLangAlias('_descr', 'cms_pages', $pt['descr'],
                                   $pt['page_id'], 'CMS');
        $this -> addSelectField($this -> getMultiLangAlias('_descr'), 'descr');
        $this -> addSelectField($pt['status']);
        $this -> setMultiLangAlias('_seo_prefix', 'cms_pages',
                                   $pt['seo_prefix'], $pt['page_id'], 'CMS');
        $this -> addSelectField($this -> getMultiLangAlias('_seo_prefix'),
                                'seo_prefix');
        $this -> setMultiLangAlias('_seo_title', 'cms_pages',
                                   $pt['seo_title'], $pt['page_id'], 'CMS');
        $this -> addSelectField($this -> getMultiLangAlias('_seo_title'),
                                'seo_title');
        $this -> setMultiLangAlias('_seo_descr', 'cms_pages',
                                   $pt['seo_descr'], $pt['page_id'], 'CMS');
        $this -> addSelectField($this -> getMultiLangAlias('_seo_descr'),
                                'seo_descr');
        $this -> setMultiLangAlias('_seo_keywords', 'cms_pages',
                                   $pt['seo_keywords'], $pt['page_id'], 'CMS');
        $this -> addSelectField($this -> getMultiLangAlias('_seo_keywords'),
                                'seo_keywords');
        $this -> addSelectField($pt['availability']);

        $where = array();

        if (isset($params['page_id']))
        {
            if (isset($params['page_index']))
                $where[] = array(
                    array($pt['page_id'], DB_EQ,
                          $this -> DBAddSlashes($params['page_id'])),
                    array($pt['page_index'], DB_EQ,
                          $this -> DBAddSlashes($params['page_index']))
                );
            else
                $where[] = array($pt['page_id'], DB_EQ,
                                 $this -> DBAddSlashes($params['page_id']));
        }
        elseif (isset($params['page_index']))
        {
            $where[] = array($pt['page_index'], DB_EQ,
                             $this -> DBAddSlashes($params['page_index']));
            $where[] = array($pt['page_id'], DB_NEQ,
                             $this -> DBAddSlashes(@$params['excl_id']));
        }

        if (isset($params['parent_id']) && $params['parent_id'] !== '')
            $where[] = array($pt['parent_id'], DB_EQ,
                             $this -> DBAddSlashes($params['parent_id']));

        if (isset($params['name']) && $params['name'])
            $where[] = array(
                array($this -> getMultiLangAlias('_name'), DB_LIKE,
                      '%' . $this -> DBAddSlashes($params['name']) . '%'),
                array($this -> getMultiLangAlias('_descr'), DB_LIKE,
                      '%' . $this -> DBAddSlashes($params['name']) . '%')
            );

        if (isset($params['status']) && $params['status'])
            $where[] = array($pt['status'], DB_EQ,
                             $this -> DBAddSlashes($params['status']));

        if (isset($params['availability']) && $params['availability'])
            $where[] = array($pt['availability'], DB_EQ,
                             $this -> DBAddSlashes($params['availability']));

        if (!empty($where))
            foreach($where as $k => $v)
            {
                if ($k > 0)
                    $this -> WhereAND();
                if (!is_array($v[0]))
                {
                    $this -> WhereValue($v[0], $v[1], $v[2]);
                }
                else
                {
                    $this -> addWhereOpenSection();
                    foreach($v as $kk => $vv)
                    {
                        if ($kk > 0)
                            $this -> WhereOR();
                        $this -> WhereValue($vv[0], $vv[1], $vv[2]);
                    }
                    $this -> addWhereCloseSection();
                }
            }

        if (isset($params['sort_order']))
            $this -> SelectOrder($pt[$params['sort_order']], 'ASC');
        else
            $this -> SelectOrder($pt['sort_order'], 'ASC');

        $this -> SelectOrder($pt['parent_id'], 'ASC');
        $this -> SelectOrder($pt['sort_order'], 'ASC');
        $this -> SelectOrder($pt['page_id'], 'ASC');

        if (isset($params['paginator']) && is_array($params['paginator']))
        {
            list($offset, $count) = $params['paginator'];
            $this -> SelectLimit($offset, $count);
        }
        elseif (isset($params['limit']) && is_array($params['limit']))
        {
            list($offset, $count) = $params['limit'];
            $this -> SelectLimit($offset, $count);
        }
    }
}

class SELECT_CMS_PAGE_LAST_ORDER extends DB_Select
{
    function initQuery($params)
    {
        $tables = CMS :: getTables();
        $pt = $tables['cms_pages']['columns'];

        $this -> addSelectTable('cms_pages');
        $this -> addSelectValue('MAX(' . $pt['sort_order'] . ')', 'number');
    }
}

class SELECT_CMS_MENU_LIST extends DB_Select
{
    function initQuery($params)
    {
        $tables = CMS :: getTables();
        $mt = $tables['cms_menu']['columns'];
        $mit = $tables['cms_menu_items']['columns'];

        $this -> addSelectField($mt['menu_id']);
        $this -> addSelectField($mt['menu_index']);
        $this -> setMultiLangAlias('_name', 'cms_menu', $mt['menu_name'],
                                   $mt['menu_id'], 'CMS');
        $this -> addSelectField($this -> getMultiLangAlias('_name'),
                                'menu_name');
        $this -> addSelectField($mt['template']);

        $this -> addLeftJoin('cms_menu_items', $mt['menu_id'], DB_EQ,
                             $mit['menu_id']);

        $this -> addSelectValue('SUM(IF(' . $mit['item_status'] . DB_EQ .
                                '\'' . CMS_MENU_ITEM_STATUS_ACTIVE . '\',1,0))',
                                'active_links');

        $this -> addSelectValue('SUM(IF(' . $mit['item_status'] . DB_EQ .
                                '\'' . CMS_MENU_ITEM_STATUS_INACTIVE . '\',1,0))',
                                'inactive_links');

        if (isset($params['menu_id']))
        {
            $this -> WhereValue($mt['menu_id'], DB_EQ, $params['menu_id']);
            if (isset($params['menu_index']))
            {
                $this -> WhereOR();
                $this -> WhereValue($mt['menu_index'], DB_EQ,
                                    $params['menu_index']);
            }
        }
        elseif (isset($params['menu_index']))
        {
            $this -> WhereValue($mt['menu_index'], DB_EQ,
                                $params['menu_index']);
            $this -> WhereAND();
            $this -> WhereValue($mit['menu_id'], DB_NEQ, @$params['excl_id']);
        }

        $this -> SelectGroup($mt['menu_id']);

        $this -> SelectOrder($mt['menu_id']);

        if (isset($params['paginator']) && is_array($params['paginator']))
        {
            list($offset, $count) = $params['paginator'];
            $this -> SelectLimit($offset, $count);
        }
        elseif (isset($params['limit']) && is_array($params['limit']))
        {
            list($offset, $count) = $params['limit'];
            $this -> SelectLimit($offset, $count);
        }
    }
}

class SELECT_CMS_MENU_ITEMS extends DB_Select
{
    function initQuery($params)
    {
        $tables = CMS :: getTables();
        $mit = $tables['cms_menu_items']['columns'];

        $this -> addSelectField($mit['menu_item_id']);
        $this -> addSelectField($mit['menu_id']);
        $this -> setMultiLangAlias('_name', 'cms_menu_items',
                                   $mit['item_name'], $mit['menu_item_id'],
                                   'CMS');
        $this -> addSelectField($this -> getMultiLangAlias('_name'),
                                'item_name');
        $this -> addSelectField($mit['item_type']);
        $this -> addSelectField($mit['item_link']);
        $this -> addSelectField($mit['item_status']);
        $this -> addSelectField($mit['param1']);
        $this -> addSelectField($mit['param2']);
        $this -> addSelectField($mit['sort_order']);

        $this -> WhereValue($mit['menu_id'], DB_EQ, $params['menu_id']);
        if (isset($params['item_status']))
        {
            $this -> WhereAND();
            $this -> WhereValue($mit['item_status'], DB_EQ,
                                $params['item_status']);
        }

        $this -> SelectOrder($mit['sort_order']);
    }
}

class SELECT_CMS_MENU_ITEM_LAST_ORDER extends DB_Select
{
    function initQuery($params)
    {
        $tables = CMS :: getTables();
        $mit = $tables['cms_menu_items']['columns'];

        $this -> addSelectTable('cms_menu_items');
        $this -> addSelectValue('MAX(' . $mit['sort_order'] . ')', 'number');

        if (isset($params['menu_id']))
            $this -> WhereValue($mit['menu_id'], DB_EQ, $params['menu_id']);
    }
}

// ---------------------------
// Update queries
// ---------------------------

class UPDATE_CMS_PAGE_DATA extends DB_Update
{
    function UPDATE_CMS_PAGE_DATA()
    {
        parent :: DB_Update('cms_pages');
    }

    function initQuery($params)
    {
        $tables = CMS :: getTables();
        $pt = $tables['cms_pages']['columns'];

        if (isset($params['page_index']))
            $this -> addUpdateValue($pt['page_index'], $params['page_index']);

        if (isset($params['parent_id']))
            $this -> addUpdateValue($pt['parent_id'], $params['parent_id']);

        if (isset($params['name']))
            $this -> addMultiLangUpdateValue($pt['name'], $params['name'],
                                             $pt['page_id'],
                                             @$params['page_id'], 'CMS');

        if (isset($params['descr']))
            $this -> addMultiLangUpdateValue($pt['descr'], $params['descr'],
                                             $pt['page_id'],
                                             @$params['page_id'], 'CMS');

        if (isset($params['status']))
            $this -> addUpdateValue($pt['status'], $params['status']);

        if (isset($params['seo_prefix']))
            $this -> addMultiLangUpdateValue($pt['seo_prefix'],
                                             $params['seo_prefix'],
                                             $pt['page_id'],
                                             @$params['page_id'], 'CMS');

        if (isset($params['seo_title']))
            $this -> addMultiLangUpdateValue($pt['seo_title'],
                                             $params['seo_title'],
                                             $pt['page_id'],
                                             @$params['page_id'], 'CMS');

        if (isset($params['seo_descr']))
            $this -> addMultiLangUpdateValue($pt['seo_descr'],
                                             $params['seo_descr'],
                                             $pt['page_id'],
                                             @$params['page_id'], 'CMS');

        if (isset($params['seo_keywords']))
            $this -> addMultiLangUpdateValue($pt['seo_keywords'],
                                             $params['seo_keywords'],
                                             $pt['page_id'],
                                             @$params['page_id'], 'CMS');

        if (isset($params['availability']))
            $this -> addUpdateValue($pt['availability'],
                                    $params['availability']);

        if (isset($params['sort_order']))
            $this -> addUpdateValue($pt['sort_order'],
                                    $params['sort_order']);

        $this -> WhereValue($pt['page_id'], DB_EQ, @$params['page_id']);
    }
}

class UPDATE_CMS_PARENT_PAGE_BY_PAGE_IDS extends DB_Update
{
    function UPDATE_CMS_PARENT_PAGE_BY_PAGE_IDS()
    {
        parent :: DB_Update('cms_pages');
    }

    function initQuery($params)
    {
        $tables = CMS :: getTables();
        $pt = $tables['cms_pages']['columns'];

        $this -> addUpdateValue($pt['parent_id'], $params['parent_id']);

        $this -> Where($pt['page_id'], DB_IN,
                       '(\'' . implode('\',\'', $params['page_ids']) . '\')');
    }
}

class UPDATE_CMS_MENU_DATA extends DB_Update
{
    function UPDATE_CMS_MENU_DATA()
    {
        parent :: DB_Update('cms_menu');
    }

    function initQuery($params)
    {
        $tables = CMS :: getTables();
        $mt = $tables['cms_menu']['columns'];

        if (isset($params['menu_index']))
            $this -> addUpdateValue($mt['menu_index'], $params['menu_index']);

        if (isset($params['template']))
            $this -> addUpdateValue($mt['template'], $params['template']);

        if (isset($params['menu_name']))
            $this -> addMultiLangUpdateValue($mt['menu_name'],
                                             $params['menu_name'],
                                             $mt['menu_id'],
                                             @$params['menu_id'], 'CMS');

        $this -> WhereValue($mt['menu_id'], DB_EQ, @$params['menu_id']);
    }
}

class UPDATE_CMS_MENU_ITEM_DATA extends DB_Update
{
    function UPDATE_CMS_MENU_ITEM_DATA()
    {
        parent :: DB_Update('cms_menu_items');
    }

    function initQuery($params)
    {
        $tables = CMS :: getTables();
        $mit = $tables['cms_menu_items']['columns'];

        if (isset($params['item_link']))
            $this -> addUpdateValue($mit['item_link'], $params['item_link']);
        if (isset($params['item_type']))
            $this -> addUpdateValue($mit['item_type'], $params['item_type']);
        if (isset($params['item_status']))
            $this -> addUpdateValue($mit['item_status'],
                                    $params['item_status']);
        if (isset($params['param1']))
            $this -> addUpdateValue($mit['param1'], $params['param1']);
        if (isset($params['param2']))
            $this -> addUpdateValue($mit['param2'], $params['param2']);
        if (isset($params['item_name']))
            $this -> addMultiLangUpdateValue($mit['item_name'],
                                             $params['item_name'],
                                             $mit['menu_item_id'],
                                             @$params['menu_item_id'], 'CMS');
        if (isset($params['sort_order']))
            $this -> addUpdateValue($mit['sort_order'], $params['sort_order']);

        $this -> WhereValue($mit['menu_item_id'], DB_EQ,
                            @$params['menu_item_id']);
    }
}

// ---------------------------
// Insert queries
// ---------------------------

class INSERT_CMS_NEW_PAGE extends DB_Insert
{
    function INSERT_CMS_NEW_PAGE()
    {
        parent :: DB_Insert('cms_pages');
    }

    function initQuery($params)
    {
        $tables = CMS :: getTables();
        $pt = $tables['cms_pages']['columns'];

        $this -> addInsertValue($params['page_index'], $pt['page_index']);
        $this -> addMultiLangInsertValue($params['name'], $pt['name'],
                                         $pt['page_id'], 'CMS');

        $this -> addInsertValue($params['parent_id'], $pt['parent_id']);

        $this -> addMultiLangInsertValue($params['descr'], $pt['descr'],
                                             $pt['page_id'], 'CMS');

        $this -> addInsertValue($params['status'], $pt['status']);

        $this -> addMultiLangInsertValue($params['seo_prefix'], $pt['seo_prefix'],
                                         $pt['page_id'], 'CMS');

        $this -> addMultiLangInsertValue($params['seo_title'], $pt['seo_title'],
                                         $pt['page_id'], 'CMS');

        $this -> addMultiLangInsertValue($params['seo_descr'], $pt['seo_descr'],
                                         $pt['page_id'], 'CMS');

        $this -> addMultiLangInsertValue($params['seo_keywords'],
                                         $pt['seo_keywords'],
                                         $pt['page_id'], 'CMS');

        $this -> addInsertValue($params['availability'], $pt['availability']);

        $this -> addInsertValue($params['sort_order'], $pt['sort_order']);
    }
}

class INSERT_CMS_NEW_MENU extends DB_Insert
{
    function INSERT_CMS_NEW_MENU()
    {
        parent :: DB_Insert('cms_menu');
    }

    function initQuery($params)
    {
        $tables = CMS :: getTables();
        $mt = $tables['cms_menu']['columns'];

        $this -> addInsertValue($params['menu_index'], $mt['menu_index']);
        $this -> addMultiLangInsertValue($params['menu_name'],
                                         $mt['menu_name'],
                                         $mt['menu_id'], 'CMS');

        if (isset($params['template']))
            $this -> addInsertValue($params['template'], $mt['template']);
    }
}

class INSERT_CMS_NEW_MENU_ITEM extends DB_Insert
{
    function INSERT_CMS_NEW_MENU_ITEM()
    {
        parent :: DB_Insert('cms_menu_items');
    }

    function initQuery($params)
    {
        $tables = CMS :: getTables();
        $mit = $tables['cms_menu_items']['columns'];

        $this -> addInsertValue($params['menu_id'], $mit['menu_id']);
        $this -> addMultiLangInsertValue($params['item_name'],
                                         $mit['item_name'],
                                         $mit['menu_item_id'], 'CMS');
        $this -> addInsertValue($params['item_type'], $mit['item_type']);
        $this -> addInsertValue($params['item_link'], $mit['item_link']);
        $this -> addInsertValue($params['item_status'], $mit['item_status']);
        $this -> addInsertValue($params['param1'], $mit['param1']);
        $this -> addInsertValue($params['param2'], $mit['param2']);
        $this -> addInsertValue($params['sort_order'], $mit['sort_order']);
    }
}

// ---------------------------
// Delete queries
// ---------------------------

class DELETE_CMS_PAGE extends DB_Delete
{
    function DELETE_CMS_PAGE()
    {
        parent :: DB_Delete('cms_pages');
    }

    function initQuery($params)
    {
        $tables = CMS :: getTables();
        $pt = $tables['cms_pages']['columns'];

        $this -> deleteMultiLangField($pt['name'], $pt['page_id'], 'CMS');
        $this -> deleteMultiLangField($pt['descr'], $pt['page_id'], 'CMS');
        $this -> deleteMultiLangField($pt['seo_prefix'], $pt['page_id'], 'CMS');
        $this -> deleteMultiLangField($pt['seo_title'], $pt['page_id'], 'CMS');
        $this -> deleteMultiLangField($pt['seo_descr'], $pt['page_id'], 'CMS');
        $this -> deleteMultiLangField($pt['seo_keywords'], $pt['page_id'],
                                      'CMS');

        $this -> WhereValue($pt['page_id'], DB_EQ, $params['page_id']);
    }
}

class DELETE_CMS_MENU_ITEMS extends DB_Delete
{
    function DELETE_CMS_MENU_ITEMS()
    {
        parent :: DB_Delete('cms_menu_items');
    }

    function initQuery($params)
    {
        $tables = CMS :: getTables();
        $mit = $tables['cms_menu_items']['columns'];

        $this -> deleteMultiLangField($mit['item_name'],
                                      $mit['menu_item_id'], 'CMS');

        if (isset($params['ids']))
        {
            if (!is_array($params['ids']))
                $params['ids'] = array($params['ids']);

            $this -> Where($mit['menu_item_id'], DB_IN, '(\'' .
                           join('\',\'', $params['ids']) . '\')');
        }
        else
        {
            $this -> WhereValue($mit['menu_id'], DB_EQ, @$params['menu_id']);
        }
    }
}

class DELETE_CMS_MENU extends DB_Delete
{
    function DELETE_CMS_MENU()
    {
        parent :: DB_Delete('cms_menu');
    }

    function initQuery($params)
    {
        $tables = CMS :: getTables();
        $mt = $tables['cms_menu']['columns'];

        $this -> deleteMultiLangField($mt['menu_name'],
                                      $mt['menu_id'], 'CMS');

        $this -> WhereValue($mt['menu_id'], DB_EQ, @$params['menu_id']);
    }
}

?>