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
 * @package OrdersExport
 * @author Alexey Florinsky
 *
 */
$moduleInfo = array
    (
        'name'          => 'OrdersExportCSV',
        'shortName'     => 'OECSV',
        'groups'        => 'Main',
        'description'   => 'Orders Export to CSV feature',
        'version'       => '0.1.47700',
        'author'        => 'Alexey Florinsky',
        'contact'       => '',
        'systemModule'  => false,
        'mainFile'      => 'orders_export_csv_api.php',
        'resFile'       => 'orders-export-csv-messages',
        'extraAPIFiles' => array(
            'DataFilterOrdersCSV' => 'abstract/orders-export-csv-filter.php',
        ),
        'actions' => array
        (
            'AdminZone' => array(
                'DoOrdersExportCSVAction'   => 'do_orders_export_csv.php'
            ),
        ),

        'hooks' => array
        (
        ),

        'views' => array
        (
            'AdminZone' => array(
               'OrdersExportCSVView'     => 'orders_export_csv_az.php'
            ),
            'CustomerZone' => array(
            )
        )
    );

?>