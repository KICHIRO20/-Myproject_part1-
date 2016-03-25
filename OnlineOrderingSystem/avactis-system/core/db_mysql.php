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
 * @copyright Copyright &copy; 2005, HBWSL
 * @package Core
 * @author Alexander Girin
 */

/**
 * Query type - create table.
 *
 * @access public
 */
define('DBQUERY_TYPE_CREATE', 'create');
/**
 * Query type - select.
*
* @access public
 */
define('DBQUERY_TYPE_SELECT', 'select');
/**
 * Query type - insert.
 *
 * @access public
 */
define('DBQUERY_TYPE_INSERT', 'insert');
/**
 * Query type - replace.
 *
 * @access public
 */
define('DBQUERY_TYPE_REPLACE', 'replace');
/**
 * Query type - multiple insert.
 *
 * @access public
 */
define('DBQUERY_TYPE_MULTIPLE_INSERT', 'minsert');

/**
 * Query type - multiple replace.
 *
 * @access public
 */
define('DBQUERY_TYPE_MULTIPLE_REPLACE', 'mreplace');

/**
 *             - insert ... select
 * @access public
 */
define('DBQUERY_TYPE_INSERT_SELECT', 'insert_select');

/**
 * Query type - update.
 *
 * @access public
 */
define('DBQUERY_TYPE_UPDATE', 'update');
/**
 * Query type - delete.
 *
 * @access public
 */
define('DBQUERY_TYPE_DELETE', 'delete');
/**
 * Query type - drop table.
 *
 * @access public
 */
define('DBQUERY_TYPE_TABLE_DELETE', 'drop');
/**
 * Query type - lock tables.
 *
 * @access public
 */
define('DBQUERY_TYPE_LOCK_TABLES', 'lock_tables');
/**
 * Query type - unlock tables.
 *
 * @access public
 */
define('DBQUERY_TYPE_UNLOCK_TABLES', 'unlock_tables');
/**
 * Data type - integer.
 *
 * @access public
 */
define('DBQUERY_FIELD_TYPE_INT', 'int');
/**
 * Data type - float.
 *
 * @access public
 */
define('DBQUERY_FIELD_TYPE_FLOAT', 'float');
/**
 * Data type - float.
 *
 * @access public
 */
define('DBQUERY_FIELD_TYPE_DOUBLE', 'double');
/**
 * Data type - varchar(1).
 *
 * @access public
 */
define('DBQUERY_FIELD_TYPE_CHAR1', 'varchar(1)');
/**
 * Data type - char(2).
 *
 * @access public
 */
define('DBQUERY_FIELD_TYPE_CHAR2', 'char(2)');
/**
 * Data type - varchar(5).
 *
 * @access public
 */
define('DBQUERY_FIELD_TYPE_CHAR5', 'varchar(5)');
/**
 * Data type - varchar(8).
 *
 * @access public
 */
define('DBQUERY_FIELD_TYPE_CHAR8', 'varchar(8)');
/**
 * Data type - varchar(10).
 *
 * @access public
 */
define('DBQUERY_FIELD_TYPE_CHAR10', 'varchar(10)');
/**
 * Data type - varchar(16).
 *
 * @access public
 */
define('DBQUERY_FIELD_TYPE_CHAR16', 'varchar(16)');
/**
 * Data type - varchar(20).
 *
 * @access public
 */
define('DBQUERY_FIELD_TYPE_CHAR20', 'varchar(20)');
/**
 * Data type - varchar(32).
 *
 * @access public
 */
define('DBQUERY_FIELD_TYPE_CHAR32', 'varchar(32)');
/**
 * Data type - varchar(50).

 * @access public
 */
define('DBQUERY_FIELD_TYPE_CHAR50', 'varchar(50)');
/**
 * Data type - varchar(64).

 * @access public
 */
define('DBQUERY_FIELD_TYPE_CHAR64', 'varchar(64)');
/**
 * Data type - varchar(100).
 *
 * @access public
 */
define('DBQUERY_FIELD_TYPE_CHAR100', 'varchar(100)');
/**
 * Data type - varchar(150).
 *
 * @access public
 */
define('DBQUERY_FIELD_TYPE_CHAR150', 'varchar(150)');
/**
 * Data type - varchar(255).
 *
 * @access public
 */
define('DBQUERY_FIELD_TYPE_CHAR255', 'varchar(255)');
/**
 * Data type - varchar(256).
 *
 * @access public
 */
define('DBQUERY_FIELD_TYPE_CHAR256', 'varchar(256)');
/**
 * Data type - varchar(1024).
 *
 * @access public
 */
define('DBQUERY_FIELD_TYPE_CHAR1024', 'varchar(1024)');
/**
 * Data type - blob.
 *
 * @access public
 */
define('DBQUERY_FIELD_TYPE_BLOB', 'blob');
/**
 * Data type - varchar(200).
 *
 * @access public
 */
define('DBQUERY_FIELD_TYPE_CHAR200', 'varchar(200)');
/**
 * Data type - decimal(12,2).
 *
 * @access public
 */
define('DBQUERY_FIELD_TYPE_DECIMAL12_2', 'decimal(12,2)');
/**
 * Data type - decimal(12,4).
 *
 * @access public
 */
define('DBQUERY_FIELD_TYPE_DECIMAL12_4', 'decimal(12,4)');
/**
 * Data type - decimal(20,5).
 *
 * @access public
 */
define('DBQUERY_FIELD_TYPE_DECIMAL20_5', 'decimal(20,5)');
/**
 * Data type - text (2^16).
 *
 * @access public
 */
define('DBQUERY_FIELD_TYPE_TEXT', 'text');
/**
 * Data type - longtext (2^32).
 *
 * @access public
 */
define('DBQUERY_FIELD_TYPE_LONGTEXT', 'longtext');
/**
 * Data type - date.
 *
 * @access public
 */
define('DBQUERY_FIELD_TYPE_DATE', 'date');
/**
 * Data type - datetime.
 *
 * @access public
 */
define('DBQUERY_FIELD_TYPE_DATETIME', 'datetime');
/**
 * Data type - boolean.
 *
 * @access public
 */
define('DBQUERY_FIELD_TYPE_BOOL', 'char(5)');
define('DB_TRUE','true');
define('DB_FALSE','false');
define('DB_NULL', 'DB_NULL');

define('DBQUERY_FIELD_BOOLEAN_DEFAULT_TRUE',  ' char(5) NOT NULL default "'.DB_TRUE. '"');
define('DBQUERY_FIELD_BOOLEAN_DEFAULT_FALSE', ' char(5) NOT NULL default "'.DB_FALSE.'"');
/**
 * Const for operator - '='.
 *
 * @access public
 */
define('DB_EQ','=');
/**
 * Const for operator - '!='.
*
 * @access public
 */
define('DB_NEQ','<>');
/**
 * Const for operator - '>'.
 *
 * @access public
 */
define('DB_GT','>');
/**
 * Const for operator - '<'.
 *
 * @access public
 */
define('DB_LT','<');
/**
 * Const for operator - '>='.
 *
 * @access public
 */
define('DB_GTE','>=');
/**
 * Const for operator - '<='.
 *
 * @access public
 */
define('DB_LTE','<=');
/**
 * Const for operator - 'LIKE'.
 *
 * @access public
 */
define('DB_LIKE','LIKE');
/**
 * Const for operator - 'NOT LIKE'.
 *
 * @access public
 */
