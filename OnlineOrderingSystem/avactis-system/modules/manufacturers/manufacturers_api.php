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
 * @package Manufacturers
 * @author Vadim Lyalikov
 *
 */

class Manufacturers
{
    function Manufacturers()
    {
    }

    function install()
    {
        $tables = Manufacturers::getTables();
        $query = new DB_Table_Create($tables);
    }

    function uninstall()
    {
        $query = new DB_Table_Delete(Manufacturers::getTables());
    }

    function getTables()
    {
        static $tables;

        if (is_array($tables))
        {
            return $tables;
        }

        $table='manufacturers';
        $tables[$table]=array();
        $tables[$table]['columns']=array(
            'manufacturer_id'           => $table.'.manufacturer_id'
           ,'manufacturer_name'         => $table.'.manufacturer_name'
           ,'manufacturer_descr'        => $table.'.manufacturer_descr'
           ,'manufacturer_image_id'     => $table.'.manufacturer_image_id'
           ,'manufacturer_site_url'     => $table.'.manufacturer_site_url'
           ,'manufacturer_active'       => $table.'.manufacturer_active'
           ,'sort_order'                => $table.'.sort_order'
        );
        $tables[$table]['types']=array(
            'manufacturer_id'           => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
           ,'manufacturer_name'         => DBQUERY_FIELD_TYPE_CHAR255 .' NOT NULL DEFAULT \'\''
           ,'manufacturer_descr'        => DBQUERY_FIELD_TYPE_LONGTEXT
           ,'manufacturer_image_id'     => DBQUERY_FIELD_TYPE_INT . ' default NULL '
           ,'manufacturer_site_url'     => DBQUERY_FIELD_TYPE_CHAR255 . ' default NULL '
           ,'manufacturer_active'       => DBQUERY_FIELD_BOOLEAN_DEFAULT_TRUE
           ,'sort_order'                => DBQUERY_FIELD_TYPE_INT .' NOT NULL DEFAULT 0'
        );
        $tables[$table]['primary']=array(
            'manufacturer_id'
        );

        global $application;
        return $application->addTablePrefix($tables);
    }

    function addManufacturer($image_id, $manufacturer_name,$manufacturer_site_url, $manufacturer_descr, $manufacturer_status)
    {
        global $application;
        $tables = $this->getTables();
        $mnf_table = $tables['manufacturers']['columns'];

        $query = new DB_Insert('manufacturers');
        $query->addInsertValue($manufacturer_name,$mnf_table['manufacturer_name']);
        $query->addInsertValue($manufacturer_site_url,$mnf_table['manufacturer_site_url']);
        $query->addInsertValue($manufacturer_descr,$mnf_table['manufacturer_descr']);
        $query->addInsertValue($image_id,$mnf_table['manufacturer_image_id']);
        $query->addInsertValue($manufacturer_status, $mnf_table['manufacturer_active']);
        $query->addInsertValue($this->__getMaxSortOrderOfManufacturers()+1,$mnf_table['sort_order']);

        $application->db->PrepareSQL($query);
        $application->db->DB_Exec();

        $file_id = $application->db->DB_Insert_Id();

        modApiFunc('EventsManager','throwEvent','ManufacturerAdded', $file_id);

        return $file_id;
    }

    function delManufacturers($mnf_ids)
    {
        global $application;
        $tables = $this->getTables();
        $mnf_table = $tables['manufacturers']['columns'];

        //
        foreach($mnf_ids as $mnf_id)
        {
        	$mnf_info = modApiFunc("Manufacturers", "getManufacturerInfo", $mnf_id);
        	if($mnf_info['manufacturer_image_id'] !== NULL)
        	{
                modApiFunc('EventsManager','throwEvent','ImageFKRemovedEvent',$mnf_info['manufacturer_image_id']);
        	}
        }

        $query = new DB_Delete('manufacturers');
        $query->Where($mnf_table['manufacturer_id'],DB_IN,'(\''.implode('\',\'',$mnf_ids).'\')');

        $application->db->PrepareSQL($query);
        $application->db->DB_Exec();
        modApiFunc('EventsManager','throwEvent','ManufacturerDeleted', $mnf_ids);

        return;
    }

    function updateManufacturer($ManufacturerID, $ManufacturerImage, $ManufacturerName, $ManufacturerUrl, $ManufacturerDesc, $ManufacturerStatus)
    {
        global $application;
        $tables = $this->getTables();
        $tr = $tables['manufacturers']['columns'];

        $query = new DB_Update('manufacturers');
        $query->addUpdateValue($tr['manufacturer_name'], $ManufacturerName);
        $query->addUpdateValue($tr['manufacturer_descr'], $ManufacturerDesc);
        $query->addUpdateValue($tr['manufacturer_image_id'], $ManufacturerImage);
        $query->addUpdateValue($tr['manufacturer_site_url'], $ManufacturerUrl);
        $query->addUpdateValue($tr['manufacturer_active'], $ManufacturerStatus);
        $query->WhereValue($tr['manufacturer_id'], DB_EQ, $ManufacturerID);
        $application->db->getDB_Result($query);
        modApiFunc('EventsManager','throwEvent','ManufacturerUpdated', $ManufacturerID);
    }

    function getManufacturersList()
    {
        global $application;
        $tables = $this->getTables();
        $file_table = $tables['manufacturers']['columns'];

        $result = execQuery('SELECT_ALL_MANUFACTURERS', array());

        return $result;
    }

