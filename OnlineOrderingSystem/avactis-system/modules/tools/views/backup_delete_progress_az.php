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
class BackupDeleteProgress
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
    function BackupDeleteProgress()
    {
    }


    /**
     *
     */
    function output()
    {
        global $application;

        $request = new Request();
        $request->setView('AdminBackupDeleteProgress');
        $request->setAction('BackupDeleteAction');
        $ProgressBarFormAction = $request->getURL();

        $ImgQtyTotal = modApiFunc("Tools", "getImgQtyTotal");
        $DeletedImgQty = modApiFunc("Tools", "getDeletedImgQty");
        if ($ImgQtyTotal)
        {
            if ($ImgQtyTotal == $DeletedImgQty+3)
            {
                $ProgressBarWidth = 400;
                modApiFunc("Tools", "unsetDeletedImgQty");
                modApiFunc("Tools", "unsetImgQtyTotal");
            }
            else
            {
                $ProgressBarWidth = floor($DeletedImgQty/$ImgQtyTotal*400);
            }
        }
        else
        {
            $ProgressBarWidth = 0;
        }
        if (!is_dir($application->getAppIni('PATH_BACKUP_DIR').modApiFunc("Tools", "getCurrentBackupFile")."/"))
        {
            $ProgressBarWidth = 400;
            modApiFunc("Tools", "unsetDeletedImgQty");
            modApiFunc("Tools", "unsetImgQtyTotal");
        }
        modApiFunc("Tools", "saveState");

        $template_contents = array(
                                   'ProgressBarFormAction' => $ProgressBarFormAction
                                  ,'ProgressBarWidth'      => $ProgressBarWidth
                                  );
        $this->_Template_Contents = $template_contents;
        $application->registerAttributes($this->_Template_Contents);
        return modApiFunc('TmplFiller', 'fill', "tools/backup_delete_progress/","container.tpl.html", array());
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