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
 * CreateGiftCertificateForm storefront view
 *
 * @package GiftCertificate
 * @access  public
 *
 */
class CreateGiftCertificateForm
{
    function getTemplateFormat()
    {
        $format = array(
            'layout-file'        => 'gift-certificate-create-form.ini'
           ,'files' => array(
               'Container'               => TEMPLATE_FILE_SIMPLE,
               'ProductDescriptionEmail' => TEMPLATE_FILE_SIMPLE,
               'ProductDescriptionPost'  => TEMPLATE_FILE_SIMPLE
            )
           ,'options' => array(
            )
        );
        return $format;
    }

    function CreateGiftCertificateForm()
    {
        global $application;


        if (modApiFunc("Session","is_set","GC_errors"))
        {
            $this->messages = modApiFunc("Session","get","GC_errors");
            modApiFunc("Session","un_set","GC_errors");
        }

        #                                              
        $this->NoView = false;
        if ($application->issetBlockTagFatalErrors("CreateGiftCertificateForm"))
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
            $application->outputTagErrors(true, "CreateGiftCertificateForm", "Errors");
            return "";
        }
        else
        {
            $application->outputTagErrors(true, "CreateGiftCertificateForm", "Warnings");
        }

        $this->gcValues = array("gc_from" => "", "gc_to" => "", "gc_amount" => "", "gc_message" => "", "gc_sendtype" => "P ", "gc_email" => "", "gc_fname" => "", "gc_lname" => "", "gc_country_id" => "", "gc_state_id" => "", "gc_city" => "", "gc_zip" => "", "gc_address" => "", "gc_phone" => "");
        if (modApiFunc('Session', 'is_Set', 'SessionPost'))
        {
            $this->gcValues = modApiFunc('Session', 'get', 'SessionPost');
            modApiFunc('Session', 'un_Set', 'SessionPost');
        }

        $_template_tags = array(
            'Local_Messages' => '',
            'Local_FormActionURL' => '',
            'Local_GC_Amount' => '',
            'Local_GC_From' =>  '',
            'Local_GC_To' => '',
            'Local_GC_Message' => '',
            'Local_GC_LastName' => '',
            'Local_GC_FirstName' => '',
            'Local_GC_CountriesList' => '',
            'Local_JSFunctions' => '',
            'Local_GC_StatesList' => '',
            'Local_GC_ZipCode' => '',
            'Local_GC_City' => '',
            'Local_GC_Address' => '',
            'Local_GC_Phone' => '',
            'Local_GC_Email' => '',
            'Local_GC_SendType_Email_Selected' => '',
            'Local_GC_SendType_Post_Selected' => '',
            'Local_GC_Currency_Sign' => '',
        );

        $application->registerAttributes($_template_tags, 'CreateGiftCertificateForm');

        $this->templateFiller = new TemplateFiller();
        $this->template = $application->getBlockTemplate('CreateGiftCertificateForm');
        $this->templateFiller->setTemplate($this->template);

