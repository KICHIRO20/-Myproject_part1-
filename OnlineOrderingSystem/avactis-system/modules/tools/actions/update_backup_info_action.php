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
 *
 * @package Tools
 * @author Alexander Girin
 */
class UpdateBackupInfo extends AjaxAction
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * constructor
     */
    function UpdateBackupInfo()
    {
    }

    /**
     *
     */
    function onAction()
    {
        global $application;

        $MessageResources = &$application->getInstance('MessageResources');
        $request = $application->getInstance('Request');
        $comment = $request->getValueByKey('comment');
        $comment_hash = $request->getValueByKey('comment_hash');

        if ($comment_hash != md5($comment) && $comment)
        {
            $filename = modApiFunc("Tools", "getCurrentBackupFile");
            $backup_dir = $application->getAppIni('PATH_BACKUP_DIR');
            $time = time();
            $admin_info = modApiFunc("Users", "getUserInfo", modApiFunc("Users", "getCurrentUserID"));
            $fp = fopen($backup_dir.$filename."/info/backup.log", "a");
            $msg = 'BCP_INFO_LOG_MSG_002';
            fwrite($fp,
            $MessageResources->getMessage(new ActionMessage(array($msg,
                                                                  modApiFunc("Localization", "timestamp_date_format", $time),
                                                                  modApiFunc("Localization", "timestamp_time_format", $time),
                                                                  $admin_info['firstname'],
                                                                  $admin_info['lastname'],
                                                                  $admin_info['email']
                                                                 )
                                                           )
                                         )
                  );
            fwrite($fp, "<i>".strtr(prepareHTMLDisplay($comment), array("\n" => "<br>"))."</i><br><hr style='border: 0px; height: 1px; color: #b2c2df; background-color: #b2c2df;'><br>");
            fclose($fp);
            modApiFunc('Session','set','ResultMessage','BCP_INFO_SAVED');
        }
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