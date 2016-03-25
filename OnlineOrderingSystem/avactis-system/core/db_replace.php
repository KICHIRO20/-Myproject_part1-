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
 * @copyright Copyright &copy; 2013 HBWSL
 * @package Core
 * @author ag
 */
loadCoreFile('dbquery.php');

/**
 *       DB_Replace -                               SQL              Replace
 *
 * @access  public
 * @author Egor V. Derevyankin
 * @package Core
 */
class DB_Replace extends DBQuery
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     *                    DB_Replace
     *
     * @return
     * @param string $table
     */
    function DB_Replace($table)
    {
        global $application;
        $this->table_prefix = $application->getAppIni('DB_TABLE_PREFIX');
        $this->QueryType=DBQUERY_TYPE_REPLACE;
        $this->ReplaceTable=$this->table_prefix.$table;
        $this->ReplaceFields=array();
        $this->ReplaceValues=array();
        $this->CopyFromTable="";
        $this->OriginalTableName = $table;
        $this->MultiLangData = array();
    }

    /**
     *
     *
     * @return
     * @param string $value
     * @param string $field
     */
    function addReplaceValue($value, $field='')
    {
        if ($field!='')
        {
            $field = $this->parseFieldName($field);
            array_push($this->ReplaceFields, $field);
        }
        $value = "'".DBQuery::DBAddSlashes($value)."'";
        array_push($this->ReplaceValues, $value);
    }

    /**
     *
     *
     * @return
     * @param string $value
     * @param string $field
     */
    function addReplaceExpression($value, $field='')
    {
        if ($field!='')
        {
            $field = $this->parseFieldName($field);
            array_push($this->ReplaceFields, $field);
        }
        $value = DBQuery::DBAddSlashes($value);
        array_push($this->ReplaceValues, $value);
    }

    /**
     *
     *
     * @return string
     */
    function getReplaceTable()
    {
        return $this->ReplaceTable;
    }

    /**
     *                             ,
     *
     * @return array
     */
    function getReplaceFields()
    {
        return $this->ReplaceFields;
    }

    /**
     *
     *
     * @return array
     */
    function getReplaceValues()
    {
        return $this->ReplaceValues;
    }

    /**
     *
     *
     * @return array
     */
    function setCopyFromTable($tableName)
    {
        $this->CopyFromTable = $this->table_prefix.$tableName;
    }

    /**
     *
     *
     * @return array
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

    function addMultiLangReplaceValue($value, $field, $key_field,
                                      $class_name = '', $field_number = 0,
                                      $lng_code = 0)
    {
        $field = $this -> parseFieldName($field);
        array_push($this -> ReplaceFields, $field);

        $value = '\'' . DBQuery :: DBAddSlashes($value) . '\'';
        array_push($this -> ReplaceValues, $value);

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
                $key_field, $lng_code, $value, '',
                $this -> parseFieldName($field)
            );
    }

    function addMultiLangReplaceExpression($value, $field, $key_field,
                                           $class_name = '', $field_number = 0,
                                           $lng_code = 0)
    {
        $field = $this -> parseFieldName($field);
        array_push($this -> ReplaceFields, $field);

        $value = DBQuery :: DBAddSlashes($value);
        array_push($this -> ReplaceValues, $value);

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
     *
     *
     * @var  string
     */
    var $ReplaceTable;

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
     *
     *
     * @var  array
     */
    var $ReplaceFields;
    /**
     *
     *
     * @var  array
     */
    var $ReplaceValues;

    /**
     *
     *
     * @var  string
     */
    var $CopyFromTable;
}
?>