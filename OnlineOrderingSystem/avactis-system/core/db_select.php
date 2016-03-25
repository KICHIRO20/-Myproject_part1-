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
loadCoreFile('dbquery.php');

/**
 * DB_Select class is used to generate the SQL query of type Select.
 *
 * Example of usage the DB_Select class, to generate the SELECT query.
 * In this example, the table names,to make selections from, are not
 * specified. The table list is prepared from full names of the fields, sent to
 * addSelectField() and WhereValue() or WhereField():
 *<code>
 *  $db_select = new DB_Select();
 *  $db_select->addSelectField('categories.category_name');
 *  $db_select->WhereValue('categories.category_name', DB_EQ, 'FILM');
 *  $db_select->WhereOR();
 *  $db_select->WhereValue('categories.category_id', DB_EQ, 22);
 *  $db_select->addLeftJoin('products', products.category_id, DB_EQ, categories.category_id);
 *  $db_select->SelectOrder('categories.category_id');
 *  $db_select->SelectLimit(0, 10);
 *  $result = $application->db->getDB_Result($db_select);
 *</code>
 * Table aliases can also be used. They must be declared with addSelectTable():
 *<code>
 *  $db_select = new DB_Select();
 *  $db_select->addSelectTable('categories', 'c');
 *  $db_select->addSelectField('c.category_name');
 *  $db_select->WhereValue('c.category_id', DB_EQ, 22);
 *</code>
 *
 * @access  public
 * @author Alexandr Girin
 * @package Core
 */
