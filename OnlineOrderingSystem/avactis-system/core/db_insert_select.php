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
 * @package Core
 * @author Egor V. Derevyankin
 *
 */

loadCoreFile('dbquery.php');

class DB_Insert_Select extends DBQuery
{
    function DB_Insert_Select($table)
    {
        global $application;
        $this->table_prefix = $application->getAppIni('DB_TABLE_PREFIX');
        $this->QueryType = DBQUERY_TYPE_INSERT_SELECT;
        $this->InsertTable = $this->table_prefix.$table;
        $this->InsertFields = array();
        $this->SelectQuery = null;
    }

    function setInsertFields($fields_array)
    {
        if(!empty($fields_array))
        {
            reset($fields_array);
            foreach($fields_array as $field)
            {
                if ($field!='')
                {
                    array_push($this->InsertFields, $field);
                };
            };
        };
    }

    function setSelectQuery($query)
    {
        $this->SelectQuery = $query;
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
    function getInsertValuesArray()
    {
        return $this->InsertValuesArray;
    }

    function getSelectQuery()
    {
        return $this->SelectQuery;
    }

    function _cmp_keys($a, $b)
    {
        $pos_a = array_search($a, $this->InsertFields);
        $pos_b = array_search($b, $this->InsertFields);
        return $pos_a - $pos_b;
    }

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
     * The object for making insert values `select` query.
     *
     * @var  object
     */
    var $SelectQuery;

};

?>