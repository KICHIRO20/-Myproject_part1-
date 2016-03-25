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
 * @package ShippingTester
 * @author Egor V. Derevyankin
 */

$moduleInfo = array
(
        'name'         => 'Shipping_Tester',
        'shortName'    => 'ST',
        'groups'       => 'Main',
        'description'  => 'Shipping Tester',
        'version'      => '0.2.47700',
        'author'       => 'Egor V. Derevyankin',
        'contact'      => '',
        'systemModule'  => false,
        'mainFile'     => 'shipping_tester_api.php',
        'resFile'      => 'shipping-tester-messages',

        'actions'   => array
        (
            'AdminZone' => array(
                'test_ship' => 'test_ship.php'
            ),
        ),

        'hooks'     => array
        (
        ),

        'views'     => array
        (
            'AdminZone' => array
            (
                'ShippingTesterWindow' => 'shipping-tester-window.php'
            ),
            'CustomerZone' => array
            (
            )
        )
);

?>