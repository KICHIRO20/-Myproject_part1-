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

class PO_EditOption
{
    function PO_EditOption()
    {
        global $application;
        $this->MessageResources = &$application->getInstance('MessageResources',"product-options-messages", "AdminZone");
        $this->Hints = &$application->getInstance('Hint');
        $request = &$application->getInstance('Request');
        $this->option_id=$request->getValueByKey('option_id');
        $this->option_info=modApiFunc("Product_Options","getOptionInfo",$this->option_id,true);
        $this->_option_types = modApiFunc("Product_Options","_getOptionsTypes",true);

        if(modApiFunc("Session","is_set","SessionPost"))
            $this->copyData();
        else
            $this->initData();

        loadCoreFile('html_form.php');
    }

    function outputJSforIE6()
    {
        $return_js_code="";

        if(strstr($_SERVER["HTTP_USER_AGENT"],"MSIE 6"))
        {
            global $application;
            $this->mTmplFiller = &$application->getInstance('TmplFiller');
            $return_js_code = $this->mTmplFiller->fill("product_options/", "edit-option-js-for-ie6.tpl.html",array());
        };

        return $return_js_code;
    }

    function copyData()
    {
        $this->DATA=modApiFunc("Session","get","SessionPost");
        modApiFunc("Session","un_set","SessionPost");
    }

    function initData()
    {
        $this->DATA=array(
            "Option" => array(
                "OptionName" => $this->option_info["option_name"]
               ,"DisplayName" => $this->option_info["display_name"]
               ,"DisplayDescr" => $this->option_info["display_descr"]
               ,"OptionType" => $this->option_info["option_type"]
               ,"ShowType" => array()
               ,"DiscardAvail" => ($this->option_info["discard_avail"]=="Y"?"YES":"NO")
               ,"DiscardValue" => $this->option_info["discard_value"]
               ,"CheckBoxText" => $this->option_info["checkbox_text"]
               ,"UseForIT" => ($this->option_info["use_for_it"]=="Y"?"YES":"NO")
            )
        );

        foreach($this->_option_types as $otype => $show_types)
            if($this->option_info['option_type']==$otype)
                $this->DATA["Option"]["ShowType"][$otype]=$this->option_info['show_type'];
            else
                $this->DATA["Option"]["ShowType"][$otype]=array_shift(array_values($show_types));
    }

    // : think over the table with X (like X-Cart), closed by JS, instead of reload
    function outputResultMessage()
    {
        global $application;
        if(modApiFunc("Session","is_set","ResultMessage"))
        {
            $msg=modApiFunc("Session","get","ResultMessage");
            modApiFunc("Session","un_set","ResultMessage");
            $template_contents=array(
                "ResultMessage" => $this->_var2msg($msg)
            );
            $this->_Template_Contents=$template_contents;
            $application->registerAttributes($this->_Template_Contents);
            $this->mTmplFiller = &$application->getInstance('TmplFiller');
            return $this->mTmplFiller->fill("product_options/", "result-message.tpl.html",array());
        }
        elseif(modApiFunc("Session","is_set","Errors"))
        {
            $return_html_code="";
            $errors=modApiFunc("Session","get","Errors");
            modApiFunc("Session","un_set","Errors");
            foreach($errors as $ekey => $eval)
            {
                $template_contents=array(
                    "ErrorMessage" => $this->_var2msg($eval)
                );
                $this->_Template_Contents=$template_contents;
                $application->registerAttributes($this->_Template_Contents);
                $this->mTmplFiller = &$application->getInstance('TmplFiller');
                $return_html_code.=$this->mTmplFiller->fill("product_options/", "error-message.tpl.html",array());
            };
            return $return_html_code;
        }
        else
            return "";
    }