define('DB_NLIKE','NOT LIKE');
/**
 * Const for operator - 'REGEXP'.
 *
 * @access public
 */
define('DB_REGEXP','REGEXP');
/**
 * Const for operator - 'NOT REGEXP'.
 *
 * @access public
 */
define('DB_NOT_REGEXP','NOT REGEXP');
/**
 * Const for operator - 'IS NULL'.
 *
 * @access public
 */
define('DB_IS_NULL','IS NULL');
/**
 * Const for operator - 'IS NOT NULL'.
 *
 * @access public
 */
define('DB_NNULL','IS NOT NULL');
/**
 * Const for operator - 'IN'.
 * @access public
 */
define('DB_IN','IN');
/**
 * Const for operator - 'NOT IN'.
 * @access public
 */
define('DB_NIN','NOT IN');
/**
 * Const for operator - 'AND'.
 *
 * @access public
 */
define('DB_AND','AND');
/**
 * Const for operator - 'OR'.
 *
 * @access public
 */
define('DB_OR','OR');
/**
 * Const for operator - 'NOT'.
 *
 * @access public
 */
define('DB_NOT','NOT');
/**
 * Const for operator - 'BETWEEN'.
 *
 * @access public
 */
define('DB_BETWEEN','BETWEEN');
/**
 * Constant - '('.
 *
 * @access public
 */
define('DB_OpenSect','(');
/**
 * Constant ')'.
 *
 * @access public
 */
define('DB_CloseSect',')');
/**
 * Constant 'MYSQL_NUM'.
 *
 * @access public
 */
define('QUERY_RESULT_NUM', MYSQLI_NUM);
/**
 * Constant 'MYSQL_ASSOC'.
 *
 * @access public
 */
define('QUERY_RESULT_ASSOC', MYSQLI_ASSOC);
/**
 * Constant 'MYSQL_BOTH'.
 *
 * @access public
 */
define('QUERY_RESULT_BOTH', MYSQLI_BOTH);
/**
 * Constant 'READ'.
 *
 * @access public
 */
define('DB_LOCK_MODE_READ', 'READ');
/**
 * Constant 'READ LOCAL'.
 *
 * @access public
 */
define('DB_LOCK_MODE_READ_LOCAL', 'READ LOCAL');
/**
 * Constant 'WRITE'.
 *
 * @access public
 */
define('DB_LOCK_MODE_WRITE', 'WRITE');
/**
 * Constant 'LOW_PRIORITY WRITE'.
 *
 * @access public
 */
define('DB_LOCK_MODE_LOW_PRIORITY_WRITE', 'LOW PRIORITY WRITE');

/**
 * Modifiers for ignoring duplicate records on insert.
 * @var unknown_type
 */
define('DB_IGNORE', 'IGNORE');

/**
 * DB_MySQL class is used to work on MySQL DMBS.
 *
 * Long Description.
 *
 * @ finish the functions on this page
 * @access public
 * @author Alexandr Girin
 * @package Core
 */
