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
 * DB_MYSQL_Lock_Tables class is used to generate the SQL command of type
 * Lock Tables.
 *
 * An example of usage the DB_MYSQL_Lock_Tables class, to generate
 * LOCK TABLES<br> command.
 *<code>
 *  $query = new DB_MYSQL_Lock_Tables();
 *  $query->addTableToLock('categories', DB_LOCK_MODE_WRITE);
 *  $query->addTableToLock('categories_descr', DB_LOCK_MODE_WRITE);
 *  $application->db->PrepareSQL($query);
 *  $application->db->DB_Exec($query);
 *</code><br>
 *
 * @access  public
 * @author Alexandr Girin, Vadim Lyalikov
 * @package Core
 */
class DB_MYSQL_Lock_Tables
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * DB_MYSQL_Lock_Tables class constructor.
     *
     * @return
     * @param string $table table name
     */
    function DB_MYSQL_Lock_Tables()
    {
        $this->QueryType=DBQUERY_TYPE_LOCK_TABLES;
        $this->TablesToLock=array();
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
     * Adds the table to the locked ones.
     * : check and describe return. See db_select.php
     *
     * @return alias of added or already existed record (?)
     * @param string $table_name table name
     * @param string $locking_mode locking mode
     * @param string $table_alias alias of the table name
     */
    function addTableToLock($table_name, $locking_mode, $table_alias='')
    {
        global $application;
        $table_prefix = $application->getAppIni('DB_TABLE_PREFIX');
        if ($table_prefix != NULL)
        {
            if (_ml_strpos($table_name, $table_prefix) === false)
            {
                $table_name = $table_prefix.$table_name;
            }
        }

        foreach($this->TablesToLock as $t_name => $t_info)
        {
            if($t_info['alias'] == $table_name)
            {
                return $table_alias;
            }
        }
        //else : new table name is not a previously added -alias-

        //Check if table is already added
        if(!array_key_exists($table_name, $this->TablesToLock))
        {
            foreach($this->TablesToLock as $t_name => $t_info)
            {
                if(($t_info['alias'] == $table_alias) && ($table_alias != ''))
                {
                    user_error('Attempt to assign the same alias "' .$talbe_alias. '" to two different tables:"' .$t_name. '" and "' .$table_name. '".', E_USER_ERROR);
                }
            }
            //else : new table alias is not a previously added -table-alias-

            $this->TablesToLock[$table_name] = array("locking_mode" => $locking_mode,
                                                     "alias" => $table_alias);
        }
        else
        {
            return $this->TablesToLock[$table_name]['alias'];
        }
        //: move this return to line "$this->TablesToLock[$table_name] = ..."?
        return $table_alias;
    }

    /**
     * Gets a list of locked tables, aliases of their names and locking modes.
     *
     * @return array field array
     */
    function getTablesToLock()
    {
        return $this->TablesToLock;
    }
    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */
    /**
     * The array of locked tables, aliases of their names and locking modes.
     *
     * @var  array
     */
    var $TablesToLock;
}
?>