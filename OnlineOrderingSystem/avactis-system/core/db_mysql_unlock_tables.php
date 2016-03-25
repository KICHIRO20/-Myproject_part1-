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
 * DB_MYSQL_Unlock_Tables class is used to generate the SQL command of type
 * Unlock Tables.
 *
 * An example of usage the DB_MYSQL_Unlock_Tables class, to generate the
 * UNLOCK TABLES<br> command.
 *<code>
 *   change the example for UNLOCK TABLES
 *  $query = new DB_MYSQL_Unlock_Tables();
 *  $application->db->PrepareSQL($query);
 *  $application->db->DB_Exec($query);
 *</code><br>
 *
 * @access  public
 * @author Alexandr Girin, Vadim Lyalikov
 * @package Core
 */
class DB_MYSQL_Unlock_Tables
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * DB_MYSQL_Unlock_Tables class constructor.
     *
     * @return
     * @param string $table a table name
     */
    function DB_MYSQL_Unlock_Tables()
    {
        $this->QueryType=DBQUERY_TYPE_UNLOCK_TABLES;
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
    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */
}
?>