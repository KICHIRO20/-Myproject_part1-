<?php
    global $application;
    loadCoreFile('csv_parser.php');
    $csv_parser = new CSV_Parser();

    $tables = Newsletter::getTables();
    $query = new DB_Table_Create($tables);

    $table = 'newsletter_settings';                         
    $columns = $tables[$table]['columns'];                           

    $param_info = array(
                     'GROUP_NAME'        => 'TIMELINE',
                     'PARAM_NAME'        => 'LOG_SEND_NEWS',
                     'PARAM_DESCRIPTION' => array( 'NAME'        => array('NLT', 'ADV_CFG_LOG_SEND_NEWS_NAME'),
                                                   'DESCRIPTION' => array('NLT', 'ADV_CFG_LOG_SEND_NEWS_DESC') ),
                     'PARAM_TYPE'          => PARAM_TYPE_LIST,
                     'PARAM_VALUE_LIST'    => array(
                             array(  'VALUE' => 'NO',
                                     'VALUE_DESCRIPTION' => array( 'NAME'        => array('NLT', 'ADV_CFG_LOG_SEND_NEWS_NO'),
                                                                   'DESCRIPTION' => array('NLT', 'ADV_CFG_LOG_SEND_NEWS_NO') ),
                                   ),
                             array(  'VALUE' => 'YES',
                                     'VALUE_DESCRIPTION' => array( 'NAME'        => array('NLT', 'ADV_CFG_LOG_SEND_NEWS_YES'),
                                                                   'DESCRIPTION' => array('NLT', 'ADV_CFG_LOG_SEND_NEWS_YES') ),
                                   )),
                     'PARAM_CURRENT_VALUE' => 'YES',
                     'PARAM_DEFAULT_VALUE' => 'YES',
    );
    modApiFunc('Settings','createParam', $param_info);

/*    list($flt,$Default_Settings) = $csv_parser->parse_file(dirname(__FILE__)."/default_settings.csv");
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
*/    
?>
