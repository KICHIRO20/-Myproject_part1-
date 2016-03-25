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
 * Checkout Info Modules List view.
 * @package Checkout
 * @author Oleg Vlasenko
 */

class CheckoutInfoList
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     *  CheckoutPaymentModulesList constructor.
     */
    function CheckoutInfoList()
    {
        global $application;

        loadCoreFile('html_form.php');

        #check if fatal errors exist in the block tag
        $this->NoView = false;
        if ($application->issetBlockTagFatalErrors("CheckoutPaymentModulesList"))
        {
            $this->NoView = true;
        }

        if(modApiFunc("Session", "is_Set", "SessionPost"))
        {
            $this->copyFormData();
            modApiFunc('Session', 'un_Set', 'SessionPost');
        }
        else
        {
            $this->initFormData();
        }

        $this->mTmplFiller = &$application->getInstance('TmplFiller');
    }

    function copyFormData()
    {
        // eliminate copying on construction
        $SessionPost = modApiFunc("Session", "get", "SessionPost");

        $this->ViewState =
            $SessionPost["ViewState"];

        //Remove some data, that should not be resent to action, from ViewState.
        if(isset($this->ViewState["ErrorsArray"]) &&
           count($this->ViewState["ErrorsArray"]) > 0)
        {
            $this->ErrorsArray = $this->ViewState["ErrorsArray"];
            unset($this->ViewState["ErrorsArray"]);
        }

        $this->POST  =
            array();
    }

    function initFormData()
    {
        $this->ViewState =
            array();
        $this->POST  =
            array();
    }

    function outputErrors()
    {
        global $application;
        if (!is_array($this->ErrorsArray) || sizeof($this->ErrorsArray) == 0)
        {
            return;
        }
        $result = "";
        $application->registerAttributes(array('ErrorIndex', 'Error'));
        $this->_error_index = 0;
        foreach ($this->ErrorsArray as $error)
        {
            $this->_error_index++;
            $this->_error = $error;
            $result .= $this->mTmplFiller->fill("checkout/checkout-info/", "error.tpl.html", array());
        }
        return $result;
    }

    /**
     * Prepares data for the View "Container".
     */
    function getGroupsList()
    {
        global $application;


        $result = modApiFunc('Checkout', 'getPersonInfoVariantList');
        $types = modApiFunc('Checkout', 'getPersonInfoTypeList');

        //                         Person Info Variant'
        //  ShippingModuleAndMethod   PaymentModule.
        //                                  PersonInfoType'   (
        //  store block'  )
        $result[] = array
        (
            "variant_id" => PERSON_INFO_TYPE_PAYMENT_MODULE_ID
           ,"visible_name" =>  getMsg('SYS','CHECKOUT_PERSON_INFO_PAYMENT_MODULE_VISIBLE_NAME')
           ,"type_id" => PERSON_INFO_TYPE_PAYMENT_MODULE_ID
        );

        $result[] = array
        (
            "variant_id" => PERSON_INFO_TYPE_SHIPPING_MODULE_AND_METHOD_ID
           ,"visible_name" =>  getMsg('SYS','CHECKOUT_PERSON_INFO_SHIPPING_MODULE_AND_METHOD_VISIBLE_NAME')
           ,"type_id" => PERSON_INFO_TYPE_SHIPPING_MODULE_AND_METHOD_ID
        );

        $value = "";
        foreach ($result as $index => $variant)
        {
            $variantId = $variant['variant_id'];
            $this->_Group_Template_Contents = array(
                "VariantId"     => $variantId
               ,"PersonInfoTypeId" => $variant['type_id']
               ,"PersonInfoTypeStatus" => $types[$variant['type_id']]['active']
            );

            $request = new Request();
            $request->setView('StoreSettingsPage');
            $request->setAction('FlipPersonInfoTypeStatus');

            $template_contents = array(
                "VariantId"     => $variantId
               ,"PersonInfoTypeId" => $variant['type_id']
               ,"PersonInfoTypeStatus" => $types[$variant['type_id']]['active']
               ,"FlipPersonInfoTypeStatusHref" => $request->getURL()
               ,"UpdateActionHref" => $this->outputUpdateActionHref()
               ,"VisibleName"   => $variant['visible_name']
               ,"Attributes"    => $this->getAttributesList($variantId)
               ,"EditFieldLink" => "checkout-info-attribute-edit.php"
               ,"SortGroupLink" => "checkout-info-sort-group.php"
               #,"CheckoutCustomAttributes" => $this->getCustomAttributesList($variantId)
            );

            $this->_Template_Contents = array_merge($this->_Template_Contents, $template_contents);
            $application->registerAttributes($this->_Template_Contents);

            //    Payment Method   Shipping Method
            //  Person Info Variant'
            if($variant['type_id'] != PERSON_INFO_TYPE_PAYMENT_MODULE_ID &&
               $variant['type_id'] != PERSON_INFO_TYPE_SHIPPING_MODULE_AND_METHOD_ID &&
               $variant['type_id'] != PERSON_INFO_TYPE_STORE_OWNER_INFO_ID)
            {
                switch($types[$variant['type_id']]['active'])
                {

                    case "false":
                    {
                        $value .= $this->TemplateFiller->fill("checkout/checkout-info/", "group_inactive.tpl.html", array());
                        break;
                    }
                    case "true":
                    {
                        $value .= $this->TemplateFiller->fill("checkout/checkout-info/", "group.tpl.html", array());
                        break;
                    }

                    default:
                    {
                        //error
                        break;
                    }
                }
            }
            else if($variant['type_id'] != PERSON_INFO_TYPE_STORE_OWNER_INFO_ID)
            {
                switch($types[$variant['type_id']]['active'])
                {
                    case "false":
                    {
                        $value .= $this->TemplateFiller->fill("checkout/checkout-info/", "group_simple_inactive.tpl.html", array());
                        break;
                    }
                    case "true":
                    {
                        $value .= $this->TemplateFiller->fill("checkout/checkout-info/", "group_simple.tpl.html", array());
                        break;
                    }
                    default:
                    {
                        //error
                        break;
                    }
                }
            }
        }

        return $value;
    }

    /**
     * Prepares data for the View "Group".
     */
    function getAttributesList($variantId)
    {
        global $application;
        $ids = modApiFunc('Checkout', 'getPersonInfoAttributeIdList', $variantId, ALL_ATTRIBUTES);

        $value = "";
        foreach ($ids as $attributeId)
        {
            $fields = modApiFunc('Checkout', 'getPersonInfoFieldsList', $variantId, $attributeId);

            $yes_no_tpl = array(
               'name'      => '',
               'value'     => '',
               'id'         => '',
               'is_checked' => '',
               'onclick'    => ''
            );

            $param = "";
            $name_textbox = "<input type='text' class='form-control form-filter input-medium' ".HtmlForm::genInputTextField(40,$fields['attribute_id'].'[name]',40,$fields['name'],"onchange='javascript:OnCheckboxClick".$variantId."(id)'")."/>";
            $descr_textbox = "<input type='text' class='form-control form-filter input-medium' ".HtmlForm::genInputTextField(60,$fields['attribute_id'].'[descr]',60,$fields['descr'],"onchange='javascript:OnCheckboxClick".$variantId."(id)'")."/>";
            if ($this->_Group_Template_Contents["PersonInfoTypeStatus"] == "false")
            {
        	    $name_textbox = $fields['name'];
        	    $param = "disabled";
            }

            foreach(array('visible','required') as $prop_name)
            {
                $var_name = $prop_name.'_checkbox';
                $$var_name = $yes_no_tpl;
                ${$var_name}['name'] = $fields['attribute_id'].'['.$prop_name.']';
                ${$var_name}['value'] = "1";
                ${$var_name}['is_checked'] = ($fields[$prop_name] == "1")?"checked":"";
                ${$var_name}['id'] = $fields['attribute_id'].'_'.$prop_name;
                ${$var_name}['onclick'] = 'OnCheckboxClick'.$variantId."('".${$var_name}['id']."')";
                if (!empty($fields['unremovable'])) $param = "disabled";
                $$var_name = HtmlForm::genCheckbox($$var_name, $param);
                $$var_name .= "\n<input type='hidden' ".HtmlForm::genHiddenField($fields['attribute_id'].'[attr_id]', $attributeId)." />";
                if (!empty($fields['unremovable']))
                {
                	$$var_name .= "\n<input type='hidden' ".HtmlForm::genHiddenField($fields['attribute_id'].'[unremovable]', "unremovable")." />";
                }
            }

            //$fields['name'];
            $template_contents = array(
                "VariantId"         => $variantId,
                "AttributeId"       => $attributeId,
                "Name"              => $name_textbox,
                "Descr"             => $descr_textbox,
                "IsVisible"         => $visible_checkbox,
                "IsRequiered"       => $required_checkbox,
            );

            $this->_Template_Contents = array_merge($this->_Template_Contents, $template_contents);
            $application->registerAttributes($this->_Template_Contents);

            switch($this->_Group_Template_Contents["PersonInfoTypeStatus"])
            {
                case "false":
                {
                    $value .= $this->TemplateFiller->fill("checkout/checkout-info/", "attribute_inactive.tpl.html", array());
                    break;
                }
                case "true":
                {
                   $value .= $this->TemplateFiller->fill("checkout/checkout-info/", "attribute.tpl.html", array());
                    break;
                }
                default:
                {
                    //error
                    break;
                }
            }
        }
        return $value;
    }

     /**
     * @abstract Prepares the list of custom attributes for each group
     * @param $variantId group id
     */
    function getCustomAttributesList($variantId)
    {
        global $application;

        $ids = modApiFunc('Checkout', 'getPersonInfoAttributeIdList', $variantId,'CUSTOM_ATTRIBUTES_ONLY');

        $value = "";

        if (count($ids) == 0) // no custom attributes
        {
            $value = $this->TemplateFiller->fill("checkout/checkout-info/", "no-custom-attributes.tpl.html", array());
            return $value;
        }

        foreach ($ids as $attributeId)
        {
            $fields = modApiFunc('Checkout', 'getPersonInfoFieldsList', $variantId, $attributeId);

            $yes_no_tpl = array(
               'name'      => '',
               'value'     => '',
               'id'         => '',
               'is_checked' => '',
               'onclick'    => ''
            );

            $param = "";
            $name_textbox = "<input type='text' style='border:1px solid #B2C2DF; font-family:Verdana,Arial,sans-serif; font-size:11px; vertical-align:top;' ".HtmlForm::genInputTextField(40,$fields['attribute_id'].'[name]',40,$fields['name'],"onchange='javascript:OnCheckboxClick".$variantId."(id)'")."/>";
            if ($this->_Group_Template_Contents["PersonInfoTypeStatus"] == "false")
            {
        	    $name_textbox = $fields['name'];
        	    $param = "disabled";
            }

            foreach(array('visible','required') as $prop_name)
            {
                $var_name = $prop_name.'_checkbox';
                $$var_name = $yes_no_tpl;
                ${$var_name}['name'] = $fields['attribute_id'].'['.$prop_name.']';
                ${$var_name}['value'] = "1";
                ${$var_name}['is_checked'] = ($fields[$prop_name] == "1")?"checked":"";
                ${$var_name}['id'] = $fields['attribute_id'].'_'.$prop_name;
                ${$var_name}['onclick'] = 'OnCheckboxClick'.$variantId."('".${$var_name}['id']."')";
                if (!empty($fields['unremovable'])) $param = "disabled";
                $$var_name = HtmlForm::genCheckbox($$var_name, $param);
                $$var_name .= "\n<input type='hidden' ".HtmlForm::genHiddenField($fields['attribute_id'].'[attr_id]', $attributeId)." />";
                if (!empty($fields['unremovable']))
                {
                	$$var_name .= "\n<input type='hidden' ".HtmlForm::genHiddenField($fields['attribute_id'].'[unremovable]', "unremovable")." />";
                }
            }

            //$fields['name'];
            $template_contents = array(
                "VariantId"         => $variantId,
                "AttributeId"       => $attributeId,
                "Name"              => $name_textbox,
                "Descr"             => $fields['descr'],
                "IsVisible"         => $visible_checkbox,
                "IsRequiered"       => $required_checkbox,
            );

            $this->_Template_Contents = array_merge($this->_Template_Contents, $template_contents);
            $application->registerAttributes($this->_Template_Contents);

            switch($this->_Group_Template_Contents["PersonInfoTypeStatus"])
            {
                case "false":
                {
                    $value .= $this->TemplateFiller->fill("checkout/checkout-info/", "attribute_inactive.tpl.html", array());
                    break;
                }
                case "true":
                {
                   $value .= $this->TemplateFiller->fill("checkout/checkout-info/", "attribute.tpl.html", array());
                   break;
                }
                default:
                {
                    //error
                    break;
                }
            }
        }
        return $value;
    }

    function outputUpdateActionHref()
    {
        $request = new Request();
        $request->setView  ( 'StoreSettingsPage' );
        $request->setAction( 'UpdateCheckoutInfo' );
        return $request->getURL();
    }

    /**
     * Otputs the view.
     */
    function output()
    {
        global $application;

        $this->MessageResources = &$application->getInstance('MessageResources');
        $this->TemplateFiller = $application->getInstance('TmplFiller');
        $this->_Template_Contents = array();

        $template_contents = array(
            "Groups" => $this->getGroupsList()
           ,"Errors" => $this->outputErrors()
        );

        $this->_Template_Contents = array_merge($this->_Template_Contents, $template_contents);
        $application->registerAttributes($this->_Template_Contents);

        return $this->TemplateFiller->fill("checkout/checkout-info/", "container.tpl.html", array());
    }

    function getTag($tag)
    {
        switch ($tag)
        {
            case 'ErrorIndex':
                $value = $this->_error_index;
                break;

            case 'Error':
                $value = $this->_error;
                break;

            default:
                $value = $this->_Template_Contents[$tag];
                break;
        };
        return $value;
    }

    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */

    var $ErrorsArray;

    /**
     * Reference to the TemplateFiller object.
     *
     * @var TemplateFiller
     */
    var $templateFiller;

    /**#@-*/
}
?>