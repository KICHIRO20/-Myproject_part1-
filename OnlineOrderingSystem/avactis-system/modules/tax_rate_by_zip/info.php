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
 * TaxRateByZip module meta info.
 *
 * @package TaxRateByZip
 * @author Garafutdinov Ravil
 */

$moduleInfo = array
    (
        'name'         => 'TaxRateByZip', # this is also a main class name
        'shortName'    => 'TAX_ZIP',
        'groups'       => 'Main',
        'description'  => 'Tax Rate by Zip module',
        'version'      => '0.1.47700',
        'author'       => 'Garafutdinov Ravil',
        'contact'      => '',
        'systemModule'  => false,
        'resFile'      => 'tax-rate-by-zip-messages',
        'mainFile'     => 'tax_rate_by_zip_api.php',
//        'constantsFile'=> 'const.php',
        'extraAPIFiles' => array(
            'DataFilterCSVTaxRatesToClean' => 'abstract/data_filter_csv_taxrates_to_clean.php',
            'DataWriterCleanTaxRatesToCSV' => 'abstract/data_writer_clean_taxrates_to_csv.php',
            'DataWriterCleanTaxRatesToDB'  => 'abstract/data_writer_clean_taxrates_to_db.php',
    ),

        'actions' => array
        (
//            # We suppose, the action name matches
//            # the class name of this action.
//            # 'action_class_name' => 'action_file_name
            'AdminZone' => array(
                'TaxRatesByZipItemsAction'       => 'taxratesbyzipitems_action.php'
               ,'DoTaxRatesImportFromCSV'        => 'do_taxrates_import_from_csv.php'
               ,'TaxRatesRedirectToImportAction' => 'redirect_to_import_action.php'
            ),
        ),

        'hooks' => array
        (
            # 'hook_class_name' => array ( 'onAction'  => 'action_class_name',
            #                              'Hook_File' => 'hook_file_name' )
        ),

        'views' => array
        (
            'AdminZone' => array
            (
                 'TaxRateByZip_Sets'      => 'tax-rate-by-zip-sets-az.php'
                ,"TaxRateByZip_AddNewSet" => 'tax-rate-by-zip-add-new-set.php'
                ,'TaxRatesImportView'     => 'taxrates_import_az.php'
            ),
            'CustomerZone' => array
            (
            )
        )
    );
?>