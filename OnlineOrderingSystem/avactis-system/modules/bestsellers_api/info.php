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

$moduleInfo = array
    (
        'name'         => 'Bestsellers_API',
        'shortName'    => 'BS',
        'groups'       => 'Main',
        'description'  => 'Bestsellers module',
        'version'      => '0.1.47700',
        'author'       => 'Egor V. Derevyankin',
        'contact'      => '',
        'systemModule'  => false,
        'constantsFile'=> 'const.php',
        'mainFile'     => 'bestsellers_api.php',
        'resFile'      => 'bestsellers-messages',

        'actions' => array
        (
            'AdminZone'    => array(
                'save_bs_links_and_settings' => 'save_bs_links_and_settings.php'
            ),
        ),

        'hooks' => array
        (
            'DeleteAllBSLinksFromCategories' => array (
                                    'onAction' => 'ConfirmDeleteProducts,ConfirmDeleteCategory'
                                   ,'Hook_File' => 'delete_all_bs_links_from_categories.php'
                                )
        ),

        'views' => array
        (
            'AdminZone' => array(
                'BS_LinksList'    => 'bs_links_list_az.php'
            ),
            'CustomerZone' => array(
                'Bestsellers'     => 'bestsellers_cz.php'
            )
        )
    );

?>