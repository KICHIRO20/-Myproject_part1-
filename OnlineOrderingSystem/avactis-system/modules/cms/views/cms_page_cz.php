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
 * @package CMS
 * @author Alexey Florinsky
 *
 */

class CMSPage
{
    function getTemplateFormat()
    {
        $format = array(
            'layout-file'        => 'cms-page-block.ini'
           ,'files' => array(
                'Container' => TEMPLATE_FILE_SIMPLE
            )
           ,'options' => array(
            )
        );
        return $format;
    }

    function CMSPage()
    {
    }

    function output()
    {
        global $application;
        $this->templateFiller = new TemplateFiller();
        $this->template = $application->getBlockTemplate('CMSPage');
        $this->templateFiller->setTemplate($this->template);
        return $this->templateFiller->fill('Container');
    }

    function getTag($tag)
    {
        $value = null;
        return $value;
    }
};

?>