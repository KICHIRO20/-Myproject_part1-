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
 * CMS class
 *
 * Common API class for CMS.
 *
 * @author Sergey Kulitsky
 * @version $Id: cms_api.php xxxx 2009-11-04 13:10:47Z azrael $
 * @package CMS
 */
class CMS
{
    function CMS()
    {
    }

    function install()
    {
    	include_once(dirname(__FILE__)."/includes/install.inc");
    }

    function getTables()
    {
        static $tables;

        if (is_array($tables))
        {
            return $tables;
        }

        $tables = array ();

        $tbl_info = 'cms_pages';
        $tables[$tbl_info] = array();
        $tables[$tbl_info]['columns'] = array
            (
                'page_id'      => $tbl_info . '.page_id',
                'page_index'   => $tbl_info . '.page_index',
                'parent_id'    => $tbl_info . '.parent_id',
                'name'         => $tbl_info . '.name',
                'descr'        => $tbl_info . '.descr',
                'status'       => $tbl_info . '.status',
		'seo_prefix'   => $tbl_info . '.seo_prefix',
                'seo_title'    => $tbl_info . '.seo_title',
                'seo_descr'    => $tbl_info . '.seo_descr',
                'seo_keywords' => $tbl_info . '.seo_keywords',
                'availability' => $tbl_info . '.availability',
                'sort_order'   => $tbl_info . '.sort_order'
            );
        $tables[$tbl_info]['types'] = array
            (
                'page_id'      => DBQUERY_FIELD_TYPE_INT .
                                  ' NOT NULL AUTO_INCREMENT',
                'page_index'   => DBQUERY_FIELD_TYPE_CHAR255 .
                                  ' NOT NULL DEFAULT \'\'',
                'parent_id'    => DBQUERY_FIELD_TYPE_INT .
                                  ' NOT NULL DEFAULT 0',
                'name'         => DBQUERY_FIELD_TYPE_CHAR255,
                'descr'        => DBQUERY_FIELD_TYPE_TEXT,
                'status'       => DBQUERY_FIELD_TYPE_CHAR1 .
                                  ' NOT NULL DEFAULT \'A\'',
                'seo_prefix'    => DBQUERY_FIELD_TYPE_CHAR255,
		'seo_title'    => DBQUERY_FIELD_TYPE_CHAR255,
                'seo_descr'    => DBQUERY_FIELD_TYPE_CHAR255,
                'seo_keywords' => DBQUERY_FIELD_TYPE_CHAR255,
                'availability' => DBQUERY_FIELD_TYPE_CHAR1 .
                                  ' NOT NULL DEFAULT \'A\'',
                'sort_order'   => DBQUERY_FIELD_TYPE_INT .
                                  ' NOT NULL DEFAULT 0'
            );
        $tables[$tbl_info]['primary'] = array
            (
                'page_id'
            );
        $tables[$tbl_info]['indexes'] = array
            (
                'UNIQUE KEY page_index' => 'page_index',
                'parent'                => 'parent_id',
                'status'                => 'status',
                'availability'          => 'availability',
                'name'                  => 'name',
                'sort_order'            => 'sort_order'
            );

        $tbl_info = 'cms_menu';
        $tables[$tbl_info] = array();
        $tables[$tbl_info]['columns'] = array
            (
                'menu_id'    => $tbl_info . '.menu_id',
                'menu_index' => $tbl_info . '.menu_index',
                'menu_name'  => $tbl_info . '.menu_name',
                'template'   => $tbl_info . '.template',
            );
        $tables[$tbl_info]['types'] = array
            (
                'menu_id'    => DBQUERY_FIELD_TYPE_INT .
                                ' NOT NULL AUTO_INCREMENT',
                'menu_index' => DBQUERY_FIELD_TYPE_CHAR255 .
                               ' NOT NULL DEFAULT \'\'',
                'menu_name'  => DBQUERY_FIELD_TYPE_CHAR255 .
                                ' NOT NULL DEFAULT \'\'',
                'template'   => DBQUERY_FIELD_TYPE_CHAR255 .
                                ' NOT NULL DEFAULT \'\'',
            );
        $tables[$tbl_info]['primary'] = array
            (
                'menu_id'
            );
        $tables[$tbl_info]['indexes'] = array
            (
                'UNIQUE KEY menu_index' => 'menu_index'
            );

        $tbl_info = 'cms_menu_items';
        $tables[$tbl_info] = array();
        $tables[$tbl_info]['columns'] = array
            (
                'menu_item_id' => $tbl_info . '.menu_item_id',
                'menu_id'      => $tbl_info . '.menu_id',
                'item_name'    => $tbl_info . '.item_name',
                'item_type'    => $tbl_info . '.item_type',
                'item_link'    => $tbl_info . '.item_link',
                'item_status'  => $tbl_info . '.item_status',
                'param1'       => $tbl_info . '.param1',
                'param2'       => $tbl_info . '.param2',
                'sort_order'   => $tbl_info . '.sort_order'
            );
        $tables[$tbl_info]['types'] = array
            (
                'menu_item_id' => DBQUERY_FIELD_TYPE_INT .
                                  ' NOT NULL AUTO_INCREMENT',
                'menu_id'      => DBQUERY_FIELD_TYPE_INT .
                                  ' NOT NULL DEFAULT 0',
                'item_name'    => DBQUERY_FIELD_TYPE_CHAR255 .
                                  ' NOT NULL DEFAULT \'\'',
                'item_type'    => DBQUERY_FIELD_TYPE_INT .
                                  ' NOT NULL DEFAULT 0',
                'item_link'    => DBQUERY_FIELD_TYPE_TEXT,
                'item_status'  => DBQUERY_FIELD_TYPE_INT .
                                  ' NOT NULL DEFAULT 0',
                'param1'       => DBQUERY_FIELD_TYPE_CHAR255 .
                                  ' NOT NULL DEFAULT \'\'',
                'param2'       => DBQUERY_FIELD_TYPE_CHAR255 .
                                  ' NOT NULL DEFAULT \'\'',
                'sort_order'   => DBQUERY_FIELD_TYPE_INT .
                                  ' NOT NULL DEFAULT 0'
            );
        $tables[$tbl_info]['primary'] = array
            (
                'menu_item_id'
            );
        $tables[$tbl_info]['indexes'] = array
            (
                'menu_id'     => 'menu_id',
                'item_status' => 'item_status',
                'sort_order'  => 'sort_order'
            );

        global $application;
        return $application->addTablePrefix($tables);
    }

