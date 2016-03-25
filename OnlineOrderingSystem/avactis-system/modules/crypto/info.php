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
 * Crypto module meta info.
 *
 * @package Crypto
 * @author Alexander Girin
 */

$moduleInfo = array
    (
        'name'         => 'Crypto', # this is also a main class name
        'shortName'    => 'CRYPTO',
        'groups'       => 'Main',
        'description'  => 'Crypto module',
        'version'      => '0.1.47700',
        'author'       => 'Alexander Girin',
        'contact'      => '',
        'systemModule'  => false,
        'mainFile'     => 'crypto_api.php',

        'actions' => array
        (
            'AdminZone' => array (
                'DecryptRsaBlowfishJavascript' => 'decrypt-rsa-blowfish-javascript-action.php',
                'resend_data_as_text_file' => 'resend_data_as_text_file-action.php'
            ),
        ),

        'hooks' => array
        (
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