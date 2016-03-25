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
 * Error module meta info.
 *
 * @package Error
 * @author Alexey Kolesnikov
 * @version $Id$
 */

$moduleInfo = array
(
    'name' => 'Error',
    'shortName' => 'ERROR',
    'groups'    => 'Main',
    'description' => 'Error module description',
    'version' => '0.1.47700',
    'author' => 'Alexey Kolesnikov',
    'contact' => '',
    'systemModule' => true,
    'mainFile' => 'error.php',
    'views' => array
    (
         'AdminZone' => array
         (
             'SetupWarnings' => 'setup_warnings.php'
         ),
         'CustomerZone' => array
         (
             'A_Error' => 'a_error.php',
             'C_Error' => 'c_error.php'
         )
    )
);
?>