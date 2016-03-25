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
/* wrapper */

loadCoreFile('htmlMimeMail.php');

class ascHtmlMimeMail extends htmlMimeMail
{
    function ascHtmlMimeMail()
    {
        global $application;
        $res = parent::HtmlMimeMail();
        $mail_settings = modApiFunc("Configuration", "getMailSettings");

        if($mail_settings['MAIL_TYPE'] == 2) // SMTP
        {
            parent::setSMTPParams
            (
                $mail_settings['MAIL_HOST']
               ,$mail_settings['MAIL_PORT']
               ,NULL
               ,$mail_settings['MAIL_AUTH'] == '1'
               ,$mail_settings['MAIL_USER']
               ,$mail_settings['MAIL_PASS']
            );
        }
        return $res;
    }

    function send($recipients, $type = NULL)
    {
        global $application;
        $mail_settings = modApiFunc("Configuration", "getMailSettings");

        if($mail_settings['MAIL_TYPE'] != 1 && $mail_settings['MAIL_TYPE'] != 2)
        {
            $type = 'mail';
        }
        else
        {
            $type = ($mail_settings['MAIL_TYPE'] == 1) ? 'mail' : 'smtp';
        }

        $res = parent::send($recipients, $type);
        return $res;
    }

    function resetMessageBuilt()
    {
        $this->is_built = false;
    }

}
?>