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

class CustomerSignInBox
{
    function getTemplateFormat()
    {
        $format = array(
            'layout-file'        => 'customer-account-auth-box.ini'
           ,'files' => array(
                'NotSigned' => TEMPLATE_FILE_SIMPLE
               ,'Signed' => TEMPLATE_FILE_SIMPLE
            )
           ,'options' => array(
            )
        );
        return $format;
    }

    function CustomerSignInBox()
    {
        global $application;

        #check if fatal errors of the block tag exist
        $this->NoView = false;
        if ($application->issetBlockTagFatalErrors("AuthBox"))
        {
            $this->NoView = true;
        }

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
            'Local_FormActionURL',
            'Local_RememberMeOptionEnabled'
          );

        $application->registerAttributes($_template_tags);
        $this->templateFiller = new TemplateFiller();
        $this->template = $application->getBlockTemplate('CustomerSignInBox');
        $this->templateFiller->setTemplate($this->template);

        if(modApiFunc('Customer_Account','getCurrentSignedCustomer') !== null)
        {
            return $this->templateFiller->fill('Signed');
        }
        else
        {
            return $this->templateFiller->fill('NotSigned');
        };
    }

    function getTag($tag)
    {
        $value = null;
	global $application;
        switch($tag)
        {
            case 'Local_FormActionURL':
                $r = new Request();
                $r->setView(CURRENT_REQUEST_URL);
                $r->setAction('customer_sign_in');
                $value = $r->getURL($application->getSectionProtocol('CustomerSignIn'));
                break;

            case 'Local_RememberMeOptionEnabled':
                if ( modApiFunc('Settings','getParamValue','CUSTOMER_ACCOUNT_SETTINGS','ENABLE_SAVE_SESSION') === 'YES' )
                    $value = "TRUE";
                else
                    $value = "FALSE";
            	break;
        };

        return $value;
    }
};

?>