    // : write a generator of the JS code
    function output__JS_OnOptionTypeChanged($otypes,$blocks_prefix)
    {
        $return_js_code="
            function onOptionTypeChanged()
            {
                var new_option_type = document.getElementById('Option_OptionType').value;\n";
        foreach(array_keys($otypes) as $ot)
        {
            $return_js_code.="document.getElementById('{$blocks_prefix}{$ot}').style.display=\"none\";\n";
            if($ot == 'UF') continue;
            if($ot!="MS")
                $return_js_code.="document.getElementById('additional_fields_for_".$ot."_01').style.display=\"none\";\n";
            if($ot=="SS")
                $return_js_code.="document.getElementById('additional_fields_for_".$ot."_02').style.display=\"none\";\n";
            $return_js_code.="document.getElementById('additional_fields_for_SSMS_01').style.display=\"none\";\n";
        };
        $return_js_code.="document.getElementById('$blocks_prefix'+new_option_type).style.display=\"\";
                if(new_option_type=='SS' || new_option_type=='CI')
                    document.getElementById('additional_fields_for_'+new_option_type+'_01').style.display=\"\";
                if(new_option_type=='SS')
                    document.getElementById('additional_fields_for_'+new_option_type+'_02').style.display=\"\";
                if(new_option_type=='SS' || new_option_type=='MS')
                    document.getElementById('additional_fields_for_SSMS_01').style.display=\"\";
                if(new_option_type=='UF')
                    document.getElementById('additional_fields_for_SS_01').style.display=\"\";\n";
        $return_js_code.="}\n";

        return $return_js_code;
            }

    function output__JS_OnDiscardAvailChanged($select_id,$text_field_id)
    {
        $return_js_code="
            function onDiscardAvailChanged()
            {
                if(document.getElementById('$select_id').value=='YES')
                    document.getElementById('$text_field_id').disabled=false;
                else
                    document.getElementById('$text_field_id').disabled=true;
            }
        ";

        return $return_js_code;
    }

    function output__JS_OnShowTypeChanged($types_array,$text_field_id)
    {
        $return_js_code="
            function onShowTypeChanged()
            {
                var types=new Array();\n";
        foreach($types_array as $k => $type)
            $return_js_code.="types[$k]='$type';\n";
        $return_js_code.="
                var new_show_type = document.getElementById('Option_ShowType_CI').value;
                var is_found=false;
                for(var i=0; i<types.length; i++)
                    if(types[i]==new_show_type)
                        is_found=true;
                if(is_found)
                    document.getElementById('$text_field_id').disabled=false;
                else
                    document.getElementById('$text_field_id').disabled=true;
        ";
        $return_js_code.="}\n";

        return $return_js_code;
    }

    function outputJSchangeToCIwarning()
    {
        $return_js_code="";

        if($this->option_info['option_type']!='CI' and count($this->option_info['values'])>1)
        {
            $return_js_code="
                if(edit_form.elements['Option[OptionType]'].value=='CI' && !confirm('".$this->MessageResources->getMessage('WRN_CHANGE_OPTION_TYPE_TO_CI')."'))
                    return false;
            ";
        };

        return $return_js_code;
    }

    function outputJSchangeINVwarning()
    {
        $return_js_code = "";

        if(!modApiFunc('Product_Options','__hasEntityPrivilegesFor',$this->option_info['parent_entity'],'inventory'))
            return $return_js_code;

        if($this->option_info['use_for_it']=='Y' and count($this->option_info['values'])>0)
        {
            $return_js_code="
                if((edit_form.elements['Option[OptionType]'].value=='CI' || edit_form.elements['Option[OptionType]'].value=='UF') && !confirm('".$this->MessageResources->getMessage('WRN_CHANGE_OTYPE_WITH_INV_TO_CI')."'))
                    return false;
                if(edit_form.elements['Option[OptionType]'].value!='CI'
                    && edit_form.elements['Option[OptionType]'].value=='UF'
                    && edit_form.elements['Option[UseForIT]'].value=='NO'
                    && !confirm('".$this->MessageResources->getMessage('WRN_CHANGE_IT_TO_NO')."'))
                    return false;
            ";
        };

        return $return_js_code;
    }

    function outputValueHeader()
    {
        global $application;

        $modifiers=modApiFunc("Product_Options","__getInnerVar","_MODIFIERS");
        $headers=array();
        for($i=0;$i<count($modifiers);$i++)
            $headers[]='MOD_'._ml_strtoupper($modifiers[$i]);

        $header_width = round(80 / count($headers)).'%';

        $tpl_contents = array(
            "modsCount" => count($modifiers)
           ,"modsNames" => '<th class="text-center">'.implode('</th><th class="text-center">',array_map(array(&$this,"_var2msg"),$headers)).'</th>'
        );

        $this->_Template_Contents=$tpl_contents;
        $application->registerAttributes($this->_Template_Contents);
        $this->mTmplFiller = &$application->getInstance('TmplFiller');
        return $this->mTmplFiller->fill("product_options/edit-option/", "values-header.tpl.html",array());

    }

    function outputValueModifiersFields($prefix,$vdata=array())
    {
        $modifiers=modApiFunc("Product_Options","__getInnerVar","_MODIFIERS");
        $return_html_code="";
        for($i=0;$i<count($modifiers);$i++)
        {
            if(!empty($vdata))
                $field_value=$vdata[$modifiers[$i]."_modifier"];
            else
                $field_value="0";

            if($modifiers[$i]=="weight")
                $sign=modApiFunc("Localization","getValue","WEIGHT_UNIT");
            else
                $sign=modApiFunc("Localization","getCurrencySign");

            $return_html_code.="<td class=\"text-center\"><input type=\"text\" class=\"form-control input-sm input-xsmall inline\" size=\"10\" maxlength=\"20\" name=\"".$prefix."[".$modifiers[$i]."_modifier]\" value=\"$field_value\"><strong>&nbsp;$sign</strong></td>"; //:                         _POST
        };
        return $return_html_code;
    }

    function outputJSModifiersArray()
    {
        $return_js_code="var modifiers = new Array();\n";
        $modifiers=modApiFunc("Product_Options","__getInnerVar","_MODIFIERS");
        for($i=0;$i<count($modifiers);$i++)
            $return_js_code.="modifiers[$i] = '".$modifiers[$i]."';\n";
        return $return_js_code;
    }

    function outputAddNewValueForm()
    {
        $return_html_code="";

        if($this->option_info['option_type']!='CI' and $this->option_info['option_type']!='UF')
        {
            global $application;
            $template_contents=array(
                "_option_id" => $this->option_id
               ,"ValueHeader" => $this->outputValueHeader()
               ,"ValueNameField" => HtmlForm::genInputTextField('255','NewValue[Name]','20','') //:                         _POST
               ,"ValueModifiersFields" => $this->outputValueModifiersFields("NewValue")
               ,"JSModifiersArray" => $this->outputJSModifiersArray()
            );

            $this->_Template_Contents=$template_contents;
            $application->registerAttributes($this->_Template_Contents);
            $this->mTmplFiller = &$application->getInstance('TmplFiller');
            $return_html_code.=$this->mTmplFiller->fill("product_options/", "edit-option-add-new-value-form.tpl.html",array());
        }

        return $return_html_code;
    }

    function outputValueIsDefaultField($vdata)
    {
        if($this->option_info['option_type']=='SS')
        {
            $return_html_code="<input type='radio' name='IsDefault' value='".$vdata["value_id"]."' ".($vdata["is_default"]=="Y"?"checked":"").">";
        }
        elseif($this->option_info['option_type']=='MS')
        {
            $return_html_code="<input type='checkbox' name='UpdateValues[".$vdata["value_id"]."][IsDefault]' ".($vdata["is_default"]=="Y"?"checked":"").">";
        }
        elseif($this->option_info['option_type']=='CI')
        {
            $return_html_code="<input type='checkbox' name='UpdateValues[".$vdata["value_id"]."][IsDefault]' ".($vdata["is_default"]=="Y"?"checked":"").">";
        }
        elseif($this->option_info['option_type']=='UF')
        {
            $return_html_code=$this->MessageResources->getMessage('LBL_NO')."<input type='hidden' name='UpdateValues[".$vdata["value_id"]."][IsDefault]' value='N'>";
        };
        return $return_html_code;
    }

    function outputValuesRows()
    {
        global $application;
        $return_html_code="";

        foreach($this->option_info['values'] as $vkey => $vdata)
        {
            $template_contents=array(
                "cbDELdisplay" => (!in_array($this->option_info['option_type'],array('CI','UF'))?'':'none')
               ,"_value_id" => $vdata["value_id"]
               ,"ValueNameField" => HtmlForm::genInputTextField('255',"UpdateValues[$vdata[value_id]][Name]",'15',prepareHTMLDisplay($vdata['value_name'])) //:                         _POST
               ,"ValueIsDefaultField" => $this->outputValueIsDefaultField($vdata)
               ,"ValueModifiers" => $this->outputValueModifiersFields("UpdateValues[$vdata[value_id]]",$vdata)
            );

            $this->_Template_Contents=$template_contents;
            $application->registerAttributes($this->_Template_Contents);
            $this->mTmplFiller = &$application->getInstance('TmplFiller');
            $return_html_code.=$this->mTmplFiller->fill("product_options/", "edit-option-value-row.tpl.html",array());
        }

        return $return_html_code;
    }

    function outputJSValuesIDs()
    {
        $return_js_code="var values_ids = new Array();\n";

        for($i=0;$i<count($this->option_info['values']);$i++)
        {
            $return_js_code.="values_ids[$i] = ".$this->option_info['values'][$i]["value_id"].";\n";
        }

        return $return_js_code;
    }

    function outputEditValuesForm()
    {
        $return_html_code="";

        if(/*$this->option_info['option_type']!='CI' and*/ count($this->option_info['values'])>0)
        {
            global $application;
            $template_contents=array(
                "_option_id" => $this->option_id
               ,"ValueHeader" => $this->outputValueHeader()
               ,"ValuesRows" => $this->outputValuesRows()
               ,"JSValuesIDs" => $this->outputJSValuesIDs()
               ,"JSModifiersArray" => $this->outputJSModifiersArray()
               ,"butDSdisplay" => (!in_array($this->option_info['option_type'],array('CI','UF'))?'':'none')
               ,"butSRTdisplay" => (count($this->option_info['values'])>1?'':'none')
            );

            $this->_Template_Contents=$template_contents;
            $application->registerAttributes($this->_Template_Contents);
            $this->mTmplFiller = &$application->getInstance('TmplFiller');
            $return_html_code.=$this->mTmplFiller->fill("product_options/", "edit-option-edit-values-form.tpl.html",array());
        }

        return $return_html_code;
    }

    function outputValuesAsSortItems()
    {
        $return_html_code="";

        for($i=0;$i<count($this->option_info['values']);$i++)
            $return_html_code.="<option value=".$this->option_info['values'][$i]['value_id'].">".$this->option_info['values'][$i]['value_name']."</option>";

        return $return_html_code;
    }

    function outputValuesSortForm()
    {
        $return_html_code="";

        if(!in_array($this->option_info['option_type'],array('CI','UF')) and count($this->option_info['values'])>1)
        {
            global $application;

            $template_contents=array(
                "WhatSort" => "values"
               ,"SortItems" => $this->outputValuesAsSortItems()
               ,"ParentEntityName" => "" // tag stub
               ,"ParentIdName" => "option_id"
               ,"ParentIdValue" => $this->option_id
            );

            $this->_Template_Contents=$template_contents;
            $application->registerAttributes($this->_Template_Contents);
            $this->mTmplFiller = &$application->getInstance('TmplFiller');
            $return_html_code.=$this->mTmplFiller->fill("product_options/", "sort-form.tpl.html",array());
        };

        return $return_html_code;
    }

    function outputActualHeader()
    {
        global $application;

        if($this->option_info['parent_entity']=='product')
        {
            $prodobj = &$application->getInstance('CProductInfo',$this->option_info['entity_id']);
            $template_contents=array(
               "ProductName" => $prodobj->getProductTagValue('Name')
            );
            $this->_Template_Contents=$template_contents;
            $application->registerAttributes($this->_Template_Contents);
            $this->mTmplFiller = &$application->getInstance('TmplFiller');
            return $this->mTmplFiller->fill("product_options/", "header-for-product.tpl.html",array());
        }
        elseif($this->option_info['parent_entity']=='ptype')
        {
            $ptinfo = modApiFunc("Catalog","getProductType",$this->option_info['entity_id']);
            $template_contents=array(
                "ProductTypeName" => $ptinfo['name']
            );
            $this->_Template_Contents=$template_contents;
            $application->registerAttributes($this->_Template_Contents);
            $this->mTmplFiller = &$application->getInstance('TmplFiller');
            return $this->mTmplFiller->fill("product_options/", "header-for-ptype.tpl.html",array());
        };
    }

    function output()
    {
        global $application;

        $otype_select=array(
            "select_name" => "Option[OptionType]"
           ,"selected_value" => $this->DATA["Option"]["OptionType"]
           ,"id" => "Option_OptionType"
           ,"onChange" => "javascript: onOptionTypeChanged();"
           ,"values" => array()
        );

        $template_contents=array();

        foreach($this->_option_types as $otype => $show_types)
        {
            $otype_select["values"][]=array(
                        "value" => $otype
                       ,"contents" => $this->MessageResources->getMessage('OT_'.$otype)
                    );

            $tmp=array(
                "select_name" => "Option[ShowType][$otype]"
               ,"selected_value" => $this->DATA["Option"]["ShowType"][$otype]
               ,"id" => "Option_ShowType_$otype"
               ,"onChange" => ($otype=="CI"?"javascript: onShowTypeChanged();":"")
               ,"values" => array()
            );
            foreach($show_types as $shtype)
                $tmp["values"][]=array(
                        "value" => $shtype
                       ,"contents" => $this->MessageResources->getMessage('ST_'.$otype.'_'.$shtype)
                    );

           $template_contents["ShowType".$otype."Field"] = HtmlForm::genDropdownSingleChoice($tmp, 'style="width: 200px;"');
        }

        $yes_no_values=array();
        foreach(array('YES','NO') as $lbl)
            $yes_no_values[]=array(
                "value" => $lbl
               ,"contents" => $this->MessageResources->getMessage('LBL_'.$lbl)
            );

        $discard_avail_select=array(
            "select_name" => "Option[DiscardAvail]"
           ,"selected_value" => $this->DATA["Option"]["DiscardAvail"]
           ,"onChange" => "javascript: onDiscardAvailChanged();"
           ,"id" => "Option_DiscardAvail"
           ,"values" => $yes_no_values
        );

        $use_for_it_select=array(
            "select_name" => "Option[UseForIT]"
           ,"selected_value" => $this->DATA["Option"]["UseForIT"]
           ,"id" => "Option_UseForIT"
           ,"values" => $yes_no_values
        );

        $request = new Request();
        $request->setView('PO_OptionsList');
        $request->setKey("parent_entity",$this->option_info["parent_entity"]);
        $request->setKey("entity_id",$this->option_info["entity_id"]);

        $template_contents = array_merge($template_contents,array(
            "ActualHeader" => $this->outputActualHeader()
           ,"ResultMessage" => $this->outputResultMessage()
           ,"_option_id" => $this->option_id
           ,"_parent_entity" => $this->option_info["parent_entity"]
           ,"_entity_id" => $this->option_info["entity_id"]
           ,"OptionNameField" => HtmlForm::genInputTextField('255','Option[OptionName]','65',prepareHTMLDisplay($this->DATA["Option"]["OptionName"]))
           ,"DisplayNameField" => HtmlForm::genInputTextField('255','Option[DisplayName]','65',prepareHTMLDisplay($this->DATA["Option"]["DisplayName"]))
           ,"OptionDescription" => HtmlForm::genInputTextAreaField("77", "Option[DisplayDescr]", "5")
           ,"DescriptionText" => $this->DATA["Option"]["DisplayDescr"]
           ,"OptionTypeField" => HtmlForm::genDropdownSingleChoice($otype_select, 'style="width: 200px;"')
           ,"JS_OnOptionTypeChanged" => $this->output__JS_OnOptionTypeChanged($this->_option_types,"show_type_for_")
           ,"DiscardAvailField" => HtmlForm::genDropdownSingleChoice($discard_avail_select, 'style="width: 200px;"')
           ,"DiscardValueField" => HtmlForm::genInputTextField('255','Option[DiscardValue]','65',prepareHTMLDisplay(@$this->DATA["Option"]["DiscardValue"]),'id=Option_DiscardValue')
           ,"JS_OnDiscardAvailChanged" => $this->output__JS_OnDiscardAvailChanged('Option_DiscardAvail','Option_DiscardValue')
           ,"CheckBoxTextField" => HtmlForm::genInputTextField('255','Option[CheckBoxText]','65',prepareHTMLDisplay($this->DATA["Option"]["CheckBoxText"]),'id=Option_CheckBoxText')
           ,"UseForITField" => HtmlForm::genDropdownSingleChoice($use_for_it_select, 'style="width: 200px;"')
           ,"JS_OnShowTypeChanged" => $this->output__JS_OnShowTypeChanged(array('CBSI','CBTA'),'Option_CheckBoxText')
           ,"EditValuesForm" => $this->outputEditValuesForm()
           ,"AddNewValueForm" => $this->outputAddNewValueForm()
           ,"CancelLink" => $request->getURL()
           ,"ValuesSortForm" => $this->outputValuesSortForm()
           ,"JSchangeToCIwarning" => $this->outputJSchangeToCIwarning()
           ,"JSchangeINVwarning" => $this->outputJSchangeINVwarning()
           ,"HintLink_OpName" => $this->Hints->getHintLink(array('OPTION_NAME','product-options-messages'))
           ,"HintLink_DspName" => $this->Hints->getHintLink(array('DISPLAY_NAME','product-options-messages'))
           ,"HintLink_DspDescr" => $this->Hints->getHintLink(array('DISPLAY_DESCR','product-options-messages'))
           ,"HintLink_OpType" => $this->Hints->getHintLink(array('OPTION_TYPE','product-options-messages'))
           ,"HintLink_ShType" => $this->Hints->getHintLink(array('SHOW_TYPE','product-options-messages'))
           ,"HintLink_DisAvail" => $this->Hints->getHintLink(array('DISCARD_AVAIL','product-options-messages'))
           ,"HintLink_DisValue" => $this->Hints->getHintLink(array('DISCARD_VALUE','product-options-messages'))
           ,"HintLink_CBText" => $this->Hints->getHintLink(array('CHECKBOX_TEXT','product-options-messages'))
           ,"HintLink_UseForIT" => $this->Hints->getHintLink(array('USE_FOR_IT','product-options-messages'))
           ,"JSforIE6" => $this->outputJSforIE6()
        ));

        $this->_Template_Contents=$template_contents;
        $application->registerAttributes($this->_Template_Contents);
        $this->mTmplFiller = &$application->getInstance('TmplFiller');
        return $this->mTmplFiller->fill("product_options/", "edit-option-container.tpl.html",array());

    }

    function getTag($tag)
    {
        return getKeyIgnoreCase($tag, $this->_Template_Contents);
    }

    function _var2msg($var)
    {
        return $this->MessageResources->getMessage($var);
    }

    var $_Template_Contents;
    var $MessageResources;
    var $option_id;
    var $option_info;

    var $DATA;
    var $_option_types;
};

?>