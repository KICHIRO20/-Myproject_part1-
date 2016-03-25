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
 * @package Banner Management
 * @author Ninad
 */

$moduleInfo = array
    (
        'name'         => 'banner',
        'shortName'    => 'BN',
        'groups'       => '',
        'description'  => 'Banner System for Avactis shopcart',
        'version'      => '0.1.%BUILD%',
        'author'       => 'Ninad',
        'contact'      => '',
        'systemModule'  => 'Extension',
    	'resFile'      => 'banner-messages',
        'mainFile'     => 'banner_api.php',
        'actions' => array
        (

            'AdminZone' => array(
             'add_banner_info' => 'add_banner_info_action.php',
             'add_banner_content_info' => 'add_banner_content_info_action.php',
            ),
        ),

        'hooks' => array
        (
        ),

        'views' => array
        (
            'AdminZone' => array
            (
                'BannerLocation' => 'banner_location_az.php'
            	,'BannerManagement' => 'banner_manage_az.php'
            	,'BannerContentManagement'=>'banner_manage_content_az.php'
            ),
            'CustomerZone' => array
            (
		         'Banners'=>'banners_cz.php',
            	 //'TopBanner'=>'top_banners_cz.php',
            )
        )
    );
?>