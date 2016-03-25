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

class PO_CRulesEditor
{
    function PO_CRulesEditor()
    {
        global $application;
        $this->MessageResources = &$application->getInstance("MessageResources","product-options-messages", "AdminZone");
        $this->NoView = false;
        loadCoreFile('html_form.php');
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

    function output_jsOptionsValuesArray()
    {
        $js_code = "";

        for($i=0;$i<count($this->Options);$i++)
        {
            $js_code .= "options_ids[options_ids.length] = ".$this->Options[$i]['option_id'].";\n";
            $vids = array();
            for($j=0;$j<count($this->Options[$i]['values']);$j++)
                $vids[]=$this->Options[$i]['values'][$j]['value_id'];
            $js_code .= "options_values[".$this->Options[$i]['option_id']."] = new Array(".implode(",",$vids).");\n";
        }

        return $js_code;
    }

    function output_jsOptionsTypesArray()
    {
        $js_code = "";

        for($i=0;$i<count($this->Options);$i++)
        {
            $js_code .= "options_types[".$this->Options[$i]['option_id']."] = '".$this->Options[$i]['option_type']."';\n";
        };

        return $js_code;
    }

    function outputCRulesTemplatesRadioGroup()
    {
        $replacments=array(
            "%SINGLE_ODIV_LINK%" => $this->MessageResources->getMessage('TD_SOL')
           ,"%LSIDE_ODIV_LINK%" => $this->MessageResources->getMessage('TD_LSL')
           ,"%RSIDE_ODIV_LINK%" => $this->MessageResources->getMessage('TD_RSL')
        );

        global $application;

        $html_code = '<table class="form" cellpadding="2" cellspacing="1">';

        for($i=0;$i<count($this->templates_names);$i+=2)
        {
            $template_contents=array(
                "TplIndex" => ($i+1)
               ,"TplText" => str_replace(array_keys($replacments),array_values($replacments),$this->MessageResources->getMessage($this->templates_names[$i]))
            );
            $this->_Template_Contents=$template_contents;
            $application->registerAttributes($this->_Template_Contents);
            $this->mTmplFiller = &$application->getInstance('TmplFiller');
            $html_code .= $this->mTmplFiller->fill("product_options/crules-editor/", "templates-rg-element.tpl.html",array());
        };

        $html_code .= '</table>';

        return $html_code;
    }

    function outputNRuleTplsSpans()
    {
        $html_code = "";

        $replacments=array(
            "%SINGLE_ODIV_LINK%" => '<a href="javascript: void(0);" onClick="exchangeOpForm(\'SingleSide\');" id="ta_s_%TINDEX%">'.$this->MessageResources->getMessage('DT_SSL').'</a>'
           ,"%LSIDE_ODIV_LINK%" => '<a href="javascript: void(0);" onClick="exchangeOpForm(\'LeftSide\');" id="ta_l_%TINDEX%">'.$this->MessageResources->getMessage('DT_LSL').'</a>'
           ,"%RSIDE_ODIV_LINK%" => '<a href="javascript: void(0);" onClick="exchangeOpForm(\'RightSide\');" id="ta_r_%TINDEX%">'.$this->MessageResources->getMessage('DT_RSL').'</a>'
        );

        foreach($this->templates_names as $index => $tname)
            $html_code.='<span id="nrule_tpl_'.($index+1).'" style="display: none;">'.str_replace("%TINDEX%",($index+1),str_replace(array_keys($replacments),array_values($replacments),$this->MessageResources->getMessage($tname))).'</span>';

        return $html_code;
    }

    function outputOptionValuesList($option_info,$odiv_id)
    {
        if(empty($option_info['values']))
            return "<i>".getMsg('PO','NOTHING_VALUES')."</i>";

        global $application;
        $html_code = "";

        $input_type = ($option_info['option_type'] == 'SS' and $odiv_id=='SingleSide') ? 'radio' : 'checkbox';

        for($i=0;$i<count($option_info['values']);$i++)
        {
            $template_contents=array(
                "InputID" => $odiv_id.'_'.$option_info['option_id'].'_'.$option_info['values'][$i]['value_id']
               ,"OptionValueName" => $option_info['values'][$i]['value_name']
               ,"RadioName" => $odiv_id.'_o'.$option_info['option_id']
               ,"RadioValue" => $option_info['values'][$i]['value_id']
               ,"CheckBoxName" => $odiv_id.'_o'.$option_info['option_id'].'[]'
               ,"OptionID" => $option_info['option_id']
               ,"ValueID" => $option_info['values'][$i]['value_id']
               ,"SideName" => $odiv_id
               ,"IsChecked" => ''
               ,"IsDisabled" => ''
            );
            $this->_Template_Contents=$template_contents;
            $application->registerAttributes($this->_Template_Contents);
            $this->mTmplFiller = &$application->getInstance('TmplFiller');
            $html_code .= $this->mTmplFiller->fill("product_options/", "odiv-one-value-as-".$input_type.".tpl.html",array());
            if($i<(count($option_info['values'])-1))
                $html_code .= '';
        };

        $html_code.="";

        return $html_code;
    }

    function outputODivOptionsForm($odiv_id)
    {
        global $application;
        $html_code = "";

        $sub_filled="0";
        $td_width = 100 / $this->odiv_fill_rules['items_per_sub'];

        foreach($this->Options as $index => $option_info)
        {
            $sub_filled++;

            if($sub_filled==1)
                $html_code.='';

            $template_contents = array(
                "OptionName" => $option_info['option_name']
               ,"OptionValuesList" => $this->outputOptionValuesList($option_info,$odiv_id)
            );

            $this->_Template_Contents=$template_contents;
            $application->registerAttributes($this->_Template_Contents);
            $this->mTmplFiller = &$application->getInstance('TmplFiller');
            $one_option_html = $this->mTmplFiller->fill("product_options/", "odiv-one-option-form.tpl.html",array());

            $html_code.=$one_option_html;

            if($sub_filled == $this->odiv_fill_rules['items_per_sub'])
            {
                $html_code.='';
                $sub_filled=0;
            };

        };

        if($sub_filled!=0)
            $html_code.='';

        return $html_code;
    }

    function outputOptionsDivs()
    {
        global $application;
        $html_code = "";

        $odivs_ids = array('SingleSide','LeftSide','RightSide');

        foreach($odivs_ids as $index => $odiv_id)
        {

            $template_contents = array(
                "ODivID" => $odiv_id
               ,"ODivOptionsForm" => $this->outputODivOptionsForm($odiv_id)
               ,"ColspanForButtonTD" => $this->odiv_fill_rules['items_per_sub']
            );

            $this->_Template_Contents=$template_contents;
            $application->registerAttributes($this->_Template_Contents);
            $this->mTmplFiller = &$application->getInstance('TmplFiller');
            $html_code .= $this->mTmplFiller->fill("product_options/", "crules-editor-odiv-container.tpl.html",array());
        }

        return $html_code;
    }

    function output()
    {
        global $application;
        $request = &$application->getInstance('Request');

        $this->parent_entity=$request->getValueByKey('parent_entity');
        $this->entity_id=$request->getValueByKey('entity_id');
        $this->Options=modApiFunc("Product_Options","getOptionsWithValues",$this->parent_entity,$this->entity_id,(NOT_CUSTOM_INPUT | NOT_UPLOAD_FILE));

        $req_to_ret = new Request();
        $req_to_ret->setView('PO_OptionsList');
        $req_to_ret->setKey('parent_entity',$this->parent_entity);
        $req_to_ret->setKey('entity_id',$this->entity_id);

        $template_contents = array(
                "_parent_entity" => $this->parent_entity
               ,"_entity_id" => $this->entity_id
               ,"ActualHeader" => $this->outputActualHeader()
               ,"ReturnToOLLink" => $req_to_ret->getURL()
               ,"CRulesTemplatesRadioGroup" => $this->outputCRulesTemplatesRadioGroup()
               ,"NRuleTplsSpans" => $this->outputNRuleTplsSpans()
               ,"NRuleTplsCount" => count($this->templates_names)
               ,"OptionsDivs" => $this->outputOptionsDivs()
               ,"jsOptionsValuesArray" => $this->output_jsOptionsValuesArray()
               ,"jsOptionsTypesArray" => $this->output_jsOptionsTypesArray()
        );

        $this->_Template_Contents=$template_contents;
        $application->registerAttributes($this->_Template_Contents);
        $this->mTmplFiller = &$application->getInstance('TmplFiller');
        return $this->mTmplFiller->fill("product_options/", "crules-editor-container.tpl.html",array());
    }

    function getTag($tag)
    {
        return getKeyIgnoreCase($tag, $this->_Template_Contents);
    }

    var $_Template_Contents;
    var $MessageResources;
    var $Options;
    var $parent_entity;
    var $entity_id;
    var $templates_names = array('TPL_01_EXCEPTION','TPL_02_EXISTENT','TPL_03_XEXCEPTION','TPL_04_XEXISTENT');
    var $odiv_fill_rules = array(
            'directions' => array(
                    'main' => 'upright'
                   ,'sub' => 'aflat'
                )
           ,'items_per_sub' => '3'
         );
};

?>