class DB_Select extends DBQuery
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * DB_Select class constructor.
     *
     * @return
     */
    function DB_Select()
    {
        $this->QueryType=DBQUERY_TYPE_SELECT;
        $this->SelectTables = array();
        $this->SelectTableAliases = array();
        $this->SelectJoins = array();
        $this->SelectFields = array();
        $this->SelectConditions = array();
        $this->SelectOrders = array();
        $this->SelectGroups = array();
        $this->WhereList = array();
        $this->HavingList = array();
        $this->MultiLangAliases = array();
    }

    /**
     * Adds tables to the SELECT query.
     *
     * @return
     * @param string $table_name table name
     * @param string $table_alias alias
     */
    function addSelectTable($table_name)
    {
        global $application;

        static $table_prefix = -1;
        if($table_prefix === -1)
        {
            $table_prefix = $application->getAppIni('DB_TABLE_PREFIX');
        }
        if ($table_prefix != NULL)
        {
            $table_name = $table_prefix.$table_name;
        }

        if (!array_key_exists($table_name, $this->SelectTables))
        {
            $add_from = true;
            if(!empty($this->SelectJoins))
            {
                foreach ($this->SelectJoins as $value)
                {
                    if ($value['TABLE'] == $table_name)
                    {
                        $add_from = false;
                    }
                }
            }
            if ($add_from)
            {
                $this->SelectTables[$table_name] = '';
            }
        }
    }

    function WhereField($operand1, $operator, $operand2)
    {
        $this->addSelectTableByField($operand2);
        $this->Where($operand1, $operator, $operand2);
    }

    function Where($operand1, $operator, $operand2)
    {
        $this->addSelectTableByField($operand1);
        parent::Where($operand1, $operator, $operand2);
    }

    //                                                ,
    //        ,                                       .
    function addSelectTableByField($field)
    {
        if($pos=_ml_strpos($field, '.'))
        {
            $table_name = _ml_substr($field, 0, $pos);
            //      $field                           ,
            if (_ml_strpos($table_name, '(') || _ml_strpos(_ml_strtolower($table_name), 'distinct') !== false)
            {
                return;
            }
            //DB_Select::addSelectTable($table_name)
            if (!array_key_exists($table_name, $this->SelectTables))
            {
                $add_from = true;
                if(!empty($this->SelectJoins))
                {
                    foreach ($this->SelectJoins as $value)
                    {
                        if ($value['TABLE'] == $table_name || $value['ALIAS'] == $table_name)
                        {
                            $add_from = false;
                        }
                    }
                }
                if ($add_from)
                {
                    $this->SelectTables[$table_name] = '';
                }
            }
        }
    }

    /**
     * Adds fields to the SELECT query.
     *
     * @return
     * @param string $field_name table name
     * @param string $field_alias alias
     */
    function addSelectField($field_name, $field_alias='')
    {
        $pos = _ml_strpos($field_name, '(');
        if ($pos !== FALSE)
        {
            $field = _ml_substr($field_name, $pos+1);
        }
        else
        {
        	$field = $field_name;
        }

        $this->addSelectTableByField($field);

        if (!array_key_exists($field_name, $this->SelectFields))
        {
            if (($field_alias!='') && in_array($field_alias, $this->SelectFields))
            {
                user_error('This alias already exists', E_USER_ERROR);
            }
            else
            {
                $this->SelectFields[$field_name] = $field_alias;
            }
        }
    }

    function addSelectValue($expression, $alias = '')
    {
        if (!array_key_exists($expression, $this -> SelectFields))
        {
            if (($alias != '') && in_array($alias, $this -> SelectFields))
            {
                user_error('This alias already exists', E_USER_ERROR);
            }
            else
            {
                $this -> SelectFields[$expression] = $alias;
            }
        }
    }

    /**
     * Adds the joined tables via LEFT JOIN.
     *
     * @return
     * @param string $table_name the name of joined table
     * @param string $field1 a field name of the table, to which the tables
     * are joined
     * @param $operator if statement
     * @param string $field2 a field name of the joined table
     */
    function addLeftJoin($table_name, $field1, $operator, $field2, $alias = '')
    {
        global $application;
        $join = array('TYPE' => ' LEFT ',
                      'TABLE' => $application->getAppIni('DB_TABLE_PREFIX').$table_name,
                      'CONDITION' => array($field1, $operator, $field2),
                      'ALIAS' => $alias
                     );
        $this->delSelectTable($table_name);
        array_push($this->SelectJoins, $join);
    }

    function addInnerJoin($table_name, $field1, $operator, $field2, $alias = '')
    {
        global $application;
        $join = array('TYPE' => ' INNER ',
                      'TABLE' => $application->getAppIni('DB_TABLE_PREFIX').$table_name,
                      'CONDITION' => array($field1, $operator, $field2),
                      'ALIAS' => $alias
                     );
        $this->delSelectTable($table_name);
        array_push($this->SelectJoins, $join);
    }

    /**
     * Adds the joined tables via LEFT JOIN using several conditions.
     *
     * @return
     * @param string $table_name the name of joined table
     * @param string $conditions is an array of joining conditions
     * example: array('f1', DB_EQ, 'f2', DB_AND, 'f3', DB_EQ, 'f4')
     * @param string $alias alian name of the joined table
     */
    function addLeftJoinOnConditions($table_name, $conditions, $alias = '')
    {
        global $application;
        $join = array('TYPE' => ' LEFT ',
                      'TABLE' => $application->getAppIni('DB_TABLE_PREFIX').$table_name,
                      'CONDITION' => $conditions,
                      'ALIAS' => $alias
                     );
        $this->delSelectTable($table_name);
        array_push($this->SelectJoins, $join);
    }

    /**
     * Adds the joined tables via INNER JOIN using several conditions.
     *
     * @return
     * @param string $table_name the name of joined table
     * @param string $conditions is an array of joining conditions
     * example: array('f1', DB_EQ, 'f2', DB_AND, 'f3', DB_EQ, 'f4')
     * @param string $alias alian name of the joined table
     */
    function addInnerJoinOnConditions($table_name, $conditions, $alias = '')
    {
        global $application;
        $join = array('TYPE' => ' INNER ',
                      'TABLE' => $application->getAppIni('DB_TABLE_PREFIX').$table_name,
                      'CONDITION' => $conditions,
                      'ALIAS' => $alias
                     );
        $this->delSelectTable($table_name);
        array_push($this->SelectJoins, $join);
    }

    function convertAliasColumns($columns, $alias)
    {
        foreach ($columns as $key => $column)
        {
            $field_name = $this -> parseFieldName($column);
            $columns[$key] = $alias . '.' . $field_name;
        }
        return $columns;
    }

    function addTableAlias($columns, $alias, $table_name_without_prefix)
    {
        $columns = $this -> convertAliasColumns($columns, $alias);
        $this -> addSelectTable($table_name_without_prefix . ' ' . $alias);
        array_push($this -> SelectTableAliases, $alias);
        return $columns;
    }

    function getSelectTableAliases()
    {
        return $this->SelectTableAliases;
    }

    /**
     * Adds the data selection method to the SELECT query.
     *
     * @return
     * @param string $field field name
     * @param string $order selection order (ASC | DESC), 'ASC' is on by default
     */
    function SelectOrder($field, $order='ASC')
    {
        $this->addSelectTableByField($field);
        array_push($this->SelectOrders, $field.' '.$order);
    }

    /**
     * Adds the data grouping method to the SELECT query.
     *
     * @return
     * @param string $field field name
     */
    function SelectGroup($field)
    {
        array_push($this->SelectGroups, $field);
    }

    /**
     * Adds a filter to the query result.
     *
     * @return
     */
    function Having($field, $operator, $value)
    {
        array_push($this->HavingList, $field);
        array_push($this->HavingList, $operator);
        array_push($this->HavingList, $value);
    }

    /**
     * Adds AND operator to the SQL query condition.
     *
     * @return
     */
    function HavingAND()
    {
        array_push($this->HavingList, DB_AND);
    }

    /**
     * Adds OR operator to the SQL query condition.
     *
     * @return
     */
    function HavingOR()
    {
        array_push($this->HavingList, DB_OR);
    }

    /**
     * Adds NOT operator to the SQL query condition.
     *
     * @return
     */
    function HavingNOT()
    {
        array_push($this->HavingList, DB_NOT);
    }

    /**
     * Adds an open bracket to the SQL query condition.
     *
     * @return
     */
    function addHavingOpenSection()
    {
        array_push($this->HavingList, DB_OpenSect);
    }

    /**
     * Adds a close bracket to the SQL query condition.
     *
     * @return
     */
    function addHavingCloseSection()
    {
        array_push($this->HavingList, DB_CloseSect);
    }

    /**
     * Adds the selected data limits to the SELECT query.
     *
     * @return
     * @param int $offset the offset data count
     * @param int $count selected data count
     */
    function SelectLimit($offset, $count)
    {
        if ($offset < 0 || !is_numeric($offset))
        {
        	$offset = 0;
        }
        $this->SelectLimits = $offset;
        if (is_numeric($count) && $count > 0)
            $this->SelectLimits .= ', ' . $count;
    }

    /**
     * Gets the array of selected fields.
     *
     * @return array field array
     */
    function getSelectFields()
    {
        return $this->SelectFields;
    }

    /**
     * Gets a table array.
     *
     * @return array table array
     */
    function getSelectTables()
    {
        return $this->SelectTables;
    }

    /**
     * Gets the array of joined tables.
     *
     * @return array array of joined tables
     */
    function getJoinTables()
    {
        return $this->SelectJoins;
    }

    /**
     * Gets the field array to be selected by.
     *
     * @return array field array
     */
    function getSelectOrders()
    {
        return $this->SelectOrders;
    }

    /**
     * Gets the field array to be groupped by.
     *
     * @return array field array
     */
    function getSelectGroups()
    {
        return $this->SelectGroups;
    }

    /**
     * Gets the value of the selection data limits.
     *
     * @return string limit
     */
    function getSelectLimits()
    {
        return $this->SelectLimits;
    }

    /**
     * Gets the array of filter conditions.
     */
    function getHavingList()
    {
        return $this->HavingList;
    }

    function isCachable()
    {
        return true;
    }

    /**
     * MultiLang functions
     */
    function getMultiLangAlias($aliasname)
    {
        if (isset($this -> MultiLangAliases[$aliasname]))
            return $this -> MultiLangAliases[$aliasname];

        return false;
    }

    function setMultiLangAlias($alias_name, $table, $field, $key,
                               $class_name = '', $field_number = 0,
                               $sql_field = true, $lng_code = 0)
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

        // getting MultiLang data
        list($multilang_table,
             $multilang_cols) = modApiFunc('MultiLang', 'getHeapTableInfo');
        $multilang_cols = $this -> convertAliasColumns($multilang_cols,
                                                       $alias_name);

        $this -> MultiLangAliases[$alias_name] = 'IF(' . $alias_name . '.value'
                                                 . ' IS NOT NULL, ' .
                                                 $alias_name . '.value' . ',' .
                                                 $field . ')';

        if ($sql_field)
            $key_condition = ' AND ' . $multilang_cols['label_key'] . DB_EQ .
                             $key . ' ';
        else
            $key_condition = ' AND ' . $multilang_cols['label_key'] . DB_EQ .
                             '\'' . $key . '\' ';

        $this -> addLeftJoin($multilang_table, $multilang_cols['lng'], DB_EQ,
                             '\'' . $lng_code . '\' AND ' .
                             $multilang_cols['label'] . DB_EQ . '\'' .
                             modApiFunc('MultiLang', 'mapMLField', $table,
                                        $this -> parseFieldName($field),
                                        $class_name, $field_number) .
                             '\'' . $key_condition, $alias_name);
    }

    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */

    /**
     * The table array, from which the data are selected.
     *
     * @var array
     */
    var $SelectTables;

    var $SelectTableAliases;

    var $MultiLangAliases;

    /**
     * The array of joined tables with joining conditions.
     *
     * @var array
     */
    var $SelectJoins;
    /**
     * The array of selected fields.
     *
     * @var array
     */
    var $SelectFields;
    /**
     * The field array to be selected by.
     *
     * @var array
     */
    var $SelectOrders;
    /**
     * The field array, to be groupped by.
     *
     * @var array
     */
    var $SelectGroups;
    /**
     * A limit.
     *
     * @var string
     */
    var $SelectLimits;

    var $HavingList;

    /**
     * Deletes a table from the FROM field in the SQL query.
     *
     * @param string $table_name name of the deleted page
     * @return
     */
    function delSelectTable($table_name, $use_prefix = true)
    {
        global $application;

        if ($use_prefix)
            $table_name = $application->getAppIni('DB_TABLE_PREFIX').$table_name;

        if (array_key_exists($table_name, $this->SelectTables))
        {
            unset($this->SelectTables[$table_name]);
        }
    }

    /**#@-*/
}
?>