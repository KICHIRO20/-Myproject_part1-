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
 * TransactionTracking module meta info.
 *
 * @package TransactionTracking
 * @author Vadim Lyalikov
 * @version $Id$
 */

$moduleInfo = array (
    'name'          => 'TransactionTracking',
    'shortName'     => 'TT',
    'groups'         => '',
    'description'   => 'TransactionTracking module description',
    'version'       => '0.1.47700',
    'author'        => 'Vadim Lyalikov',
    'contact'       => '',
    'systemModule'  => false,
    'mainFile'      => 'transaction_tracking_api.php',
    'constantsFile'=> 'const.php',
    'resFile'      => 'transaction-tracking-messages',
    'extraAPIFiles' => array(
         'TransactionTrackingInstaller' => 'includes/transaction_tracking_installer.php'
    ),
    'actions'       => array(
             'UpdateTransactionTrackingSettings' => 'update-transaction-tracking-settings-action.php'
    ),
    'views'         => array(
         'AdminZone'    => array(
             'TransactionTrackingSettings' => 'transaction-tracking-settings-az.php',
         ),
         'CustomerZone' => array(
             'TransactionTrackingHtmlCode' => 'transaction-tracking-html-code-cz.php',
             'VisitorTrackingCode'         => 'visitor-tracking-code-cz.php',
         )
    )
);
?>