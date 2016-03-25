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
class BackupCreate
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
    function BackupCreate()
    {
        $this->container_template_folder_name = "tools/backup_create/";
    }


    /**
     *
     */
    function output()
    {
        global $application;

        $request = new Request();
        $request->setView('AdminBackupProgress');
        $request->setAction('DBStat');
        $Backup_Start_Link = $request->getURL();

        $request = new Request();
        $request->setView('AdminBackup');
        $request->setAction('BackupCancel');
        $Backup_Cancel_Link = $request->getURL();

        $template_contents = array(
                                   'BackupStartLink' => $Backup_Start_Link
                                  ,'BackupCancelLink' => $Backup_Cancel_Link
                                  );
        $this->_Template_Contents = $template_contents;
        $application->registerAttributes($this->_Template_Contents);
        return modApiFunc('TmplFiller', 'fill', $this->container_template_folder_name,  "container.tpl.html", array());
    }

    function getTag($tag)
    {
        global $application;
        $value = null;
        if (array_key_exists($tag, $this->_Template_Contents))
        {
            $value = $this->_Template_Contents[$tag];
        }
        return $value;
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