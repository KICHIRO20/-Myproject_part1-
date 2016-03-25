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
 * @package Bestsellers
 * @author Egor V. Derevyankin
 *
 */

class Bestsellers_API
{
    function Bestsellers_API()
    {}

    function install()
    {
        $query = new DB_Table_Create(Bestsellers_API::getTables());
    }

    function uninstall()
    {
        $query = new DB_Table_Delete(Bestsellers_API::getTables());
    }

    function getTables()
    {
        static $tables;

        if (is_array($tables))
        {
            return $tables;
        };

        $table = 'bs_links';
        $tables[$table] = array(
            'columns'   => array(
                'link_id'       => $table.'.link_id'
               ,'category_id'   => $table.'.category_id'
               ,'bs_id'         => $table.'.bs_id'
               ,'sort_order'    => $table.'.sort_order'
             )
           ,'types'     => array(
                'link_id'       => DBQUERY_FIELD_TYPE_INT.' not null auto_increment'
               ,'category_id'   => DBQUERY_FIELD_TYPE_INT.' not null DEFAULT 0'
               ,'bs_id'         => DBQUERY_FIELD_TYPE_INT.' not null DEFAULT 0'
               ,'sort_order'    => DBQUERY_FIELD_TYPE_INT.' not null DEFAULT 0'
             )
           ,'primary'   => array(
                'link_id'
             )
           ,'indexes'   => array(
                'IDX_cid'               => 'category_id'
               ,'UNIQUE KEY UNQ_cid_bs' => 'category_id, bs_id'
             )
        );

        $table = 'bs_settings';
        $tables[$table] = array(
            'columns'   => array(
                'setting_id'    => $table.'.setting_id'
               ,'category_id'   => $table.'.category_id'
               ,'setting_key'   => $table.'.setting_key'
               ,'setting_value' => $table.'.setting_value'
             )
           ,'types'     => array(
                'setting_id'    => DBQUERY_FIELD_TYPE_INT.' not null auto_increment'
               ,'category_id'   => DBQUERY_FIELD_TYPE_INT.' not null DEFAULT 0'
               ,'setting_key'   => DBQUERY_FIELD_TYPE_CHAR255." not null DEFAULT ''"
               ,'setting_value' => DBQUERY_FIELD_TYPE_CHAR255." not null DEFAULT ''"
             )
           ,'primary'   => array(
                'setting_id'
             )
           ,'indexes'   => array(
                'IDX_cid'                   => 'category_id'
               ,'UNIQUE KEY UNQ_cid_skey'   => 'category_id, setting_key'
             )
        );

        global $application;
        return $application->addTablePrefix($tables);
    }

    function getSettings($category_id)
    {
        $settings = $this->__getDefaultSettings();
        $res = execQuery('SELECT_BESTSELLERS_SETTINGS', array('category_id'=>$category_id));

        for($i=0; $i<count($res); $i++)
        {
            $settings[$res[$i]['setting_key']] = $res[$i]['setting_value'];
        };

        return $settings;
    }

    function updateSettings($category_id, $settings)
    {
        if(!is_array($settings) or empty($settings))
        {
            return;
        };

        $params = array('category_id'=>$category_id);
        foreach($settings as $setting_key => $setting_value)
        {
            $params['setting_key'] = $setting_key;
            $params['setting_value'] = $setting_value;
            execQuery('REPLACE_BESTSELLERS_SETTINGS', $params);
        };

        return;
    }

    function addBSLinkToCategory($category_id, $bs_id)
    {
        $this->addBSLinksToCategory($category_id, array($bs_id));
    }

    function addBSLinksToCategory($category_id, $bs_ids)
    {
        $so = $this->__getMaxBSSortOrderForCategory($category_id);

        $params = array('so'=>$so,
                        'category_id'=>$category_id,
                        'bs_ids'=>$bs_ids);
        return execQuery('MULTIPLE_INSERT_BESTSELLERS_LINKS_TO_CATEGORY', $params);
    }

