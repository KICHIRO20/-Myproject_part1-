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

class DownloadProductFilePrompt
{
    function getTemplateFormat()
    {
        $format = array(
            'layout-file'        => 'product-files-download-prompt.ini'
           ,'files' => array(
                'DownloadPrompt' => TEMPLATE_FILE_SIMPLE
               ,'DownloadButton' => TEMPLATE_FILE_SIMPLE
               ,'IncorrectHotlink' => TEMPLATE_FILE_SIMPLE
            )
           ,'options' => array(
            )
        );
        return $format;
    }

    function DownloadProductFilePrompt()
    {
        global $application;

        #check if fatal errors of the block tag exist
        $this->NoView = false;
        if ($application->issetBlockTagFatalErrors("DownloadProductFilePrompt"))
        {
            $this->NoView = true;
        }
    }

    function output_DownloadButton()
    {
        if($this->HotlinkInfo['was_try'] < $this->HotlinkInfo['max_try']
            and $this->HotlinkInfo['expire_date'] > time()
            and $this->HotlinkInfo['status']=='U')
        {
            global $application;
            $_template_tags = array(
                    'DownloadDirectLink'
                );
            $application->registerAttributes($_template_tags);
            return $this->templateFiller->fill('DownloadButton');
        }
        else
        {
            global $application;
            $MR = &$application->getInstance('MessageResources','messages','CustomerZone');
            return $MR->getMessage('HOTLINK_LOCKED');
        };
    }

    function output()
    {
        global $application;
        $request = &$application->getInstance('Request');
        $hl_key = $request->getValueByKey('key');

        $this->HotlinkInfo = modApiFunc('Product_Files','getHotlinkInfoByKey',$hl_key);
        $this->FileInfo = modApiFunc('Product_Files','getFileInfoByHotlinkKey',$hl_key);

        if($this->FileInfo != null)
        {
            $_template_tags = array(
                        'FileName'
                       ,'FSize'
                       ,'DownloadButton'
                    );
            $tpl_page = 'DownloadPrompt';
        }
        else
        {
            $_template_tags = array(
                                   );
            $tpl_page = 'IncorrectHotlink';
        }

        $application->registerAttributes($_template_tags);
        $this->templateFiller = new TemplateFiller();
        $this->template = $application->getBlockTemplate('DownloadProductFilePrompt');
        $this->templateFiller->setTemplate($this->template);

        return $this->templateFiller->fill($tpl_page);
    }

    function getTag($tag)
    {
        $value = null;

        switch($tag)
        {
            case 'FileName':
                $value = $this->FileInfo['file_name'];
                break;
            case 'FSize':
                $value = modApiFunc('Product_Files','formatFileSize',$this->FileInfo['file_size']);
                break;
            case 'DownloadButton':
                $value = $this->output_DownloadButton();
                break;
            case 'DownloadDirectLink':
                $download_request = new Request();
                $download_request->setView('Download');
                $download_request->setAction('download_product_file');
                $download_request->setKey('key',$this->HotlinkInfo['hotlink_key']);
                $value = $download_request->getURL();
                break;
        };

        return $value;
    }

    var $HotlinkInfo;
    var $FileInfo;
};

?>