<?php

global $application;
loadCoreFile('csv_parser.php');
$csv_parser = new CSV_Parser();

$tables = Shipping_Cost_Calculator::getTables();
$query = new DB_Table_Create($tables);

$table='scc_settings';
$columns = $tables[$table]['columns'];                           

list($flt, $Default_Settings) = $csv_parser->parse_file(dirname(__FILE__)."/default_settings.csv");
if(count($Default_Settings) > 0)
{
    foreach($Default_Settings as $key => $setting)
    {
        $query = new DB_Insert($table);
        $query->addInsertValue($setting["key"], $columns['key']);
        $query->addInsertValue($setting["value"], $columns['value']);
        $application->db->getDB_Result($query);
    };
};

?>
