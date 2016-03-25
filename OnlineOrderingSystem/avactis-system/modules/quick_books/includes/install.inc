<?php

/**
 * @package QuickBooks
 * @author Egor V. Derevyankin
 * 
 */

    global $application;
    loadCoreFile('csv_parser.php');
    $csv_parser = new CSV_Parser();

    $tables = Quick_Books::getTables();
    $query = new DB_Table_Create($tables);

    $table = 'qb_settings';                         
    $columns = $tables[$table]['columns'];                           

    list($flt,$Default_Settings) = $csv_parser->parse_file(dirname(__FILE__)."/default_settings.csv");
    if(count($Default_Settings) > 0)
    {
        foreach($Default_Settings as $key => $setting)
        {
            $query = new DB_Insert($table);
            $query->addInsertValue($setting["key"], $columns['setting_key']);
            $query->addInsertValue($setting["value"], $columns['setting_value']);
            $application->db->getDB_Result($query);
        };
    };
?>
