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
 *       DB_Table_Delete -
 *
 *                             DB_Table_Delete,                      DELETE
 *<code>
 *  $db_table_delete = new DB_Table_Delete(Module::getTables());
 *  # or
 *  $db_table_delete = new DB_Table_Delete('table_name');
 *  $application->db->PrepareSQL($db_table_delete);
 *  $application->db->DB_Exec();
 *</code>
 *
 * @access  public
 * @author Alexander Girin
 * @package Core
 */
class DB_Table_Delete extends DBQuery
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * DB_Table_Delete class constructor.
     *
     * @return
     * @param mixed $tables table name or array of meta description of module
     * tables
     */
    function DB_Table_Delete($tables)
    {
        global $application;
        $table_prefix = $application->getAppIni('DB_TABLE_PREFIX');
        $this->QueryType=DBQUERY_TYPE_TABLE_DELETE;
        if (is_array($tables))
        {
            $this->DeleteTables = array();
            foreach ($tables as $key => $val)
            {
                array_push($this->DeleteTables, $table_prefix.$key);
            }
        }
        else
        {
            $this->DeleteTables=$table_prefix.$tables;
        }
    }

    /**
     * Gets a name or names of tables.
     *
     * @return
     */
    function getDeleteTables()
    {
        return $this->DeleteTables;
    }


    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */

    /**
     * The name of the table or tables.
     *
     * @var  mixed
     */
    var $DeleteTables;

    /**#@-*/
}
?>