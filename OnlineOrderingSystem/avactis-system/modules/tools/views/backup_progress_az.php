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
 * Tools Module, Backup Progress View
 *
 * @package Tools
 * @author Alexander Girin
 */
class BackupProgress
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
    function BackupProgress()
    {
    }


    /**
     *
     */
    function output()
    {
        global $application;

        $request = new Request();
        $request->setView('AdminBackupProgress');
        $request->setAction('BackupProgressAction');
        $ProgressBarFormAction = $request->getURL();

        $request = new Request();
        $request->setView('AdminBackupProgress');
        $request->setAction('BackupImagesProgressAction');
        $ProgressBar2FormAction = $request->getURL();

        $tables = modApiFunc("Tools", "getDBStat");
        if (sizeof($tables))
        {
            $ProgressBarWidth = floor(modApiFunc("Tools", "getDBRecordsExported")/$tables["Total_Records"]*400);
            $DBBackup = "true";
        }
        else
        {
            $ProgressBarWidth = 400;
            $DBBackup = "false";
        }

        $ProgressBarWidth2 = 0;
        if ($DBBackup == "false")
        {
            if (!(($count = modApiFunc("Tools", "getCopiedImageFilesCount")) === NULL))
            {
                $total_files_count = modApiFunc("Tools", "getImageFilesCount");
                if ($count == "no")
                {
                    $ProgressBarWidth2 = 0;
                }
                else
                {
                    $ProgressBarWidth2 = floor($count/$total_files_count*400);
                }
            }
            else
            {
                $ProgressBarWidth2 = 400;
            }
        }

        $template_contents = array(
                                   'DBBackup'              => $DBBackup
                                  ,'ProgressBarFormAction' => $ProgressBarFormAction
                                  ,'ProgressBarWidth'      => $ProgressBarWidth
                                  ,'ProgressBar2FormAction' => $ProgressBar2FormAction
                                  ,'ProgressBarWidth2'      => $ProgressBarWidth2
                                  );
        $this->_Template_Contents = $template_contents;
        $application->registerAttributes($this->_Template_Contents);
        return modApiFunc('TmplFiller', 'fill', "tools/backup_progress/","container.tpl.html", array());
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