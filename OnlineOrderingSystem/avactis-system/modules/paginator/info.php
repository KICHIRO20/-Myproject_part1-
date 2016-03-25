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
 * Paginator module meta info.
 *
 * @package Paginator
 * @author Alexander Girin
 * @version 0.1
 */

$moduleInfo = array (
    'name' => 'Paginator',
    'shortName'     => 'PAGIN',
    'groups'         => 'Main',
    'description' => 'Paginator module description',
    'version' => '0.1.47700',
    'author' => 'Alexander Girin',
    'contact' => '',
    'systemModule' => false,
    'mainFile'     => 'paginator_api.php',
    'actions' => array(
         'Paginator_SetPage' => 'paginator_setpage.php',
         'Paginator_SetRowsPerPage' => 'paginator_setrowsperpage.php'
    ),
    'views' => array(
         'AdminZone' => array(
             'PaginatorLine' => 'paginator_line_az.php'
            ,'PaginatorRows' => 'paginator_rows_per_page_az.php'
         ),
         'CustomerZone' => array(
             'PaginatorLine' => 'paginator_line.php'
            ,'PaginatorDropdown' => 'paginator_rows_per_page.php'
         ),
        'Aliases' => array(
             'PaginatorProductsShown' => 'PaginatorLine'
            ,'PaginatorPagesShown'    => 'PaginatorLine'
            ,'PaginatorCustomerReviewsShown' => 'PaginatorLine'
        )
    )
);
?>