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
 * @package ProductFiles
 * @author Egor V. Derevyankin
 *
 */

$moduleInfo = array
    (
        'name'         => 'Product_Files',
        'shortName'    => 'PF',
        'groups'       => 'Main',
        'description'  => 'Product Files module',
        'version'      => '0.1.47700',
        'author'       => 'Egor V. Derevyankin',
        'contact'      => '',
        'systemModule'  => false,
        'mainFile'     => 'product_files_api.php',
        'resFile'      => 'product-files-messages',

        'actions' => array
        (
            'AdminZone' => array (
                'update_pf_settings'        =>  'update_pf_settings.php'
               ,'add_file_to_product'       =>  'add_file_to_product.php'
               ,'del_files_from_product'    =>  'del_files_from_product.php'
               ,'change_hotlink_status'     =>  'change_hotlink_status.php'
               ,'zero_hotlink_tries'        =>  'zero_hotlink_tries.php'
               ,'update_hl_edate'           =>  'update_hl_edate.php'
               ,'update_files_of_product'   =>  'update_files_of_product.php'
           ),
           'download_product_file'     =>  'download_product_file.php'
           ,'direct_download_file'      =>  'direct_download_file.php'
        ),

        'hooks' => array
        (
            'DeleteAllFilesFromProducts'    => array(
                                    'onAction' => 'ConfirmDeleteProducts,ConfirmDeleteCategory'
                                   ,'Hook_File' => 'delete_all_files_from_products.php'
                                 )
           ,'CopyAllFilesFromProductToProduct' => array(
                                    'onAction' => 'CopyToProducts'
                                   ,'Hook_File' => 'copy_all_files_from_product_to_product.php'
                                 )
        ),

        'views' => array
        (
            'AdminZone' => array(
                'PF_Settings'       => 'settings_az.php'
               ,'PF_FilesList'      => 'files_list_az.php'
               ,'PF_OrderHotlinks'  => 'order_hotlinks_az.php'
            ),
            'CustomerZone' => array(
                'DownloadProductFilePrompt'    => 'download_product_file_prompt_cz.php'
            )
        )
    );

?>