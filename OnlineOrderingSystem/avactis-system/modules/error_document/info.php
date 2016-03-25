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
 * Error_Document module meta info.
 *
 * @package Erro_rDocument
 * @author HBWSL
 * @version 1.0
 */

$moduleInfo = array
(
    'name' => 'Error_Document',
    'shortName' => 'ERRD',
    'groups'    => 'Main',
    'description' => 'Error document module description',
    'version' => '0.1.47700',
    'author' => 'HBWSL',
    'contact' => '',
    'systemModule' => true,
    'resFile'       => 'error_document',
    'constantsFile' => 'const.php',
    'mainFile' => 'error_document_api.php',
        'actions' => array
        (
            'AdminZone' => array(
                'UpdateErrDocStatus'       => 'update_err_doc_status_action.php'
            ),
        ),
    'views' => array
    (
         'AdminZone' => array
         (
             'Error_Document_Setting' => 'error-document-setting-az.php'
         ),
         'CustomerZone' => array()
    )
);
?>