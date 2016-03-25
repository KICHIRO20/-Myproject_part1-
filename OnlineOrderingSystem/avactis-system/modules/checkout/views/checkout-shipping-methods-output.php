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
 *
 * @package ShippingCostCalcultor
 * @author Egor V. Derevyankin
 */
class CheckoutShippingMethodsOutput
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    /**
     * @return format of the templates for this view                                                    .
     */
    function getTemplateFormat()
    {
        $format = array(
            'layout-file'        => "checkout-shipping-methods-output-config.ini"
           ,'files' => array(
                'OutputContainer'               => TEMPLATE_FILE_SIMPLE
            )
           ,'options' => array(
            )
        );
        return $format;
    }

    /**
     * ChckoutShippingMethodOutput constructor
     */
    function CheckoutShippingMethodsOutput()
    {
        global $application;

        $this->CHECKOUT_PREREQUISITE_NAME = "shippingModuleAndMethod";

        $this->HTML_LOCAL_TAGS_PREFIX = "Local_";
        $this->NoView = false;
        if ($application->issetBlockTagFatalErrors($this->BlockTemplateName))
        {
            $this->NoView = true;
        }
    }

    /**
     * @return HTML code for this view
     */
    function output()
    {

  //      $this->_CurrShippingMethodId = NULL;
 //       $arg_list = func_get_args();

 //       if(sizeof($arg_list) == 1)
  //      {
  //          $this->_CurrShippingMethodId = $arg_list[0];
  //      }

//        $this->ShippingMethodInfo=modApiFunc($this->ModuleAPIClassName, "getShippingMethodInfo", $this->_CurrShippingMethodId,true);

        global $application;

        //          -                             PersonInfoType,                   ,
        //                                    .
        $person_info_types = modApiFunc("Checkout", "getPersonInfoTypeList");
        foreach($person_info_types as $id => $info)
        {
            if($info['tag'] == $this->CHECKOUT_PREREQUISITE_NAME &&
               $info['active'] == DB_FALSE)
            {
                return "";
            }
        }

        $shipping_module_id = modApiFunc("Checkout","getChosenShippingModuleIdCZ");
        // no shipping is choosen - nothing to show
        if (!$shipping_module_id)
            return '';

        $shipping_method_id = modApiFunc("Checkout","getChosenShippingMethodIdCZ");
        $shipping_module_info = modApiFunc("Checkout","getShippingModuleInfo",$shipping_module_id);

        if($shipping_module_info["GlobalUniqueShippingModuleID"] == modApiFunc("Checkout","getNotNeedShippingModuleID"))
        {
            $mRes = &$application->getInstance('MessageResources',"messages");
            $this->ShippingMethodInfo["method_name"]=$mRes->getMessage('SHIPPING_NOT_NEEDED');
            $application->registerAttributes(array('Local_ShippingMethodName' => ""));
            $this->templateFiller = &$application->getInstance('TemplateFiller');
            $this->template = $application->getBlockTemplate($this->BlockTemplateName);
            $this->templateFiller->setTemplate($this->template);
            $retval = $this->templateFiller->fill("NotNeedShippingOutput");
            return $retval;
        }

        if($shipping_module_info["GlobalUniqueShippingModuleID"]=="6F82BA03-C5B1-585B-CE2E-B8422A1A19F6")
        {
            $mRes = &$application->getInstance('MessageResources',"messages");
            $this->ShippingMethodInfo["method_name"]=$mRes->getMessage('ALL_SM_ARE_INACTIVE');
            $application->registerAttributes(array('Local_ShippingMethodName' => ""));
            $this->templateFiller = &$application->getInstance('TemplateFiller');
            $this->template = $application->getBlockTemplate($this->BlockTemplateName);
            $this->templateFiller->setTemplate($this->template);
            $retval = $this->templateFiller->fill("AllInactiveOutput");
            return $retval;
        };

        $formatted_cart = modApiFunc("Shipping_Cost_Calculator","formatCart",modApiFunc("Cart","getCartContent"));
        modApiFunc("Shipping_Cost_Calculator","setShippingInfo",modApiFunc("Checkout", "getPrerequisiteValidationResults", "shippingInfo"));
        modApiFunc("Shipping_Cost_Calculator","setCart",$formatted_cart);
        $this->ShippingMethodInfo=modApiFunc("Shipping_Cost_Calculator","getCalculatedMethod",$shipping_module_info["APIClassName"],$shipping_method_id);

        //print_r($this->ShippingMethodInfo);

        $application->registerAttributes(array('Local_ShippingMethodCost' => "",
                                               "Local_TotalShippingCharge" => '',
                                               "Local_TotalShippingAndHandlingCost" => '',
                                               'Local_ShippingMethodName' => "",
                                               'Local_ShippingMethodDays' => ""));

        $this->templateFiller = &$application->getInstance('TemplateFiller');
        $this->template = $application->getBlockTemplate($this->BlockTemplateName);
        $this->templateFiller->setTemplate($this->template);

        $retval = $this->templateFiller->fill("OutputContainer");
        return $retval;
    }

    /**
     * @param $tag name of the requested tag
     * @return value of the tag
     */
    function getTag($tag)
    {
        global $application;
        $obj = &$application->getInstance('MessageResources',"messages");
        $value = null;

        switch ($tag)
        {
            case 'Local_ShippingMethodName':
                $value = $this->ShippingMethodInfo['method_name'];
                break;
            case 'Local_ShippingMethodCost':
                $value=modApiFunc("Localization", "currency_format",$this->ShippingMethodInfo['shipping_cost']['ShippingMethodCost']);
                break;
            case 'Local_TotalShippingCharge':
                $value=modApiFunc("Localization", "currency_format",$this->ShippingMethodInfo['shipping_cost']['TotalShippingCharge']);
                break;
            case 'Local_TotalShippingAndHandlingCost':
                $value=modApiFunc("Localization", "currency_format",$this->ShippingMethodInfo['shipping_cost']['TotalShippingAndHandlingCost']);
                break;
            case 'Local_ShippingMethodDays':
                $_days=$this->ShippingMethodInfo['days'];
                if(is_numeric($_days) and ($_days > 0))
                    $value = $_days . " " . $obj->getMessage('LABEL_DAYS');
                elseif(is_string($_days) and $_days!="")
                    $value = $_days;
                break;
            default:
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
//    var $ModuleAPIClassName = "Shipping_Module_FedEx";
    var $BlockTemplateName = "CheckoutShippingMethodsOutput";

//    var $_CurrShippingMethodId;
    var $HTML_LOCAL_TAGS_PREFIX;
    var $ShippingMethodInfo;
    /**#@-*/
}
?>