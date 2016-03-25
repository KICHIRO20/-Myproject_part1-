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

class CheckoutShippingMethodsSelect
{

    function getTemplateFormat()
    {
        $format = array(
            'layout-file'        => "checkout-shipping-methods-select-config.ini"

           ,'files' => array(
                'ImpossibleToComputeShippingCost' => TEMPLATE_FILE_SIMPLE
               ,'ShippingMethodsContainer' => TEMPLATE_FILE_SIMPLE
               ,'APIMethodsContainer' => TEMPLATE_FILE_SIMPLE
               ,'OneMethod' => TEMPLATE_FILE_SIMPLE
            )
           ,'options' => array(
            )
        );
        return $format;
    }

    function CheckoutShippingMethodsSelect()
    {
        global $application;

        $this->CHECKOUT_PREREQUISITE_NAME = "shippingModuleAndMethod";
        $this->CustomerChoice = modApiFunc('Shipping_Cost_Calculator', 'getCustomerChoice');

        $this->NoView = false;
        if ($application->issetBlockTagFatalErrors($this->BlockTemplateName))
        {
            $this->NoView = true;
        }
    }

    function output()
    {
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

        $shippingPrerequisites = modApiFunc("Checkout", "getPrerequisitesValidationResults", "shippingModuleAndMethod");
        $modulePrerequisites = $shippingPrerequisites['shippingModuleAndMethod'];
        if ($modulePrerequisites['isMet'] == true)
            $this->current_selected_method = $modulePrerequisites['validatedData']['method_code']['value'];

        $application->registerAttributes(
                                            array(
                                                "Local_ShippingMethods" => ''
                                            )
                                        );

        $this->templateFiller = &$application->getInstance('TemplateFiller');
        $this->template = $application->getBlockTemplate($this->BlockTemplateName);
        $this->templateFiller->setTemplate($this->template);

        $retval = $this->templateFiller->fill("ShippingMethodsContainer");
        return $retval;
    }

    function outShippingAPIsMethods()
    {
        global $application;

        $formatted_cart=modApiFunc("Shipping_Cost_Calculator","formatCart",modApiFunc("Cart","getCartContent"));
        modApiFunc("Shipping_Cost_Calculator","setShippingInfo",modApiFunc("Checkout", "getPrerequisiteValidationResults", "shippingInfo"));
        modApiFunc("Shipping_Cost_Calculator","setCart",$formatted_cart);
        $shipping_methods=modApiFunc("Shipping_Cost_Calculator","calculateShippingCost");
        $this->shipping_methods=$shipping_methods;

        $return_html_code="";

        if(is_array($shipping_methods) and !empty($shipping_methods))
        {
            if(count($shipping_methods)==1)
            {
                $a_keys=array_keys($shipping_methods);
                if($a_keys[0]=="Shipping_Not_Needed")
                {
                    $mRes = &$application->getInstance('MessageResources',"messages");
                    $this->current_api_id=modApiFunc("Checkout","getNotNeedShippingModuleID");
                    $this->current_api_name="Shipping_Not_Needed";
                    $this->current_sm_key=0;
                    $this->shipping_methods["Shipping_Not_Needed"]["methods"]["0"]["method_name"]= $mRes->getMessage('SHIPPING_NOT_NEEDED');
                    $application->registerAttributes(
                        array(
                                "Local_FormMethodIdFieldName" => '',
                                "Local_ShippingMethodId" => '',
                                "Local_ShippingMethodName" => ''
                            )
                        );

                    $this->templateFiller = &$application->getInstance('TemplateFiller');
                    $this->template = $application->getBlockTemplate($this->BlockTemplateName);
                    $this->templateFiller->setTemplate($this->template);
                    $return_html_code.= $this->templateFiller->fill("NotNeedShippingInput");
                    return $return_html_code;
                };
                if($a_keys[0]=="Shipping_Module_All_Inactive")
                {
                    $mRes = &$application->getInstance('MessageResources',"messages");
                    $all_inactive_info=modApiFunc("Shipping_Module_All_Inactive","getInfo");
                    $this->current_api_id=$all_inactive_info["GlobalUniqueShippingModuleID"];
                    $this->current_api_name="Shipping_Module_All_Inactive";
                    $this->current_sm_key=0;
                    $this->shipping_methods["Shipping_Module_All_Inactive"]["methods"]["0"]["method_name"]= $mRes->getMessage('ALL_SM_ARE_INACTIVE');
                    $application->registerAttributes(
                        array(
                                "Local_FormMethodIdFieldName" => '',
                                "Local_ShippingMethodId" => '',
                                "Local_ShippingMethodName" => ''
                            )
                        );

                    $this->templateFiller = &$application->getInstance('TemplateFiller');
                    $this->template = $application->getBlockTemplate($this->BlockTemplateName);
                    $this->templateFiller->setTemplate($this->template);
                    $return_html_code.= $this->templateFiller->fill("AllInactiveInput");
                    return $return_html_code;
                }
            };

            foreach($shipping_methods as $API_Name => $api_result)
            {
                $api_info=modApiFunc($API_Name,"getInfo");
                $this->current_api_id=$api_info["GlobalUniqueShippingModuleID"];
                $this->current_api_name=$API_Name;

                $application->registerAttributes(
                        array(
                            "Local_CustomAPIspace" => '',
                            "Local_APIShippingMethods" => '',
                        )
                    );

                $this->templateFiller = &$application->getInstance('TemplateFiller');
                $this->template = $application->getBlockTemplate($this->BlockTemplateName);
                $this->templateFiller->setTemplate($this->template);
                $return_html_code.= $this->templateFiller->fill("APIMethodsContainer");
            };
        }
        else
        {
            $application->registerAttributes(
                          array(
                            "StoreOwnerEmail" => '',
                            )
                         );
            $this->templateFiller = &$application->getInstance('TemplateFiller');
            $this->template = $application->getBlockTemplate($this->BlockTemplateName);
            $this->templateFiller->setTemplate($this->template);
            $return_html_code.= $this->templateFiller->fill("ImpossibleToComputeShippingCost");
        };


        return $return_html_code;
    }

