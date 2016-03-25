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
 * @package QuickBooks
 * @author Egor V. Derevyankin
 *
 */
$moduleInfo = array
    (
        'name'          => 'Quick_Books',
        'shortName'     => 'QB',
        'groups'        => 'Main',
        'description'   => 'Quick Books integration module',
        'version'       => '0.1.47700',
        'author'        => 'Egor V. Derevyankin',
        'contact'       => '',
        'systemModule'  => false,
        'mainFile'      => 'quick_books_api.php',
        'resFile'       => 'quick-books-messages',
        'extraAPIFiles' => array(
            'DataWriterOrdersIIF'    => 'abstract/data_writer_orders_iif.php'
        ),
        'actions' => array
        (
            'AdminZone' => array(
                'update_qb_settings' => 'update_qb_settings.php'
               ,'do_orders_export'   => 'do_orders_export.php'
           ),
        ),

        'hooks' => array
        (
        ),

        'views' => array
        (
            'AdminZone' => array(
                'QB_Settings'   => 'qb_settings_az.php'
               ,'QB_Export'     => 'qb_export_az.php'
            ),
            'CustomerZone' => array(
            )
        )
    );

?>