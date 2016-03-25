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
 * @copyright Copyright &copy; 2005, HBWSL.
 * @package Core
 * @author ag
 */

loadCoreFile('dbquery.php');


/**
 * DB_Delete class is used to generate SQL request of type Delete .
 *
 * Example of usage DB_Delete, to generate the DELETE query.
 *<code>
 *  $db_delete = new DB_Delete('categories');
 *  $db_delete->WhereValue('category_id', DB_EQ, 17);
 *  $db_delete->WhereAND();
 *  $db_delete->WhereValue('category_name', DB_NEQ, 'DVD');
 *  $application->db->PrepareSQL($db_delete);
 *  $application->db->DB_Exec();
 *</code>
 *
 * @access  public
 * @author Alexandr Girin
 * @package Core
 */
class DB_Delete extends DBQuery
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * DB_Delete class constructor.
     *
     * @return
     * @param string $table table name
     */
    function DB_Delete($table)
    {
        global $application;
        $table_prefix = $application->getAppIni('DB_TABLE_PREFIX');
        $this->QueryType=DBQUERY_TYPE_DELETE;
        $this->DeleteTable=$table_prefix.$table;
        $this->UsingList = array();
        $this->WhereList = array();
        $this->OriginalTableName = $table;
        $this->MultiLangData = array();
    }

    function addUsingTable($tables)
    {
        global $application;
        $table_prefix = $application->getAppIni('DB_TABLE_PREFIX');
    	if (is_array($tables)) {
    		foreach($tables as $table) {
            	$this->UsingList[] = $table_prefix.$tables;
    		}
        }
        else {
            $this->UsingList[] = $table_prefix.$tables;
        }
        $this->UsingList = array_unique($this->UsingList);
    }

        /**
     * Adds the data selection method to the SELECT query.
     *
     * @return
     * @param string $field field name
     * @param string $order selection order (ASC | DESC), 'ASC' is on by default
     */
    function DeleteOrder($field, $order='ASC')
    {
        array_push($this->DeleteOrders, $field.' '.$order);
    }

    function DeleteLimit($count)
    {
        if ($count > 0) {
            $this->DeleteLimits = $count;
        }
    }

    /**
     * Gets a table name.
     *
     * @return
     */
    function getDeleteTable()
    {
        return $this->DeleteTable;
    }

    function getUsingClause()
    {
        return empty($this->UsingList) ? '' : ' USING '.implode(', ', $this->UsingList).' ';
    }

    function getOrderClause()
    {
        return empty($this->DeleteOrders) ? '' : ' ORDER BY '.implode(', ', $this->DeleteOrders).' ';
    }

    function getLimitClause()
    {
        return empty($this->DeleteLimits) ? '' : ' LIMIT '.$this->DeleteLimits.' ';
    }

    /**
     * functions for MultiLang support
     */
    function getMultiLangData()
    {
        return $this -> MultiLangData;
    }

    function deleteMultiLangField($field, $key_field, $class_name = '',
                                  $field_number = 0)
    {
        // if class_name is not specified assume
        // it is active in MultiLang class
        if (!$class_name)
            $class_name = modApiFunc('MultiLang', 'getClass');

        $this -> MultiLangData[] = array(
            modApiFunc('MultiLang', 'mapMLField',
                       $this -> OriginalTableName,
                       $this -> parseFieldName($field),
                       $class_name, $field_number),
            $key_field
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
     * The table name, from which data are removed.
     *
     * @var  string parameter
     */
    var $DeleteTable;

    /**
     * The tables references, which used in WHERE clause.
     *
     * @var array
     */
    var $UsingList;

    /**
     * original table name (need for multilang support)
     */
    var $OriginalTableName;

    /**
     * The array of multilang data.
     *
     * @var  array of sets (label, key)
     */
    var $MultiLangData;

    var $DeleteOrders = array();
    /**
     * A limit.
     *
     * @var string
     */
    var $DeleteLimits;

    /**#@-*/
}
?>