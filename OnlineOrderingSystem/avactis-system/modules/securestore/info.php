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
 * Cart module meta info.
 *
 * @package Cart
 * @author Alexander Girin
 */

$moduleInfo = array
(
 'name'         => 'SecureStore',
 'shortName'    => 'SS',
 'groups'       => 'Main',
 'description'  => '',
 'version'      => '0.1.47700',
 'author'       => 'Pragati',
 'contact'      => '',
 'systemModule' => false,
 'mainFile'     => 'secure_store_api.php',
 'resFile'      => 'securestore-messages',

 'actions' => array
 (
  'FindUpdatedFileAction'		=> 		'findupdatedfile.php',
  'SendResultMailAction'  		=>		'sendresultmail.php'
 ),

 'views' => array
 (
  'AdminZone' => array
  (
   'FindUpdatedFile'   =>  'find-updated-files-az.php'
  ),
 )
 );
 ?>