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
 * @package Statistics
 * @author Egor V. Derevyankin
 *
 */

$moduleInfo = array
    (
        'name'         => 'Statistics',
        'shortName'    => 'STAT',
        'groups'       => 'Main',
        'description'  => 'Statistics module',
        'version'      => '0.1.47700',
        'author'       => 'Egor V. Derevyankin',
        'contact'      => '',
        'systemModule'  => false,
        'constantsFile'=> 'const.php',
        'mainFile'     => 'statistics_api.php',
        'resFile'      => 'statistics-messages',

        'actions' => array
        (
        ),

        'hooks' => array
        (
        ),

        'views' => array
        (
            'AdminZone' => array(
            ),
            'CustomerZone' => array(
            )
        )
    );


?>