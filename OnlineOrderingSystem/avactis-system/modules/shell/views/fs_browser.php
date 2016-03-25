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

class FSBrowser
{
    function FSBrowser()
    {
	}

    function output()
    {
        global $application;
        $fargs = func_get_args();

        $start_dir = (isset($fargs[4])) ? $fargs[4] : $application->getAppIni('PATH_ASC_ROOT');
        $start_dir = str_replace("\\","/",$start_dir);

        $browser_type = _ml_strtolower($fargs[0]);

        $template_contents = array(
            'StartFolder' => $start_dir
           ,'ParentBlock' => $fargs[1]
           ,'ParentField' => $fargs[2]
           ,'PostChoiceJScode' => (isset($fargs[3])) ? $fargs[3] : ''
        );

        $this->_Template_Contents=$template_contents;
        $application->registerAttributes($this->_Template_Contents);
        $this->mTmplFiller = &$application->getInstance('TmplFiller');
        return $this->mTmplFiller->fill("shell/".$browser_type."_browser/", "container.tpl.html",array());
    }

    function getTag($tag)
    {
        return getKeyIgnoreCase($tag, $this->_Template_Contents);
    }
};

?>