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
 * TaxExempts module.
 * "TaxExempts -> "FullTaxExemptForm" View.
 *
 * @package
 * @access  public
 *
 */
class FullTaxExemptForm
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     *                                                     .
     */
    function getTemplateFormat()
    {
        $format = array(
            'layout-file'        => 'full-tax-exempt-form-config.ini'
           ,'files' => array(
                'ClaimFullTaxExempt'      => TEMPLATE_FILE_SIMPLE
            )
           ,'options' => array(
            )
        );
        return $format;
    }

    /**
     * constructor
     */
    function FullTaxExemptForm()
    {
        global $application;

        #
        $this->NoView = false;
        if ($application->issetBlockTagFatalErrors("FullTaxExemptForm"))
        {
            $this->NoView = true;
        }
    }

/**
 *                      /         promo code' .
 */
    function outputFullTaxExemptForm()
    {
        global $application;

        $show_form = modApiFunc('Settings','getParamValue','TAXES_PARAMS','ALLOW_FULL_TAX_EXEMPTS');

        if($show_form == DB_FALSE)
        {
            $retval  = "";
        }
        else
        {
            $_template_tags = array('Local_FullTaxExemptStatus' => "",
                                    'Local_FullTaxExemptCustomerInput' => "");

            $application->registerAttributes($_template_tags);
            $this->templateFiller = new TemplateFiller();
            $this->template = $application->getBlockTemplate('FullTaxExemptForm');
            $this->templateFiller->setTemplate($this->template);

            $retval = $this->templateFiller->fill("ClaimFullTaxExempt");
        }
        return $retval;
    }

    /**
     *
     *
     * @ $request->setView  ( '' ) -                     view
     */
    function output()
    {
        global $application;

        #
        if ($this->NoView)
        {
            $application->outputTagErrors(true, "FullTaxExemptForm", "Errors");
            return "";
        }
        else
        {
            $application->outputTagErrors(true, "FullTaxExemptForm", "Warnings");
        }

        $retval = $this->outputFullTaxExemptForm();
        return $retval;
    }

    function getTag($tag)
    {
        global $application;
        $value = null;
        switch ($tag)
        {
            case 'Local_FullTaxExemptStatus':
                switch(modApiFunc("TaxExempts", "getFullTaxExemptStatus"))
                {
                    case DB_TRUE:
                        $value = "CHECKED";
                        break;
                    default:
                        $value = "";
                        break;
                }
                break;

            case 'Local_FullTaxExemptCustomerInput':
                $value = prepareHTMLDisplay(modApiFunc("TaxExempts", "getFullTaxExemptCustomerInput"));
                break;

            case 'Local_FullTaxExemptStatus_Error':
                $FullTaxExemptStatusError = modApiFunc("TaxExempts", "getFullTaxExemptStatusError");
                modApiFunc("PromoCodes", "setFullTaxExemptStatusError", "");
                if(!empty($FullTaxExemptStatusError))
                {
                    $value = getMsg('SYS',$FullTaxExemptStatusError);
                }
                else
                {
                    $value = "";
                }
                break;

            default:
                $value = NULL;
                break;
        }
        return $value;
    }

    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */

    /**
     *                  TemplateFiller.
     * @var TemplateFiller
     */
    var $templateFiller;

    /**
     *                         .
     * @var array
     */
    var $template;

    /**#@-*/

}
?>