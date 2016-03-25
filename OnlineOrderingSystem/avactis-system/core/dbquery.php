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
 * DBQuery class is used to generate SQL queries.
 *
 * This class is a parent one for: DB_Select, DB_Table_Create,
 * DB_Insert, DB_Update, DB_Delete. In this class, common variables and methods are defined for all these classes.
 * Variables:
 * <ul>
 *  <li>$DBQuery::QueryType  defines a query type (select, create table, insert, update, delete)
 *  <li>$DBQuery::WhereList - defines an array of query conditions of type select, update, delete
 * </ul>
 * Methods:
 * <ul>
 *  <li>DBQuery::WhereField($operand1, $operator, $operand2) adds a condition to the condition array
 * DBQuery::$WhereList, the second operand is a table field
 *  <li>DBQuery::WhereValue($operand1, $operator, $operand2)  adds a condition to the condition array
 * DBQuery::$WhereList, the second operand is a value
 *  <li>DBQuery::WhereAND() - adds the logical operator AND for conditions, specified by method
 * DBQuery::$WhereList
 *  <li>DBQuery::WhereOR() - adds the logical operator OR for conditions, specified by method
 * DBQuery::$WhereList
 *  <li>DBQuery::WhereNOT() - adds the logical operator NOT bfore the condition, specified by method
 * DBQuery::$WhereList
 *  <li>DBQuery::addWhereOpenSection() - adds an open bracket to the array DBQuery::$WhereList
 *  <li>DBQuery::addWhereCloseSection() - adds a close bracket to the array DBQuery::$WhereList
 *  <li>DBQuery::fCount($field_name) - a SQL alias for Count function
 *  <li>DBQuery::fPassword($password) - a SQL alias for Password function
 *  <li>DBQuery::getQueryType() - returns the query type, specified in DBQuery::$QueryType
 *  <li>DBQuery::getWhereList() - returns the condition array, specified in DBQuery::$WhereList
 * </ul>
 *
 * @see DB_Table_Create
 * @see DB_Select
 * @see DB_Insert
 * @see DB_Update
 * @see DB_Delete
 *
 * @ finish the functions on this page
 * @access public
 * @author Alexandr Girin
 * @package Core
 */
