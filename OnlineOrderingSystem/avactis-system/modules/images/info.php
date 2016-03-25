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
 * @package Images
 * @author Vadim Lyalikov
 *
 */

$moduleInfo = array
    (
        'name'         => 'Images',
        'shortName'    => 'IMG',
        'groups'       => 'Main',
        'description'  => 'Images module',
        'version'      => '0.1.47700',
        'author'       => 'Vadim Lyalikov',
        'contact'      => '',
        'systemModule'  => false,
        'constantsFile'=> 'const.php',
        'mainFile'     => 'images_api.php',
        'resFile'      => 'images-messages',

        'actions' => array
        (
            'AdminZone' => array(
                'images_delete_image' => 'images_delete_image_action.php'
               ,'images_upload_local_file' => 'images_upload_local_file_action.php'
               ,'images_upload_server_file' => 'images_upload_server_file_action.php'
               ,'images_upload_url' => 'images_upload_url_action.php'
               ,'images_update_alt_text' => 'images_update_alt_text_action.php'
           ),
        ),

        'hooks' => array
        (
            'remove_not_used_images' => array (
                                    'onAction' => 'upload_image'
                                   ,'Hook_File' => 'remove_not_used_images.php'
                                )
        ),

        'views' => array
        (
            'AdminZone' => array(
                'image_input_az'    => 'image_input_az.php'
               ,'image_output_az'   => 'image_output_az.php'
            ),
            'CustomerZone' => array(
                'image_output_cz'   => 'image_output_cz.php'
            )
        )
    );

?>