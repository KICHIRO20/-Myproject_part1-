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

class CustomerNewPasswordForm
{
    function getTemplateFormat()
    {
        $format = array(
            'layout-file'        => 'customer-account-new-password-form.ini'
           ,'files' => array(
                'Form' => TEMPLATE_FILE_SIMPLE
               ,'AccessDenied' => TEMPLATE_FILE_SIMPLE
            )
           ,'options' => array(
            )
        );
        return $format;
    }

    function CustomerNewPasswordForm()
    {
        $this->NoView = false;

        $this->customer_obj = null;

        $request = new Request();
        $this->key = $request->getValueByKey('key');

        $account_name = modApiFunc('Customer_Account','getAccountByActivationKey',$this->key);

        if($account_name != null)
        {
            global $application;
            $this->customer_obj = &$application->getInstance('CCustomerInfo',$account_name);
        };

        $settings = modApiFunc('Customer_Account','getSettings');
        if($settings['CHECKOUT_TYPE'] == CHECKOUT_TYPE_QUICK)
        {
            $this->NoView = true;
        };
    }

    function output()
    {
        if($this->NoView)
        {
            return '';
        };

        global $application;

        $_template_tags = array(
            'CustomerAccountName'
           ,'Local_FormActionURL'
          );

        $application->registerAttributes($_template_tags);
        $this->templateFiller = new TemplateFiller();
        $this->template = $application->getBlockTemplate('CustomerNewPasswordForm');
        $this->templateFiller->setTemplate($this->template);

        if($this->customer_obj != null)
        {
            return $this->templateFiller->fill('Form');
        }
        else
        {
            return $this->templateFiller->fill('AccessDenied');
        };
    }

    function getTag($tag)
    {
        $value = null;

        switch($tag)
        {
            case 'CustomerAccountName':
                $value = $this->customer_obj->getDisplayAccountName();
                break;
            case 'Local_FormActionURL':
                $r = new Request();
                $r->setView('CustomerNewPassword');
                $r->setAction('save_account_password');
                $r->setKey('key',$this->key);
                $value = $r->getURL();
                break;
        };

        return $value;
    }

    var $customer_obj;
    var $key;
};

?>