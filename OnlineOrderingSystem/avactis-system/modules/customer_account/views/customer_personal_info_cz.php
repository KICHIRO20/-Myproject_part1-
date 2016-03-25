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

class CustomerPersonalInfo
{
    function getTemplateFormat()
    {
        $format = array(
            'layout-file'        => 'customer-account-personal-info.ini'
           ,'files' => array(
                'Container' => TEMPLATE_FILE_SIMPLE
               ,'AccessDenied' => TEMPLATE_FILE_SIMPLE
               ,'GroupContainer' => TEMPLATE_FILE_SIMPLE
               ,'Attribute' => TEMPLATE_FILE_SIMPLE
               ,'AttributeRequired' => TEMPLATE_FILE_SIMPLE
               ,'AttributeInvalid' => TEMPLATE_FILE_SIMPLE
               ,'AttributeRequiredInvalid' => TEMPLATE_FILE_SIMPLE
            )
           ,'options' => array(
            )
        );
        return $format;
    }

    function CustomerPersonalInfo()
    {
        global $application;

        #check if fatal errors of the block tag exist
        $this->NoView = false;
        if ($application->issetBlockTagFatalErrors("CustomerPersonalInfo"))
        {
            $this->NoView = true;
        }

        $settings = modApiFunc('Customer_Account','getSettings');
        if($settings['CHECKOUT_TYPE'] == CHECKOUT_TYPE_QUICK)
        {
            $this->NoView = true;
        };

        $this->customer_obj = null;

        $email = modApiFunc('Customer_Account','getCurrentSignedCustomer');

        if($email != null)
        {
            $this->customer_obj = &$application->getInstance('CCustomerInfo',$email);
            $this->customer_obj->setPersonInfoAttrsType(PERSON_INFO_GROUP_ATTR_VISIBLE);
        };

        loadCoreFile('html_form.php');

        $this->invalid_fields = array();
        if(modApiFunc('Session','is_set','InvalidFields'))
        {
            $this->invalid_fields = modApiFunc('Session','get','InvalidFields');
            modApiFunc('Session','un_set','InvalidFields');
        };
    }

    function out_PersonalInfoGroups()
    {
        $groups = $this->customer_obj->getPersonInfoGroupsNames();

        $html_code = '';

        for($i=0; $i<count($groups); $i++)
        {
            $this->current_group_name = $groups[$i];
            if(!modApiFunc('Customer_Account','isPersionInfoGroupActive',$this->current_group_name))
                continue;

            $html_code .= $this->templateFiller->fill('GroupContainer');
        };

        return $html_code;
    }

    function out_GroupAttributes()
    {
        $attrs = $this->customer_obj->getPersonInfoGroupAttrsNames($this->current_group_name);

        $html_code = '';

        for($i=0; $i<count($attrs); $i++)
        {
            $this->current_attr_name = $attrs[$i];

            //
            if(preg_match('/password/i',$this->current_attr_name))
                continue;

            $attr_tpl = 'Attribute';

            if($this->current_group_name == 'Customer')
            {
                $attr_info = $this->customer_obj->getPersonInfoAttrInfoByName($this->current_attr_name, $this->current_group_name);
                if(/*!preg_match('/password/i',$this->current_attr_name) and */$attr_info['is_required'] == 'Y')
                    $attr_tpl .= 'Required';
            };

            if(array_key_exists($this->current_group_name, $this->invalid_fields)
                and in_array($this->current_attr_name, $this->invalid_fields[$this->current_group_name]))
            {
                $attr_tpl .= 'Invalid';
            };

            $html_code .= $this->templateFiller->fill($attr_tpl);
        };

        return $html_code;
    }

    function out_CountrySelect($selected_value)
    {
        if($selected_value == null or $selected_value == '')
        {
            $selected_value = modApiFunc('Location','getDefaultCountryId');
        };

        $countries = modApiFunc('Location','getCountries');

        $countries_select = array(
            "select_name" => 'customer_info['.$this->current_group_name.']['.$this->current_attr_name.']'
           ,"selected_value" => $selected_value
           ,"id" => 'customer_info_'.$this->current_group_name.'_'.$this->current_attr_name
           ,"onChange" => "try { refreshStatesList('".'customer_info_'.$this->current_group_name.'_'.$this->current_attr_name.
                          "', '".'customer_info_'.$this->current_group_name.'_State'.
                          "', '".'customer_info_'.$this->current_group_name.'_State_div'."'); } catch(ex) {};"
           ,"values" => array()
        );

        foreach($countries as $country_id => $country_name)
        {
            $countries_select["values"][] = array(
                "value" => $country_id
               ,"contents" => $country_name
            );
        };

        return HtmlForm::genDropdownSingleChoice($countries_select);
    }

