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

class CustomerRegistrationForm
{
    function getTemplateFormat()
    {
        $format = array(
            'layout-file'        => 'customer-account-reg-form.ini'
           ,'files' => array(
                'Container' => TEMPLATE_FILE_SIMPLE
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

    function CustomerRegistrationForm()
    {
        global $application;

        #check if fatal errors of the block tag exist
        $this->NoView = false;
        if ($application->issetBlockTagFatalErrors("CustomerRegistrationForm"))
        {
            $this->NoView = true;
        };

        $settings = modApiFunc('Customer_Account','getSettings');
        if($settings['CHECKOUT_TYPE'] == CHECKOUT_TYPE_QUICK)
        {
            $this->NoView = true;
        };

        loadCoreFile('html_form.php');

        $this->customer_info = null;

        if(modApiFunc('Session','is_set','customer_info'))
        {
            $this->customer_info = modApiFunc('Session','get','customer_info');
            modApiFunc('Session','un_set','customer_info');
        };

        $this->invalid_fields = array();

        if(modApiFunc('Session','is_set','invalid_fields'))
        {
            $this->invalid_fields = modApiFunc('Session','get','invalid_fields');
            modApiFunc('Session','un_set','invalid_fields');
        };
    }

    function out_GroupAttributes()
    {
        $attrs = modApiFunc('Customer_Account','getPersonInfoGroupAttrs',$this->current_group_info['group_id']);

        $html_code = '';

        for($i=0; $i<count($attrs); $i++)
        {
            $this->current_attr_info = $attrs[$i];
            if($this->current_attr_info['is_visible'] != 'Y')
                continue;
            $this->current_attr_name = $this->current_attr_info['attr_name'];

            $tpl_name = 'Attribute';

            if($this->current_attr_info['is_required'] == 'Y')
                $tpl_name .= 'Required';

            if(in_array($this->current_attr_name, $this->invalid_fields))
                $tpl_name .= 'Invalid';

            $html_code .= $this->templateFiller->fill($tpl_name);
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
        $country = null;
        if(isset($this->customer_info) && isset($this->customer_info[$this->current_group_name]['Country']))
        {
            $country = $this->customer_info[$this->current_group_name]['Country'];
        };

        if($country == null or $country == '')
        {
            $country = modApiFunc('Location','getDefaultCountryId');
        };

        $states = modApiFunc('Location','getStates',$country);

        if($selected_value == null or $selected_value == '')
        {
            $selected_value = modApiFunc('Location','getDefaultStateId',$country);
        };

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

        $this->current_group_name = 'Customer';
        $this->current_group_info = modApiFunc('Customer_Account','getPersonInfoGroupInfoByName',$this->current_group_name);

        $_template_tags = array(
            'Local_FormActionURL'
           ,'RegisterInfoGroup'
           ,'Local_Form'
           ,'Local_FieldName'
           ,'Local_Field'
          );

        $application->registerAttributes($_template_tags);
        $this->templateFiller = new TemplateFiller();
        $this->template = $application->getBlockTemplate('CustomerRegistrationForm');
        $this->templateFiller->setTemplate($this->template);

        return $this->templateFiller->fill('Container');
    }

    function getTag($tag)
    {
        $value = null;

        switch($tag)
        {
            case 'Local_FormActionURL':
                $request = new Request();
                $request->setAction('register_customer');
                $request->setView('Registration');
                $value = $request->getURL();
                break;
            case 'Local_Form':
                $value = $this->out_GroupAttributes();
                break;
            case 'Local_FieldName':
                $value = $this->current_attr_info['visible_name'];
                break;
            case 'Local_Field':
                $attr_value = '';
                if($this->customer_info != null and isset($this->customer_info[$this->current_group_name][$this->current_attr_name]))
                {
                    $attr_value = $this->customer_info[$this->current_group_name][$this->current_attr_name];
                };
                switch(_ml_strtolower($this->current_attr_name))
                {
                    case 'country':
                        $value = $this->out_CountrySelect($attr_value);
                        break;
                    case 'state':
                        $value = $this->out_StatesSelect($attr_value);
                        break;
                    default:
                        $value = '<input class="form-control" type="'.(preg_match("/password/i",$this->current_attr_name) ? 'password' : 'text').'" '.HtmlForm::genInputTextField(
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

    var $current_group_info;
    var $current_group_name;
    var $current_attr_info;
    var $current_attr_name;
    var $customer_info;
    var $invalid_fields;
};

?>