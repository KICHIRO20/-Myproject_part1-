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
 * @package ProductOptions
 * @author Egor V. Derevyankin
 *
 */

class OptionsChoice
{
    function getTemplateFormat()
    {
        $format = array(
            'layout-file'        => "product-options-options-choice-config.ini"

           ,'files' => array(
                'OptionsChoice' => TEMPLATE_FILE_SIMPLE
               ,'JScheckOptions' => TEMPLATE_FILE_SIMPLE
               ,'SingleSelectDropDown' => TEMPLATE_FILE_SIMPLE
               ,'SingleSelectRadioGroup' => TEMPLATE_FILE_SIMPLE
               ,'MultipleSelectMultipleSelect' => TEMPLATE_FILE_SIMPLE
               ,'MultipleSelectCheckboxGroup' => TEMPLATE_FILE_SIMPLE
               ,'CustomInputSimpleInput' => TEMPLATE_FILE_SIMPLE
               ,'CustomInputTextArea' => TEMPLATE_FILE_SIMPLE
               ,'CustomInputCheckBoxSimpleInput' => TEMPLATE_FILE_SIMPLE
               ,'CustomInputCheckBoxTextArea' => TEMPLATE_FILE_SIMPLE
               ,'UploadFileDefaultFileInput' => TEMPLATE_FILE_SIMPLE
               ,'DropDownItem' => TEMPLATE_FILE_SIMPLE
               ,'MultipleSelectItem' => TEMPLATE_FILE_SIMPLE
               ,'RadioGroupItem' => TEMPLATE_FILE_SIMPLE
               ,'CheckboxGroupItem' => TEMPLATE_FILE_SIMPLE
            )
           ,'options' => array(
            )
        );
        return $format;
    }

    function OptionsChoice()
    {
        global $application;
        $this->Messages = &$application->getInstance('MessageResources',"product-options-messages", "AdminZone");

        $this->NoView = false;
        if ($application->issetBlockTagFatalErrors($this->BlockTemplateName))
        {
            $this->NoView = true;
        };

        $this->sent_combination = null;
        $this->sent_vids = null;
        $this -> sent_product_id = null;

        if (modApiFunc('Request', 'getValueByKey', 'presetCombination') != '')
        {
            list($type, $customer_id, $wl_id) = explode(
                '_',
                modApiFunc('Request', 'getValueByKey', 'presetCombination')
            );
            if ($type == 'wl')
            {
                $wl_info = modApiFunc('Wishlist', 'getWishlistRecordCartData',
                                      $wl_id, $customer_id);
                if ($wl_info
                    && @$wl_info['parent_entity'] == 'product')
                {
                    $this -> sent_product_id = $wl_info['entity_id'];
                    $this -> sent_combination = @$wl_info['options'];
                }
            }
        }
        elseif(modApiFunc('Session','is_set','sentCombination'))
        {
            $this->sent_combination = modApiFunc('Session','get','sentCombination');
            modApiFunc('Session','un_set','sentCombination');
            if(!is_array($this->sent_combination) or empty($this->sent_combination))
                $this->sent_combination = null;
        };

        if(!is_array($this->sent_combination) or empty($this->sent_combination))
            $this->sent_combination = null;
        if($this->sent_combination != null)
        {
            $vids = array();
            foreach($this->sent_combination as $oid => $vdata)
            {
                if(is_numeric($vdata))
                    $vids[]=$vdata;
                elseif(is_array($vdata) and !empty($vdata) and !isset($vdata['val']))
                    $vids = array_merge($vids,array_keys($vdata));
            };
            $this->sent_vids = $vids;
        };
    }

    function outputJScheckOptions()
    {
        return $this->templateFiller->fill("JScheckOptions");
    }

    function output_JSexsArrays()
    {
        $return_js_code = "var POexceptions = new Array();\n"."var POexistents = new Array();\n";
        $exs = modApiFunc("Product_Options","getExsForEntity",'product',$this->product_id);
        $messages = array();
        $sets = modApiFunc("Product_Options","getOptionsSettingsForEntity",'product',$this->product_id);
        $ex_message = $sets['EX_MSG'];
        for($i=0;$i<count($exs);$i++)
        {
            $return_js_code.="PO".$exs[$i]['ex_type']."s[$i] = new Array(";

            $cmb = Product_Options::_unserialize_combination($exs[$i]['combination']);
            $elements=array();
            foreach($cmb as $oid => $vdata)
            {
                if(is_numeric($vdata))
                    $elements[]="'".$oid."_".$vdata."'";
                elseif(is_array($vdata) and !empty($vdata))
                {
                    foreach($vdata as $vid => $val)
                        $elements[]="'".$oid."_".$vid."'";
                };
            };
            $return_js_code.=implode(",",$elements).");\n";
            if($exs[$i]['ex_type']=='exception')
                $messages[]=$exs[$i]['exception_message'];
        }

        $return_js_code.="var ExceptionsMessages = new Array('".implode("','",array_map("addslashes",$messages))."');\n";
        $return_js_code.="var NotExistentMessage = '".addslashes($ex_message)."';\n";

        return $return_js_code;
    }

