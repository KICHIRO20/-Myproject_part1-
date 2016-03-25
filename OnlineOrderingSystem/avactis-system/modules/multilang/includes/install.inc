<?php
    global $application;

    $tables = MultiLang :: getTables();           
    $query = new DB_Table_Create($tables);

    // installing the languages     
    // assuming the csv file has the following format:
    // language name; 2-byte code; 3-byte code; codepage
    $fp = @fopen(dirname(__FILE__) . '/languages.csv', 'r');
    if ($fp)
    {
        $table = 'multilang_languages';
        $cols = $tables[$table]['columns'];

        while($lang = fgetcsv($fp, 65536, ';'))
            if ($lang[0] && $lang[1])
            {
                $query = new DB_Insert($table);
                $query -> AddInsertValue(_ml_strtoupper($lang[1]), $cols['lng']);
                $query -> AddInsertValue($lang[0], $cols['lng_name']);
                $query -> AddInsertValue(@$lang[3], $cols['codepage']);
                $application -> db -> getDB_Result($query);
            }

        fclose($fp);
    }
?>