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
 * @package CustomerAccount
 * @author Egor V. Derevyankin
 *
 */

class MessageBox
{
    function getTemplateFormat()
    {
        $format = array(
            'layout-file'        => 'customer-account-reg-msg.ini'
           ,'files' => array(
                'Container' => TEMPLATE_FILE_SIMPLE
               ,'Message'   => TEMPLATE_FILE_SIMPLE
               ,'Error'     => TEMPLATE_FILE_SIMPLE
            )
           ,'options' => array(
            )
        );
        return $format;
    }

    function MessageBox()
    {
        global $application;

        #check if fatal errors of the block tag exist
        $this->NoView = false;
        if ($application->issetBlockTagFatalErrors("MessageBox"))
        {
            $this->NoView = true;
        };

        $settings = modApiFunc('Customer_Account','getSettings');
        if($settings['CHECKOUT_TYPE'] == CHECKOUT_TYPE_QUICK)
        {
            $this->NoView = true;
        };

        if(!modApiFunc('Session','is_set','ResultMessage') and !modApiFunc('Session','is_set','RegisterErrors'))
        {
            $this->NoView = true;
        };
    }

    function out_MessagesList()
    {
        if(modApiFunc('Session','is_set','ResultMessage'))
        {
            $html_code = '';
            $messages = modApiFunc('Session','get','ResultMessage');
            modApiFunc('Session','un_set','ResultMessage');
            if(!is_array($messages))
            {
                $messages = array($messages);
            };
            foreach($messages as $k => $message)
            {
                $this->current_message = $message;
                $html_code .= $this->templateFiller->fill('Message');
            };
            return $html_code;
        };

        if(modApiFunc('Session','is_set','RegisterErrors'))
        {
            $html_code = '';
            $errors = modApiFunc('Session','get','RegisterErrors');
            modApiFunc('Session','un_set','RegisterErrors');
            foreach($errors as $k => $error)
            {
                $this->current_error_number = $k+1;
                $this->current_error_message = $error;
                $html_code .= $this->templateFiller->fill('Error');
            };
            return $html_code;
        };

        return '';
    }

    function output()
    {
        if($this->NoView)
        {
            return '';
        };

        global $application;

        $_template_tags = array(
            'Local_MessagesList'
           ,'Local_Message'
           ,'Local_ErrorNumber'
           ,'Local_ErrorMessage'
          );

        $application->registerAttributes($_template_tags);
        $this->templateFiller = new TemplateFiller();
        $this->template = $application->getBlockTemplate('MessageBox');
        $this->templateFiller->setTemplate($this->template);

        return $this->templateFiller->fill('Container');
    }

    function getTag($tag)
    {
        $value = null;

        switch($tag)
        {
            case 'Local_MessagesList':
                $value = $this->out_MessagesList();
                break;
            case 'Local_Message':
                $value = cz_getMsg($this->current_message);
                break;
            case 'Local_ErrorNumber':
                $value = $this->current_error_number;
                break;
            case 'Local_ErrorMessage':
                if(preg_match("/^E_.+/",$this->current_error_message))
                {
                    $value = cz_getMsg($this->current_error_message);
                }
                else
                {
                    $value = $this->current_error_message;
                };
                break;
        };

        return $value;
    }

    var $message;
    var $current_error_number;
    var $current_error_message;
    var $current_message;
    var $MR;
};

?>