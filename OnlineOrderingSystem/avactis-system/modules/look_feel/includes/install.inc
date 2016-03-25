<?php
    global $application;

    $tables = Configuration :: getTables();
    $columns = $tables['store_settings']['columns'];

    $query = new DB_Insert('store_settings');
    $query->addInsertValue(STOREFRONT_ACTIVE_SKIN, $columns['name']);
    $query->addInsertValue('string', $columns['type']);
    $query->addInsertValue('metro', $columns['value']);
    $application->db->getDB_Result($query);
    
    $query = new DB_Insert('store_settings');
    $query->addInsertValue(STOREFRONT_SKIN_SETTINGS, $columns['name']);
    $query->addInsertValue('string', $columns['type']);
    $query->addInsertValue('', $columns['value']);
    $application->db->getDB_Result($query);
    
    $query = new DB_Insert('store_settings');
    $query->addInsertValue(STOREFRONT_SKIN_PANEL_SETTINGS, $columns['name']);
    $query->addInsertValue('string', $columns['type']);
    $query->addInsertValue('', $columns['value']);
    $application->db->getDB_Result($query);
?>
