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
 * @package ProductFiles
 * @author Egor V. Derevyankin
 *
 */

class PF_Settings
{
    function PF_Settings()
    {
        loadCoreFile('html_form.php');
    }

    function outputResultMessage()
    {
        global $application;
        if(modApiFunc("Session","is_set","ResultMessage"))
        {
            $msg=modApiFunc("Session","get","ResultMessage");
            modApiFunc("Session","un_set","ResultMessage");
            $template_contents=array(
                "ResultMessage" => getMsg('PF', $msg)
            );
            $this->_Template_Contents=$template_contents;
            $application->registerAttributes($this->_Template_Contents);
            $this->mTmplFiller = &$application->getInstance('TmplFiller');
            return $this->mTmplFiller->fill("product_files/settings/", "result-message.tpl.html",array());
        }
        else
        {
            return "";
        }
    }

    function output()
    {
        global $application;

        $settings = modApiFunc('Product_Files','getSettings');

        $template_contents = array(
            'HLTLField' => HtmlForm::genInputTextField('25','pf_sets[HL_TL]','5',$settings['HL_TL'])
           ,'HLMaxTryField' => HtmlForm::genInputTextField('25','pf_sets[HL_MAX_TRY]','5',$settings['HL_MAX_TRY'])
           ,"ResultMessage"      => $this->outputResultMessage()
        );

        $this->_Template_Contents=$template_contents;
        $application->registerAttributes($this->_Template_Contents);
        $this->mTmplFiller = &$application->getInstance('TmplFiller');
        return $this->mTmplFiller->fill("product_files/settings/", "container.tpl.html",array());
    }

    function getTag($tag)
    {
        return getKeyIgnoreCase($tag, $this->_Template_Contents);
    }

    var $_Template_Contents;
};

?>