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
 * EventsManager module meta info.
 *
 * @package EventsManager
 * @author Alexey Florinsky
 * @version $Id$
 */

$moduleInfo = array
(
    'name' => 'EventStack',
    'shortName' => 'EVST',
    'groups' => 'Main',
    'description' => 'EventStack module description',
    'version' => '0.1.47700',
    'author' => 'Alexey Florinsky',
    'contact' => '',
    'systemModule' => true,
    'mainFile' => 'event_stack_api.php',
    'extraAPIFiles' => array(
         'EventInfoBase' => 'abstract/eventstack_base.php',
         'EventInfo_ProductInfoDisplayed' => 'abstract/eventstack_base.php',
         'EventInfo_OrderCreated' => 'abstract/eventstack_base.php',
		 'EventInfo_OrderStatusUpdated' => 'abstract/eventstack_base.php',
		 'EventInfo_OrdersWillBeDeleted' => 'abstract/eventstack_base.php',
		 'EventInfo_CustomerRegistered' => 'abstract/eventstack_base.php',

         'REST_Events' => 'rest/REST_Events.php',
	),
	'views' => array
    (
         'AdminZone' => array
         (
         ),
         'CustomerZone' => array
         (
         )
    )
);
?>