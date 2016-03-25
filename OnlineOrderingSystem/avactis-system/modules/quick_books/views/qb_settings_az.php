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
 * @package QuickBooks
 * @author Egor V. Derevyankin
 *
 */

class QB_Settings
{
    function QB_Settings()
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
                "ResultMessage" => getMsg('QB',$msg)
            );
            $this->_Template_Contents=$template_contents;
            $application->registerAttributes($this->_Template_Contents);
            $this->mTmplFiller = &$application->getInstance('TmplFiller');
            return $this->mTmplFiller->fill("quick_books/settings/", "result-message.tpl.html",array());
        }
        else
        {
            return "";
        }
    }

    function output()
    {
        global $application;

        $settings = modApiFunc('Quick_Books','getSettings');

        $op_as_inv_select = array(
            "select_name" => "qbs[OP_AS_INV]"
           ,"selected_value" => $settings['OP_AS_INV']
        	,'class' => 'form-control input-sm input-xsmall'
        	,"values" => array(
                array('value'=>'Y','contents'=>getMsg('QB','LBL_YES'))
               ,array('value'=>'N','contents'=>getMsg('QB','LBL_NO'))
           )
        );

        $template_contents = array(
            "TrnsClassField" => HtmlForm::genInputTextField('255','qbs[TRNS_CLASS]','70',$settings['TRNS_CLASS'])
           ,"AccTaxField" => HtmlForm::genInputTextField('255','qbs[ACC_TAX]','70',$settings['ACC_TAX'])
           ,"AccProductField" => HtmlForm::genInputTextField('255','qbs[ACC_PRODUCT]','70',$settings['ACC_PRODUCT'])
           ,"AccShippingField" => HtmlForm::genInputTextField('255','qbs[ACC_SHIPPING]','70',$settings['ACC_SHIPPING'])
           ,"AccInventoryField" => HtmlForm::genInputTextField('255','qbs[ACC_INVENTORY]','70',$settings['ACC_INVENTORY'])
           ,"AccGlobalDiscountField" => HtmlForm::genInputTextField('255','qbs[ACC_GLOBAL_DISCOUNT]','70',$settings['ACC_GLOBAL_DISCOUNT'])
           ,"AccPromoCodeDiscountField" => HtmlForm::genInputTextField('255','qbs[ACC_PROMOCODE_DISCOUNT]','70',$settings['ACC_PROMOCODE_DISCOUNT'])
           ,"AccQuantityDiscountField" => HtmlForm::genInputTextField('255','qbs[ACC_QUANTITY_DISCOUNT]','70',$settings['ACC_QUANTITY_DISCOUNT'])
           ,"OpAsInvField" => HtmlForm::genDropdownSingleChoice($op_as_inv_select)
           ,"MinQISField" => HtmlForm::genInputTextField('255','qbs[MIN_QIS]','70',$settings['MIN_QIS'])
           ,"AccCOGSField" => HtmlForm::genInputTextField('255','qbs[ACC_COGS]','70',$settings['ACC_COGS'])
           ,"OrdersPrefixField" => HtmlForm::genInputTextField('255','qbs[QB_ORDERS_PREFIX]','70',$settings['QB_ORDERS_PREFIX'])
	   ,"ResultMessage"         => $this->outputResultMessage()
        );

        $this->_Template_Contents=$template_contents;
        $application->registerAttributes($this->_Template_Contents);
        $this->mTmplFiller = &$application->getInstance('TmplFiller');
        return $this->mTmplFiller->fill("quick_books/settings/", "container.tpl.html",array());
    }

    function getTag($tag)
    {
        return getKeyIgnoreCase($tag, $this->_Template_Contents);
    }

};

?>