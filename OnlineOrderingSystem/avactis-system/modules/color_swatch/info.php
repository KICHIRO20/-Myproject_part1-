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
/***********************************************************************
| Avactis (TM) Shopping Cart software developed by Pentasoft Corp.
| http://www.avactis.com
| -----------------------------------------------------------------------
| All source codes & content (c) Copyright 2004-2010, Pentasoft Corp.
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
 * ColorSwatch module meta info.
 *
 * @package ColorSwatch
 * @author HBWSL
 */

$moduleInfo = array
(
    'name'         => 'ColorSwatch',
    'shortName'    => 'CLRSWTCH',
    'groups'       => 'Main',
    'description'  => '',
    'version'      => '1.0.47700',
    'author'       => 'Avactis Team',
    'contact'      => '',
    'systemModule' => false,
    'mainFile'     => 'color_swatch_api.php',
    'resFile'      => '',

    'actions' => array
    (
        'add_color_swatch_image_action'=> 'add_color_swatch_image_action.php',
		'update_values_of_color_swatch_action' => 'update_values_of_color_swatch_action.php',
		'del_values_of_color_swatch_action' => 'del_values_of_color_swatch_action.php',
                'update_number_label_action' => 'update_number_label_action.php'
    ),

    'hooks' => array
    (
    ),

    'views' => array
    (
        'AdminZone' => array
        (
		'PI_ColorSwatch'        => 'product_color_swatch_az.php'
        ),
        'CustomerZone' => array
        (
           'ColorSwatchImages' => 'product_color_swatch_cz.php'
        ),

    )
);

?>