    function output_jsCheckCustomInputs()
    {
        $return_js_code = "";

        for($i=0;$i<count($this->Options);$i++)
        {
            if($this->Options[$i]['option_type']=='CI')
            {
                $js_condition = "product_form.elements['po[".$this->Options[$i]['option_id']."][val]'].value==''";
                if(preg_match("/^CB/",$this->Options[$i]['show_type']))
                {
                    $js_condition.=" && product_form.elements['po[".$this->Options[$i]['option_id']."][cb]'].checked";
                };
                $return_js_code.="
                if($js_condition)
                {
                    alert('".addslashes(str_replace("%FIELD%",$this->Options[$i]['option_name'],$this->Messages->getMessage('A_CZ_PLZ_FILL_FIELD')))."');
                    product_form.elements['po[".$this->Options[$i]['option_id']."][val]'].focus();
                    return false;
                }";
                if(preg_match("/^CB/",$this->Options[$i]['show_type']))
                {
                    $return_js_code.="
                if(!product_form.elements['po[".$this->Options[$i]['option_id']."][cb]'].checked)
                {
                    product_form.elements['po[".$this->Options[$i]['option_id']."][val]'].value='';
                }
                    ";
                }
            };
        }
        return $return_js_code;
    }

    function outputModifiersLine()
    {
        $return_html_code="";

        if(isset($this->current_value_data['price_modifier']) and $this->current_value_data['price_modifier']!=0)
        {
            if($this->current_value_data['price_modifier']>0)
                $return_html_code.="+";
            $return_html_code.=modApiFunc("Localization","currency_format",$this->current_value_data['price_modifier']);
        };

        if($return_html_code!="")
            $return_html_code="(".$return_html_code.")";

        return $return_html_code;
    }

    function outputItems()
    {
        $return_html_code="";

        $odata=$this->Options[$this->current_option_key];
        $values=$odata['values'];
        $fileName=$this->STYPES[$odata['show_type']].'Item';

        for($i=0;$i<count($values);$i++)
        {
            $this->current_value_data=$values[$i];
            $return_html_code.=$this->templateFiller->fill($fileName);
        }

        // show discard value
        if($odata['option_type']=='SS' and $odata['discard_avail']=='Y')
        {
            $this->current_value_data=array(
                          'value_id' => '0'
                         ,'value_name' => $odata['discard_value']
                         ,'is_default' => 'N'
                        );
            $return_html_code.=$this->templateFiller->fill($fileName);
        }

        $this->current_value_data=array();
        return $return_html_code;
    }

    function outputOneOption($option_data)
    {
        if(count($option_data['values'])>0)
        {
            if($option_data['option_type']=='CI')
                $this->current_value_data=$option_data['values'][0];

            $fileName = $this->OTYPES[$option_data['option_type']].$this->STYPES[$option_data['show_type']];
            return $this->templateFiller->fill($fileName);
        }
        else
            return "";
    }

    function outputOptionsList()
    {
        $return_html_code="";

        foreach($this->Options as $ok => $option_data)
        {
            $this->current_option_key=$ok;
            $return_html_code.=$this->outputOneOption($option_data);
        };

        return $return_html_code;
    }

