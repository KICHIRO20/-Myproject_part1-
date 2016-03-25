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
 * @author ag
 */
loadCoreFile('dbquery.php');

/**
 * DB_Table_Create is used to create tables in the database. The table array
 * is described in <Module_Name>::getTables();
 *
 * An example of usage the DB_Table_Create class is used in the install() method
 * of module:
 *<code>
 *      $tables = $this->getTables();
 *      $query = new DB_Table_Create($tables);
 *</code>
 * See meta description of the tables.
 * @see ExampleModule::getTables()
 *
 * @access  public
 * @author Alexandr Girin
 * @package Core
 */
class DB_Table_Create extends DBQuery
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * DB_Table_Create class constructor.
     *
     * @return
     * @param array $tables array of meta description of the tables.
     * @ change error messages to error description from resource file
     */
    function DB_Table_Create($tables)
    {
        global $application;
        $table_prefix = $application->getAppIni('DB_TABLE_PREFIX');
        foreach ($tables as $table_name => $table_properties)
        {
            $table_name = $table_prefix.$table_name;
            if (DB_MySQL::DB_isTableExists($table_name))
            {
                _fatal(array( "CODE" => "CORE_043"), $table_name);
            }
            $this->QueryType=DBQUERY_TYPE_CREATE;
            $this->CreateTable=$table_name;
            $this->CreateFields=array();
            $this->CreateKeys=array();
            $this->CreateIndexes=array();
            foreach ($table_properties['columns'] as $key => $field)
            {
                $this->addField($this->parseFieldName($field), $table_properties['types'][$key]);
            }

            if (isset($table_properties['primary'])&&sizeof($table_properties['primary'])>0)
            {
                $primary_key = array();
                foreach ($table_properties['primary'] as $pk)
                {
                    array_push($primary_key, $this->parseFieldName($table_properties['columns'][$pk]));
                }
                $this->addKey(implode(', ', $primary_key));
            }

            if (isset($table_properties['indexes'])&&sizeof($table_properties['indexes'])>0)
            {
                foreach ($table_properties['indexes'] as $index_name => $field_names)
                {
                    $field_names = str_replace(' ', '', $field_names);
                    $fields = explode(',', $field_names);
                    $index = array();
                    foreach ($fields as $field)
                    {
                        //         ,
                        if (is_int($_pos = _ml_strpos($field, '(')))
                        {
                            $_len = _ml_substr($field, $_pos);
                            $_field_without_len = _ml_substr($field, 0, $_pos);
                            array_push($index, $this->parseFieldName($table_properties['columns'][$_field_without_len]).$_len);
                        }
                        else
                        {
                            array_push($index, $this->parseFieldName($table_properties['columns'][$field]));
                        }
                    }
                    $this->addIndex(implode(', ', $index), $index_name);
                }
            }
            $application->db->getDB_Result($this);
        }
    }

    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */

    /**
     * Adds a new field in the created table.
     *
     * @return
     * @param string $field_name field name
     * @param string $field_type field type, the default is 'int'
     * @param boolean $field_NULL null value of the field, true is by default
     * @param string $field_default default value of the field, the default is ''
     * @param string $field_extras optional parameter, e.g. - 'auto_increment',
     * the default is ''
     */
    function addField($field_name, $field_type=DBQUERY_FIELD_TYPE_INT, $field_NULL='', $field_default='', $field_extras='')
    {
        $field = array(
                       'field_name'    => $field_name,
                       'field_type'    => $field_type,
                       'field_NULL'    => $field_NULL,
                       'field_default' => $field_default,
                       'field_extras'  => $field_extras
                      );
        array_push($this->CreateFields, $field);
    }

    /**
     * Adds a new key field.
     *
     * @return
     * @param string $key_field field name
     */
    function addKey($key_field)
    {
        array_push($this->CreateKeys, $key_field);
    }

    /**
     * Adds a new index.
     *
     * @return
     * @param string $index_field field name
     */
    function addIndex($index_field, $index_name='')
    {
        $this->CreateIndexes[$index_field] = $index_name;
    }

    /**
     * Gets a name of the created table.
     *
     * @return string table name
     */
    function getTable()
    {
        return $this->CreateTable;
    }

    /**
     * Gets a field array of the created table.
     *
     * @return array field array
     */
    function getFields()
    {
        return $this->CreateFields;
    }

    /**
     * Gets an array of primary keys on the created table.
     *
     * @return array ptimary key array
     */
    function getKeys()
    {
        return $this->CreateKeys;
    }

    /**
     * Gets an index array of the created table.
     *
     * @return array
     */
    function getIndexes()
    {
        return $this->CreateIndexes;
    }

    /**
     * A table name.
     *
     * @var string
     */
    var $CreateTable;
    /**
     * A field array of the created table.
     *
     * @var array
     */
    var $CreateFields;
    /**
     * A key array of the created table.
     *
     * @var array
     */
    var $CreateKeys;
    /**
     * An index array of the created table.
     *
     * @var array
     */
    var $CreateIndexes;

    /**#@-*/
}