        $retval = $this->templateFiller->fill("Container");
        return $retval;
    }

    function outputMessages()
    {
        if (!empty($this->messages))
        {
            $message = "";
            $tf = new TmplFiller();

            foreach ($this->messages as $msg)
            {
                switch($msg)
                {
                    case GC_E_FIELD_TO:
                        $txt = getMsg('GCT','GC_E_FIELD_TO');
                    break;
                    case GC_E_FIELD_FROM:
                        $txt = getMsg('GCT','GC_E_FIELD_FROM');
                    break;
                    case GC_E_FIELD_AMOUNT:
                        $txt = getMsg('GCT','GC_E_FIELD_AMOUNT');
                    break;
                    case GC_E_FIELD_AMOUNT_SEPARATOR:
                        $txt = getMsg('GCT','GC_E_FIELD_AMOUNT_SEPARATOR');
                    break;
                    case GC_E_FIELD_ADDRESS:
                        $txt = getMsg('GCT','GC_E_FIELD_ADDRESS');
                    break;
                    case GC_E_FIELD_CITY:
                        $txt = getMsg('GCT','GC_E_FIELD_CITY');
                    break;
                    case GC_E_FIELD_FNAME:
                        $txt = getMsg('GCT','GC_E_FIELD_FNAME');
                    break;
                    case GC_E_FIELD_LNAME:
                        $txt = getMsg('GCT','GC_E_FIELD_LNAME');
                    break;
                    case GC_E_FIELD_EMAIL:
                        $txt = getMsg('GCT','GC_E_FIELD_EMAIL');
                    break;
                    case GC_E_FIELD_COUNTRYID:
                        $txt = getMsg('GCT','GC_E_FIELD_COUNTRYID');
                    break;
                    case GC_E_FIELD_STATEID:
                        $txt = getMsg('GCT','GC_E_FIELD_STATEID');
                    break;
                    case GC_E_FIELD_ZIP:
                        $txt = getMsg('GCT','GC_E_FIELD_ZIP');
                    break;
                    default:
                        $txt = getMsg('GCT','GC_E_FAILED_SAVE');
                }
                $message .= $tf->fill("gift-certificate/create-gc-form/default/", "gc-error.tpl.html", array('ErrorMessage' => $txt), true);
            }
            return $message;
        }
        return "";

    }

    function getTag($tag)
    {
        global $application;
        $value = null;

        switch($tag)
        {
            case 'Local_Messages':
                $value = $this->outputMessages();
                break;
            case 'Local_FormActionURL':
                $request=new Request();
                $request->setView  (CURRENT_REQUEST_URL);
                $request->setAction('AddGCToCart');
                $value = $request->getURL();
                break;
            case 'Local_GC_Amount':
                $value = escapeAttrHTML($this->gcValues['gc_amount']);
                break;
            case 'Local_GC_From':
                $value = escapeAttrHTML($this->gcValues['gc_from']);
                break;
            case 'Local_GC_To':
                $value = escapeAttrHTML($this->gcValues['gc_to']);
                break;
            case 'Local_GC_Message':
                $value = $this->gcValues['gc_message'];
                break;
            case 'Local_GC_LastName':
                $value = escapeAttrHTML($this->gcValues['gc_lname']);
                break;
            case 'Local_GC_FirstName':
                $value = escapeAttrHTML($this->gcValues['gc_fname']);
                break;
            case 'Local_GC_CountriesList':
                $country_id = $this->gcValues['gc_country_id'];
                #$JavascriptFunctions = "";
                $value = modApiFunc("Checkout", "genCountrySelectList", $country_id);
                break;
            case 'Local_JSFunctions':
                $value = modApiFunc("Location", "getJavaScriptCountriesStatesArrays") . modApiFunc("Checkout", "getJavascriptSynchronizeCountriesAndStatesLists");
                break;
            case 'Local_GC_StatesList':
                $state_id = $this->gcValues['gc_state_id'];
                $country_id = $this->gcValues['gc_country_id'];
                if(empty($country_id))
                {
                    //     : move the message to the resource.
                    /*_fatal(*/ //echo "The country is not specified yet. It's impossible to check if the state has been selected valid." /*)*/;
                    $country_id = 223; // USA
                    //The list will be updated automatically by the script body.onload()
                }
                $value = modApiFunc("Checkout", "genStateSelectList", $state_id, $country_id);

                break;
            case 'Local_GC_ZipCode':
                $value = escapeAttrHTML($this->gcValues['gc_zip']);
                break;
            case 'Local_GC_City':
                $value = escapeAttrHTML($this->gcValues['gc_city']);
                break;
            case 'Local_GC_Address':
                $value = escapeAttrHTML($this->gcValues['gc_address']);
                break;
            case 'Local_GC_Phone':
                $value = escapeAttrHTML($this->gcValues['gc_phone']);
                break;
            case 'Local_GC_Email':
                $value = escapeAttrHTML($this->gcValues['gc_email']);
                break;
            case 'Local_GC_SendType_Email_Selected':
                if ($this->gcValues['gc_sendtype'] == "E")
                    $value = 'selected="selected"';
                break;
            case 'Local_GC_SendType_Post_Selected':
                if ($this->gcValues['gc_sendtype'] == "P")
                    $value = 'selected="selected"';
                break;
            case 'Local_GC_Currency_Sign':
                    $value = DEFAULT_CURRENCY_SIGN;
                break;
            default:
                if (isset($this->gc_item[$tag]))
                {
                    $value = escapeAttrHTML($this->gc_item[$tag]);
                }
                break;
        }

        return $value;
    }

    var $gc_item = array();
    var $messages = array();
}
?>