    function getManufacturerInfo($mnf_id)
    {
        global $application;

        if($mnf_id == MANUFACTURER_NOT_DEFINED)
        {
            $MessageResources = &$application->getInstance('MessageResources',"manufacturers-messages", "AdminZone");
            $msg = $MessageResources->getMessage('MANUFACTURER_NOT_DEFINED');

        	return array
        	(
        	    'manufacturer_id' => $mnf_id
        	   ,'manufacturer_name' => $msg
        	   ,'manufacturer_active' => DB_TRUE
        	);
        }

        //  The id can't be a string or have a negative value.
        if (empty($mnf_id) || !is_numeric($mnf_id) || $mnf_id <= 0)
        {
            return NULL;
        }

        $mnfs = modApiFunc("Manufacturers", "getManufacturersList");
        foreach($mnfs as $mnf_info)
        {
            if($mnf_info['manufacturer_id'] == $mnf_id)
               return $mnf_info;
        }
        return NULL;
    }

    function getManufacturerProductAttributeValues($b_only_active = true, $without_undefined = false, $return_all = false)
    {
       global $application, $zone;
       $tables = $this->getTables();
       $table = $tables['manufacturers']['columns'];

       $setting_filtering = modApiFunc('Settings', 'getParamValue','CATALOG_NAVIGATION','CTL_NAV_MANUFACTURER_FILTER');
       $setting_nonempty = modApiFunc('Settings', 'getParamValue','CATALOG_NAVIGATION','CTL_NAV_NONEMPTY_MANUCATRURERS');

       $_ids = array();
       if ($return_all == false && $setting_nonempty == "HIDE_EMPTY" && $zone == "CustomerZone")
       {
           $GlobalProductFilterClone = modApiFunc('CProductListFilter','getProductListParamsObject');
           $GlobalProductFilterClone->filter_manufacturer_id_list = null;
           if ($setting_filtering == "WHOLE_CATALOG")
           {
               $GlobalProductFilterClone->select_mode_recursiveness = IN_CATEGORY_RECURSIVELY;
               $GlobalProductFilterClone->category_id = 1;
           }
           $_ids = modApiFunc('Catalog', 'getProductListByFilter', $GlobalProductFilterClone, RETURN_AS_ID_LIST);
       }

       $params = array(
           "setting_filtering" => $setting_filtering,
           "setting_nonempty" => $setting_nonempty,
           "b_only_active" => $b_only_active,
           "return_all" => $return_all,
           "zone" => $zone,
           "_ids" => $_ids
       );

       if ($return_all == true || !empty($_ids) || $zone == "AdminZone"  || $setting_nonempty == "SHOW_EMPTY")
           $res = execQuery('SELECT_MANUFACTURERS', $params);
       else
           $res = array();

       if($without_undefined === false)
       {
           $MessageResources = &$application->getInstance('MessageResources',"manufacturers-messages", "AdminZone");
           $msg = $MessageResources->getMessage('MANUFACTURER_NOT_DEFINED');
           array_unshift($res, array('id' => MANUFACTURER_NOT_DEFINED, 'value' => $msg));
       }
       return $res;
    }

    function __getMaxSortOrderOfManufacturers()
    {
        global $application;
        $tables = $this->getTables();
        $mnf_table = $tables['manufacturers']['columns'];

        $query = new DB_Select();
        $query->addSelectField($query->fMax($mnf_table['sort_order']), 'max_sort_order');
        $res = $application->db->getDB_Result($query);
        return $res[0]['max_sort_order'];
    }

    /**
     * Sets editable promo code ID.
     * :                                     ,                                                .
     *
     */
    function setEditableManufacturerID($epmid)
    {
//        if ($this->isCorrectManufacturerId($epmid))
//        {
            $this->editableManufacturerID = $epmid;
//        }
    }

    function unsetEditableManufacturerID()
    {
        $this->editableManufacturerID=NULL;
    }

    /**
     * Get editable promo code ID.
     *
     * @return integer Editable promo code ID.
     */
    function getEditableManufacturerID()
    {
        return $this->editableManufacturerID;
    }

    /**
     *                            .
     */
    function saveState()
    {
        modApiFunc('Session', 'set', 'editableManufacturerID', $this->editableManufacturerID);
    }

    /**
     *                                          .
     */
    function loadState()
    {
        //                                    promo code
        if(modApiFunc('Session', 'is_Set', 'editableManufacturerID'))
        {
            $this->setEditableManufacturerID(modApiFunc('Session', 'get', 'editableManufacturerID'));
        }
        else
        {
            $this->editableManufacturerID = NULL;
        }
    }

    function setManufacturersSortOrder($params)
    {
        global $application;
        $tables = $this->getTables();
        $mnfs_table = $tables['manufacturers']['columns'];

    	$new_sort_order = 0;
    	foreach($params as $mnf_id)
    	{
            $query = new DB_Update('manufacturers');
            $query->addUpdateValue($mnfs_table['sort_order'],$new_sort_order);
            $query->WhereValue($mnfs_table['manufacturer_id'], DB_EQ, $mnf_id);
            $application->db->PrepareSQL($query);
            $application->db->DB_Exec();

    		$new_sort_order++;
    	}
        modApiFunc('EventsManager','throwEvent','ManufacturerSortOrderUpdated', $params);
    }
};

?>