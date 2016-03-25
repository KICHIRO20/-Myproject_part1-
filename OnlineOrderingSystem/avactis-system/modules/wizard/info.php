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
 * @package Wizard
 * @author HBWSL
 *
 */
$moduleInfo = array
    (
	'name'          => 'Wizard',
	'shortName'     => 'WZ',
	'groups'        => 'Main',
	'description'   => 'Wizard module',
	'version'       => '0.1.47700',
	'author'        => 'HBWSL',
	'contact'       => '',
	'systemModule'  => false,
	'mainFile'      => 'wizard_api.php',
	'resFile'       => 'wizard-messages',
    'actions'       => array(
             'AdminZone' => array(
             )
                            ),
    'views'         => array(
         'AdminZone'    => array(
             'SetupGuide'           => 'setup_guide_az.php'
                                ),
         'CustomerZone'    => array()
                            )
);
?>