    function uninstall()
    {
        $query = new DB_Table_Delete($this -> getTables());
        global $application;
        $application -> db -> getDB_Result($query);
    }

    /**
     * Functions to work with pages
     */

    function searchPages($filter)
    {
        $pages = execQuery('SELECT_CMS_PAGES_BY_FILTER', $filter);

        if (!is_array($pages))
            return array();

        return $pages;
    }

    function searchPgPages($filter, $pg_enable = PAGINATOR_ENABLE)
    {
        if ($pg_enable == PAGINATOR_ENABLE)
            $filter['use_paginator'] = true;

        return execQueryPaginator('SELECT_CMS_PAGES_BY_FILTER', $filter);
    }

    function getPageTree($start_id = 0, $zone = 'AZ', $max_level = 0,
                         $signed = 'N', $suffix = ' ', $level = 0)
    {
        $result = array();

        if ($max_level > 0 && $level >= $max_level)
            return $result;

        $pages = execQuery('SELECT_CMS_PAGE_NAMES_BY_PARENT_ID',
                           array('parent_id' => $start_id,
                                 'zone' => $zone,
                                 'signed' => $signed));

        $level_str = '';
        for($i = 0; $i < $level; $i++)
            $level_str .= $suffix;

        if (is_array($pages))
            foreach($pages as $page)
            {
                $result[] = array('page_id' => $page['page_id'],
                                  'name' => $page['name'],
                                  'level' => $level_str);
                $result = array_merge($result,
                                      $this -> getPageTree($page['page_id'],
                                                           $zone,
                                                           $max_level,
                                                           $signed,
                                                           $suffix,
                                                           $level + 1));
            }

        return $result;
    }

    function getPageInfo($page_id, $zone = 'CZ')
    {
        $page_info = modApiFunc('CMS', 'searchPages',
                                array('page_id' => $page_id,
                                      'page_index' => $page_id));
        if (!$page_info)
            return array();

        $page_info = $page_info[0];

        if ($zone == 'CZ')
        {
            $signed = modApiFunc('Customer_Account', 'getCurrentSignedCustomer');
            if ($signed && $page_info['availability'] == 'A')
                $page_info = array();
            if (!$signed && $page_info['availability'] == 'R')
                $page_info = array();
            if ($page_info['status'] == 'D')
                $page_info = array();
        }

        return $page_info;
    }

