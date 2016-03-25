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
 * @package Shell
 * @author Egor V. Derevyankin
 *
 */

class shell_del_uploaded extends AjaxAction
{
    function shell_del_uploaded()
    {
    }

    function onAction()
    {
        global $application;
        $request = $application->getInstance('Request');

        $to_del = $request->getValueByKey('to_del');

        if(is_array($to_del) and !empty($to_del))
        {
            foreach($to_del as $fpath)
            {
                if(preg_match("/^__shell_upload_\d+$/",basename(dirname($fpath))))
                    modApiFunc('Shell','removeDirectory',dirname($fpath));
            };
        };

    }
};

?>