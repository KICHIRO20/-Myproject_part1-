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

/**
 *       DB_Table_Clone -
 *
 * @access  public
 * @author Alexandr Girin
 * @package Core
 */
class DB_Table_Clone
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     *                    DB_Table_Clone
     *
     * @return
     * @param array $tables
     * @
     */
    function DB_Table_Clone($APIClassName, $TableName, $NewTableName="")
    {
        global $application;

        if (!$NewTableName)
        {
            $NewTableName = $TableName."_copy";
        }
        $tables = modApiFunc($APIClassName, "getTables");

        $table = array();
        $table[$NewTableName] = $tables[$TableName];
        $query = new DB_Table_Create($table);

        $query = new DB_Insert($NewTableName);
        $query->setCopyFromTable($TableName);
        $application->db->getDB_Result($query);
    }

    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */

    /**#@-*/
}
?>