    function getPagePath($page_id)
    {
        $result = '';
        do
        {
            $parent = execQuery('SELECT_CMS_PAGE_PARENT_ID',
                                array('page_id' => $page_id));
            if (!$parent)
                return '';

            $result .= $parent[0]['parent_id'] . '/';
            $page_id = $parent[0]['parent_id'];
        }
        while($page_id);
    }

    function updatePagesSortOrder($sort_array)
    {
        if (!is_array($sort_array) || empty($sort_array))
            return;

        foreach($sort_array as $k => $page_id)
            execQuery('UPDATE_CMS_PAGE_DATA',
                      array('page_id' => $page_id, 'sort_order' => $k + 1));
    }

    function deletePage($page_id)
    {
        $parent_id = execQuery('SELECT_CMS_PAGE_PARENT_ID',
                               array('page_id' => $page_id));

        if (!$parent_id)
            return;

        $parent_id = $parent_id[0]['parent_id'];

        $pages = execQuery('SELECT_CMS_PAGE_NAMES_BY_PARENT_ID',
                           array('parent_id' => $page_id));

        $ids = array();
        if (is_array($pages))
            foreach($pages as $v)
                $ids[] = $v['page_id'];

        execQuery('UPDATE_CMS_PARENT_PAGE_BY_PAGE_IDS',
                  array('parent_id' => $parent_id, 'page_ids' => $ids));

        execQuery('DELETE_CMS_PAGE', array('page_id' => $page_id));
    }

    function getPageLastOrderNumber()
    {
        $result = execQuery('SELECT_CMS_PAGE_LAST_ORDER', array());

        if ($result && $result[0]['number'] > 0)
            return $result[0]['number'] + 1;

        return 1;
    }

    /**
     * End of Functions to work with pages
     */

    /**
     * Functions to work with menu
     */

    function searchMenu($filter)
    {
        $menu = execQuery('SELECT_CMS_MENU_LIST', $filter);

        if (!is_array($menu))
            return array();

        return $menu;
    }

    function searchPgMenu($filter, $pg_enable = PAGINATOR_ENABLE)
    {
        if ($pg_enable == PAGINATOR_ENABLE)
            $filter['use_paginator'] = true;

        return execQueryPaginator('SELECT_CMS_MENU_LIST', $filter);
    }

    function getMenuItems($menu_id, $active_only = true)
    {
        $params = array('menu_id' => $menu_id);

        if ($active_only)
            $params['item_status'] = CMS_MENU_ITEM_STATUS_ACTIVE;

        return execQuery('SELECT_CMS_MENU_ITEMS', $params);
    }

    function getMenuLastOrderNumber($menu_id)
    {
        $result = execQuery('SELECT_CMS_MENU_ITEM_LAST_ORDER',
                            array('menu_id' => $menu_id));

        if ($result && $result[0]['number'] > 0)
            return $result[0]['number'] + 1;

        return 1;
    }

    function updateMenuItemsSortOrder($sort_array)
    {
        if (!is_array($sort_array) || empty($sort_array))
            return;

        foreach($sort_array as $k => $menu_item_id)
            execQuery('UPDATE_CMS_MENU_ITEM_DATA',
                      array('menu_item_id' => $menu_item_id,
                            'sort_order' => $k + 1));
    }

    function getSystemPageList()
    {
        global $application;

        $result = array();

        // getting the list of sections
        if (!isset($application -> Configs_array['Layouts']))
            LayoutConfigurationManager :: static_get_cz_layouts_list();

        if (is_array($application -> Configs_array['Layouts']))
            foreach($application -> Configs_array['Layouts'] as $k => $v)
                if ($k != 'Site' && $k != 'Templates')
                    $result[] = $k;

        return $result;
    }

    function deleteMenu($menu_id)
    {
        execQuery('DELETE_CMS_MENU_ITEMS', array('menu_id' => $menu_id));

        execQuery('DELETE_CMS_MENU', array('menu_id' => $menu_id));
    }

    /**
     * End of Functions to work with menu
     */
}

?>