    function outShippingMethodsFromOneAPI()
    {
        global $application;

        $api_result=$this->shipping_methods[$this->current_api_name];

        $return_html_code="";

        foreach($api_result['methods'] as $key => $method_info)
        {
            $this->current_sm_key=$key;
            $application->registerAttributes(
                           array(
                               "Local_ShippingMethodName" => '',
                               "Local_FormMethodIdFieldName" => '',
                               "Local_ShippingMethodId" => '',
                               "Local_ShippingMethodCost" => '',
                               "Local_TotalShippingCharge" => '',
                               "Local_TotalShippingAndHandlingCost" => '',
                               "Local_ShippingMethodIsChecked" => '',
                               "Local_ShippingMethodDays" => ''
                          )
             );
            $this->templateFiller = &$application->getInstance('TemplateFiller');
            $this->template = $application->getBlockTemplate($this->BlockTemplateName);
            $this->templateFiller->setTemplate($this->template);

            $return_html_code.= $this->templateFiller->fill("OneMethod");
        };

        return $return_html_code;

    }

    function outAPICustomSpace()
    {
        global $application;

        $short_api_name=_ml_strtolower(str_replace("Shipping_Module_","",$this->current_api_name));
        $custom_file_name=$short_api_name."-custom-space.html";
        $tmp_tpl = $application->getBlockTemplate($this->BlockTemplateName);
        $full_path_to_file=getTemplateFileAbsolutePath($tmp_tpl["template"]["directory"]."/".$custom_file_name);

        if(file_exists($full_path_to_file))
            return file_get_contents($full_path_to_file);
        else
            return "";
    }

    function getTag($tag)
    {
        global $application;
        $obj = &$application->getInstance('MessageResources',"messages");
        $value = null;
        switch ($tag)
        {

            case 'Local_ShippingMethods':
                $value = $this->outShippingAPIsMethods();
                $HiddenField = "<input type=\"hidden\" name=\"SubmitedCheckoutStoreBlocksList[shipping-method-list-input]\" />";
                $value .=  $HiddenField;
                break;

            case 'Local_CustomAPIspace':
                $value = $this->outAPICustomSpace();
                break;

            case 'Local_APIShippingMethods':
                $value = $this->outShippingMethodsFromOneAPI();
                break;

            case 'StoreOwnerEmail':
                $value = modApiFunc("Configuration","getTagValue","StoreOwnerEmail");
                break;

            case 'Local_ShippingMethodName':
                $value = $this->shipping_methods[$this->current_api_name]["methods"][$this->current_sm_key]['method_name'];
                break;

            case 'Local_FormMethodIdFieldName':
                $value = "shippingModuleAndMethod[method_code]";
                break;

            case 'Local_ShippingMethodId':
                $ShippingMethodId=$this->shipping_methods[$this->current_api_name]['methods'][$this->current_sm_key]['id'];
                $value = $this->current_api_id .  "_" . $ShippingMethodId;
                break;

            case 'Local_ShippingMethodDays':
                $_days=$this->shipping_methods[$this->current_api_name]["methods"][$this->current_sm_key]['days'];
                if(is_numeric($_days) and ($_days > 0))
                    $value = $_days . " " . $obj->getMessage('LABEL_DAYS');
                elseif(is_string($_days) and $_days!="")
                    $value = $_days;
                break;

            case 'Local_ShippingMethodCost':
                $_cost=$this->shipping_methods[$this->current_api_name]["methods"][$this->current_sm_key]['shipping_cost']['ShippingMethodCost'];
                $value = modApiFunc("Localization", "currency_format",$_cost);
                break;

            case 'Local_TotalShippingCharge':
                $_cost=$this->shipping_methods[$this->current_api_name]["methods"][$this->current_sm_key]['shipping_cost']['TotalShippingCharge'];
                $value = modApiFunc("Localization", "currency_format",$_cost);
                break;

            case 'Local_TotalShippingAndHandlingCost':
                $_cost=$this->shipping_methods[$this->current_api_name]["methods"][$this->current_sm_key]['shipping_cost']['TotalShippingAndHandlingCost'];
                $value = modApiFunc("Localization", "currency_format",$_cost);
                break;

            case 'Local_ShippingMethodIsChecked':
                $ShippingMethodId = $this->shipping_methods[$this->current_api_name]["methods"][$this->current_sm_key]['id'];
                // check if estimated shipping cost address is present
                if ($this->CustomerChoice === false)
                {
                    if ($this->current_api_id."_".$ShippingMethodId == $this->current_selected_method)
                        $value = 'checked="checked"';
                    else
                        $value = "";
                }
                else
                {
                    if ($this->current_api_id."_".$ShippingMethodId == $this->CustomerChoice['module'] . '_' . $this->CustomerChoice['method'])
                        $value = 'checked="checked"';
                    else
                        $value = "";
                }
                break;

            default:
                break;
        }
        return $value;
    }

    var $BlockTemplateName = "CheckoutShippingMethodsSelect";

    var $shipping_methods=array();
    var $current_api_id=-1;
    var $current_api_name="";
    var $current_sm_key=-1;
    var $current_selected_method="";

    var $CustomerChoice;
};
?>