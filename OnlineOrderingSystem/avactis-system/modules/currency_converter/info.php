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
 * @package CurrencyConverter
 * @author Egor V. Derevyankin
 */

$moduleInfo = array
(
        'name'          => 'Currency_Converter',
        'shortName'     => 'CC',
        'groups'        => 'Main',
        'description'   => 'Currency Converter',
        'version'       => '0.1.47700',
        'author'        => 'Egor V. Derevyankin',
        'contact'       => '',
        'systemModule'  => false,
        'mainFile'      => 'currency_converter_api.php',
        'constantsFile' => 'const.php',
        'resFile'       => 'currency-converter-messages',

        'actions'       => array(
            'AdminZone' => array(
                'add_ccrate'    => 'add_ccrate.php'
               ,'del_ccrates'   => 'del_ccrates.php'
           ),
         ),

        'views'         => array(
            'AdminZone' => array(
                'CurrencyRateEditor' => 'currency_rate_editor.php'
             ),
            'CustomerZone' => array(
             )
         ),
);

?>