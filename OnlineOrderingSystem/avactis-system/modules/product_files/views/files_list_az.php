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

class PF_FilesList
{
    function PF_FilesList()
    {
    }

    function outputResultMessage()
    {
        global $application;
        if(modApiFunc("Session","is_set","ResultMessage"))
        {
            $msg=modApiFunc("Session","get","ResultMessage");
            modApiFunc("Session","un_set","ResultMessage");
            $template_contents=array(
                "ResultMessage" => getMsg('PF',$msg)
            );
            $this->_Template_Contents=$template_contents;
            $application->registerAttributes($this->_Template_Contents);
            $this->mTmplFiller = &$application->getInstance('TmplFiller');
            return $this->mTmplFiller->fill("product_files/misc/", "result-message.tpl.html",array());
        }
        elseif(modApiFunc("Session","is_set","Errors"))
        {
            $return_html_code="";
            $errors=modApiFunc("Session","get","Errors");
            modApiFunc("Session","un_set","Errors");
            foreach($errors as $ekey => $eval)
            {
                $template_contents=array(
                    "ErrorMessage" => (is_int($eval) ? modApiFunc('Shell','getMsgByErrorCode',$eval) : getMsg('PF',$eval))
                );
                $this->_Template_Contents=$template_contents;
                $application->registerAttributes($this->_Template_Contents);
                $this->mTmplFiller = &$application->getInstance('TmplFiller');
                $return_html_code.=$this->mTmplFiller->fill("product_files/misc/", "error-message.tpl.html",array());
            }

            return $return_html_code;
        }
        else
            return "";
    }

    function outFilesByOne()
    {
        global $application;
        $html_code = '';

        foreach($this->PFiles as $k => $pf_info)
        {
            $r = new Request();
            $r->setAction('direct_download_file');
            $r->setKey('file_id',$pf_info['file_id']);

            $template_contents = array(
                'CycleColor' => ($k % 2) == 0 ? '#FFFFFF' : '#EEF2F8'
               ,'FileID' => $pf_info['file_id']
               ,'FileName' => $pf_info['file_name']
               ,'FSize' => modApiFunc('Product_Files','formatFileSize',$pf_info['file_size'])
               ,'FileDescr' => prepareHTMLDisplay($pf_info['file_descr'])
               ,'DirectFileLink' => $r->getURL()
            );

            $this->_Template_Contents=$template_contents;
            $application->registerAttributes($this->_Template_Contents);
            $this->mTmplFiller = &$application->getInstance('TmplFiller');
            $html_code .= $this->mTmplFiller->fill("product_files/files_list/", "one-file.tpl.html",array());
        }

        return $html_code;
    }

    function output_FilesList()
    {
        global $application;
        $this->mTmplFiller = &$application->getInstance('TmplFiller');
        $template_contents = array(
            'ProductID' => $this->product_id
           ,'ListByOne' => count($this->PFiles) ? $this->outFilesByOne() : $this->mTmplFiller->fill("product_files/files_list/", "empty_list.tpl.html",array())
           ,'DeleteButton' => count($this->PFiles) ? $this->mTmplFiller->fill("product_files/files_list/", "delete_button.tpl.html",array()) : ''
        );

        $this->_Template_Contents=$template_contents;
        $application->registerAttributes($this->_Template_Contents);
        return $this->mTmplFiller->fill("product_files/files_list/", "list.tpl.html",array());
    }

    function output_AddFileForm()
    {
        global $application;

        $err_code = modApiFunc('Product_Files','checkFileUploading');

        if($err_code != 0)
        {
            $template_contents = array(
                'ErrorAddFile' => ($err_code == 1) ? getMsg('PF','ERR_UPL_DISABLED_IN_INI') :
                    ( ($err_code == 2) ? getMsg('PF','ERR_UPL_DIR_IS_NOT_DIR',$application->getAppIni('PRODUCT_FILES_DIR')) :
                        getMsg('PF','ERR_UPL_DIR_IS_NOT_WRITABLE',$application->getAppIni('PRODUCT_FILES_DIR')) )
            );

            $this->_Template_Contents=$template_contents;
            $application->registerAttributes($this->_Template_Contents);
            $this->mTmplFiller = &$application->getInstance('TmplFiller');
            return $this->mTmplFiller->fill("product_files/files_list/", "error-add-file-form.tpl.html",array());
        }
        else
        {
            $template_contents = array(
                'ProductID' => $this->product_id
               ,'AddFileNoteMessage' => getMsg('PF','ADD_FILE_NOTE_MSG',modApiFunc('Product_Files','formatFileSize',modApiFunc('Shell','getMaxUploadSize')))
            );

            $this->_Template_Contents=$template_contents;
            $application->registerAttributes($this->_Template_Contents);
            $this->mTmplFiller = &$application->getInstance('TmplFiller');
            return $this->mTmplFiller->fill("product_files/files_list/", "add-file-form.tpl.html",array());
        };
    }

    function output_AddFileForm2()
    {
        global $application;

        $template_contents = array(
            'ProductID' => $this->product_id
        );

        $this->_Template_Contents=$template_contents;
        $application->registerAttributes($this->_Template_Contents);
        $this->mTmplFiller = &$application->getInstance('TmplFiller');
        return $this->mTmplFiller->fill("product_files/files_list/", "add-file-form-2.tpl.html",array());
    }

    function output()
    {
        global $application;
        $request = &$application->getInstance('Request');
        $this->product_id = $request->getValueByKey('product_id');

        $prod_obj = &$application->getInstance('CProductInfo',$this->product_id);
        $this->PFiles = modApiFunc('Product_Files','getFilesListForProduct',$this->product_id);

        $template_contents = array(
            'ProductName' => $prod_obj->getProductTagValue('Name')
           ,'Local_ProductBookmarks' => getProductBookmarks('files',$this->product_id)
           ,'ResultMessage' => $this->outputResultMessage()
           ,'FilesList' => $this->output_FilesList()
           ,'AddFileForm' => $this->output_AddFileForm2()
           ,'UpdButDisplay' => (count($this->PFiles) > 0 ? '' : 'none')
        );

        $this->_Template_Contents=$template_contents;
        $application->registerAttributes($this->_Template_Contents);
        $this->mTmplFiller = &$application->getInstance('TmplFiller');
        return $this->mTmplFiller->fill("product_files/files_list/", "container.tpl.html",array());
    }

    function getTag($tag)
    {
        if ($tag == 'ProductInfoLink') {
            $cz_layouts = LayoutConfigurationManager::static_get_cz_layouts_list();
            LayoutConfigurationManager::static_activate_cz_layout(array_shift(array_keys($cz_layouts)));
            $request = new CZRequest();
            $request->setView  ( 'ProductInfo' );
            $request->setAction( 'SetCurrentProduct' );
            $request->setKey   ( 'prod_id', $this->product_id);
            $request->setProductID($this->product_id);
            return $request->getURL();
        }
        return getKeyIgnoreCase($tag, $this->_Template_Contents);
    }

    var $_Template_Contents;
    var $product_id;
    var $PFiles;
};

?>