class DB_MySQL
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * DB_MySQL class constructor.
     *
     * The main methods of DB_MySQL are:
     *      DB_MySQL::getDB_Result($QueryData, $result_type) is used to execute and get a data set for queries.
     *      As $QueryData parameter, the object of the classes DB_SELECT, DB_INSERT etc. is transferred,
     *      $result_type, the type of returned value is transferred: QUERY_RESULT_NUM - query result as a numeric array,
     *      QUERY_RESULT_ASSOC - query result as an associative array, QUERY_RESULT_BOTH - query result as both arrays,
     *      $result_type = QUERY_RESULT_ASSOC on default.
     * An example:
     * <code>
     *      $query = new DB_Select();
     *      #or
     *      $query = new DB_Insert('table_name');
     *      #or
     *      $query = new DB_Update('table_name');
     *      #or
     *      $query = new DB_Delete('table_name');
     *
     *      global $application;
     *      $application->getDB_Result($query);
     * </code>
     *
     * @return datatype description
     * @param type $parname description
     */
    function DB_MySQL()
    {
        global $application;
        $this->table_prefix = $application->getAppIni("DB_TABLE_PREFIX");
    }

    /**
     * Gets a data array as a result of the execution the SELECT query.
     *
     * @param object $QueryData - the object of the DB classes
     * @param int $result_type - returned data type of the SELECT query,
     * MYSQL_ASSOC is by default
     * @return array data array, as a result of the execution the SELECT query
     *
     */
    function getDB_Result($QueryData, $result_type = QUERY_RESULT_ASSOC)
    {
    	CProfiler::DBLayerStart();
        // logging changes to category tree
        $_cat_tree_logging = false;
        $_cat_tree_changing = false;
        $_cat_table = $this -> table_prefix . 'categories';
        $_cat_descr_table = $this -> table_prefix . 'categories_descr';
        switch($QueryData->getQueryType())
        {
            case DBQUERY_TYPE_INSERT:
            case DBQUERY_TYPE_MULTIPLE_INSERT:
            case DBQUERY_TYPE_INSERT_SELECT:
                if ($QueryData -> getInsertTable() == $_cat_table) {
                    $_cat_tree_logging = true;
                    $_cat_tree_changing = true;
                }
                if ($QueryData -> getInsertTable() == $_cat_descr_table) {
                    $_cat_tree_changing = true;
                }
                break;

            case DBQUERY_TYPE_REPLACE:
            case DBQUERY_TYPE_MULTIPLE_REPLACE:
                if ($QueryData -> getReplaceTable() == $_cat_table) {
                    $_cat_tree_logging = true;
                    $_cat_tree_changing = true;
                }
                if ($QueryData -> getReplaceTable() == $_cat_descr_table) {
                    $_cat_tree_changing = true;
                }
                break;

            case DBQUERY_TYPE_UPDATE:
                if (in_array($_cat_table, $QueryData -> UpdateTables)) {
                    $_cat_tree_logging = true;
                    $_cat_tree_changing = true;
                }
                if (in_array($_cat_descr_table, $QueryData -> UpdateTables)) {
                    $_cat_tree_changing = true;
                }
                break;

            case DBQUERY_TYPE_DELETE:
                if ($QueryData -> getDeleteTable() == $_cat_table) {
                    $_cat_tree_logging = true;
                    $_cat_tree_changing = true;
                }
                if ($QueryData -> getDeleteTable() == $_cat_descr_table) {
                    $_cat_tree_changing = true;
                }
                break;
        }

        if ($_cat_tree_changing) {
            modApiFunc('Catalog', 'resetFullCategoryStructure');
        }

        if ($_cat_tree_logging)
            if (modApiFunc('Settings', 'getParamValue',
                'TIMELINE', 'LOG_CATEGORY_TREE_CHANGES') != 'YES')
                $_cat_tree_logging = false;

        if ($_cat_tree_logging)
            $_status_before = modApiFunc('Catalog', 'checkCatalogTree');

        $this->PrepareSQL($QueryData);

        // Run SQL
        $this -> DB_MultiLang_PreExec($QueryData);
        $this->DB_Exec();
        $this -> DB_MultiLang_PostExec($QueryData);
        $this->DB_Result($result_type);
        $result = $this->ResultArray;

        // logging changes to category tree
        if ($_cat_tree_logging)
        {
            $_status_after = modApiFunc('Catalog', 'checkCatalogTree');

            modApiFunc('Timeline', 'addCatTreeLog', $_status_before,
                       $_status_after, $this -> QueryString);
        }
		CProfiler::DBLayerStop();
        return $result;
    }

    function getDB_Result_num_rows($QueryData, $result_type = QUERY_RESULT_ASSOC)
    {
        // logging changes to category tree
        $_cat_tree_logging = false;
        $_cat_table = $this -> table_prefix . 'categories';
        switch($QueryData->getQueryType())
        {
            case DBQUERY_TYPE_INSERT:
            case DBQUERY_TYPE_MULTIPLE_INSERT:
            case DBQUERY_TYPE_INSERT_SELECT:
                if ($QueryData -> getInsertTable() == $_cat_table)
                    $_cat_tree_logging = true;
                break;

            case DBQUERY_TYPE_REPLACE:
            case DBQUERY_TYPE_MULTIPLE_REPLACE:
                if ($QueryData -> getReplaceTable() == $_cat_table)
                    $_cat_tree_logging = true;
                break;

            case DBQUERY_TYPE_UPDATE:
                if (in_array($_cat_table, $QueryData -> UpdateTables))
                    $_cat_tree_logging = true;
                break;

            case DBQUERY_TYPE_DELETE:
                if ($QueryData -> getDeleteTable() == $_cat_table)
                    $_cat_tree_logging = true;
                break;
        }

        if ($_cat_tree_logging)
            if (modApiFunc('Settings', 'getParamValue',
                'TIMELINE', 'LOG_CATEGORY_TREE_CHANGES') != 'YES')
                $_cat_tree_logging = false;

        if ($_cat_tree_logging)
            $_status_before = modApiFunc('Catalog', 'checkCatalogTree');

        $this->PrepareSQL($QueryData);

        $query_id = md5($this->QueryString);

        // Run SQL
        $this -> DB_MultiLang_PreExec($QueryData);
        $this->DB_Exec();
        $this -> DB_MultiLang_PostExec($QueryData);
        $result = mysqli_num_rows($this->QueryResult);

        // logging changes to category tree
        if ($_cat_tree_logging)
        {
            $_status_after = modApiFunc('Catalog', 'checkCatalogTree');

            modApiFunc('Timeline', 'addCatTreeLog', $_status_before,
                       $_status_after, $this -> QueryString);
        }

        return $result;
    }

    function _getSQL($QueryData)
    {
        $this->PrepareSQL($QueryData);
        return $this->getQueryString();
    }

    /**
     * Connects with the database.
     *
     * @return resource the id of the database connection.
     * @param string $link the id name
     * @ to add the error message, specified in the resource file
     */
    function DB_Connect($link = 'db_link')
    {
        global $$link;
        global $application;
        $server = $application->getAppIni('DB_SERVER');
        $database = $application->getAppIni('DB_NAME');
        $username = $application->getAppIni('DB_USER');
        $password = $application->getAppIni('DB_PASSWORD');
        $pconnect = $application->getAppIni('DB_PCONNECT');
        $character_set = $application->getAppIni('SQL_CHARACTER_SET');

/*        if ($pconnect == 'true')
        {
            $$link = mysql_pconnect($server, $username, $password, 128);
        }
        else
        {
            $$link = mysql_connect($server, $username, $password, FALSE, 128);
        }*/

        $$link = mysqli_connect($server, $username, $password, $database);

        if ($$link)
        {
/*            if(!mysql_select_db($database, $$link))
            {
                _fatal(array( "CODE" => "CORE_041"), $database);
            }*/
        }
        else
        {
            _fatal(array( "CODE" => "CORE_042"));
        }

        if ($character_set)
            mysqli_query($$link, 'SET NAMES ' . $character_set);

        return $$link;
    }

    /**
     * Closes the database connection.
     *
     * @return boolean Returns TRUE in case of successful end, FALSE in case of
     * an error
     * @param string $link the id connection name
     */
    function DB_Close($link = 'db_link')
    {
        global $$link;
        return mysqli_close($$link);
    }

    /**
     * Prepares an error message.
     *
     * @return
     * @param $query - query contents
     * @param $errno - error number
     * @param $errno - error description
     */
    function DB_Error($query, $errno, $error)
    {
//        CTrace::err('MySQL Error: #'.$errno.' '.$error, 'SQL:'.$query);
//        CTrace::backtrace();
//        die('MySQL query error, see error log for more details.');
	_fatal(array("CODE"=>"DB_".$errno,"MESSAGE"=>'MySQL Error: #'.$errno.' '.$error.' SQL: '.$query, 'SQL:'.$query));
    }

    /**
     * Sends and executes the query.
     *
     * @return returns a resource identifier or FALSE if the query was not
     * executed correctly
     * @param $query - query string
     * @param $link - connection id
     */
    function DB_Query($query, $link = 'db_link', $keep_insert_id = true)
    {
        global $$link;
        CProfiler::DBExecStart($query);
        //CTrace::dbg(addcslashes($query, "\0..\37!@\177..\377"));
        $result = mysqli_query($$link, $query) or self::DB_Error($query, mysqli_errno($$link), mysqli_error($$link));
        if ($keep_insert_id)
        {
            $this -> insert_id = mysqli_insert_id($$link);
        }
        CProfiler::DBExecStop($query);
        return $result;
    }

    /**
     * Checks if the table exists in the database.
     *
     * @return boolean Returns true if the table exists, false otherwise
     * @param string $table_name - the table name
     * @param resource $link - the connection id
     */
    function DB_isTableExists($table_name, $link = 'db_link')
    {
        global $$link, $application;
        $database = $application->getAppIni('DB_NAME');
        $tables = self::DB_List_Tables($database);
        return isset($tables[$table_name]);
    }

    /**
     * An alias of mysql_list_tables.
     *
     * @return array returns the table list, specified in the database
     * @param string $db_name - the database name
     * @param resource $link - the connection id
     */
    function DB_List_Tables($db_name)
    {
        if (! isset(self::$table_list_cache) || self::$table_list_cache==null ) {
            self::$table_list_cache = array();
            $tables = DB_MySQL::DB_Query("SHOW TABLES FROM `$db_name`");
            while ($table = DB_MySQL::DB_Fetch_Array($tables, QUERY_RESULT_NUM))
            {
                self::$table_list_cache[$table[0]] = true;
            }
        }
        return self::$table_list_cache;
    }

    /**
     * An alias of mysql_fetch_array: Fetches a result row as an associative array,
     * a numeric array, or both.
     *
     * @return Returns an array that corresponds to the fetched row, or FALSE if
     * there are no more rows
     * @param resource $db_query - resource result
     */
    function DB_Fetch_Array($db_query, $result_type)
    {
        return mysqli_fetch_array($db_query, $result_type);
    }

    /**
     * An alias of mysql_num_rows:Gets number of rows in result.
     *
     * @return returns the number of rows in a result set
     * @param $db_query - resource result
     */
    function DB_Num_Rows($db_query)
    {
        return mysqli_num_rows($db_query);
    }

    /**
     * An alias of mysql_data_seek :Moves internal result pointer.
     *
     * @return Returns TRUE in case of successful end, FALSE in case of an error.
     * @param $db_query - resource result
     * @param $row_number
     */
    function DB_Data_Seek($db_query, $row_number)
    {
        return mysqli_data_seek($db_query, $row_number);
    }

    /**
     * An alias of mysql_insert_id():Gets the ID generated from the previous
     * INSERT operation.
     *
     * @return returns the ID generated for an AUTO_INCREMENT column by the
     * previous INSERT query using the given link_identifier and returns 0 if
     * the previous query does not generate an AUTO_INCREMENT value.
     * Update by azrael: not an alias anymore so use this function
     *                   instead of mysql_insert_id since multilang queries
     *                   may actually run several queries which may change
     *                   the result of mysql_insert_id
     */
    function DB_Insert_Id() {
        // return mysql_insert_id();
        return $this -> insert_id;
    }

    /**
     * An alias of mysql_affected_rows():Get the number of affected rows by the
     * last INSERT, UPDATE, REPLACE or DELETE query.
     *
     * @return  Returns the number of affected rows on success, and -1
     * if the last query failed.
     */
    function DB_Affected_Rows()
    {
        return mysqli_affected_rows();
    }

    /**
     * Gets a query string.
     *
     * @return string query string
     */
    function getQueryString()
    {
        return $this->QueryString;
    }

    /**
     * Gets database data dump, including the database structure.
     *
     * @return array data array
     */
    function getDataDump($tables, $currentTable, $currentLimit, $recordsExported, $fp, $link = 'db_link')
    {
        global $$link;
        global $application;
        $table_engine = CConf::get('mysql_table_engine');

        $data = "";
        if ($currentTable == 0)
        {
            fwrite($fp,"-- --------------------------------------------------------\n-- Avactis Shopping Cart Software\n-- Database Backup\n-- Generation Time: ".date("M d, Y at h:i A", time())."\n-- --------------------------------------------------------\n");
        }
        $start_time = time();

        while (isset($tables[$currentTable]))
        {
            $query = new DB_MYSQL_Lock_Tables();
            $query->addTableToLock($tables[$currentTable]['table_name'], DB_LOCK_MODE_WRITE);
            $this->DB_Exec($query);

            if (!((time() - $start_time)>=2))
            {
                if ($currentLimit == 0)
                {
                    $query = " SHOW CREATE TABLE ".$tables[$currentTable]['table_name'];
                    $query_result = $this->DB_Query($query);

                    $result = $this->DB_Fetch_Array($query_result, QUERY_RESULT_NUM);
                    $create_table_query = _ml_substr($result[1], 0, (_ml_strrpos($result[1], "\n")+1)).") ENGINE=$table_engine;";

                    $data = "\n\n-- \n-- Table structure for table `".$tables[$currentTable]['table_name']."`\n-- \n\nDROP TABLE IF EXISTS `".$tables[$currentTable]['table_name']."`;\n";
                    $data.= $create_table_query;
                    $data.= "\n\n-- \n-- Dumping data for table `".$tables[$currentTable]['table_name']."`\n-- \n\n";

                    fwrite($fp, $data);
                }

                $query = " SHOW COLUMNS FROM ".$tables[$currentTable]['table_name'];
                $query = $this->DB_Query($query);
                $columns = array();
                while ($table_column = $this->DB_Fetch_Array($query, QUERY_RESULT_ASSOC))
                {
                    $columns[] = $table_column["Field"];
                }
                $query = " SELECT * FROM ".$tables[$currentTable]['table_name']." LIMIT ".(100*$currentLimit).", 100";
                $query = $this->DB_Query($query);
                $insert = true;
                $data = "";
                while ($row_data = $this->DB_Fetch_Array($query, QUERY_RESULT_ASSOC))
                {
                    if ($insert)
                    {
                        $table_name = $tables[$currentTable]['table_name'];
                        $data.= "LOCK TABLES `$table_name` WRITE;\nINSERT INTO `$table_name` (`".implode("`, `", $columns)."`) VALUES \n";
                        $insert = false;
                    }
                    $row = array();
                    foreach ($row_data as $key => $value)
                    {
                        if (!is_int($key))
                        {
                            $row[] = "'".addcslashes(addslashes($value), "\0..\31")."'";
                        }
                    }
                    $data.= "(".implode(", ", $row)."),\n";
                    $recordsExported++;
                }
                if ($data && $data[_byte_strlen($data)-2] == ",")
                {
                    $data[_byte_strlen($data)-2] = ";";
                    fwrite($fp, $data."UNLOCK TABLES;\n");
                }
                $currentLimit++;
                if ($currentLimit*100 >= $tables[$currentTable]['records_count'])
                {
                    $currentLimit = 0;
                    $currentTable++;
                }
            }
            else
            {
                $query = new DB_MYSQL_Unlock_Tables();
                $this->DB_Exec($query);
                break;
            }
        }

        $retval = array(
                "currentTable" => $currentTable
               ,"currentLimit" => $currentLimit
               ,"recordsExported" => $recordsExported
//               ,"data" => $data
               );

        return $retval;
    }

    function importData($dump_file, $table_prefix, $offset, $str_offset)
    {
        CProfiler::ioStart($dump_file, 'read');
        $fp = fopen($dump_file, "r");
        fseek($fp, $offset);
        $this->getSQLCommandFromFile($fp, $offset, $str_offset, $table_prefix, time());
        fclose($fp);
        CProfiler::ioStop();
        return array("offset" => $offset, "str_offset" => $str_offset);
    }

    /**
     *
     *
     * @param
     * @return
     */
    function getSQLCommandFromFile($fp, &$offset, &$str_offset, $table_prefix, $start_time)
    {
        $SQLpart = "";
        while (!feof($fp))
        {
            if (((time() - $start_time) >= 2) && !$SQLpart)
            {
                return "restore_timeout";
            }
            $str = fgets($fp);
            $offset+= _byte_strlen($str);
            $str_offset++;
            $temp_str = trim($str);
            if (!_byte_strlen($temp_str))
            {
                continue;
            }
            if ($temp_str[0] == "#" || _ml_substr($temp_str, 0, 2) == "--")
            {
                continue;
            }
/*@
            if ( !($c_pos = _ml_strpos($temp_str, "#") === false) &&
                (!($dq_pos = _ml_strpos($temp_str, "\"") === false) || !($q_pos = _ml_strpos($temp_str, "'") === false))
               )
            {
                continue;
            }
            if (_ml_substr($temp_str, 0, 2) == "/*")
            {
                while (!feof($fp))
                {
                    $str = fgets($fp);
                    $offset+= _byte_strlen($str);
                    $temp_str = trim($str);
                    if ()
                    {

                    }
                }
                continue;
            }*/
            if ($temp_str[_byte_strlen($temp_str) - 1] == ";")
            {
                $SQLpart.= _byte_substr($temp_str, 0, (_byte_strlen($temp_str) - 1) );
                if (!(_ml_strpos($SQLpart, "DROP TABLE IF EXISTS ") === false))
                {
                    $SQLpart = strtr($SQLpart, array("DROP TABLE IF EXISTS `" => "DROP TABLE IF EXISTS `".$table_prefix));
                }
		elseif (!(_ml_strpos($SQLpart, "CREATE TABLE IF NOT EXISTS ") === false))
                {
                    $SQLpart = strtr($SQLpart, array("CREATE TABLE IF NOT EXISTS `" => "CREATE TABLE IF NOT EXISTS `".$table_prefix));
                }
                elseif (!(_ml_strpos($SQLpart, "CREATE TABLE ") === false))
                {
                    $SQLpart = strtr($SQLpart, array("CREATE TABLE `" => "CREATE TABLE `".$table_prefix));
                }
                elseif (!(_ml_strpos($SQLpart, "INSERT INTO ") === false))
                {
                    $SQLpart = strtr($SQLpart, array("INSERT INTO `" => "INSERT INTO `".$table_prefix));
                }
                elseif (!(_ml_strpos($SQLpart, "REPLACE INTO ") === false))
                {
                    $SQLpart = strtr($SQLpart, array("REPLACE INTO `" => "REPLACE INTO `".$table_prefix));
                }
                elseif (!(_ml_strpos($SQLpart, "LOCK TABLES ") === false))
                {
                    $SQLpart = strtr($SQLpart, array("LOCK TABLES `" => "LOCK TABLES `".$table_prefix));
                }
                elseif (!(_ml_strpos($SQLpart, "UPDATE ") === false))
                {
                    $SQLpart = strtr($SQLpart, array("UPDATE `" => "UPDATE `".$table_prefix));
                }
                elseif (!(_ml_strpos($SQLpart, "DELETE FROM ") === false))
                {
                    $SQLpart = strtr($SQLpart, array("DELETE FROM `" => "DELETE FROM `".$table_prefix));
                }
		elseif (!(_ml_strpos($SQLpart, "ALTER TABLE ") === false))
                {
                    $SQLpart = strtr($SQLpart, array("ALTER TABLE `" => "ALTER TABLE `".$table_prefix));
                }

                $this->DB_Query($SQLpart);
                $SQLpart = "";
            }
            else
            {
                $SQLpart.= $temp_str;
            }
        }
    }

    function dropTablesAfterRestore($tables)
    {
        $SQL = "DROP TABLE ";
        $SQL.= "`".implode("`, `", $tables)."`";
        $this->DB_Query($SQL);
//        foreach ($tables as $table_name)
//        {
//            $this->DB_Query("ALTER TABLE `restore_".$table_name."` RENAME `".$table_name."`");
//        }
    }

    function renameAndDeleteTablesAfterRestore($tables)
    {
        $tables_to_rename = array();
        foreach ($tables as $table_name)
        {
            $tables_to_rename[] = "`".$table_name."` TO `old_".$table_name."`";
            $tables_to_rename[] = "`restore_".$table_name."` TO `".$table_name."`";
        }
        $SQL = "RENAME TABLE ";
        $SQL.= implode(", ", $tables_to_rename);
        $this->DB_Query($SQL);
        $SQL = "DROP TABLE ";
        $SQL.= "`old_".implode("`, `old_", $tables)."`";
        $this->DB_Query($SQL);
    }

    function optimizeTables($Tables)
    {
        global $application;
        $tables_to_optimize = array();
        foreach ($Tables as $table_name)
        {
            $tables_to_optimize[] = "`".$table_name."`";
        }
        global $$link;
        $res = mysqli_query($$link, "OPTIMIZE TABLE ".implode(", ", $tables_to_optimize));
    }

    function table_move($table_name, $new_table_name)
    {
        $SQL = "DROP TABLE IF EXISTS ".$this->table_prefix.$new_table_name;
        $this->DB_Query($SQL);

        $SQL = "RENAME TABLE ".$this->table_prefix.$table_name." TO ".$this->table_prefix.$new_table_name;
        $this->DB_Query($SQL);
    }

    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */

    /**
     * Executes all needed multilang queries
     * for a delete/replace query
     */
    function DB_MultiLang_PreExec($QueryData)
    {
        global $application;

        $querytype = $QueryData -> getQueryType();

        // checkin the type of the query
        if ($querytype != DBQUERY_TYPE_DELETE
            && $querytype != DBQUERY_TYPE_REPLACE)
            return;

        $multilang_data = $QueryData -> getMultiLangData();
        // if no multilang data then return
        if (!is_array($multilang_data) || empty($multilang_data))
            return;

        $table_prefix = $application->getAppIni('DB_TABLE_PREFIX');
        list($ml_table, $ml_columns) = modApiFunc('MultiLang',
                                                  'getHeapTableInfo');
        $ml_table = $table_prefix . $ml_table;
        $table_list = array($ml_table);
        clearQueriesCache($table_list);

        foreach($multilang_data as $v)
        {
            @list($label, $key_field, $lng_code, $value, $key_value, $field) = $v;

            switch($querytype)
            {
                case DBQUERY_TYPE_DELETE:
                    $SQL = 'SELECT ' . $key_field . ' FROM ' .
                           $QueryData->getDeleteTable();
                    $SQL .= ' ' . str_replace('USING', ',', $QueryData->getUsingClause());

                    $where = $QueryData->getWhereList();
                    if (sizeof($where)>0)
                    {
                        $SQL .= ' WHERE ';
                        foreach ($where as $val)
                            $SQL .= $val.' ';
                    }
                    $SQL .= $QueryData->getOrderClause();
                    $SQL .= $QueryData->getLimitClause();
                    $rp = $this -> DB_Query($SQL);
                    if ($rp !== false)
                        while ($row = mysqli_fetch_row($rp))
                            $this -> DB_Query(
                                "DELETE FROM $ml_table WHERE label='" .
                                addslashes($label) . "' AND label_key='" .
                                addslashes($row[0]) . "'"
                            );
                    break;

                case DBQUERY_TYPE_REPLACE:
                    // for replace queries we assume the following:
                    // 1. all the fields are specified
                    // 2. the provided fields are enough to determine
                    //    if the record exists (to be replaced)
                    //    or not (to be inserted)
                    // 3. it is not replace into ... select * from ...

                    // queries like: replace into ... select * from ...
                    // will be ignored
                    if ($QueryData -> getCopyFromTable())
                        return;

                    $f_list = $QueryData -> getReplaceFields();

                    // if fields are not specified -> do nothing
                    if (!is_array($f_list) || empty($f_list))
                        return;

                    // if key field is not specified -> do nothing
                    if (!in_array($key_field, $f_list))
                        return;

                    $v_list = $QueryData -> getReplaceValues();
                    $key_index = array_search($key_field, $f_list);
                    $key_value = $v_list[$key_index];

                    // checking if the record exists for the specified key
                    $SQL = 'SELECT ' . $key_field . ' FROM ' .
                           $QueryData -> getReplaceTable() .
                           ' WHERE ' . $key_field . '=' . $key_value;
                    $rp = $this -> DB_Query($SQL);

                    // if the record exists then we need to remove the field
                    // to avoid default record changing
                    if ($rp !== false)
                        if (mysqli_fetch_row($rp))
                            if (in_array($field, $f_list))
                            {
                                $f_index = array_search($field, $f_list);
                                unset($QueryData -> ReplaceFields[$f_index]);
                                unset($QueryData -> ReplaceValues[$f_index]);
                            }
            }
        }
    }

    /**
     * Executes all needed multilang queries
     * for an insert/update/replace query
     */
    function DB_MultiLang_PostExec($QueryData)
    {
        global $application;

        $querytype = $QueryData -> getQueryType();

        // checking the type of the query
        if ($querytype != DBQUERY_TYPE_INSERT
            && $querytype != DBQUERY_TYPE_UPDATE
            && $querytype != DBQUERY_TYPE_REPLACE)
            return;

        // if the last query is unsuccessful -> do nothing
        if (!$this -> QueryResult)
            return;

        $multilang_data = $QueryData -> getMultiLangData();
        // if no multilang data then return
        if (!is_array($multilang_data) || empty($multilang_data))
            return;

        $table_prefix = $application->getAppIni('DB_TABLE_PREFIX');
        list($ml_table, $ml_columns) = modApiFunc('MultiLang',
                                                  'getHeapTableInfo');
        $ml_table = $table_prefix . $ml_table;
        $table_list = array($ml_table);
        clearQueriesCache($table_list);

        // keeping the last insert_id
        $default_insert_id = $this -> DB_Insert_Id();

        foreach($multilang_data as $v)
        {
            // indicator if we update single record
            $single_record = false;

            list($label, $key_field, $lng_code, $value) = $v;
            if (isset($v[4]))
                $key_value = $v[4];

            // getting the key value...
            switch($querytype)
            {
                case DBQUERY_TYPE_INSERT:
                    $f_list = $QueryData -> getInsertFields();
                    if (in_array($key_field, $f_list))
                    {
                        // if key field is defined...
                        $v_list = $QueryData -> getInsertValues();
                        $key_value = $v_list[array_search($key_field, $f_list)];
                        if (_ml_substr($key_value, 0, 1) == '\'' ||
                            _ml_substr($key_value, 0, 1) == '"')
                            $key_value = _ml_substr($key_value, 1, -1);
                    }
                    else
                    {
                         // otherwise assumes it should be
                         // the auto_increment field
                         // and gets its value...
                         $key_value = $default_insert_id;
                    }
                    // DBQUERY_TYPE_INSERT always inserts a single record
                    $single_record = true;
                    break;

                case DBQUERY_TYPE_REPLACE:
                    // queries like: replace into ... select * from ...
                    // will be ignored
                    if ($QueryData -> getCopyFromTable())
                        return;

                    $f_list = $QueryData -> getReplaceFields();
                    if (in_array($key_field, $f_list))
                    {
                        // if key field is defined...
                        $v_list = $QueryData -> getReplaceValues();
                        $key_value = $v_list[array_search($key_field, $f_list)];
                        if (_ml_substr($key_value, 0, 1) == '\'' ||
                            _ml_substr($key_value, 0, 1) == '"')
                            $key_value = _ml_substr($key_value, 1, -1);
                    }
                    else
                    {
                         // otherwise assumes it should be
                         // the auto_increment field
                         // and gets its value...
                         $key_value = $default_insert_id;
                    }
                    // we assume DBQUERY_TYPE_REPLACE always replaces
                    // a single record, though in theory it can delete
                    // several records before inserting a new one
                    $single_record = true;
                    break;

                case DBQUERY_TYPE_UPDATE:
                    if ($key_value)
                    {
                        // key value is specified - only one record is being changed
                        $single_record = true;
                    }
                    else
                    {
                        // if $key_field is not specified -> do nothing;
                        // to_do: decide if a fatal error should be thrown
                        if (!$key_field)
                            continue;

                        // by default consider there may be several records to update
                        $single_record = false;

                        // checking if the key is specified in the field list
                        $u_values = $QueryData -> getUpdateValues();
                        if (is_array($u_values))
                            foreach($u_values as $u_record)
                            {
                                list($u_field, $u_value) = explode(' = ', $u_record, 2);
                                if (trim($u_field) == $key_field)
                                {
                                    // key field is found...
                                    $u_value = trim($u_value);
                                    if (_ml_substr($u_value, 0, 1) == '\'' ||
                                        _ml_substr($u_value, 0, 1) == '"')
                                    {
                                        // $key_field is not an expression so only one record is being updated
                                        $key_value = _ml_substr($u_value, 1, -1);
                                        $single_record = true;
                                        break;
                                    }
                                    else
                                    {
                                        // a paranoial check... the key field may be included several times
                                        // so the last one should be considered
                                        $single_record = false;
                                    }
                                }
                            }
                    }
                    break;
            }

            if ($single_record)
            {
                // if only one record is being changed...
                // if label/label_key is empty -> do nothing!
                if (!$label || !$key_value)
                    continue;

                // trying to get the id for the record
                // (so replace does not rewrite the ml_id field)
                $id = modApiFunc('MultiLang', 'getMLID', $label, $key_value, $lng_code);
                $id_string = '';
                if ($id)
                    $id_string = ', ' . $ml_columns['ml_id'] . '=\'' .
                                 addslashes($id) . '\'';
                $this -> DB_Query(
                    "REPLACE $ml_table SET $ml_columns[label]='" .
                    addslashes($label) . "', $ml_columns[label_key]='" .
                    addslashes($key_value) . "', $ml_columns[lng]='" .
                    addslashes($lng_code) . "', $ml_columns[value]=$value" .
                    $id_string, 'db_link', false
                );
            }
            else
            {
                // here we have an update query
                // (a subject to be changed in future)
                // which changes several records in general
                // so we have to find all the records
                // and apply multilang actions to all of them

                // modifying the query to get all the records
                $SQL = ' SELECT ' . $key_field . ' FROM ';
                $SQL .= $QueryData->getUpdateTable() . ' ';
                $where = $QueryData->getWhereList();
                if (sizeof($where)>0)
                {
                    $SQL .= ' WHERE ';
                    foreach ($where as $val)
                        $SQL.= $val.' ';
                }

                // getting the records
                $rp = $this -> DB_Query($SQL);
                if ($rp !== false)
                {
                    while ($row = mysqli_fetch_row($rp))
                    {
                        // if label/key_value is empty -> do_nothing!
                        if (!$label || !$row[0])
                            continue;

                        // trying to get the id for the record
                        // (so replace does not rewrite the ml_id field)
                        $id = modApiFunc('MultiLang', 'getMLID', $label, $row[0], $lng_code);
                        $id_string = '';
                        if ($id)
                            $id_string = ', ' . $ml_columns['ml_id'] . '=\'' .
                                         addslashes($id) . '\'';
                        $this -> DB_Query(
                            "REPLACE $ml_table SET $ml_columns[label]='" .
                            addslashes($label) . "', $ml_columns[label_key]='" .
                            addslashes($row[0]) . "', $ml_columns[lng]='" .
                            addslashes($lng_code) . "', $ml_columns[value]=$value" .
                            $id_string, 'db_link', false
                        );
                    }
                }
            }
        }
    }

    /**
     * Prepares the SQL query string.
     *
     * @return
     * @param object $QueryData - the object of one of the DB_Select, DB_Table_Create,
     * DB_Insert, DB_Update, DB_Delete classes.
     */
    function PrepareSQL($QueryData)
    {
        $this->querytype = $QueryData->getQueryType();
        switch ($this->querytype)
        {
            case DBQUERY_TYPE_CREATE:
                  $this->QueryString = $this->PrepareCreateQuery($QueryData);
                  break;
            case DBQUERY_TYPE_SELECT:
                  $this->QueryString = $this->PrepareSelectQuery($QueryData);
                  break;
            case DBQUERY_TYPE_INSERT:
                  $table_list = array($QueryData->getInsertTable());
                  clearQueriesCache($table_list);
                  $this->QueryString = $this->PrepareInsertQuery($QueryData);
                  break;
            case DBQUERY_TYPE_REPLACE:
                  $table_list = array($QueryData->getReplaceTable());
                  clearQueriesCache($table_list);
                  $this->QueryString = $this->PrepareReplaceQuery($QueryData);
                  break;
            case DBQUERY_TYPE_MULTIPLE_INSERT:
                  $table_list = array($QueryData->getInsertTable());
                  clearQueriesCache($table_list);
                  $this->QueryString = $this->PrepareMultipleInsertQuery($QueryData);
                  break;
            case DBQUERY_TYPE_MULTIPLE_REPLACE:
                  $table_list = array($QueryData->getReplaceTable());
                  clearQueriesCache($table_list);
                  $this->QueryString = $this->PrepareMultipleReplaceQuery($QueryData);
                  break;
            case DBQUERY_TYPE_INSERT_SELECT:
                  $table_list = array($QueryData->getInsertTable());
                  clearQueriesCache($table_list);
                  $this->QueryString = $this->PrepareInsertSelectQuery($QueryData);
                  break;
            case DBQUERY_TYPE_UPDATE:
                  $table_list = array($QueryData->getUpdateTable());
                  clearQueriesCache($table_list);
                  $this->QueryString = $this->PrepareUpdateQuery($QueryData);
                  break;
            case DBQUERY_TYPE_DELETE:
                  $table_list = array($QueryData->getDeleteTable());
                  clearQueriesCache($table_list);
                  $this->QueryString = $this->PrepareDeleteQuery($QueryData);
                  break;
            case DBQUERY_TYPE_TABLE_DELETE:
                  $table_list = array($QueryData->getDeleteTables());
                  clearQueriesCache($table_list);
                  $this->QueryString = $this->PrepareTableDeleteQuery($QueryData);
                  break;
            case 'DEBUG':
                  $this->QueryString = $QueryData->Query;
                  break;
            case DBQUERY_TYPE_LOCK_TABLES:
                  $this->QueryString = $this->PrepareLockTablesQuery($QueryData);
                  break;
            case DBQUERY_TYPE_UNLOCK_TABLES:
                  $this->QueryString = $this->PrepareUnlockTablesQuery($QueryData);
                  break;
            default:
                  $this->QueryString = '';
                  break;
        }
    }

    /**
     * Executes the query.
     *
     */
    function DB_Exec()
    {
        global $application;

        // MultiLang edit: for some update queries the actual query may be empty...
        // MultiLang edit: keeping the insert_id only for insert and replace queries...
        if ($this->QueryString)
            $this->QueryResult = $this->DB_Query($this->QueryString, 'db_link', ($this -> querytype == DBQUERY_TYPE_INSERT || $this -> querytype == DBQUERY_TYPE_REPLACE));
        else
            $this->QueryResult = true;

        return $this->QueryResult;
    }

    /**
     * Prepares a data array, as a result of the query execution.
     *
     * @param int $result_type - returned data type of the SELECT query,
     * MYSQL_ASSOC is by default
     *
     */
    function DB_Result($result_type)
    {
        if (is_object($this->QueryResult))
        {
            $this->ResultArray = array();
            while ($row = $this->DB_Fetch_Array($this->QueryResult, $result_type))
            {
                array_push($this->ResultArray, $row);
            }
        }
        else
        {
            $this->ResultArray = $this->QueryResult;
        }
    }

    /**
     * Prepares the query of type CREATE TABLE.
     *
     * @ to finish the function
     * @return a string, containing the SQL query 'CREATE'
     * @param DB_Table_Create object $QueryData
     */
    function PrepareCreateQuery($QueryData)
    {
        self::$table_list_cache = null;

        $SQL = ' CREATE TABLE ';
        $table = $QueryData->getTable();
        $SQL.= $table.' (';
        $fields = $QueryData->getFields();
        if (sizeof($fields)>0)
        {
            foreach ($fields as $val)
            {
                $SQL.= $val['field_name'].' '.$val['field_type'].' '.$val['field_NULL'].' '.($val['field_default']!=''? 'default '.$val['field_default']:'').' '.$val['field_extras'].",\n";
            }
        }
        $keys = $QueryData->getKeys();
        if (sizeof($keys)>0)
        {
            $SQL.= 'PRIMARY KEY ('.implode(', ',$keys)."),\n";
        }
        $indexes = $QueryData->getIndexes();
        if (sizeof($indexes)>0)
        {
            foreach ($indexes as $key => $val)
            {
                if(_ml_strpos(_ml_strtoupper($val),"UNIQUE") === 0)
                {
                    $SQL.= ' '.$val.' ('.$key."),\n";
                }
                elseif(_ml_strpos(_ml_strtoupper($val),"FULLTEXT") === 0)
                {
                    $SQL.= ' '.$val.' ('.$key."),\n";
                }
                else
                {
                    $SQL.= ' INDEX '.$val.' ('.$key."),\n";
                }
            }
        }
        if ($SQL[(_byte_strlen($SQL)-2)]==',')
        {
            $SQL = _byte_substr($SQL, 0, (_byte_strlen($SQL)-2));
        }

        $table_engine = CConf::get('mysql_table_engine');
        $SQL.= ") ENGINE=$table_engine DEFAULT CHARSET=utf8";

        return $SQL;
    }

    /**
     * Prepares the query of type SELECT.
     *
     * @todj to add the checking of data existance and validity
     * @return a string, containing the SQL query 'SELECT'
     * @param DB_Select object $QueryData
     */
    function PrepareSelectQuery($QueryData)
    {
        // removing Multilang aliases from table list
        if (is_array($QueryData -> MultiLangAliases))
            foreach($QueryData -> MultiLangAliases as $k => $v)
                $QueryData -> delSelectTable($k, false);

        $SQL = ' SELECT ';
        $fields = $QueryData->getSelectFields();
        if (sizeof($fields)>0)
        {
            foreach ($fields as $key => $val)
            {
                $SQL.= $key.' '.$val.', ';
            }
            $SQL = _byte_substr($SQL, 0, (_byte_strlen($SQL)-2));
        }
        else
        {
            $SQL.= ' * ';
        }

        $SQL.= ' FROM ( ';
        $tables = $QueryData->getSelectTables();
        if (sizeof($tables)>0)
        {
            foreach ($tables as $key => $val)
            {
                if (!in_array($key, $QueryData->SelectTableAliases))
                {
                    $SQL.= $key.' '.$val.', ';
                }
            }
            $SQL = _byte_substr($SQL, 0, (_byte_strlen($SQL)-2));
        }
        else
        {
            //Error - No Tables to Select
        }
        $SQL.= ' ) ';

        $join = $QueryData->getJoinTables();
        if (sizeof($join)>0)
        {
            foreach ($join as $val)
            {
                $SQL.= ' '.$val['TYPE'].' JOIN '.$val['TABLE'];
                if ($val['ALIAS'])
                    $SQL .= ' AS ' . $val['ALIAS'];
                $SQL .= ' ON ';
                foreach ($val['CONDITION'] as $value)
                {
                    $SQL.= $value.' ';
                }
            }
        }

        $where = $QueryData->getWhereList();
        if (sizeof($where)>0)
        {
            $SQL.= ' WHERE ';
            foreach ($where as $val)
            {
                $SQL.= $val.' ';
            }
        }

        $group = $QueryData->getSelectGroups();
        if (sizeof($group)>0)
        {
            $SQL.= ' GROUP BY '.implode(', ', $group);
        }

        $having = $QueryData->getHavingList();
        if (sizeof($having)>0)
        {
            $SQL.= ' HAVING ';
            foreach ($having as $val)
            {
                $SQL.= $val.' ';
            }
        }

        $order = $QueryData->getSelectOrders();
        if (sizeof($order)>0)
        {
            $SQL.= ' ORDER BY '.implode(', ', $order);
        }

        $limit = $QueryData->getSelectLimits();
        if (sizeof($limit)>0)
        {
            $SQL.= ' LIMIT '.$limit;
        }
        return $SQL;
    }

    /**
     * Prepares the query of type LOCK TABLES.
     *
     * @return a string, containing the SQL query 'LOCK TABLES'
     * @param DB_LockTables object $QueryData
     */
    function PrepareLockTablesQuery($QueryData)
    {
        $SQL = ' LOCK TABLES ';
        $tables = $QueryData->getTablesToLock();
        if (sizeof($tables)>0)
        {
            foreach ($tables as $key => $info)
            {
                $SQL.= $key;
                if(!empty($info['alias']))
                {
                    $SQL.= ' AS ' . $info['alias'];
                }
                if(!empty($info['locking_mode']))
                {
                    $SQL.= ' ' . $info['locking_mode'];
                }
                $SQL.= ', ';
            }
            $SQL = _byte_substr($SQL, 0, (_byte_strlen($SQL)-2));
        }
        else
        {
            //Error - No Tables to Lock
        }
        return $SQL;
    }

    /**
     * Prepares the query of type UNLOCK TABLES.
     *
     * @return a string, containing the SQL query 'UNLOCK TABLES'
     */
    function PrepareUnlockTablesQuery()
    {
        $SQL = ' UNLOCK TABLES ';
        return $SQL;
    }

    /**
     * Prepares the query of type INSERT.
     *
     * @ to add the checkings
     * @return a string, containing the SQL query 'INSERT'
     * @param DB_Insert object $QueryData
     */
    function PrepareInsertQuery($QueryData)
    {
        $SQL = ' INSERT INTO ';
        $table = $QueryData->getInsertTable();
        $SQL.= $table.' ';
        $copyFromTableName = $QueryData->getCopyFromTable();
        if ($copyFromTableName)
        {
            $SQL.= 'SELECT * FROM '.$copyFromTableName;
        }
        else
        {
            $fields = $QueryData->getInsertFields();
            if (sizeof($fields)>0)
            {
                $SQL.= '('.implode(', ', $fields).')';
            }
            $SQL.= ' VALUES ';
            $values = $QueryData->getInsertValues();
            $SQL.= '('.implode(', ', $values).')';
        }
        return $SQL;
    }

    /**
     * Prepares the query of type REPLACE.
     *
     * @ to add the checkings
     * @return a string, containing the SQL query 'REPLACE'
     * @param DB_Replace object $QueryData
     */
    function PrepareReplaceQuery($QueryData)
    {
        $SQL = ' REPLACE INTO ';
        $table = $QueryData->getReplaceTable();
        $SQL.= $table.' ';
        $copyFromTableName = $QueryData->getCopyFromTable();
        if ($copyFromTableName)
        {
            $SQL.= 'SELECT * FROM '.$copyFromTableName;
        }
        else
        {
            $fields = $QueryData->getReplaceFields();
            if (sizeof($fields)>0)
            {
                $SQL.= '('.implode(', ', $fields).')';
            }
            $SQL.= ' VALUES ';
            $values = $QueryData->getReplaceValues();

            // MultiLang edit: in some cases the query may be empty
            if (!is_array($values) || empty($values))
                return '';

            $SQL.= '('.implode(', ', $values).')';
        }
        return $SQL;
    }

    function PrepareMultipleInsertQuery($QueryData)
    {
        $table = $QueryData->getInsertTable();
        $fields = $QueryData->getInsertFields();
        $values_array = $QueryData->getInsertValuesArray();

        $parsed_fields = array();
        foreach($fields as $field)
        {
            $parsed_fields[] = $QueryData->parseFieldName($field);
        }
        $SQL = 'INSERT '.$QueryData->getModifiers().' INTO '.$table;
        if(!empty($parsed_fields))
        {
            $SQL .= ' ('.implode(', ', $parsed_fields).')';
        };

        $SQL .= ' VALUES ('.implode('), (',$values_array).')';

        return $SQL;
    }

    function PrepareInsertSelectQuery($QueryData)
    {
        $SQL = ' INSERT '.$QueryData->getModifiers().' INTO '.$QueryData->getInsertTable().' ';

        $fields = $QueryData->getInsertFields();
        $parsed_fields = array();
        foreach ($fields as $field) {
            $parsed_fields[] = $QueryData->parseFieldName($field);
        }
        if (! empty($parsed_fields)) {
            $SQL .= '('.implode(', ', $parsed_fields).') ';
        };
        $SQL .= $this->PrepareSelectQuery($QueryData->getSelectQuery());

        return $SQL;
    }

    function PrepareMultipleReplaceQuery($QueryData)
    {
        $table = $QueryData->getReplaceTable();
        $fields = $QueryData->getReplaceFields();
        $values_array = $QueryData->getReplaceValuesArray();

        $parsed_fields = array();
        foreach($fields as $field)
        {
            $parsed_fields[] = $QueryData->parseFieldName($field);
        }
        $SQL = 'REPLACE INTO '.$table;
        if(!empty($parsed_fields))
        {
            $SQL .= ' ('.implode(', ', $parsed_fields).')';
        };

        $SQL .= ' VALUES ('.implode('), (',$values_array).')';

        return $SQL;
    }

    /**
     * Prepares the query of type UPDATE.
     *
     * @ to add the checkings
     * @return a string, containing the SQL query 'UPDATE'
     * @param DB_Update object $QueryData
     */
    function PrepareUpdateQuery($QueryData)
    {
        $SQL = ' UPDATE ';
        $table = $QueryData->getUpdateTable();
        $SQL.= $table.' ';
        $SQL.= ' SET ';
        $values = $QueryData->getUpdateValues();
        // MultiLang edit: for some update queries the actual query may be empty...
        if (!is_array($values) || empty($values))
            return '';
        $SQL.= implode(', ', $values);

        $where = $QueryData->getWhereList();
        if (sizeof($where)>0)
        {
            $SQL.= ' WHERE ';
            foreach ($where as $val)
            {
                $SQL.= $val.' ';
            }
        }
        return $SQL;
    }

    /**
     * Prepares the query of type DELETE.
     *
     * @ to add the checkings
     * @return a string, containing the SQL query 'UPDATE'
     * @param DB_Delete object $QueryData
     */
    function PrepareDeleteQuery($QueryData)
    {
        $SQL = ' DELETE FROM ';
        $table = $QueryData->getDeleteTable();
        $SQL.= $table.' '.$QueryData->getUsingClause();

        $where = $QueryData->getWhereList();
        if (sizeof($where)>0)
        {
            $SQL.= ' WHERE ';
            foreach ($where as $val)
            {
                $SQL.= $val.' ';
            }
        }
        $SQL .= $QueryData->getOrderClause();
        $SQL .= $QueryData->getLimitClause();
        return $SQL;
    }

    /**
     * Prepares the query of type DROP TABLE.
     *
     * @ add the checkings
     * @return a string, containing the SQL query 'DROP TABLE'
     * @param DB_Table_Delete object $QueryData
     */
    function PrepareTableDeleteQuery($QueryData)
    {
        self::$table_list_cache = null;

        $SQL = ' DROP TABLE ';
        $tables = $QueryData->getDeleteTables();
        if (is_array($tables))
        {
            $table_names = array();
            foreach ($tables as $table_name)
            {
                $table_names[] = $table_name;
            }
            $SQL.= implode(', ', $table_names);
        }
        else
        {
            $SQL.= $tables.' ';
        }
        return $SQL;
    }

    /**
     * A query string.
     *
     * @var string
     */
    var $QueryString;

    /**
     * A query result.
     *
     * @var string
     */
    var $QueryResult;

    /**
     * The data array, as a result of the query execution.
     *
     * @var array
     */
    var $ResultArray;

    var $table_prefix;

    var $querytype;

    /**
     * mysql_insert_id of last query except multilang ones
     */
    var $insert_id;

    static private $table_list_cache;

    /**#@-*/
}
?>