    function output()
    {
        global $application;
        $return_html_code="";

        $this->product_id=func_get_arg(0);
        if ($this -> sent_product_id
            && $this->product_id != $this -> sent_product_id)
        {
            $this->sent_combination = null;
            $this->sent_vids = null;
            $this -> sent_product_id = null;
        }
        $this->Options=modApiFunc("Product_Options","getOptionsWithValues",'product',$this->product_id);
        $this->mods_map=modApiFunc("Product_Options","getModsMap");
        $this->flip_mods_map=array_flip($this->mods_map);

        if(!empty($this->Options))
        {
            $application->registerAttributes(
                                            array(
                                                "Local_jsCheckOptions" => ''
                                               ,"Local_jsExsArrays" => ''
                                               ,"Local_jsCheckCustomInputs" => ''
                                               ,"Local_ProductOptionsForm" => ''
                                               ,"Local_OptionName" => ''
                                               ,"Local_OptionDisplayText" => ''
                                               ,"Local_OptionDescription" => ''
                                               ,"Local_OptionID" => ''
                                               ,"Local_Items" => ''
                                               ,"Local_OptionValueID" => ''
                                               ,"Local_OptionValueName" => ''
                                               ,"Local_OptionValueIsChecked" => ''
                                               ,"Local_OptionValueSalePriceModifier" => ''
                                               ,"Local_OptionValueWeightModifier" => ''
                                               ,"Local_OptionValuePerItemShippingCostModifier" => ''
                                               ,"Local_OptionValuePerItemHandlingCostModifier" => ''
                                               ,"Local_OptionCheckBoxText" => ''
                                               ,'Local_ProductOptionsChoice' => ''

                                            )
                                        );

            $this->templateFiller = new TemplateFiller();
            $this->template = $application->getBlockTemplate($this->BlockTemplateName);
            $this->templateFiller->setTemplate($this->template);

            $return_html_code = $this->templateFiller->fill("OptionsChoice");
        }

        return $return_html_code.'<input type="hidden" name="options_sent" value="yes" />';
    }

    function getTag($tag)
    {
        $value = null;

        switch($tag)
        {
            case "Local_jsCheckOptions":
                $value = $this->outputJScheckOptions();
                break;

            case "Local_jsExsArrays":
                $value = $this->output_JSexsArrays();
                break;

            case "Local_jsCheckCustomInputs":
                $value = $this->output_jsCheckCustomInputs();
                break;

            case "Local_ProductOptionsForm":
                $value = $this->outputOptionsList();
                break;

            case "Local_OptionName":
                $value = $this->Options[$this->current_option_key]['option_name'];
                break;

            case "Local_OptionDisplayText":
                $value = $this->Options[$this->current_option_key]['display_name'];
                break;

            case "Local_OptionDescription":
                $value = $this->Options[$this->current_option_key]['display_descr'];
                break;

            case "Local_OptionID":
                $value = $this->Options[$this->current_option_key]['option_id'];
                break;

            case "Local_OptionCheckBoxText":
                $value = $this->Options[$this->current_option_key]['checkbox_text'];
                break;

            case "Local_Items":
                $value = $this->outputItems();
                break;

            case "Local_OptionValueID":
                $value = $this->current_value_data['value_id'];
                break;

            case "Local_OptionValueName":
                if($this->Options[$this->current_option_key]['option_type']!='CI')
                    $value = $this->current_value_data['value_name'];
                else
                    if($this->sent_combination != null)
                        $value = $this->sent_combination[$this->Options[$this->current_option_key]['option_id']]['val'];
                    else
                        $value = ($this->Options[$this->current_option_key]['values'][0]['is_default']=='Y')?$this->Options[$this->current_option_key]['values'][0]['value_name']:'';
                break;

            case "Local_OptionValueIsChecked":
                $checked_tag = (in_array($this->Options[$this->current_option_key]['show_type'],array('DD','MS')))?'selected="selected"':'checked="checked"';
                if($this->sent_vids != null)
                    $value = in_array($this->current_value_data['value_id'],$this->sent_vids) ? $checked_tag : '';
                else
                    $value = ($this->current_value_data['is_default']=='Y'?$checked_tag:'');
                break;

            case "Local_OptionValueSalePriceModifier":
                $value = "";
                if(isset($this->current_value_data[$this->flip_mods_map['SalePrice'].'_modifier']) and $this->current_value_data[$this->flip_mods_map['SalePrice'].'_modifier']!=0)
                {
                    if($this->current_value_data[$this->flip_mods_map['SalePrice'].'_modifier']>0)
                        $value.="+";
                    $value.=modApiFunc("Localization","currency_format",$this->current_value_data[$this->flip_mods_map['SalePrice'].'_modifier']);
                };
                break;

            case "Local_OptionValueWeightModifier":
                $value = "";
                if(isset($this->current_value_data[$this->flip_mods_map['Weight'].'_modifier']) and $this->current_value_data[$this->flip_mods_map['Weight'].'_modifier']!=0)
                {
                    if($this->current_value_data[$this->flip_mods_map['Weight'].'_modifier']>0)
                        $value.="+";
                    $value.=modApiFunc("Localization","format",$this->current_value_data[$this->flip_mods_map['Weight'].'_modifier'],"weight").modApiFunc("Localization","getUnitTypeValue","weight");
                };
                break;

            case "Local_OptionValuePerItemShippingCostModifier":
                $value = "";
                if(isset($this->current_value_data[$this->flip_mods_map['PerItemShippingCost'].'_modifier']) and $this->current_value_data[$this->flip_mods_map['PerItemShippingCost'].'_modifier']!=0)
                {
                    if($this->current_value_data[$this->flip_mods_map['PerItemShippingCost'].'_modifier']>0)
                        $value.="+";
                    $value.=modApiFunc("Localization","currency_format",$this->current_value_data[$this->flip_mods_map['PerItemShippingCost'].'_modifier']);
                };
                break;

            case "Local_OptionValuePerItemHandlingCostModifier":
                $value = "";
                if(isset($this->current_value_data[$this->flip_mods_map['PerItemHandlingCost'].'_modifier']) and $this->current_value_data[$this->flip_mods_map['PerItemHandlingCost'].'_modifier']!=0)
                {
                    if($this->current_value_data[$this->flip_mods_map['PerItemHandlingCost'].'_modifier']>0)
                        $value.="+";
                    $value.=modApiFunc("Localization","currency_format",$this->current_value_data[$this->flip_mods_map['PerItemHandlingCost'].'_modifier']);
                };
                break;

            case "ProductID":
                $value = $this->product_id;
                break;

            case 'Local_ProductOptionsChoice':
                $sets = modApiFunc('Product_Options', 'getOptionsSettingsForEntity', 'product', $this->product_id);
                $combinations_formula = strtr($sets['CR_FORMULA'], array(';' => '', 'and' => '&&', 'or' => '||'));

                $inventory = array();
                $inventory = modApiFunc('Product_Options', 'getEntityInventory', 'product', $this->product_id);
                foreach (array_keys($inventory) as $i) {
                    $inventory[$i]['formula'] = strtr($inventory[$i]['formula'], array(';' => '', 'and' => '&&', 'or' => '||'));
                }

                $json = new Services_JSON();
                $value = $json->encode($this->getModifiers()) . ', ' . $json->encode($inventory) . ', ' . $json->encode($combinations_formula);
                break;

        };

        return $value;
    }

