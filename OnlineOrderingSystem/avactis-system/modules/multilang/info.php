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
 * @package MultiLang
 * @author Sergey Kulitsky
 *
 */
$moduleInfo = array
    (
        'name'          => 'MultiLang',
        'shortName'     => 'ML',
        'groups'        => 'Main',
        'description'   => 'MultiLang module',
        'version'       => '0.1.47700',
        'author'        => 'Sergey Kulitsky',
        'contact'       => '',
        'systemModule'  => false,
        'mainFile'      => 'multilang_api.php',
        'resFile'       => 'multilang-messages',
        'extraAPIFiles' => array(
            'DataReaderLabelsDB'  => 'abstract/data_reader_labels_db.php',
            'DataWriterLabelsCSV' => 'abstract/data_writer_labels_csv.php',
            'DataReaderLabelsCSV' => 'abstract/data_reader_labels_csv.php',
            'DataWriterLabelsDB'  => 'abstract/data_writer_labels_db.php',
            'DataReaderRecordsDB' => 'abstract/data_reader_records_db.php',
            'DataWriterRecordsDB' => 'abstract/data_writer_records_db.php'
        ),
        'actions' => array
        (
            'AdminZone' => array(
                'UpdateLanguages'    => 'update_languages_az.php',
                'ML_UpdateLabels'    => 'update_labels_az.php',
                'ML_UpdateLabelData' => 'update_label_data_az.php',
                'ChangePageLanguage' => 'change_page_language_az.php',
                'do_export_labels'   => 'do_export_labels_az.php',
                'do_import_labels'   => 'do_import_labels_az.php',
                'do_change_def_lng'  => 'do_change_def_lng_az.php'
            ),
            'ChangeLanguage' => 'change_language.php'
        ),
        'views' => array
        (
            'AdminZone' => array(
                'SelectLanguage'        => 'ml_select_language_az.php',
                'LanguageSettings'      => 'ml_language_settings_az.php',
                'LabelEditor'           => 'ml_label_editor_az.php',
                'LabelData'             => 'ml_label_data_az.php',
                'SelectPageLanguage'    => 'ml_select_page_language_az.php',
                'ML_ExportLabels'       => 'ml_export_labels_az.php',
                'ML_ImportLabels'       => 'ml_import_labels_az.php',
                'ChangeDefaultLanguage' => 'ml_change_default_language_az.php'
            ),
            'CustomerZone' => array(
                'SelectLanguage' => 'ml_select_language_cz.php',
                'GetLangPageURL' => 'ml_get_lang_page_url_cz.php'
            ),
        )
    );

?>