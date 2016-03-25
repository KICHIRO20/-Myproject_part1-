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
loadCoreFile('db_mysql.php');

/**
 * DB_Insert class is used to generate the SQL query of type Insert.
 *
 * Example of usage DB_Insert, to generate INSERT<br> query.
 * When inserting the value of only one field, the values of other ones are
 * on by default:
 *<code>
 *  $db_insert = new DB_Insert('categories');
 *  $db_insert->addInsertValue('Games', 'category_name');
 *  $application->db->PrepareSQL($db_insert);
 *  $application->db->DB_Exec();
 *</code><br>
 * When inserting values of all the fields, i.e. when calling the addInsertValue()
 * function, the second parameter - a field name might not be specified.
 * But it is important to keep the sequence of fields in the database:
 *<code>
 *  $db_insert = new DB_Insert('categories');
 *  $db_insert->addInsertValue(2);                   //  category_id
 *  $db_insert->addInsertValue(1);                   // the value of the field parent_category_id
 *  $db_insert->addInsertValue('Games');             // the value of the field category_name
 *  $db_insert->addInsertValue('Games Description'); // the value of the field category_descr
 *  $application->db->PrepareSQL($db_insert);
 *  $application->db->DB_Exec();
 *</code>
 *
 * @access  public
 * @author Alexandr Girin
 * @package Core
 */
class DB_Insert extends DBQuery
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * DB_Insert class constructor.
     *
     * @return
     * @param string $table the table name
     */
    function DB_Insert($table)
    {
        global $application;
        static $table_prefix = -1;
        if($table_prefix === -1)
        {
            $table_prefix = $application->getAppIni('DB_TABLE_PREFIX');
        }
        $this->table_prefix = $table_prefix;
        $this->QueryType=DBQUERY_TYPE_INSERT;
        $this->InsertTable=$this->table_prefix.$table;
        $this->InsertFields=array();
        $this->InsertValues=array();
        $this->CopyFromTable="";
        $this->OriginalTableName = $table;
        $this->MultiLangData = array();
    }

    /**
     * Adds the field value.
     *
     * @return
     * @param string $value the value
     * @param string $field the field name
     */
    function addInsertValue($value, $field='')
    {
        if ($field!='')
        {
            $field = $this->parseFieldName($field);
            array_push($this->InsertFields, $field);
        }
        if(DB_NULL === $value || $value === NULL)
        {
            $value = "NULL";
        }
        else
        {
            $value = "'".DBQuery::DBAddSlashes($value)."'";
        }
        array_push($this->InsertValues, $value);
    }

    /**
     * Adds the field value in the form of expression.
     *
     * @return
     * @param string $value the value
     * @param string $field the field name
     */
    function addInsertExpression($value, $field='')
    {
        if ($field!='')
        {
            $field = $this->parseFieldName($field);
            array_push($this->InsertFields, $field);
        }
        $value = DBQuery::DBAddSlashes($value);
        array_push($this->InsertValues, $value);
    }

    /**
     * Gets the table name.
     *
     * @return string the table name
     */
    function getInsertTable()
    {
        return $this->InsertTable;
    }

    /**
     * Gets a list of fields, to which values are assigned.
     *
     * @return array the field array
     */
    function getInsertFields()
    {
        return $this->InsertFields;
    }

    /**
     * Gets a list of values.
     *
     * @return array the value array
     */
    function getInsertValues()
    {
        return $this->InsertValues;
    }

    /**
     * Copies from the other table.
     *
     * @return array the value array
     */
    function setCopyFromTable($tableName)
    {
        $this->CopyFromTable = $this->table_prefix.$tableName;
    }

    /**
     * Gets the name of the table which is copied from.
     *
     * @return array the value array
     */
    function getCopyFromTable()
    {
        return $this->CopyFromTable;
    }

    /**
     * functions for MultiLang support
     */
    function getMultiLangData()
    {
        return $this -> MultiLangData;
    }

    function addMultiLangInsertValue($value, $field, $key_field = '',
                                     $class_name = '', $field_number = 0,
                                     $lng_code = 0)
    {
        $field = $this -> parseFieldName($field);
        array_push($this -> InsertFields, $field);

        if(DB_NULL === $value || $value === NULL)
            $value = "NULL";
        else
            $value = '\'' . DBQuery :: DBAddSlashes($value) . '\'';
        array_push($this -> InsertValues, $value);

        // if $lng_code is not specified retrive it
        if (!$lng_code)
            $lng_code = modApiFunc('MultiLang', 'getLanguageNumber');
        elseif (!is_numeric($lng_code))
            $lng_code = modApiFunc('MultiLang', '_readLanguageNumber', $lng_code);

        // if class_name is not specified assume
        // it is active in MultiLang class
        if (!$class_name)
            $class_name = modApiFunc('MultiLang', 'getClass');

        // parsing the key field
        $key_field = $this -> parseFieldName($key_field);

        // if non-default language is chosen...
        if ($lng_code != modApiFunc('MultiLang', 'getDefaultLanguageNumber'))
            $this -> MultiLangData[] = array(
                modApiFunc('MultiLang', 'mapMLField',
                           $this -> OriginalTableName,
                           $this -> parseFieldName($field),
                           $class_name, $field_number),
                $key_field, $lng_code, $value
            );
    }

    function addMultiLangInsertExpression($value, $field, $key_field = '',
                                          $class_name = '', $field_number = 0,
                                          $lng_code = 0)
    {
        $field = $this -> parseFieldName($field);
        array_push($this -> InsertFields, $field);

        if(DB_NULL === $value || $value === NULL)
            $value = "NULL";
        else
            $value = DBQuery :: DBAddSlashes($value);
        array_push($this -> InsertValues, $value);

        // if $lng_code is not specified retrive it
        if (!$lng_code)
            $lng_code = modApiFunc('MultiLang', 'getLanguageNumber');
        elseif (!is_numeric($lng_code))
            $lng_code = modApiFunc('MultiLang', '_readLanguageNumber', $lng_code);

        // if class_name is not specified assume
        // it is active in MultiLang class
        if (!$class_name)
            $class_name = modApiFunc('MultiLang', 'getClass');

        // parsing the key field
        $key_field = $this -> parseFieldName($key_field);

        // if non-default language is chosen...
        if ($lng_code != modApiFunc('MultiLang', 'getDefaultLanguageNumber'))
            $this -> MultiLangData[] = array(
                modApiFunc('MultiLang', 'mapMLField',
                           $this -> OriginalTableName,
                           $this -> parseFieldName($field),
                           $class_name, $field_number),
                $key_field, $lng_code, $value
            );
    }

    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */

    /**
     * The table name, where a record is inserted.
     *
     * @var  string
     */
    var $InsertTable;


    /**
     * The array of fields, where values are inserted.
     *
     * @var  array
     */
    var $InsertFields;

    /**
     * The array of inserted values.
     *
     * @var  array
     */
    var $InsertValues;

    /**
     * original table name (need for multilang support)
     */
    var $OriginalTableName;

    /**
     * The array of multilang data.
     *
     * @var  array of sets (label, key, lng, value)
     */
    var $MultiLangData;

    /**
     * The array of inserted values.
     *
     * @var  string
     */
    var $CopyFromTable;
}
?>