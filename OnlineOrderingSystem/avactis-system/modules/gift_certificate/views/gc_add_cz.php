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
 * GiftCertificate module.
 * GiftCertificateForm storefront view
 *
 * @package GiftCertificate
 * @access  public
 *
 */
class GiftCertificateForm
{
    function getTemplateFormat()
    {
        $format = array(
            'layout-file'        => 'gift-certificate-form-config.ini'
           ,'files' => array(
                'AddGCForm'      => TEMPLATE_FILE_SIMPLE,
                'GCInfo'         => TEMPLATE_FILE_SIMPLE,
            )
           ,'options' => array(
            )
        );
        return $format;
    }

    function GiftCertificateForm()
    {
        global $application;

        #                                              
        $this->NoView = false;
        if ($application->issetBlockTagFatalErrors("GiftCertificateForm"))
        {
            $this->NoView = true;
        }
    }

    function output()
    {
        global $application;

        #                                         
        if ($this->NoView)
        {
            $application->outputTagErrors(true, "GiftCertificateForm", "Errors");
            return "";
        }
        else
        {
            $application->outputTagErrors(true, "GiftCertificateForm", "Warnings");
        }

        $_template_tags = array(
            'Local_Error' => "",
            'Local_AddURL' => "",
            'Local_AppliedGCList' => '',
            'Local_GC_Remainder' => '',
            'Local_GC_Amount' => '',
            'Local_GC_Code' =>  '',
            'Local_GC_RemoveURL' => '',
        );

        $application->registerAttributes($_template_tags);

        $this->templateFiller = new TemplateFiller();
        $this->template = $application->getBlockTemplate('GiftCertificateForm');
        $this->templateFiller->setTemplate($this->template);

        $retval = $this->templateFiller->fill("AddGCForm");
        return $retval;
    }

    function outputAppliedGCList()
    {
        $gc_list = modApiFunc('GiftCertificateApi','getCurrentGiftCertificateList');
        $html = '';
        foreach($gc_list as $gc_code)
        {
            $gc = new GiftCertificate($gc_code);
            $this->gc_item['Local_GC_Code'] = $gc->code;
            $this->gc_item['Local_GC_Amount'] = modApiFunc('Localization', 'currency_format',$gc->amount);
            $this->gc_item['Local_GC_Remainder'] = modApiFunc('Localization', 'currency_format', $gc->remainder);

            $request = new Request();
            $request->setView  ( CURRENT_REQUEST_URL );
            $request->setAction( 'RemoveGiftCertificateAction' );
            $request->setKey('gc_code', $gc->code);
            $this->gc_item['Local_GC_RemoveURL'] = $request->getURL();
            $html .= $this->templateFiller->fill("GCInfo");
        }
        return $html;
    }

    function getTag($tag)
    {
        global $application;
        $value = null;

        switch($tag)
        {
            case 'Local_AppliedGCList':
                $value = $this->outputAppliedGCList();
                break;
            case 'Local_AddURL':
                $request = new Request();
                $request->setView  ( CURRENT_REQUEST_URL );
                $request->setAction( 'AddGiftCertificateAction' );
                $value = $request->getURL();
                break;
            case 'Local_Error':
                if (modApiFunc('Session','is_Set','gc_addcode_action_erors'))
                {
                    $errors = modApiFunc('Session','get','gc_addcode_action_erors');
                    modApiFunc('Session','un_Set','gc_addcode_action_erors');
                    foreach ($errors as $e)
                    {
                        switch($e)
                        {
                            case GC_E_NOT_APPLICABLE:
                                $value = getLabel('GIFTCERTIFICATE_ERROR_NOT_APPLICABLE');
                                break;
                            case GC_E_INVALID_CODE:
                                $value = getLabel('GIFTCERTIFICATE_ERROR_INVALID_CODE');
                                break;
                            default:
                                $value = getLabel('GIFTCERTIFICATE_ERROR_NOT_APPLICABLE');
                                break;
                        }
                    }
                }
                else
                {
                    $value = '';
                }
                break;
            default:
                if (isset($this->gc_item[$tag]))
                {
                    $value = $this->gc_item[$tag];
                }
                break;
        }

        return $value;
    }

    var $gc_item = array();
}
?>