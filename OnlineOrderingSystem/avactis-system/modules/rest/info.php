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


$moduleInfo = array
(
    'name' => 'RESTManager',
    'shortName' => 'REST',
    'groups' => 'Main',
    'description' => 'REST Api module',
    'version' => '0.1.47700',
    'author' => 'Alexey Florinsky',
    'contact' => '',
    'systemModule' => true,
    'mainFile' => 'rest_api.php',
    'constantsFile' => 'const.php',
    'resFile'      => 'rest-messages',
    'extraAPIFiles' => array(
         'RESTResponse' => 'abstract/RESTResponse.php',

		 'REST_Test' => 'rest/REST_Test.php',
		 'REST_Errors' => 'rest/REST_Errors.php',
	),
	'views' => array
    (
         'AdminZone' => array
         (
         ),
         'CustomerZone' => array
         (
         )
    ),
    'actions' => array
    (
        'AdminZone' => array(
            'REST_Request_Action' => 'rest_request_action.php',
        ),
    )
);
?>