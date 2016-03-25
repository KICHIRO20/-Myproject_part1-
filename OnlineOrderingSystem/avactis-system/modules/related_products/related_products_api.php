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
 * @package RelatedProducts
 * @author Egor V. Derevyankin
 *
 */

class Related_Products
{
    function Related_Products()
    {}

    function install()
    {
        $query = new DB_Table_Create(Related_Products::getTables());
    }

    function uninstall()
    {
        $query = new DB_Table_Delete(Related_Products::getTables());
    }

    function getTables()
    {
        static $tables;

        if (is_array($tables))
        {
            return $tables;
        };

        $table = 'rp_links';
        $tables[$table] = array(
            'columns'   => array(
                'link_id'       => $table.'.link_id'
               ,'product_id'    => $table.'.product_id'
               ,'rp_id'         => $table.'.rp_id'
               ,'sort_order'    => $table.'.sort_order'
             )
           ,'types'     => array(
                'link_id'       => DBQUERY_FIELD_TYPE_INT.' not null auto_increment'
               ,'product_id'    => DBQUERY_FIELD_TYPE_INT.' not null DEFAULT 0'
               ,'rp_id'         => DBQUERY_FIELD_TYPE_INT.' not null DEFAULT 0'
               ,'sort_order'    => DBQUERY_FIELD_TYPE_INT.' not null DEFAULT 0'
             )
           ,'primary'   => array(
                'link_id'
             )
           ,'indexes'   => array(
                'IDX_pid'               => 'product_id'
               ,'UNIQUE KEY UNQ_pid_rp' => 'product_id, rp_id'
             )
        );

        global $application;
        return $application->addTablePrefix($tables);
    }

    function getSettings()
    {}

    function updateSettings($settings)
    {}

    function addRPLinkToProduct($product_id, $rp_id)
    {
        $this->addRPLinksToProduct($product_id, array($rp_id));
    }

    function addRPLinksToProduct($product_id, $rp_ids)
    {
        global $application;
        loadCoreFile('db_multiple_insert.php');

        $tables = $this->getTables();
        $rp_table = $tables['rp_links']['columns'];

        $so = $this->__getMaxRPSortOrderForProduct($product_id);

        $query = new DB_Multiple_Insert('rp_links');
        $query->setInsertFields(array('product_id','rp_id','sort_order'));
        foreach($rp_ids as $rp_id)
        {
            $i_arr = array(
                'product_id' => $product_id
               ,'rp_id'      => $rp_id
               ,'sort_order' => ++$so
            );
            $query->addInsertValuesArray($i_arr);
        };

        $application->db->PrepareSQL($query);
        return $application->db->DB_Exec();
    }

    function deleteRPLinksFromProduct($product_id, $rp_ids)
    {
        global $application;
        $tables = $this->getTables();
        $rp_table = $tables['rp_links']['columns'];

        $query = new DB_Delete('rp_links');
        $query->WhereValue($rp_table['product_id'], DB_EQ, $product_id);
        $query->WhereAND();
        $query->Where($rp_table['rp_id'], DB_IN, "(".implode(", ",$rp_ids).")");

        $application->db->PrepareSQL($query);
        return $application->db->DB_Exec();
    }

    function deleteAllRPLinksFromProduct($product_id)
    {
        $this->deleteAllRPLinksFromProducts(array($product_id));
    }

    function deleteAllRPLinksFromProducts($products_ids)
    {
        global $application;
        $tables = $this->getTables();
        $rp_table = $tables['rp_links']['columns'];

        $query = new DB_Delete('rp_links');
        $query->Where($rp_table['product_id'], DB_IN, "(".implode(", ",$products_ids).")");

        $application->db->PrepareSQL($query);
        return $application->db->DB_Exec();
    }

    function copyAllRPLinksFromProductToProduct($from_pid, $to_pid)
    {
        $rp_ids = $this->getRPIDsForProduct($from_pid);

        if(!empty($rp_ids))
        {
            $this->addRPLinksToProduct($to_pid, $rp_ids);
        };

        return;
    }

    function OnProductsWereDeleted($products_ids)
    {
        if(!is_array($products_ids) or empty($products_ids))
        {
            return;
        };

        global $application;
        $tables = $this->getTables();
        $rp_table = $tables['rp_links']['columns'];

        $query = new DB_Delete('rp_links');
        $query->Where($rp_table['product_id'], DB_IN, "(".implode(", ",$products_ids).")");
        $query->WhereOR();
        $query->Where($rp_table['rp_id'], DB_IN, "(".implode(", ",$products_ids).")");

        $application->db->PrepareSQL($query);
        return $application->db->DB_Exec();
    }

    function getRPIDsForProduct($product_id)
    {
        $params = array(
            "product_id" => $product_id
        );
        $res = execQuery("SELECT_RELATED_PRODUCTS",$params);
        $rp_ids = array();

        for($i=0; $i<count($res); $i++)
        {
            $rp_ids[] = $res[$i]['rp_id'];
        };

        return $rp_ids;
    }

    function getRPIDsForProducts($products_ids)
    {
        if(!is_array($products_ids) or empty($products_ids))
        {
            return array();
        };

        $params = array(
            "product_id" => $products_ids
        );
        $res = execQuery("SELECT_RELATED_PRODUCTS",$params);

        $rp_ids = array();

        for($i=0; $i<count($res); $i++)
        {
            $rp_ids[] = $res[$i]['rp_id'];
        };

        return $rp_ids;
    }

    function __getMaxRPSortOrderForProduct($product_id)
    {
        global $application;
        $tables = $this->getTables();
        $rp_table = $tables['rp_links']['columns'];

        $query = new DB_Select();
        $query->addSelectTable('rp_links');
        $query->addSelectField($query->fMax($rp_table['sort_order']), 'max_so');
        $query->WhereValue($rp_table['product_id'], DB_EQ, $product_id);

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