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

_use(dirname(__FILE__).'/file_selector.php');
/**
 * @package Shell
 * @author Vadim Lyalikov
 *
 */

class ServerFileSelector extends FileSelector
{
    function ServerFileSelector()
    {
    }

    function output()
    {
        global $application;
        $this->template_folder = "server_file_selector";
        $this->fsbrowser_tag = 'server_only_fselector_';
        $this->fsbrowser_file_tag = 'server_only_server_file_';

        $fargs = func_get_args();
        if(!isset($fargs[3]))
        {
            $fargs[3] = "";
        }
        if(!isset($fargs[4]))
        {
            $fargs[4] = "";
        }
        return parent::output($fargs[0], $fargs[1], $fargs[2], $fargs[4]);
    }
};

?>