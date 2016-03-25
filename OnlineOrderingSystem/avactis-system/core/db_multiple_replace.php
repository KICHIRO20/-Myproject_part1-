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
 * @author Vadim Lyalikov
 *
 */

loadCoreFile('dbquery.php');

class DB_Multiple_Replace extends DBQuery
{
    function DB_Multiple_Replace($table)
    {
        global $application;
        $this->table_prefix = $application->getAppIni('DB_TABLE_PREFIX');
        $this->QueryType = DBQUERY_TYPE_MULTIPLE_REPLACE;
        $this->ReplaceTable = $this->table_prefix.$table;
        $this->ReplaceFields = array();
        $this->ReplaceValuesArray = array();
    }

    function setReplaceFields($fields_array)
    {
        if(!empty($fields_array))
        {
            reset($fields_array);
            foreach($fields_array as $field)
            {
                if ($field!='')
                {
                    array_push($this->ReplaceFields, $field);
                };
            };
        };
    }

    function addReplaceValuesArray($values_array)
    {
        if(!empty($values_array))
        {
            $prepared_values_array = array();
            if(!empty($this->ReplaceFields))
            {
                uksort($values_array, array(&$this, "_cmp_keys"));
            };
            reset($values_array);
            foreach($values_array as $key => $value)
            {
                $value = "'".$this->DBAddSlashes($value)."'";
                $prepared_values_array[] = $value;
            };
            $this->ReplaceValuesArray[] = implode(', ',$prepared_values_array);
        };
    }

    /**
     * Gets the table name.
     *
     * @return string the table name
     */
    function getReplaceTable()
    {
        return $this->ReplaceTable;
    }

    /**
     * Gets a list of fields, to which values are assigned.
     *
     * @return array the field array
     */
    function getReplaceFields()
    {
        return $this->ReplaceFields;
    }

    /**
     * Gets a list of values.
     *
     * @return array the value array
     */
    function getReplaceValuesArray()
    {
        return $this->ReplaceValuesArray;
    }

    function _cmp_keys($a, $b)
    {
        $pos_a = array_search($a, $this->ReplaceFields);
        $pos_b = array_search($b, $this->ReplaceFields);
        return $pos_a - $pos_b;
    }

    /**
     * The table name, where a record is replaced.
     *
     * @var  string
     */
    var $ReplaceTable;


    /**
     * The array of fields, where values are replaced.
     *
     * @var  array
     */
    var $ReplaceFields;

    /**
     * The array of replaced values.
     *
     * @var  array
     */
    var $ReplaceValuesArray;

};

?>