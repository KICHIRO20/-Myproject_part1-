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
 * @package DataConverter
 * @author Egor V. Derevyankin
 *
 */

$moduleInfo = array (
    'name'          => 'Data_Converter',
    'shortName'     => 'DCONV',
    'groups'        => 'Main',
    'description'   => 'Data Converter module',
    'version'       => '0.1.47700',
    'author'        => 'Egor V. Derevyankin',
    'contact'       => '',
    'systemModule'  => false,
    'constantsFile' => 'const.php',
    'mainFile'      => 'data_converter_api.php',
    'resFile'       => 'data-converter-messages',
    'extraAPIFiles' => array(
        'Convert_Worker'    => 'abstract/convert_worker.php'
       ,'CWorker'           => 'abstract/cworker.php'
       ,'DataReaderDefault' => 'abstract/data_reader_default.php'
       ,'DataFilterDefault' => 'abstract/data_filter_default.php'
       ,'DataWriterDefault' => 'abstract/data_writer_default.php'
       ,'DataWriterCache'   => 'abstract/data_writer_cache.php'
       ,'DataReaderCache'   => 'abstract/data_reader_cache.php'
       ,'DataReaderCSV'     => 'abstract/data_reader_csv.php'
       ,'DataWriterCSV'     => 'abstract/data_writer_csv.php'
       ,'DataWriterDynamicCSV' => 'abstract/data_writer_dynamic_csv.php'
    ),
    'actions'       => array(
    ),
    'views'         => array(
         'AdminZone'    => array(
         ),
         'CustomerZone' => array(
         )
    )
);

?>