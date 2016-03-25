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
 * Tools Module, Restore Progress View
 *
 * @package Tools
 * @author Alexander Girin
 */
class RestoreProgress
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
    function RestoreProgress()
    {
    }


    /**
     *
     */
    function output()
    {
        global $application;

        $request = new Request();
        $request->setView('AdminRestoreProgress');
        $request->setAction('BackupProgressAction');
        $ProgressBarCFormAction = $request->getURL();

        $request = new Request();
        $request->setView('AdminRestoreProgress');
        $request->setAction('BackupImagesProgressAction');
        $ProgressBarC2FormAction = $request->getURL();

        $DBBackup = "false";
        $DBRestore = "false";
        $ProgressBarWidthC = 0;
        $tables = modApiFunc("Tools", "getDBStat");
        if (sizeof($tables))
        {
            $ProgressBarWidthC = floor(modApiFunc("Tools", "getDBRecordsExported")/$tables["Total_Records"]*400);
            $DBBackup = "true";
            $DBRestore = "false";
        }
        else
        {
            $ProgressBarWidthC = 400;
            $DBBackup = "false";
            $DBRestore = "false";
        }

        $ProgressBarWidthC2 = 0;
        if ($DBBackup == "false" && modApiFunc("Session", "get", "RestoreStatus") != 'RESTORE')
        {
            if (!(($count = modApiFunc("Tools", "getCopiedImageFilesCount")) === NULL))
            {
                $total_files_count = modApiFunc("Tools", "getImageFilesCount");
                if ($count == "no")
                {
                    $ProgressBarWidthC2 = 0;
                }
                else
                {
                    $ProgressBarWidthC2 = floor($count/$total_files_count*400);
                }
                $DBRestore = "false";
            }
            else
            {
                $ProgressBarWidthC2 = 400;
                $DBRestore = "true";
            }
        }
        if (modApiFunc("Session", "get", "RestoreStatus") == 'RESTORE')
        {
            $DBRestore = "true";
        }

        $request = new Request();
        $request->setView('AdminRestoreProgress');
        $request->setAction('RestoreProgressAction');
        $ProgressBarFormAction = $request->getURL();

        $request = new Request();
        $request->setView('AdminRestoreProgress');
        $request->setAction('RestoreImagesProgressAction');
        $ProgressBar2FormAction = $request->getURL();

        $ProgressBarWidth = 0;
        $ProgressBarWidth2 = 0;
        if ($DBRestore == 'true')
        {
            $str_offset = modApiFunc("Tools", "getRestoreFileStrOffset");
            if (!($str_offset === NULL))
            {
                if (modApiFunc("Tools", "getRestoreTablePrefix") != "restore_")
                {
                    $ProgressBarWidth = 200;
                }
                else
                {
                    $ProgressBarWidth = 0;
                }
                if (modApiFunc("Tools", "getStringsCountInRestoreFile") != 0)
                {
                    $ProgressBarWidth+= floor($str_offset/modApiFunc("Tools", "getStringsCountInRestoreFile")*400);
                }
                $DBRestore = "true";
            }
            else
            {
                $ProgressBarWidth = 400;
                $DBRestore = "false";
            }

            $ProgressBarWidth2 = 0;
            if ($DBRestore == "false")
            {
                if ($count = modApiFunc("Tools", "getCopiedImageFilesCount"))
                {
                    $ProgressBarWidth2 = floor($count/modApiFunc("Tools", "getImageFilesCount", "restore")*400);
                }
                else
                {
                    $ProgressBarWidth2 = 400;
                }
            }
        }

        $request = new Request();
        $request->setView('AdminRestoreProgress');
        $request->setAction('SetRestoreFile');
        $StartRestoreFormAction = $request->getURL();

        $WithBackup = 'Yes';
        if (!modApiFunc("Session", "is_set", "AutoBackup"))
        {
            $WithBackup = 'No';
            modApiFunc("Session", "set", "AutoBackup", 'passed');
        }

        $template_contents = array(
                                   'WithBackup'              => $WithBackup
                                  ,'RestoreStatus'           => modApiFunc("Tools", "getRestoreStatus")
                                  ,'DBBackup'                => $DBBackup
                                  ,'ProgressBarCFormAction'  => $ProgressBarCFormAction
                                  ,'ProgressBarWidthC'       => $ProgressBarWidthC
                                  ,'ProgressBarC2FormAction' => $ProgressBarC2FormAction
                                  ,'ProgressBarWidthC2'      => $ProgressBarWidthC2
                                  ,'StartRestoreFormAction'  => $StartRestoreFormAction
                                  ,'DBRestore'               => $DBRestore
                                  ,'ProgressBarFormAction'   => $ProgressBarFormAction
                                  ,'ProgressBarWidth'        => $ProgressBarWidth
                                  ,'ProgressBar2FormAction'  => $ProgressBar2FormAction
                                  ,'ProgressBarWidth2'       => $ProgressBarWidth2
                                  );
        $this->_Template_Contents = $template_contents;
        $application->registerAttributes($this->_Template_Contents);
        return modApiFunc('TmplFiller', 'fill', "tools/restore_progress/","container.tpl.html", array());
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