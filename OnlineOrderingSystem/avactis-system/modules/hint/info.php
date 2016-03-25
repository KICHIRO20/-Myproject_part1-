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
 * Catalog module meta info.
 *
 * @package Catalog
 * @author Alexey Kolesnikov
 * @version $Id$
 */

$moduleInfo = array (
    'name'          => 'Hint',
    'shortName'     => 'HINT',
    'groups'        => 'Main',
    'description'   => 'Hint module description',
    'version'       => '0.1.47700',
    'author'        => 'Alexander Girin',
    'contact'       => '',
    'systemModule'  => false,
    'mainFile'      => 'hint_api.php',
    'actions'       => array(
        'AdminZone'    => array(
            'SetHintContent' => 'set_hint_content.php'
        ),
    ),
    'views'         => array(
         'AdminZone'    => array(
            'ShowHint' => 'show_hint.php'
         ),
         'CustomerZone' => array(
         )
    )
);
?>