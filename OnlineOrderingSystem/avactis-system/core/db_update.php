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
 * DB_Update class is used to generate the SQL query of type Update.
 *
 * Example of usage the DB_Update class to generate the UPDATE query.
 *<code>
 *  $db_update = new DB_Update('categories');
 *  $db_update->addUpdateValue('category_name', 'FILM');
 *  $db_update->WhereValue('category_id', DB_EQ, 1);
 *  $application->db->PrepareSQL($db_update);
 *  $application->db->DB_Exec();
 *</code>
 * When generating the query conditions functions WhereField() or WhereValue() can be used along with WhereAND(), WhereOR(),
 * WhereNOT, addWhereOpenSection(), addWhereCloseSection(), for example:
 * <code>
 * $db_update->addWhereOpenSection();
 * $db_update->WhereField('field_name1', DB_NEQ, 'field_name2');
 * $db_update->WhereOR();
 * $db_update->WhereValue('field_name3', DB_EQ, 4);
 * $db_update->addWhereCloseSection();
 * </code>
 * The query condition will be the following:<br>
 * WHERE (field_name1<>field_name2 OR field_name3='4')
 *
 * @access  public
 * @author Alexandr Girin
 * @package Core
 */
class DB_Update extends DBQuery
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * DB_Update class constructor.
     *
     * @return
     * @param string $table table name
     */
    function DB_Update($table)
    {
        global $application;
        $table_prefix = $application->getAppIni('DB_TABLE_PREFIX');
        $this->QueryType=DBQUERY_TYPE_UPDATE;
        $this->UpdateTables = array($table_prefix.$table);
        $this->UpdateValues=array();
        $this->WhereList = array();
        $this->OriginalTableName = $table;
        $this->MultiLangData = array();

    }

    function addUpdateTable($table)
    {
        global $application;
        $table_prefix = $application->getAppIni('DB_TABLE_PREFIX');
        $this->UpdateTables[] = $table_prefix.$table;
    }

    /**
     * Replaces the UpdateTables array with the provided table
     * Usage: useful when the table should be set in initQuery
     */
    function replaceUpdateTable($table)
    {
        global $application;
        $table_prefix = $application->getAppIni('DB_TABLE_PREFIX');
        $this->UpdateTables = array($table_prefix.$table);
    }

    /**
     * Adds the field, whose value will be updated.
     *
     * @return
     * @param string $field field name
     * @param string $value update value
     */
    function addUpdateValue($field, $value)
    {
        if($value === DB_NULL || $value === NULL)
        {
            $value = "NULL";
        }
        else
        {
            $value = "'".$this->DBAddSlashes($value)."'";
        }
        array_push($this->UpdateValues, $field.' = '.$value);
    }

    /**
     * Adds the field, whose value will be updated. As a value, an expression
     * is passed.
     *
     * @return
     * @param string $field field name
     * @param string $expression update value
     */
    function addUpdateExpression($field, $expression)
    {
        array_push($this->UpdateValues, $field.' = '.$expression);
    }

    /**
     * Gets a table name.
     *
     * @return string table name
     */
    function getUpdateTable()
    {
        return implode(', ', $this->UpdateTables);
    }

    /**
     * Gets a list of update values.
     *
     * @return array value array.
     */
    function getUpdateValues()
    {
        return $this->UpdateValues;
    }

    /**
     * functions for MultiLang support
     */
    function getMultiLangData()
    {
        return $this -> MultiLangData;
    }

    function addMultiLangUpdateValue($field, $value, $key_field,
                                     $key_value = '', $class_name = '',
                                     $field_number = 0, $lng_code = 0)
    {
        if($value === DB_NULL || $value === NULL)
            $value = "NULL";
        else
            $value = "'" . $this->DBAddSlashes($value) . "'";

        // if $lng_code is not specified retrive it
        if (!$lng_code)
            $lng_code = modApiFunc('MultiLang', 'getLanguageNumber');
        elseif (!is_numeric($lng_code))
            $lng_code = modApiFunc('MultiLang', '_readLanguageNumber', $lng_code);

        // if class_name is not specified assume
        // it is active in MultiLang class
        if (!$class_name)
            $class_name = modApiFunc('MultiLang', 'getClass');

        if ($lng_code != modApiFunc('MultiLang', 'getDefaultLanguageNumber'))
            $this -> MultiLangData[] = array(
                modApiFunc('MultiLang', 'mapMLField',
                           $this -> OriginalTableName,
                           $this -> parseFieldName($field),
                           $class_name, $field_number),
                $key_field, $lng_code, $value, $key_value
            );
        else
            array_push($this->UpdateValues, $field . ' = ' . $value);
    }

    function addMultiLangUpdateExpression($field, $expression, $key_field,
                                          $key_value = '', $class_name = '',
                                          $field_number = 0, $lng_code = 0)
    {
        // if $lng_code is not specified retrive it
        if (!$lng_code)
            $lng_code = modApiFunc('MultiLang', 'getLanguageNumber');
        elseif (!is_numeric($lng_code))
            $lng_code = modApiFunc('MultiLang', '_readLanguageNumber', $lng_code);

        // if class_name is not specified assume
        // it is active in MultiLang class
        if (!$class_name)
            $class_name = modApiFunc('MultiLang', 'getClass');

        if ($lng_code != modApiFunc('MultiLang', 'getDefaultLanguage'))
            $this -> MultiLangData[] = array(
                modApiFunc('MultiLang', 'mapMLField',
                           $this -> OriginalTableName,
                           $this -> parseFieldName($field),
                           $class_name, $field_number),
                $key_field, $lng_code, $expression, $key_value
            );
        else
            array_push($this->UpdateValues, $field . ' = ' . $expression);
    }

    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */

    /**
     * The table name, which is updated.
     *
     * @var  string variable
     */
    var $UpdateTable;
    var $UpdateTables;

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
     * Tha array of fields and update values.
     *
     * @var  string variable
     */
    var $UpdateValues;

    /**#@-*/
}
?>