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

class PO_InvEditor
{
    function PO_InvEditor()
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

    function outputNRecordForm()
    {
        global $application;

        $template_contents = array(
            "ODivID" => "SingleSide"
        );

        $this->_Template_Contents=$template_contents;
        $application->registerAttributes($this->_Template_Contents);
        $this->mTmplFiller = &$application->getInstance('TmplFiller');
        return $this->mTmplFiller->fill("product_options/", "inv-editor-nrecord-form.tpl.html",array());
    }

    function outputOptionValuesList($option_info,$odiv_id)
    {
        if(empty($option_info['values']))
            return "<i>".getMsg('PO','NOTHING_VALUES')."</i>";

        global $application;
        $html_code = "<table border='0' cellpadding='0' cellspacing='0' width='100%'>";

        $input_type = ($option_info['option_type'] == 'SS') ? 'radio' : 'checkbox';

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
               ,"IsChecked" => ($i==0 and $option_info['use_for_it']=='Y') ? 'checked' : ''
               ,"IsDisabled" => $option_info['use_for_it']!='Y' ? 'disabled' : ''
            );
            $this->_Template_Contents=$template_contents;
            $application->registerAttributes($this->_Template_Contents);
            $this->mTmplFiller = &$application->getInstance('TmplFiller');
            $html_code .= $this->mTmplFiller->fill("product_options/", "odiv-one-value-as-".$input_type.".tpl.html",array());
            if($i<(count($option_info['values'])-1))
                $html_code .= '<tr style="height: 5px; background: #FFFFFF;"><td colspan="2"></td></tr>';
        };

        $html_code.="</table>";

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
                $html_code.='<tr>';

            $template_contents = array(
                "OptionName" => ($option_info['use_for_it']=='Y' ? '' : '<span style="color: red;">*</span> ') . $option_info['option_name']
               ,"OptionValuesList" => $this->outputOptionValuesList($option_info,$odiv_id)
            );

            $this->_Template_Contents=$template_contents;
            $application->registerAttributes($this->_Template_Contents);
            $this->mTmplFiller = &$application->getInstance('TmplFiller');
            $one_option_html = $this->mTmplFiller->fill("product_options/", "odiv-one-option-form.tpl.html",array());

            $html_code.='<td width="'.$td_width.'%" valign="top">'.$one_option_html.'</td>';

            if($sub_filled == $this->odiv_fill_rules['items_per_sub'])
            {
                $html_code.='</tr>';
                $sub_filled=0;
            };

        };

        if($sub_filled!=0)
            $html_code.='</tr>';

        return $html_code;
    }

    function outputOptionsDiv()
    {
        global $application;

        $template_contents = array(
            "ODivID" => "SingleSide"
           ,"ODivOptionsForm" => $this->outputODivOptionsForm("SingleSide")
           ,"ColspanForButtonTD" => $this->odiv_fill_rules['items_per_sub']
           ,"hintMessageForInv" => ($this->not_used_for_inv_count>0) ? $this->MessageResources->getMessage('MSG_TO_USE_FOR_INV') : ''
        );

        $this->_Template_Contents=$template_contents;
        $application->registerAttributes($this->_Template_Contents);
        $this->mTmplFiller = &$application->getInstance('TmplFiller');
        return $this->mTmplFiller->fill("product_options/", "inv-editor-odiv-container.tpl.html",array());
    }

    function outputInitSSide()
    {
        $js_code = "";
        for($i=0;$i<count($this->Options);$i++)
        {
            if($this->Options[$i]['option_type']=='SS' and $this->Options[$i]['use_for_it']=='Y' and !empty($this->Options[$i]['values']))
                $js_code .= "sside[sside.length] = new Array(".$this->Options[$i]['option_id'].",".$this->Options[$i]['values'][0]['value_id'].");\n";
        };

        return $js_code;
    }

    function output_jsDropDownDefinition()
    {
        global $application;

        $template_contents = array(
            "initValue" => $this->sets['INV_PER_PAGE']
           ,"callbackName" => 'changeSectorSize'
        );
        $this->_Template_Contents=$template_contents;
        $application->registerAttributes($this->_Template_Contents);
        $this->mTmplFiller = &$application->getInstance('TmplFiller');
        return $this->mTmplFiller->fill("product_options/misc/", "js-drop-down-definition.tpl.html",array());
    }

    function output()
    {
        global $application;
        $request = &$application->getInstance('Request');
        $this->parent_entity = $request->getValueByKey('parent_entity');
        $this->entity_id = $request->getValueByKey('entity_id');
        $this->Options = modApiFunc("Product_Options","getOptionsWithValues",$this->parent_entity,$this->entity_id,NOT_CUSTOM_INPUT);

        for($i=0;$i<count($this->Options);$i++)
            if($this->Options[$i]['use_for_it']!='Y')
                $this->not_used_for_inv_count++;

        $this->sets = modApiFunc("Product_Options","getOptionsSettingsForEntity",$this->parent_entity,$this->entity_id);

        $req_to_ret = new Request();
        $req_to_ret->setView('PO_OptionsList');
        $req_to_ret->setKey('parent_entity',$this->parent_entity);
        $req_to_ret->setKey('entity_id',$this->entity_id);

        $aanic_select = array(
            "select_name" => "os[AANIC]"
           ,"id" => "os_AANIC"
           ,"selected_value" => $this->sets["AANIC"]
           ,"values" => array(
                array(
                    "value" => "Y"
                   ,"contents" => $this->MessageResources->getMessage('LBL_YES')
                )
               ,array(
                    "value" => "N"
                   ,"contents" => $this->MessageResources->getMessage('LBL_NO')
                )
           )
        );

        $template_contents = array(
                "_parent_entity" => $this->parent_entity
               ,"_entity_id" => $this->entity_id
               ,"ActualHeader" => $this->outputActualHeader()
               ,"ReturnToOLLink" => $req_to_ret->getURL()
               ,"NRecordForm" => $this->outputNRecordForm()
               ,"OptionsDiv" => $this->outputOptionsDiv()
               ,"AANIC_select" => HtmlForm::genDropdownSingleChoice($aanic_select)
               ,"CurrentActiveSS" => "ss".$this->sets['INV_PER_PAGE']
               ,"InitSSide" => $this->outputInitSSide()
               ,"jsDropDownDefinition" => $this->output_jsDropDownDefinition()
        );

        $this->_Template_Contents=$template_contents;
        $application->registerAttributes($this->_Template_Contents);
        $this->mTmplFiller = &$application->getInstance('TmplFiller');
        return $this->mTmplFiller->fill("product_options/", "inv-editor-container.tpl.html",array());
    }

    function getTag($tag)
    {
        return getKeyIgnoreCase($tag, $this->_Template_Contents);
    }

    var $_Template_Contents;
    var $MessageResources;
    var $Options;
    var $not_used_for_inv_count=0;
    var $parent_entity;
    var $entity_id;
    var $odiv_fill_rules = array(
            'directions' => array(
                    'main' => 'upright'
                   ,'sub' => 'aflat'
                )
           ,'items_per_sub' => '4'
         );
}

?>