    function deleteBSLinksFromCategory($category_id, $bs_ids)
    {
        return execQuery('DELETE_BESTSELLER_LINKS_FROM_CATEGORY', array('category_id'=>$category_id, 'bs_ids'=>$bs_ids));
    }

    function deleteAllBSLinksFromCategory($category_id)
    {
        $this->deleteAllBSLinksFromCatgeories(array($category_id));
    }

    function deleteAllBSLinksFromCatgeories($categories_ids)
    {
        return execQuery('DELETE_BESTSELLER_LINKS_BY_CATEGORIES_ID', array('categories_ids'=>$categories_ids));
    }

    function getHardBSLinksForCategory($category_id)
    {
        return $this->getHardBSLinksForCategories(array($category_id));
    }

    function getHardBSLinksForCategories($categories_ids)
    {
        if(!is_array($categories_ids) or empty($categories_ids))
        {
            return array();
        };

        $res = execQuery('SELECT_HARD_BESTSELLERS_LINKS_FOR_CATEGORIES', array('categories_ids' => $categories_ids));
        $bs_ids = array();

        for($i=0; $i<count($res); $i++)
        {
            $bs_ids[] = $res[$i]['bs_id'];
        };

        return $bs_ids;
    }

    function getStatBSLinksForCategory($category_id)
    {
        return $this->getStatBSLinksForCategories(array($category_id));
    }

    function getStatBSLinksForCategories($categories_ids)
    {
        if(!is_array($categories_ids) or empty($categories_ids))
        {
            return array();
        };

        $bs_ids = array();

        foreach($categories_ids as $category_id)
        {
            $sets = $this->getSettings($category_id);
            if($sets['ADD_BS_FROM_STAT'] != 'Y')
                continue;

            $stat_data = modApiFunc('Statistics', 'getProductsSellingStat', $category_id,
                array('begin'=>(time()-$sets['BS_FROM_STAT_PERIOD']), 'end'=>time()),
                $sets['BS_FROM_STAT_COUNT'],
                ($sets['SHOW_RECURSIVELY'] == 'Y' ? STAT_CATEGORY_RECURSIVE : STAT_CATEGORY_THIS_ONLY),
                STAT_PRODUCTS_EXISTS_ONLY);

            for($i=0;$i<count($stat_data);$i++)
            {
                $bs_ids[] = $stat_data[$i]['product_id'];
            };
        };

        return array_values(array_unique($bs_ids));
    }

    function OnProductsWereDeleted($products_ids)
    {
        if(!is_array($products_ids) or empty($products_ids))
        {
            return;
        };
        return execQuery('DELETE_BESTSELLER_LINKS_BY_PRODUCTS_ID',array('products_ids'=>$products_ids));
    }

    function OnCategoriesWereDeleted($categories_ids)
    {
        if(!is_array($categories_ids) or empty($categories_ids))
        {
            return;
        };

        execQuery('DELETE_BESTSELLER_LINKS_BY_CATEGORIES_ID', array('categories_ids'=>$categories_ids));
        execQuery('DELETE_BESTSELLER_SETTINGS_BY_CATEGORIES_ID', array('categories_ids'=>$categories_ids));

        return;
    }

    function __getMaxBSSortOrderForCategory($category_id)
    {
        $res = execQuery('SELECT_MAX_BESTSELLERS_SORT_ORDER_FOR_CATEGORY', array('category_id'=>$category_id));

        if(count($res) == 1)
        {
            return $res[0]['max_so'];
        }
        else
        {
            return 0;
        };
    }

    function __getDefaultSettings()
    {
        return array(
            'ADD_BS_FROM_STAT'    => 'N'
           ,'BS_FROM_STAT_COUNT'  => 5
           ,'BS_FROM_STAT_PERIOD' => (3600 * 24 * 30) // 30 days in seconds
           ,'SHOW_RECURSIVELY'    => 'N'
        );
    }
};

?>