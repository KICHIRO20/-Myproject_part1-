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
 * @package Statistics
 * @author Egor V. Derevyankin
 *
 */

class Statistics
{
    function Statistics()
    {}

    function install()
    {
        $query = new DB_Table_Create(Statistics::getTables());

        modApiFunc('EventsManager','addEventHandler','ProductsWasSold',__CLASS__,'OnProductsWasSold');
    }

    function uninstall()
    {
        $query = new DB_Table_Delete(Statistics::getTables());

        modApiFunc('EventsManager','removeEventHandler','ProductsWasSold',__CLASS__,'OnProductsWasSold');
    }

    function getTables()
    {
        static $tables;

        if (is_array($tables))
        {
            return $tables;
        };

/*
 * 	      	                                                   .

        $table = 'stat_products_sold';
        $tables[$table] = array(
            'columns'   => array(
                'stat_id'       => $table.'.stat_id'
               ,'product_id'    => $table.'.product_id'
               ,'time'          => $table.'.time'
               ,'quantity'      => $table.'.quantity'
             )
           ,'types'     => array(
                'stat_id'       => DBQUERY_FIELD_TYPE_INT.' not null auto_increment'
               ,'product_id'    => DBQUERY_FIELD_TYPE_INT.' not null'
               ,'time'          => DBQUERY_FIELD_TYPE_INT.' not null'
               ,'quantity'      => DBQUERY_FIELD_TYPE_INT.' not null'
             )
           ,'primary'   => array(
                'stat_id'
             )
           ,'indexes'   => array(
                'IDX_pid'   => 'product_id'
               ,'IDX_time'  => 'time'
             )
        );

        $table = 'stat_products_sold_categories';
        $tables[$table] = array(
            'columns'   => array(
                'record_id'     => $table.'.record_id'
               ,'stat_id'       => $table.'.stat_id'
               ,'category_id'   => $table.'.category_id'
             )
           ,'types'     => array(
                'record_id'     => DBQUERY_FIELD_TYPE_INT.' not null auto_increment'
               ,'stat_id'       => DBQUERY_FIELD_TYPE_INT.' not null'
               ,'category_id'   => DBQUERY_FIELD_TYPE_INT.' not null'
             )
           ,'primary'   => array(
                'record_id'
             )
           ,'indexes'   => array(
                'IDX_sid'   => 'stat_id'
             )
        );
*/

        $table = 'stat_products_sold';
        $tables[$table] = array(
            'columns'   => array(
                'stat_id'        => $table.'.stat_id'
               ,'product_id'     => $table.'.product_id'
               ,'categories_ids' => $table.'.categories_ids'
               ,'time'           => $table.'.time'
               ,'quantity'       => $table.'.quantity'
             )
           ,'types'     => array(
                'stat_id'        => DBQUERY_FIELD_TYPE_INT.' not null auto_increment'
               ,'product_id'     => DBQUERY_FIELD_TYPE_INT.' not null'
               ,'categories_ids' => DBQUERY_FIELD_TYPE_CHAR255.' not null'
               ,'time'           => DBQUERY_FIELD_TYPE_INT.' not null'
               ,'quantity'       => DBQUERY_FIELD_TYPE_INT.' not null'
             )
           ,'primary'   => array(
                'stat_id'
             )
           ,'indexes'   => array(
                'IDX_pid'   => 'product_id'
               ,'IDX_time'  => 'time'
               ,'IDX_cats_ids' => 'categories_ids'
             )
        );

        global $application;
        return $application->addTablePrefix($tables);
    }

    /**
     * @param int $category_id - ID
     * @param array $period = ('begin' => timestamp, 'end' => timestamp) -
     *
     * @param int $limit -                   (
     *                          ,    STAT_NO_LIMIT)
     * @param int $what_category = STAT_CATEGORY_THIS_ONLY ||
     * STAT_CATEGORY_RECURSIVE -
     *
     * @param int $what_products = STAT_PRODUCTS_ALL ||
     * STAT_PRODUCTS_EXISTS_ONLY -                                 ,
     *
     */
    function getProductsSellingStat($category_id, $period, $limit = STAT_NO_LIMIT, $what_category = STAT_CATEGORY_THIS_ONLY, $what_products = STAT_PRODUCTS_EXISTS_ONLY)
    {
        global $application;
        $tables = $this->getTables();
        $ps_table = $tables['stat_products_sold']['columns'];

        $categories_ids = array();
        if($what_category == STAT_CATEGORY_RECURSIVE)
        {
            $categories = modApiFunc('Catalog','getSubcategoriesFullListWithParent',$category_id,false,false);
            foreach($categories as $cat_info)
            {
                $categories_ids[] = $cat_info['id'];
            };
        }
        else
        {
            $categories_ids[] = $category_id;
        };

        $query = new DB_Select();
        $query->addSelectField($ps_table['product_id'], 'product_id');
        $query->addSelectField($query->fSum($ps_table['quantity']), 'sum_quantity');
        $query->addSelectTable('stat_products_sold');
        $query->WhereValue($ps_table['categories_ids'], DB_REGEXP, '[[.vertical-line.]]'.implode('|',$categories_ids).'[[.vertical-line.]]');
        $query->WhereAND();
        $query->Where($ps_table['time'], DB_GTE, $period['begin']);
        $query->WhereAND();
        $query->Where($ps_table['time'], DB_LTE, $period['end']);

        if($what_products == STAT_PRODUCTS_EXISTS_ONLY)
        {
            $catalog_tables = modApiStaticFunc('Catalog','getTables');
            $query->addSelectTable('products');
            $query->WhereAND();
            $query->WhereField($ps_table['product_id'], DB_EQ, $catalog_tables['products']['columns']['id']);
        };

        $query->SelectGroup('product_id');
        $query->SelectOrder('sum_quantity', 'DESC');

        if($limit != STAT_NO_LIMIT)
        {
            $query->SelectLimit(0, $limit);
        };

        return $application->db->getDB_Result($query);
    }

    function OnProductsWasSold($list)
    {
        global $application;
        $tables = $this->getTables();
        $ps_table = $tables['stat_products_sold']['columns'];

        foreach ($list as $product)
        {
            $product_id = $product['PRODUCT_ID'];
            $quantity = $product['PRODUCT_QUANTITY'];

            $prod_obj = new CProductInfo($product_id);
            $categories_ids = $prod_obj->getCategoriesIDs();

            $query = new DB_Insert('stat_products_sold');
            $query->addInsertValue($product_id, $ps_table['product_id']);
            $query->addInsertValue('|'.implode('|',$categories_ids).'|', $ps_table['categories_ids']);
            $query->addInsertValue(time(), $ps_table['time']);
            $query->addInsertValue($quantity, $ps_table['quantity']);

            $application->db->PrepareSQL($query);
            $application->db->DB_Exec();
        }
    }
};

?>