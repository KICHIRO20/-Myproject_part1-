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
class BackupRestore
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
    function BackupRestore()
    {
    }


    /**
     *
     */
    function output()
    {
        global $application;
        $MessageResources = &$application->getInstance('MessageResources');

        if ($error = modApiFunc("Tools", "getRestoreError"))
        {
            modApiFunc("Tools", "unsetRestoreError");
            modApiFunc("Tools", "saveState");
            $template_contents = array(
                                       'ErrorMsg' => $MessageResources->getMessage($error)
                                      );
            $this->_Template_Contents = $template_contents;
            $application->registerAttributes($this->_Template_Contents);
            return modApiFunc('TmplFiller', 'fill', "tools/backup_restore/","error.tpl.html", array());
        }

        $request = new Request();
        $request->setView('AdminRestoreProgress');
        $request->setAction('CreateAutoBackup');
        $request->setKey('AutoBackup', '');
        $Backup_Start_Link = $request->getURL();

        $template_contents = array(
                                   'RestoreStartLink' => $Backup_Start_Link
                                  );
        $this->_Template_Contents = $template_contents;
        $application->registerAttributes($this->_Template_Contents);
        return modApiFunc('TmplFiller', 'fill', "tools/backup_restore/","container.tpl.html", array());
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