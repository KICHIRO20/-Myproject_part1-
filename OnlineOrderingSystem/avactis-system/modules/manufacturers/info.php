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

$moduleInfo = array
    (
        'name'         => 'Manufacturers',
        'shortName'    => 'MNF',
        'groups'       => 'Main',
        'description'  => 'Manufacturers module',
        'version'      => '0.1.47700',
        'author'       => 'Vadim Lyalikov',
        'contact'      => '',
        'systemModule'  => false,
        'mainFile'     => 'manufacturers_api.php',
        'constantsFile' => 'const.php',
        'resFile'      => 'manufacturers-messages',

        'actions' => array
        (
           'AdminZone' => array(
                'add_manufacturer' => 'add_manufacturer.php'
               ,'update_manufacturer' => 'update_manufacturer.php'
               ,'del_manufacturers' => 'del_manufacturers.php'
               ,'set_editable_manufacturer' => 'set_editable_manufacturer_action.php'
               ,'SaveSortedManufacturers' => 'save_sorted_manufacturers_action.php'
           ),
           'update_manufacturers_sort_order' => 'update_manufacturers_sort_order.php'
        ),

        'views' => array
        (
            'AdminZone' => array(
                'ManufacturersList' => 'manufacturers_list_az.php'
               ,'AddManufacturer' => 'add_manufacturer_az.php'
               ,'EditManufacturer' => 'edit_manufacturer_az.php'
               ,'SortManufacturers' => 'sort_manufacturers_az.php'
            ),
            'CustomerZone' => array(
                'ManufacturersFilter' => 'manufacturers_filter_cz.php'
               ,'ManufacturerInfo' => 'manufacturer_info_cz.php'
            ),
            'Aliases' => array(
                'ManufacturersDropDownFilter' => 'ManufacturersFilter'
            )
        )
    );

?>