    function out_StatesSelect($selected_value)
    {
        $country = $this->customer_obj->getPersonInfo('Country', $this->current_group_name);
        if($country == null or $country == '')
        {
            $country = modApiFunc('Location','getDefaultCountryId');
        };

        $states = modApiFunc('Location','getStates',$country);

        if($selected_value == null || $selected_value == '')
        {
            $selected_value = modApiFunc('Location','getDefaultStateId',$country);
        }
        #else if (count($states) != 0)
        #    $selected_value = "";

        $states_select = array(
            "select_name" => 'customer_info['.$this->current_group_name.']['.$this->current_attr_name.']'
           ,"selected_value" => $selected_value
           ,"id" => 'customer_info_'.$this->current_group_name.'_'.$this->current_attr_name
           ,"values" => array()
        );

        foreach($states as $state_id => $state_name)
        {
            $states_select["values"][] = array(
                "value" => $state_id
               ,"contents" => $state_name
            );
        };

        $uid = uniqid('onload_');

        $js_syn = modApiFunc("Checkout", "getJavascriptSynchronizeCountriesAndStatesLists").
                  modApiFunc("Location", "getJavascriptCountriesStatesArrays").
                 "<script type=\"text/javascript\">" . "\n" .
                 "<!--\n" . "\n" .
                 "var ".$uid."_bak = window.onload;" . "\n" .
                 "window.onload = function()" . "\n" .
                 "{" . "\n" .
                 "    if(".$uid."_bak){".$uid."_bak();}" . "\n" .
                 "refreshStatesList('".'customer_info_'.$this->current_group_name.'_Country'.
                        "', '".'customer_info_'.$this->current_group_name.'_'.$this->current_attr_name.
                        "', '".'customer_info_'.$this->current_group_name.'_'.$this->current_attr_name.'_div'."');" .
                 "}" . "\n" .
                 "//-->" . "\n" .
                 "</script>" . "\n";

        $html_select = HtmlForm::genDropdownSingleChoice($states_select);
        $html_input = "<div id=\"".'customer_info_'.$this->current_group_name.'_'.$this->current_attr_name."_div\" style=\"display: none;\">".
                      "<input type=\"text\" id=\"".'customer_info_'.$this->current_group_name.'_'.$this->current_attr_name."_text\" ".
                      HtmlForm::genInputTextField("125",'customer_info['.$this->current_group_name.']['.$this->current_attr_name."_text]","32",(($selected_value) ? prepareHTMLDisplay($selected_value) : '')).
                      " /></div>";

        return $js_syn.$html_select.$html_input;
    }

    function output()
    {
        if($this->NoView)
        {
            return '';
        };

        global $application;

        $_template_tags = array(
            'Local_FormActionURL'
           ,'Local_PersonalInfo'
           ,'Local_FormName'
           ,'Local_Form'
           ,'Local_FieldName'
           ,'Local_Field'
          );

        $application->registerAttributes($_template_tags);
        $this->templateFiller = new TemplateFiller();
        $this->template = $application->getBlockTemplate('CustomerPersonalInfo');
        $this->templateFiller->setTemplate($this->template);

        if($this->customer_obj !== null)
        {
            return $this->templateFiller->fill('Container');
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
            case 'Local_FormActionURL':
                $r = new Request();
                $r->setAction('save_personal_info');
                $r->setView('CustomerPersonalInfo');
                $value = $r->getURL();
                break;
            case 'Local_PersonalInfo':
                $value = $this->out_PersonalInfoGroups();
                break;
            case 'Local_FormName':
                $group_info = $this->customer_obj->getPersonInfoGroupInfoByName($this->current_group_name);
                $value = getMsg('CA',$group_info['lang_code']);
                break;
            case 'Local_Form':
                $value = $this->out_GroupAttributes();
                break;
            case 'Local_FieldName':
                $attr_info = $this->customer_obj->getPersonInfoAttrInfoByName($this->current_attr_name, $this->current_group_name);
                $value = prepareHTMLDisplay($attr_info['visible_name']);
                break;
            case 'Local_Field':
                $attr_value = $this->customer_obj->getPersonInfo($this->current_attr_name, $this->current_group_name);
                switch(_ml_strtolower($this->current_attr_name))
                {
                    case 'country':
                        $value = $this->out_CountrySelect($attr_value);
                        break;
                    case 'state':
                        $value = $this->out_StatesSelect($attr_value);
                        break;
//                    case 'accountname':
//                    case 'email':
//                        if($this->current_group_name == 'Customer')
//                        {
//                            if($this->customer_obj->getPersonInfo('account') == $attr_value)
//                            {
//                                $value = prepareHTMLDisplay($attr_value);
//                                break;
//                            };
//                        };
                    default:
                        $input_type = 'text';
                        if(preg_match("/password$/i",$this->current_attr_name))
                        {
                            $input_type="password";
                        };
                        $value = '<input type="'.$input_type.'" '.HtmlForm::genInputTextField(
                                  255
                                 ,'customer_info['.$this->current_group_name.']['.$this->current_attr_name.']'
                                 ,32
                                 ,prepareHTMLDisplay($attr_value)).' />';
                        break;
                }
                break;
        };

        return $value;
    }

    var $customer_obj;
    var $current_group_name;
    var $current_attr_name;
    var $invalid_fields;
};

?>