/**
 * Class to get Create Table Object
 */
class DB_Table_Create_Query extends DBQuery
{
	//------------------------------------------------
	//               PUBLIC DECLARATION
	//------------------------------------------------

	/**#@+
	 * @access public
	 */

	/**
	 * DB_Table_Create class constructor.
	 *
	 * @return
	 * @param array $tables array of meta description of the tables.
	 * @ change error messages to error description from resource file
	 */
	function DB_Table_Create_Query($tables)
	{
		global $application;
		$table_prefix = $application->getAppIni('DB_TABLE_PREFIX');
		foreach ($tables as $table_name => $table_properties)
		{
			$table_name = $table_prefix.$table_name;
// 			if (DB_MySQL::DB_isTableExists($table_name))
// 			{
// 				_fatal(array( "CODE" => "CORE_043"), $table_name);
// 			}
			$this->QueryType=DBQUERY_TYPE_CREATE;
			$this->CreateTable=$table_name;
			$this->CreateFields=array();
			$this->CreateKeys=array();
			$this->CreateIndexes=array();
			foreach ($table_properties['columns'] as $key => $field)
			{
				$this->addField($this->parseFieldName($field), $table_properties['types'][$key]);
			}

			if (isset($table_properties['primary'])&&sizeof($table_properties['primary'])>0)
			{
				$primary_key = array();
				foreach ($table_properties['primary'] as $pk)
				{
					array_push($primary_key, $this->parseFieldName($table_properties['columns'][$pk]));
				}
				$this->addKey(implode(', ', $primary_key));
			}

			if (isset($table_properties['indexes'])&&sizeof($table_properties['indexes'])>0)
			{
				foreach ($table_properties['indexes'] as $index_name => $field_names)
				{
					$field_names = str_replace(' ', '', $field_names);
					$fields = explode(',', $field_names);
					$index = array();
					foreach ($fields as $field)
					{
						//         ,
						if (is_int($_pos = _ml_strpos($field, '(')))
						{
							$_len = _ml_substr($field, $_pos);
							$_field_without_len = _ml_substr($field, 0, $_pos);
							array_push($index, $this->parseFieldName($table_properties['columns'][$_field_without_len]).$_len);
						}
						else
						{
							array_push($index, $this->parseFieldName($table_properties['columns'][$field]));
						}
					}
					$this->addIndex(implode(', ', $index), $index_name);
				}
			}
		}
	}

	/**#@-*/

	//------------------------------------------------
	//              PRIVATE DECLARATION
	//------------------------------------------------

	/**#@+
	 * @access private
	 */

	/**
	 * Adds a new field in the created table.
	 *
	 * @return
	 * @param string $field_name field name
	 * @param string $field_type field type, the default is 'int'
	 * @param boolean $field_NULL null value of the field, true is by default
	 * @param string $field_default default value of the field, the default is ''
	 * @param string $field_extras optional parameter, e.g. - 'auto_increment',
	 * the default is ''
	 */
	function addField($field_name, $field_type=DBQUERY_FIELD_TYPE_INT, $field_NULL='', $field_default='', $field_extras='')
	{
		$field = array(
				'field_name'    => $field_name,
				'field_type'    => $field_type,
				'field_NULL'    => $field_NULL,
				'field_default' => $field_default,
				'field_extras'  => $field_extras
		);
		array_push($this->CreateFields, $field);
	}

	/**
	 * Adds a new key field.
	 *
	 * @return
	 * @param string $key_field field name
	 */
	function addKey($key_field)
	{
		array_push($this->CreateKeys, $key_field);
	}

	/**
	 * Adds a new index.
	 *
	 * @return
	 * @param string $index_field field name
	 */
	function addIndex($index_field, $index_name='')
	{
		$this->CreateIndexes[$index_field] = $index_name;
	}

	/**
	 * Gets a name of the created table.
	 *
	 * @return string table name
	 */
	function getTable()
	{
		return $this->CreateTable;
	}

	/**
	 * Gets a field array of the created table.
	 *
	 * @return array field array
	 */
	function getFields()
	{
		return $this->CreateFields;
	}

	/**
	 * Gets an array of primary keys on the created table.
	 *
	 * @return array ptimary key array
	 */
	function getKeys()
	{
		return $this->CreateKeys;
	}

	/**
	 * Gets an index array of the created table.
	 *
	 * @return array
	 */
	function getIndexes()
	{
		return $this->CreateIndexes;
	}

	/**
	 * A table name.
	 *
	 * @var string
	 */
	var $CreateTable;
	/**
	 * A field array of the created table.
	 *
	 * @var array
	 */
	var $CreateFields;
	/**
	 * A key array of the created table.
	 *
	 * @var array
	 */
	var $CreateKeys;
	/**
	 * An index array of the created table.
	 *
	 * @var array
	 */
	var $CreateIndexes;

	/**#@-*/
}



?>