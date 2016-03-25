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

class OptionsCombination
{

    function getTemplateFormat()
    {
        $format = array(
            'layout-file'        => 'product-options-options-combination.ini'
           ,'files' => array(
                'OptionsCombinationCartPage' => TEMPLATE_FILE_SIMPLE
               ,'OptionsCombinationOrderPage' => TEMPLATE_FILE_SIMPLE
               ,'CombinationItemCartPage' => TEMPLATE_FILE_SIMPLE
               ,'CombinationItemOrderPage' => TEMPLATE_FILE_SIMPLE
            )
           ,'options' => array(
            )
        );
        return $format;
    }

    function OptionsCombination()
    {
        global $application;

        #check if fatal errors of the block tag exist
        $this->NoView = false;
        if ($application->issetBlockTagFatalErrors("OptionsCombination"))
        {
            $this->NoView = true;
        }
    }

    function getModVal($mod_name)
    {
        if(is_numeric($this->current_combination_data))
        {
            for($i=0;$i<count($this->current_option_data['values']);$i++)
                if($this->current_option_data['values'][$i]['value_id']==$this->current_combination_data)
                    return $this->current_option_data['values'][$i][$this->flip_mods_map[$mod_name].'_modifier'];
            if($this->current_combination_data==0)
            {
                return 0;
            }
        }
        elseif(is_array($this->current_combination_data)
            and !isset($this->current_combination_data['val']))
        {
            $mod_sum=array();
            for($i=0;$i<count($this->current_option_data['values']);$i++)
                if(array_key_exists($this->current_option_data['values'][$i]['value_id'],$this->current_combination_data))
                    $mod_sum[]=$this->current_option_data['values'][$i][$this->flip_mods_map[$mod_name].'_modifier'];
            return array_sum($mod_sum);
        }
        elseif(is_array($this->current_combination_data) and isset($this->current_combination_data['val']))
        {
            return $this->current_option_data['values'][0][$this->flip_mods_map[$mod_name].'_modifier'];
        };
    }

    function outputValue()
    {
        if(is_numeric($this->current_combination_data))
        {
            for($i=0;$i<count($this->current_option_data['values']);$i++)
            {
                if($this->current_option_data['values'][$i]['value_id']==$this->current_combination_data)
                {
                    return $this->current_option_data['values'][$i]['value_name'];
                }
            }
            if($this->current_combination_data==0)
            {
                return $this->current_option_data['discard_value'];
            }
        }
        elseif(is_array($this->current_combination_data) and !isset($this->current_combination_data['val']))
        {
            $output_names=array();
            for($i=0;$i<count($this->current_option_data['values']);$i++)
            {
                if(array_key_exists($this->current_option_data['values'][$i]['value_id'],$this->current_combination_data))
                {
                    $output_names[]=$this->current_option_data['values'][$i]['value_name'];
                }
            }
            return implode(", ",$output_names);
        }
        elseif(is_array($this->current_combination_data) and isset($this->current_combination_data['val']))
        {
            if(isset($this->current_combination_data['is_file']))
                return basename($this->current_combination_data['val']);
            else
                return $this->current_combination_data['val'];
        }
    }

    function outputCombination()
    {
        $return_html_code="";
        if ($this->order_mode_enabled)
        {
            foreach($this->combination as $odata)
            {
                $this->current_combination_data=$odata;
                $return_html_code.=$this->templateFiller->fill("CombinationItem".$this->page_name."Page");
            }
        }
        else
        {
            foreach($this->combination as $oid => $odata)
            {
                $this->current_option_data=modApiFunc("Product_Options","getOptionInfo",$oid,true);
                $this->current_combination_data=$odata;
                if((is_array($odata) and empty($odata)) or (isset($odata['val']) and $odata['val']==''))
                    $return_html_code.="";
                else
                    $return_html_code.=$this->templateFiller->fill("CombinationItem".$this->page_name."Page");
            }
        }
        return $return_html_code;
    }

