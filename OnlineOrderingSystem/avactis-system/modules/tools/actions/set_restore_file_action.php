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
_use(dirname(__FILE__).'/db_stat_action.php');
/**
 *
 * @package Tools
 * @author Alexander Girin
 */
class SetRestoreFile extends AjaxAction
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * DBStat constructor
     */
    function SetRestoreFile()
    {
    }

    /**
     *
     */
    function onAction()
    {
        global $application;

        $filename = modApiFunc("Tools", "getRestoreFile");
        modApiFunc("Tools", "setStringsCountInRestoreFile", $filename);
        modApiFunc("Tools", "setRestoreTablePrefix", "restore_");
        modApiFunc("Tools", "setRestoreFileOffset", 0);
        modApiFunc("Tools", "setRestoreFileStrOffset", 0);
        modApiFunc("Tools", "setRestoreStatus", 'RESTORE');
        modApiFunc("Tools", "saveState");
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