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
 * @package CMS
 * @author Sergey Kulitsky
 *
 */
$moduleInfo = array
    (
        'name'          => 'CMS',
        'shortName'     => 'CMS',
        'groups'        => 'Main',
        'description'   => 'CMS module',
        'version'       => '0.1.47700',
        'author'        => 'Sergey Kulitsky',
        'contact'       => '',
        'systemModule'  => false,
        'mainFile'      => 'cms_api.php',
        'constantsFile' => 'const.php',
        'resFile'       => 'cms-messages',
        'extraAPIFiles' => array(
            'CCMSPageInfo' => 'abstract/cms_page_info.php'
        ),
        'actions' => array(
            'AdminZone' => array(
                'update_page_data' => 'update_page_data.php',
                'update_pages'     => 'update_pages.php',
                'update_menu_data' => 'update_menu_data.php',
                'update_menu'      => 'update_menu.php',
                'update_nav_menu_location' => 'update_nav_menu_location.php',
                'update_nav_menu_data' => 'update_nav_menu_data.php',
                'SearchNavMenu' => 'search_nav_menu.php'
            )
        ),
        'views' => array(
            'AdminZone' => array(
                'CMS_Pages'     => 'cms_pages_az.php',
                'CMS_Page_Data' => 'cms_page_data_az.php',
                'CMS_Menu'      => 'cms_menu_az.php',
                'CMS_Menu_Data' => 'cms_menu_data_az.php',
                'CMS_Nav_Menu'  => 'cms_nav_menu_az.php'
            ),
            'CustomerZone' => array(
                'CMSPage'     => 'cms_page_cz.php',
                'CMSPageTree' => 'cms_page_tree_cz.php',
                'CMSMenu'     => 'cms_menu_cz.php',
                'PageView'    => 'pageview_cz.php',
            )
        )
    );

?>