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
 * @author HBWSL
 * @verison 1.0
 * @package Core
 * @access public
 * Function to do_db_delta to create "Create Table" queries
 * If table already exist it will create and Alter command to modifiy the changes.
 */

/**
 * @param mixed $queries
 * @param boolean $execute, default true
 */
function do_dbDelta( $queries = '', $execute = true ) {
	global $application;

	/** Check the queries is empty **/
	if(empty($queries)){
		return "no query defined";
	}

	/** Check the $queries is an array if not creating array using semicolen(;) as deliminator **/
	if ( !is_array($queries) ) {
		$queries = explode( ';', $queries );
		$queries = array_filter( $queries );
	}

	/** Fileter for feature Use **/
	$queries = apply_filters( 'avactis_do_dbdelta_queries', $queries );

	$create_queries = array(); // Creation Queries
	$dml_queries = array(); // Insertion Queries
	$for_update = array();

	// Create a tablename index for an array ($create_queries) of queries
	foreach($queries as $qry) {
		if (preg_match("|CREATE TABLE ([^ ]*)|", $qry, $matches)) {
			$create_queries[ trim( $matches[1], '`' ) ] = $qry;
			$for_update[$matches[1]] = 'Created table '.$matches[1];
		} else if (preg_match("|CREATE DATABASE ([^ ]*)|", $qry, $matches)) {
			array_unshift($create_queries, $qry);
		} else if (preg_match("|INSERT INTO ([^ ]*)|", $qry, $matches)) {
			$dml_queries[] = $qry;
		} else if (preg_match("|UPDATE ([^ ]*)|", $qry, $matches)) {
			$dml_queries[] = $qry;
		} else {
			// Unrecognized query type
		}
	}

	/**
	 * Filter the dbDelta SQL queries for creating tables and/or databases.
	 *
	 * Queries filterable via this hook contain "CREATE TABLE" or "CREATE DATABASE".
	 * @param array $create_queries An array of dbDelta create SQL queries.
	 */
	$create_queries = apply_filters( 'avactis_do_dbdelta_create_queries', $create_queries );

	/**
	 * Filter the dbDelta SQL queries for inserting or updating.
	 *
	 * Queries filterable via this hook contain "INSERT INTO" or "UPDATE".
	 *
	 * @param array $dml_queries An array of dbDelta insert or update SQL queries.
	 */
	$dml_queries = apply_filters( 'avactis_do_dbdelta_insert_queries', $dml_queries );

	/** Getting the DATABASE name **/
	$database = $application->getAppIni('DB_NAME');

	/** array of All the tables in the database **/
	$db_Object=$application->db;
	$all_tables = $db_Object->DB_List_Tables($database);

	foreach ( $create_queries as $table => $qry ) {
		// Upgrade global tables only for the main site. Don't upgrade at all if DO_NOT_UPGRADE_all_tables is defined.
		if ( $db_Object->DB_isTableExists($table) ){
			if( false || defined( 'DO_NOT_UPGRADE_all_tables' ) ){
				unset( $create_queries[ $table ], $for_update[ $table ] );
				continue;
			}
		}else{
		/** else the table does not exist skipping the further process**/
			continue;
		}

		/** Getting Table MetaData by using describe tabe sql **/
		$tablefields = $db_Object->DB_Query("DESCRIBE {$table};");

		if ( ! $tablefields )
			continue;

		/** Clear the field and index arrays. **/
		$column_fields = $indices = array();

		/** Get all of the field names in the query from between the parentheses. **/
		preg_match("|\((.*)\)|ms", $qry, $match2);
		$qryline = trim($match2[1]);

		// Separate field lines into an array.
		$flds = explode("\n", $qryline);
		//     : Remove this?
		//echo "<hr/><pre>\n".print_r(strtolower($table), true).":\n".print_r($create_queries, true)."</pre><hr/>";

		// For every field line specified in the query.
		foreach ($flds as $fld) {

			// Extract the field name.
			preg_match("|^([^ ]*)|", trim($fld), $fvals);
			$fieldname = trim( $fvals[1], '`' );

			// Verify the found field name.
			$validfield = true;
			switch (strtolower($fieldname)) {
				case '':
				case 'primary':
				case 'index':
				case 'fulltext':
				case 'unique':
				case 'key':
					$validfield = false;
					$indices[] = trim(trim($fld), ", \n");
					break;
			}
			$fld = trim($fld);

			// If it's a valid field, add it to the field array.
			if ($validfield) {
				$column_fields[strtolower($fieldname)] = trim($fld, ", \n");
			}
		}

		// For every field in the table.
		foreach ($tablefields as $tablefield) {

			// If the table field exists in the field array ...
			if (array_key_exists(strtolower($tablefield['Field']), $column_fields)) {

				// Get the field type from the query.
				preg_match("|".$tablefield['Field']." ([^ ]*( unsigned)?)|i", $column_fields[strtolower($tablefield['Field'])], $matches);
				$fieldtype = $matches[1];
				if($fieldtype=='int'){
					$fieldtype='int(11)';
				}

				// Is actual field type different from the field type in query?
				if ($tablefield['Type'] != $fieldtype) {
					// Add a query to change the column type
					$create_queries[] =
						"ALTER TABLE {$table} CHANGE COLUMN {$tablefield['Field']} " . $column_fields[strtolower($tablefield['Field'])];
					$for_update[$table.'.'.$tablefield->Field] =
						"Changed type of {$table}.{$tablefield['Field']} from {$tablefield['Type']} to {$fieldtype}";
				}

				// Get the default value from the array
				//     : Remove this?
				//echo "{$column_fields[strtolower($tablefield->Field)]}<br>";

				if (preg_match("| DEFAULT '(.*?)'|i", $column_fields[strtolower($tablefield['Field'])], $matches)) {
					$default_value = $matches[1];
					if ($tablefield['Default'] != $default_value) {
						// Add a query to change the column's default value
						$create_queries[] = "ALTER TABLE {$table} ALTER COLUMN {$tablefield['Field']} SET DEFAULT '{$default_value}'";
						$for_update[$table.'.'.$tablefield['Field']] =
							"Changed default value of {$table}.{$tablefield['Field']} from {$tablefield['Default']} to {$default_value}";
					}
				}

				// Remove the field from the array (so it's not added).
				unset($column_fields[strtolower($tablefield['Field'])]);
			} else {
				// This field exists in the table, but not in the creation queries?
			}
		}

		// For every remaining field specified for the table.
		foreach ($column_fields as $fieldname => $fielddef) {
			// Push a query line into $create_queries that adds the field to that table.
			$create_queries[] = "ALTER TABLE {$table} ADD COLUMN $fielddef";
			$for_update[$table.'.'.$fieldname] = 'Added column '.$table.'.'.$fieldname;
		}

		// Index stuff goes here. Fetch the table index structure from the database.
		$tableindices = $db_Object->DB_Query("SHOW INDEX FROM {$table};");

		if ($tableindices) {
			// Clear the index array.
			unset($index_ary);

			// For every index in the table.
			foreach ($tableindices as $tableindex) {

				// Add the index to the index data array.
				$keyname = $tableindex['Key_name'];
				$index_ary[$keyname]['columns'][] = array('fieldname' => $tableindex['Column_name'], 'subpart' => $tableindex['Sub_part']);
				$index_ary[$keyname]['unique'] = ($tableindex['Non_unique'] == 0)?true:false;
			}

			// For each actual index in the index array.
			foreach ($index_ary as $index_name => $index_data) {


				// Build a create string to compare to the query.
				$index_string = '';
				if ($index_name == 'PRIMARY') {
					$index_string .= 'PRIMARY ';
				} else if($index_data['unique']) {
					$index_string .= 'UNIQUE ';
				}
				$index_string .= 'KEY ';
				if ($index_name != 'PRIMARY') {
					$index_string .= $index_name;
				}
				$index_columns = '';

				// For each column in the index.
				foreach ($index_data['columns'] as $column_data) {
					if ($index_columns != '') $index_columns .= ',';

					// Add the field to the column list string.
					$index_columns .= $column_data['fieldname'];
					if ($column_data['subpart'] != '') {
						$index_columns .= '('.$column_data['subpart'].')';
					}
				}

				/** check if the key is Index **/
				if($index_string=='KEY '.$index_name){
					$index_strin_temp="INDEX ".$index_name.' ('.$index_columns.')';
				}else{
					$index_strin_temp="";
				}

				// Add the column list to the index create string.

				$index_string .= '('.$index_columns.')';

				if (!(($aindex = array_search($index_string, $indices)) === false)||!(($aindex = array_search($index_strin_temp, $indices)) === false)) {
					unset($indices[$aindex]);
					//     : Remove this?
					//echo "<pre style=\"border:1px solid #ccc;margin-top:5px;\">{$table}:<br />Found index:".$index_string."</pre>\n";
				}
				//     : Remove this?
				//else echo "<pre style=\"border:1px solid #ccc;margin-top:5px;\">{$table}:<br />
				//<b>Did not find index:</b>".$index_string."<br />".print_r($indices, true)."</pre>\n";
			}
		}

		// For every remaining index specified for the table.
		foreach ( (array) $indices as $index ) {
			// Push a query line into $create_queries that adds the index to that table.
			$create_queries[] = "ALTER TABLE {$table} ADD $index";
			$for_update[] = 'Added index ' . $table . ' ' . $index;
		}

		// Remove the original table creation query from processing.
		unset( $create_queries[ $table ], $for_update[ $table ] );
	}

	$allqueries = array_merge($create_queries, $dml_queries);
	if ($execute) {
		foreach ($allqueries as $query) {
			//     : Remove this?
			//echo "<pre style=\"border:1px solid #ccc;margin-top:5px;\">".print_r($query, true)."</pre>\n";
 			$db_Object->DB_Query($query);
		}
	}
	return $for_update;
}

/**
 * do_dbDetla with avactis query structure
 * @param array $table
 * @param boolean $execute, default true
 */
function avactis_db_delta($tableArray, $execute = true ) {
	global $application;
	foreach($tableArray as $tableName=>$columns){
		$queryArray[]=$application->db->PrepareCreateQuery(new DB_Table_Create_Query(array($tableName=>$columns)));
	}
	return do_dbDelta($queryArray,$execute);

}
?>