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
 * Tools Module, Backup View
 *
 * @package Tools
 * @author Alexander Girin
 */

_use(dirname(__FILE__) . '/backup_create_az.php');

class AutoBackupCreate extends BackupCreate
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * Constructor
     */
    function AutoBackupCreate()
    {
        $this->container_template_folder_name = "tools/auto_backup_create/";
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