    function output()
    {
        global $application;
        $args=func_get_args();
        $this->combination=$args[0];

        if(isset($args[1]))
            $this->page_name = $args[1];
        else
            $this->page_name = "Cart";

        // order mode, in this case the $this->combination contains
        // options array from an order.
        if(isset($args[2]) && $args[2] == true)
        {
            $this->order_mode_enabled = true;
        }

        if(empty($this->combination))
            return "";

        $this->mods_map=modApiFunc("Product_Options","getModsMap");
        $this->flip_mods_map=array_flip($this->mods_map);

        $_template_tags = array(
                                "Local_ProductOptionsSelected" => ''
                               ,"Local_OptionName" => ''
                               ,"Local_OptionDisplayText" => ''
                               ,"Local_OptionDescription" => ''
                               ,"Local_OptionValueID" => ''
                               ,"Local_OptionValueName" => ''
                               ,"Local_OptionValueSalePriceModifier" => ''
                               ,"Local_OptionValueWeightModifier" => ''
                               ,"Local_OptionValuePerItemShippingCostModifier" => ''
                               ,"Local_OptionValuePerItemHandlingCostModifier" => ''
                               );
        $application->registerAttributes($_template_tags);
        $this->templateFiller = new TemplateFiller();
        $this->template = $application->getBlockTemplate('OptionsCombination');
        $this->templateFiller->setTemplate($this->template);

        return $this->templateFiller->fill("OptionsCombination".$this->page_name."Page");

    }

    function getTag($tag)
    {
        $value = null;

        if ($this->order_mode_enabled)
        {
            switch($tag)
            {
                case 'Local_ProductOptionsSelected':
                    $value = $this->outputCombination();
                    break;
                case 'Local_OptionDisplayText':
                    $value = $this->current_combination_data['option_name'];
                    break;
                case "Local_OptionDescription":
                    $value = $this->current_combination_data['display_descr'];
                    break;
                case 'Local_OptionValueName':
                    $value = $this->current_combination_data['option_value'];
                    break;
                case 'Local_OptionValueWeightModifier':
                case 'Local_OptionValuePerItemShippingCostModifier':
                case 'Local_OptionValuePerItemHandlingCostModifier':
                case 'Local_OptionValueID':
                case 'Local_OptionName':
                case 'Local_OptionValueSalePriceModifier':
                    $value = '';
                    break;
            }
            return $value;
        }

        switch($tag)
        {
            case 'Local_ProductOptionsSelected':
                $value = $this->outputCombination();
                break;
            case 'Local_OptionName':
                $value = $this->current_option_data['option_name'];
                break;
            case 'Local_OptionDisplayText':
                $value = $this->current_option_data['display_name'];
                break;
            case "Local_OptionDescription":
                $value = $this->current_option_data['display_descr'];
                break;
            case 'Local_OptionValueID':
                $value = is_numeric($this->current_combination_data)?$this->current_combination_data:0;
                break;
            case 'Local_OptionValueName':
                $value = $this->outputValue();
                break;
            case 'Local_OptionValueSalePriceModifier':
                $mod_val = $this->getModVal('SalePrice');
                $value = "";
                if ($mod_val > 0)
                {
                    $value = "+".modApiFunc("Localization","currency_format",$mod_val);
                }
                elseif ($mod_val < 0)
                {
                    $value = modApiFunc("Localization","currency_format",$mod_val);
                }
                break;
            case 'Local_OptionValueWeightModifier':
                $mod_val = $this->getModVal('Weight');
                $value = "";
                if ($mod_val > 0)
                {
                    $value = "+".modApiFunc("Localization","format",$mod_val,"weight").modApiFunc("Localization","getUnitTypeValue","weight");
                }
                elseif ($mod_val < 0)
                {
                    $value = modApiFunc("Localization","format",$mod_val,"weight").modApiFunc("Localization","getUnitTypeValue","weight");
                }
                break;
            case 'Local_OptionValuePerItemShippingCostModifier':
                $mod_val = $this->getModVal('PerItemShippingCost');
                if ($mod_val > 0)
                {
                    $value = "+".modApiFunc("Localization","currency_format",$mod_val);
                }
                elseif ($mod_val < 0)
                {
                    $value = modApiFunc("Localization","currency_format",$mod_val);
                }
                break;
            case 'Local_OptionValuePerItemHandlingCostModifier':
                $mod_val = $this->getModVal('PerItemHandlingCost');
                if ($mod_val > 0)
                {
                    $value = "+".modApiFunc("Localization","currency_format",$mod_val);
                }
                elseif ($mod_val < 0)
                {
                    $value = modApiFunc("Localization","currency_format",$mod_val);
                }
                break;
        }
        return $value;
    }

    var $combination;
    var $page_name;
    var $current_option_data;
    var $current_combination_data;
    var $mods_map;
    var $flip_mods_map;
    var $order_mode_enabled = false;

};

?>