class DBQuery
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */


    /**
     * DBQuery class constructor.
     *
     * Long Description
     *
     * @ finish the functions on this page
     * @return
     */
    function DBQuery($query='')
    {
        $this->QueryType = 'DEBUG';
        $this->Query = $query;
    }

    /**
     * An SQL alias for COUNT.
     *
     * @return string
     */
    function fCount($field_name)
    {
        return 'COUNT('.$field_name.')';
    }

    function fCountDistinct($field_name)
    {
        return 'COUNT(DISTINCT '.$field_name.')';
    }

    /**
     * An SQL alias for MAX.
     *
     * @return string
     */
    function fMax($field_name)
    {
        return 'MAX('.$field_name.')';
    }

    /**
     * An SQL alias for MIN.
     *
     * @return string
     */
    function fMin($field_name)
    {
        return 'MIN('.$field_name.')';
    }

    /**
     * An SQL alias for SUM.
     *
     * @return string
     */
    function fSum($field_name)
    {
        return 'SUM('.$field_name.')';
    }

    /**
     * An SQL alias for NOW.
     *
     * @return string
     */
    function fNow()
    {
        return 'NOW()';
    }

    /**
     * An SQL alias for PASSWORD.
     *
     * @return string
     */
    function fPassword($password)
    {
        return 'PASSWORD('.$password.')';
    }

    /**
     * An SQL alias for IF .
     *
     * Example of usage:
     * <code>
     *  $query = new DB_Update('table');
     *  $query->addUpdateValue('field1', $query->fIf("'1'".DB_EQ.'2', 3, "'4'"));
     * </code>
     *
     * @param string $expr1 if expr1 = true, then expr2 is returned, expr3 otherwise
     * @param string $expr2
     * @param string $expr3
     * @return string
     */
    function fIf($expr1, $expr2, $expr3)
    {
        return ' IF('.$expr1.', '.$expr2.', '.$expr3.') ';
    }

    function fLower($expr)
    {
        return ' LOWER('.$expr.') ';
    }

    /**
     * Adds a condition to the SQL query of type SELECT, UPDATE, DELETE, where
     * the value is the second operand.
     *
     * @return
     * @param $operand1 the first condition operand
     * @param $operator condition operator
     * @param $operand2 the second condition operand
     */
    function WhereValue($operand1, $operator, $operand2)
    {
        $operand2 = "'".$this->DBAddSlashes($operand2)."'";
        $this->Where($operand1, $operator, $operand2);
    }

    /**
     * Adds a condition to the SQL query of type SELECT, UPDATE, DELETE, where
     * the field of the table in the database is the second operand.
     *
     * @return
     * @param $operand1 the first condition operand
     * @param $operator condition operator
     * @param $operand2 the second condition operand
     */
    function WhereField($operand1, $operator, $operand2)
    {
        $this->Where($operand1, $operator, $operand2);
    }


    /**
     * Adds AND operator to the condition of the SQL query.
     *
     * @return
     */
    function WhereAND()
    {
        array_push($this->WhereList, DB_AND);
    }

    /**
     * Adds OR operator to the condition of the SQL query.
     *
     * @return
     */
    function WhereOR()
    {
        array_push($this->WhereList, DB_OR);
    }

    /**
     * Adds NOT operator to the condition of the SQL query.
     *
     * @return
     */
    function WhereNOT()
    {
        array_push($this->WhereList, DB_NOT);
    }

    /**
     * Adds an open bracket to the condition of the SQL query.
     *
     * @return
     */
    function addWhereOpenSection()
    {
        array_push($this->WhereList, DB_OpenSect);
    }

    /**
     * Adds a close bracket to the condition of the SQL query.
     *
     * @return
     */
    function addWhereCloseSection()
    {
        array_push($this->WhereList, DB_CloseSect);
    }

    /**
     * Gets the SQL query type.
     *
     * @return string Query type
     */
    function getQueryType()
    {
        return $this->QueryType;
    }

    /**
     * Gets the condition array of the SQL query.
     *
     * @return array
     */
    function getWhereList()
    {
        return $this->WhereList;
    }

    /**
     *                                     (    . IGNORE, DELAYED     .)
     *
     * @param $mods_array array
     * @return none
     */
    function setModifiers($mods_array)
    {
        if (!is_array($mods_array)) {
            $mods_array = array($mods_array);
        }
        $this->Modifiers = $mods_array;
    }

    /**
     *
     *
     * @return string
     */
    function getModifiers()
    {
        return ! empty($this->Modifiers) ? implode(' ', $this->Modifiers) : '';
    }

    /**#@-*/

    function quoteValue($v)
    {
        return "'".DBQuery::DBAddSlashes($v)."'";
    }

    function quoteArray(&$arr)
    {
        foreach(array_keys($arr) as $i) {
            $arr[$i] = "'".DBQuery::DBAddSlashes($arr[$i])."'";
        }
    }

    function arrayToIn($arr)
    {
        DBQuery::quoteArray($arr);
        return '(' . implode(', ', $arr) . ')';
    }

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */

    /**
     * The SQL query type.
     *
     * @var  $QueryType - query type (Select, Delete and etc.)
     */
    var $QueryType;

    /**
     *                      select
     *
     * @var array
     */
    var $Modifiers;

    /**
     * A condition array.
     *
     * @var  $WhereList - a condition array
     */
    var $WhereList;

    /**
     * Adds '\' before ' " \
     *
     * @ apply to php.ini settings
     * @return string
     * @param string $value
     */
    function DBAddSlashes($value)
    {
        return addslashes($value);
    }

    /**
     * Deletes '\' before ' " \
     *
     * @ apply to php.ini settings
     * @return string
     * @param string $value
     */
    function DBStripSlashes($value)
    {
        if (is_array($value))
        {
            foreach ($value as $key => $val)
            {
                $value[$key] =DBQuery::DBStripSlashes($val);
            }
        }
        else
        {
            $value = stripslashes($value);
        }
        return $value;
    }

    /**
     * Adds a condition to the SQL query of type SELECT, UPDATE, DELETE.
     *
     * @return
     * @param $operand1 the first condition operand
     * @param $operator condition operator
     * @param $operand2 the second condition operand
     */
    function Where($operand1, $operator, $operand2)
    {
        array_push($this->WhereList, $operand1);
        array_push($this->WhereList, $operator);
        array_push($this->WhereList, $operand2);
    }

    /**
     * Parses a field name of the table and returns only a field name, without
     * any table name specification.
     *
     * @return string - fieled name
     * @param string $field_name full field name
     */
    function parseFieldName($field_name)
    {
        if (is_int($pos=_ml_strpos($field_name, '.')))
        {
            return _ml_substr($field_name, $pos+1);
        }
        else
        {
            return $field_name;
        }
    }

    /**
     * @see DB_Select::addSelectTable($table_name)
     */
    function addSelectTable($table_name)
    {
    }
    /**#@-*/
}
?>