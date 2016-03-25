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
 * @package Froogle
 * @author Egor Makarov
 *
 */
$moduleInfo = array
    (
        'name'         => 'Froogle',
        'shortName'    => 'FRG',
        'groups'       => 'Main',
        'description'  => 'Export products to Froogle',
        'version'      => '0.1.47700',
        'author'       => 'Egor Makarov',
        'contact'      => '',
        'systemModule'  => false,
        'mainFile'     => 'froogle_api.php',
        'constantsFile'=> 'const.php',
        'resFile'      => 'froogle-messages',
        'extraAPIFiles' => array(
            'DataWriterProductsFroogle'    => 'abstract/data_writer_products_froogle.php'
        ),

        'actions' => array
        (
           'AdminZone' => array(
               'do_froogle_export'   => 'do_froogle_export.php'
           ),
        ),

        'hooks' => array
        (
        ),

        'views' => array
        (
            'AdminZone' => array(
               'Froogle_Export'     => 'froogle_export_az.php'
            ),
            'CustomerZone' => array(
            )
        )
    );

?>