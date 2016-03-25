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
 * @package FeaturedProducts
 * @author Egor V. Derevyankin
 *
 */

class Featured_Products
{
    function Featured_Products()
    {}

    function install()
    {
        $query = new DB_Table_Create(Featured_Products::getTables());
    }

    function uninstall()
    {
        $query = new DB_Table_Delete(Featured_Products::getTables());
    }

    function getTables()
    {
        static $tables;

        if (is_array($tables))
        {
            return $tables;
        };

        $table = 'fp_links';
        $tables[$table] = array(
            'columns'   => array(
                'link_id'       => $table.'.link_id'
               ,'category_id'   => $table.'.category_id'
               ,'fp_id'         => $table.'.fp_id'
               ,'sort_order'    => $table.'.sort_order'
             )
           ,'types'     => array(
                'link_id'       => DBQUERY_FIELD_TYPE_INT.' not null auto_increment'
               ,'category_id'   => DBQUERY_FIELD_TYPE_INT.' not null DEFAULT 0'
               ,'fp_id'         => DBQUERY_FIELD_TYPE_INT.' not null DEFAULT 0'
               ,'sort_order'    => DBQUERY_FIELD_TYPE_INT.' not null DEFAULT 0'
             )
           ,'primary'   => array(
                'link_id'
             )
           ,'indexes'   => array(
                'IDX_cid'               => 'category_id'
               ,'UNIQUE KEY UNQ_cid_fp' => 'category_id, fp_id'
             )
        );

        global $application;
        return $application->addTablePrefix($tables);
    }

    function addFPLinkToCategory($category_id, $fp_id)
    {
        $this->addFPLinksToCategory($category_id, array($fp_id));
    }

    function addFPLinksToCategory($category_id, $fp_ids)
    {
        global $application;
        loadCoreFile('db_multiple_insert.php');

        $so = $this->__getMaxFPSortOrderForCategory($category_id);

        $query = new DB_Multiple_Insert('fp_links');
        $query->setInsertFields(array('category_id','fp_id','sort_order'));
        foreach($fp_ids as $fp_id)
        {
            $i_arr = array(
                'category_id' => $category_id
               ,'fp_id'       => $fp_id
               ,'sort_order'  => ++$so
            );
            $query->addInsertValuesArray($i_arr);
        };

        $application->db->PrepareSQL($query);
        return $application->db->DB_Exec();
    }

    function deleteFPLinksFromCategory($category_id, $fp_ids)
    {
        global $application;
        $tables = $this->getTables();
        $fp_table = $tables['fp_links']['columns'];

        $query = new DB_Delete('fp_links');
        $query->WhereValue($fp_table['category_id'], DB_EQ, $category_id);
        $query->WhereAND();
        $query->Where($fp_table['fp_id'], DB_IN, "(".implode(", ",$fp_ids).")");

        $application->db->PrepareSQL($query);
        return $application->db->DB_Exec();
    }

    function deleteAllFPLinksFromCategory($category_id)
    {
        $this->deleteAllFPLinksFromCatgeories(array($category_id));
    }

    function deleteAllFPLinksFromCatgeories($categories_ids)
    {
        global $application;
        $tables = $this->getTables();
        $fp_table = $tables['fp_links']['columns'];

        $query = new DB_Delete('fp_links');
        $query->Where($fp_table['category_id'], DB_IN, "(".implode(", ",$categories_ids).")");

        $application->db->PrepareSQL($query);
        return $application->db->DB_Exec();
    }

    function OnProductsWereDeleted($products_ids)
    {
        if(!is_array($products_ids) or empty($products_ids))
        {
            return;
        };

        global $application;
        $tables = $this->getTables();
        $fp_table = $tables['fp_links']['columns'];

        $query = new DB_Delete('fp_links');
        $query->Where($fp_table['fp_id'], DB_IN, "(".implode(", ",$products_ids).")");

        $application->db->PrepareSQL($query);
        return $application->db->DB_Exec();
    }

    function OnCategoriesWereDeleted($categories_ids)
    {
        if(!is_array($categories_ids) or empty($categories_ids))
        {
            return;
        };

        global $application;
        $tables = $this->getTables();
        $fp_table = $tables['fp_links']['columns'];

        $query = new DB_Delete('fp_links');
        $query->Where($fp_table['category_id'], DB_IN, "(".implode(", ",$categories_ids).")");

        $application->db->PrepareSQL($query);
        return $application->db->DB_Exec();
    }

    function getFPIDsForCategory($category_id)
    {
        return $this->getFPIDsForCategories(array($category_id));
    }

    function getFPIDsForCategories($categories_ids)
    {
        if(!is_array($categories_ids) or empty($categories_ids))
        {
            return array();
        };

        $params = array(
            "categories_ids" => $categories_ids
        );
        $res = execQuery("SELECT_FEATURED_PRODUCTS",$params);
        $fp_ids = array();

        for($i=0; $i<count($res); $i++)
        {
            $fp_ids[] = $res[$i]['fp_id'];
        };

        return $fp_ids;
    }

    function __getMaxFPSortOrderForCategory($category_id)
    {
        global $application;
        $tables = $this->getTables();
        $fp_table = $tables['fp_links']['columns'];

        $query = new DB_Select();
        $query->addSelectTable('fp_links');
        $query->addSelectField($query->fMax($fp_table['sort_order']), 'max_so');
        $query->WhereValue($fp_table['category_id'], DB_EQ, $category_id);

        $res = $application->db->getDB_Result($query);

        if(count($res) == 1)
        {
            return $res[0]['max_so'];
        }
        else
        {
            return 0;
        };
    }

};

?>