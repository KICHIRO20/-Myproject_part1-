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

class PO_AddOption
{
    function PO_AddOption()
    {
        global $application;
        $this->MessageResources = &$application->getInstance('MessageResources',"product-options-messages", "AdminZone");
        $this->Hints = &$application->getInstance('Hint');
        $this->_option_types = modApiFunc("Product_Options","_getOptionsTypes",true);

        if(modApiFunc("Session","is_set","SessionPost"))
            $this->copyData();
        else
            $this->initData();

        loadCoreFile('html_form.php');
    }

    function copyData()
    {
        $this->DATA=modApiFunc("Session","get","SessionPost");
        modApiFunc("Session","un_set","SessionPost");
    }

    function initData()
    {
        $this->DATA=array(
            "NewOption" => array(
                "OptionName" => ""
               ,"DisplayName" => ""
               ,"DisplayDescr" => ""
               ,"OptionType" => array_shift(array_keys($this->_option_types))
               ,"DiscardAvail" => "NO"
               ,"DiscardValue" => ""
               ,"CheckBoxText" => ""
               ,"UseForIT" => "NO"
            )
        );
        foreach($this->_option_types as $ot => $shtypes)
        {
            $this->DATA["NewOption"]["ShowType"][$ot] = array_shift(array_values($shtypes));
        }
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
                "ResultMessage" => $this->MessageResources->getMessage($msg)
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
                    "ErrorMessage" => $this->MessageResources->getMessage($eval)
                );
                $this->_Template_Contents=$template_contents;
                $application->registerAttributes($this->_Template_Contents);
                $this->mTmplFiller = &$application->getInstance('TmplFiller');
                $return_html_code.=$this->mTmplFiller->fill("product_options/", "error-message.tpl.html",array());
            }
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
                var new_option_type = document.getElementById('NewOption_OptionType').value;\n";
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
                var new_show_type = document.getElementById('NewOption_ShowType_CI').value;
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

    function outputActualHeader()
    {
        global $application;

        if($this->parent_entity=='product')
        {
            $prodobj = &$application->getInstance('CProductInfo',$this->entity_id);
            $template_contents=array(
               "ProductName" => $prodobj->getProductTagValue('Name')
            );
            $this->_Template_Contents=$template_contents;
            $application->registerAttributes($this->_Template_Contents);
            $this->mTmplFiller = &$application->getInstance('TmplFiller');
            return $this->mTmplFiller->fill("product_options/", "header-for-product.tpl.html",array());
        }
        elseif($this->parent_entity=='ptype')
        {
            $ptinfo = modApiFunc("Catalog","getProductType",$this->entity_id);
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
        $request = &$application->getInstance('Request');
        $this->parent_entity=$request->getValueByKey("parent_entity");
        $this->entity_id=$request->getValueByKey("entity_id");

        $otype_select=array(
            "select_name" => "NewOption[OptionType]"
           ,"selected_value" => $this->DATA["NewOption"]["OptionType"]
           ,"id" => "NewOption_OptionType"
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
                "select_name" => "NewOption[ShowType][$otype]"
               ,"selected_value" => $this->DATA["NewOption"]["ShowType"][$otype]
               ,"id" => "NewOption_ShowType_$otype"
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
            "select_name" => "NewOption[DiscardAvail]"
           ,"selected_value" => $this->DATA["NewOption"]["DiscardAvail"]
           ,"onChange" => "javascript: onDiscardAvailChanged();"
           ,"id" => "NewOption_DiscardAvail"
           ,"values" => $yes_no_values
        );

        $use_for_it_select=array(
            "select_name" => "NewOption[UseForIT]"
           ,"selected_value" => $this->DATA["NewOption"]["UseForIT"]
           ,"id" => "NewOption_UseForIT"
           ,"values" => $yes_no_values
        );

        $request = new Request();
        $request->setView('PO_OptionsList');
        $request->setKey("parent_entity",$this->parent_entity);
        $request->setKey("entity_id",$this->entity_id);

        $template_contents = array_merge($template_contents,array(
            "ActualHeader" => $this->outputActualHeader()
           ,"ResultMessage" => $this->outputResultMessage()
           ,"_parent_entity" => $this->parent_entity
           ,"_entity_id" => $this->entity_id
           ,"OptionNameField" => HtmlForm::genInputTextField('255','NewOption[OptionName]','65',$this->DATA["NewOption"]["OptionName"])
           ,"DisplayNameField" => HtmlForm::genInputTextField('255','NewOption[DisplayName]','65',$this->DATA["NewOption"]["DisplayName"])
           ,"OptionDescription" => HtmlForm::genInputTextAreaField("77", "NewOption[DisplayDescr]", "5")
           ,"DescriptionText" => $this->DATA["NewOption"]["DisplayDescr"]
           ,"OptionTypeField" => HtmlForm::genDropdownSingleChoice($otype_select, 'style="width: 200px;"')
           ,"JS_OnOptionTypeChanged" => $this->output__JS_OnOptionTypeChanged($this->_option_types,"show_type_for_")
           ,"DiscardAvailField" => HtmlForm::genDropdownSingleChoice($discard_avail_select, 'style="width: 200px;"')
           ,"DiscardValueField" => HtmlForm::genInputTextField('255','NewOption[DiscardValue]','65',$this->DATA["NewOption"]["DiscardValue"],'id=NewOption_DiscardValue')
           ,"JS_OnDiscardAvailChanged" => $this->output__JS_OnDiscardAvailChanged('NewOption_DiscardAvail','NewOption_DiscardValue')
           ,"CheckBoxTextField" => HtmlForm::genInputTextField('255','NewOption[CheckBoxText]','65',$this->DATA["NewOption"]["CheckBoxText"],'id=NewOption_CheckBoxText')
           ,"UseForITField" => HtmlForm::genDropdownSingleChoice($use_for_it_select, 'style="width: 200px;"')
           ,"JS_OnShowTypeChanged" => $this->output__JS_OnShowTypeChanged(array('CBSI','CBTA'),'NewOption_CheckBoxText')
           ,"CancelLink" => $request->getURL()
           ,"HintLink_OpName" => $this->Hints->getHintLink(array('OPTION_NAME','product-options-messages'))
           ,"HintLink_DspName" => $this->Hints->getHintLink(array('DISPLAY_NAME','product-options-messages'))
           ,"HintLink_DspDescr" => $this->Hints->getHintLink(array('DISPLAY_DESCR','product-options-messages'))
           ,"HintLink_OpType" => $this->Hints->getHintLink(array('OPTION_TYPE','product-options-messages'))
           ,"HintLink_ShType" => $this->Hints->getHintLink(array('SHOW_TYPE','product-options-messages'))
           ,"HintLink_DisAvail" => $this->Hints->getHintLink(array('DISCARD_AVAIL','product-options-messages'))
           ,"HintLink_DisValue" => $this->Hints->getHintLink(array('DISCARD_VALUE','product-options-messages'))
           ,"HintLink_CBText" => $this->Hints->getHintLink(array('CHECKBOX_TEXT','product-options-messages'))
           ,"HintLink_UseForIT" => $this->Hints->getHintLink(array('USE_FOR_IT','product-options-messages'))
        ));

        $this->_Template_Contents=$template_contents;
        $application->registerAttributes($this->_Template_Contents);
        $this->mTmplFiller = &$application->getInstance('TmplFiller');
        return $this->mTmplFiller->fill("product_options/", "add-option-container.tpl.html",array());

    }

    function getTag($tag)
    {
        return getKeyIgnoreCase($tag, $this->_Template_Contents);
    }

    var $_Template_Contents;
    var $MessageResources;

    var $parent_entity;
    var $entity_id;

    var $DATA;
    var $_option_types;
};

?>