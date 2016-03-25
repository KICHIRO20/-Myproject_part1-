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
 * @package Layout CMS
 * @author Alexey Astafyev
 *
 */
$moduleInfo = array
    (
        'name'          => 'Layout_CMS',
        'shortName'     => 'LC',
        'groups'        => 'Main',
        'description'   => 'Layout CMS module',
        'version'       => '0.1.47700',
        'author'        => 'Alexey Astafyev',
        'contact'       => '',
        'constantsFile' => 'const.php',
        'systemModule'  => false,
        'mainFile'      => 'layout_cms_api.php',
        'resFile'       => 'layout-cms-messages',
        'actions' => array
        (
            'AdminZone' => array(
                'delete_page' => 'delete_page_az.php',
                'add_new_page' => 'add_new_page_az.php',
                'get_layout_tmpl' => 'get_layout_tmpl_az.php',
                'save_layout_tmpl' => 'save_layout_tmpl_az.php',
                'get_available_blocks' => 'get_available_blocks_az.php'
            )
        ),
        'views' => array
        (
            'AdminZone' => array(
                'LayoutCMS' => 'layout_cms_az.php'
            ),
        )
    );

?>