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

class FileSelector
{
    function FileSelector()
    {
        $this->template_folder = "file_selector";
        $this->fsbrowser_tag = 'fselector_';
        $this->fsbrowser_file_tag = 'server_file_';
    }

    function output()
    {
        global $application;

        $fargs = func_get_args();

        $template_contents = array(
            'ParentBlock' => $fargs[0]
           ,'ParentField' => $fargs[1]
           ,'Local_FSBrowser' => getFSBrowser('FS',$this->fsbrowser_tag.$fargs[1],$this->fsbrowser_file_tag.$fargs[1])
           ,'PostJScode' => $fargs[2]
           ,'SelectorType' => empty($fargs[3]) ? "" : $fargs[3]
           ,'MsgAboutFileSize' => getMsg('SH','MSG_ABOUT_MAX_FILE_SIZE',modApiFunc('Localization','formatFileSize',modApiFunc('Shell','getMaxUploadSize')))
        );

        $this->_Template_Contents=$template_contents;
        $application->registerAttributes($this->_Template_Contents);
        $this->mTmplFiller = &$application->getInstance('TmplFiller');
        return $this->mTmplFiller->fill("shell/".$this->template_folder."/", "container.tpl.html",array());
    }

    function getTag($tag)
    {
        return getKeyIgnoreCase($tag, $this->_Template_Contents);
    }

    var $_Template_Contents;
};

?>