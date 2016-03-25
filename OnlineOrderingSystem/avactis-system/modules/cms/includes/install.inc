<?php
        global $application;
    	
        $tables = CMS::getTables();           
        $query = new DB_Table_Create($tables);
?>