    function getOptionValueModifiers($value = null)
    {
        return array(
            'price' => $value ? (float) modApiFunc('Currency_Converter', 'convert', $value['price_modifier'], $this->main_currency, $this->current_currency) : 0,
            'weight' => $value ? (float) $value['weight_modifier'] : 0,
        );
    }

    function getModifiers()
    {
        if (! $this->Options) {
            return array();
        }
        $this->main_currency = modApiFunc('Localization', 'getCurrencyCodeById', modApiFunc('Localization', 'getMainStoreCurrency'));
        $this->current_currency = modApiFunc('Localization', 'getCurrencyCodeById', modApiFunc('Localization', 'getSessionDisplayCurrency'));
        $options_modifiers = array();

        foreach ($this->Options as $i => $o) {
            unset($field);
            $modifiers = array();
            switch ($o['show_type']) {
                case 'DD':
                case 'RG':
                    foreach ($o['values'] as $v) {
                        $vid = $v['value_id'];
                        $modifiers[$vid] = $this->getOptionValueModifiers($v);
                    }
                    break;

                case 'MS':
                case 'CBG':
                    foreach ($o['values'] as $v) {
                        $vid = $v['value_id'];
                        $modifiers[$vid] = $this->getOptionValueModifiers($v);
                    }
                    break;

                case 'CBSI':
                case 'CBTA':
                    $modifiers['off'] = $this->getOptionValueModifiers();
                    $modifiers['on'] = $this->getOptionValueModifiers($o['values'][0]);
                    break;

                case 'SI':
                case 'TA':
                    $modifiers['on'] = $this->getOptionValueModifiers($o['values'][0]);
                    break;

                case 'DFI':
                    $modifiers['on'] = $this->getOptionValueModifiers($o['values'][0]);
                    break;
            }
            $options_modifiers[ $o['option_id'] ] = $modifiers;
        }
        return $options_modifiers;
    }

    var $BlockTemplateName = "OptionsChoice";

    var $product_id;
    var $Options;
    var $current_option_key;
    var $current_value_data;
    var $Messages;
    var $mods_map;
    var $flip_mods_map;
    var $sent_combination;
    var $sent_vids;

    var $OTYPES = array(
        'SS' => 'SingleSelect'
       ,'MS' => 'MultipleSelect'
       ,'CI' => 'CustomInput'
       ,'UF' => 'UploadFile'
    );
    var $STYPES = array(
        'DD' => 'DropDown'
       ,'RG' => 'RadioGroup'
       ,'MS' => 'MultipleSelect'
       ,'CBG' => 'CheckboxGroup'
       ,'CBSI' => 'CheckBoxSimpleInput'
       ,'CBTA' => 'CheckBoxTextArea'
       ,'SI' => 'SimpleInput'
       ,'TA' => 'TextArea'
       ,'DFI' => 'DefaultFileInput'
    );

}

?>