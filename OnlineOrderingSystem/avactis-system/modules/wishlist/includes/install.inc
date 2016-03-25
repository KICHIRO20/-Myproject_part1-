<?php
    global $application;

    $tables = WishList :: getTables();           
    $query = new DB_Table_Create($tables);
?>