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

class PO_OptionsList
{
    function PO_OptionsList()
    {
        global $application;
        $this->MessageResources = &$application->getInstance('MessageResources',"product-options-messages", "AdminZone");
        $this->Hints = &$application->getInstance('Hint');
        $this->NoView = false;
        loadCoreFile('html_form.php');
    }

    function outputJSforIE6()
    {
        $return_js_code="";

        if(strstr($_SERVER["HTTP_USER_AGENT"],"MSIE 6")!=false)
        {
            global $application;
            $this->mTmplFiller = &$application->getInstance('TmplFiller');
            $return_js_code = $this->mTmplFiller->fill("product_options/", "js-for-ie6.tpl.html",array());
        };

        return $return_js_code;
    }

    function outputResultMessage()
    {
        global $application;

        $return_html_code="";

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
            $return_html_code .= $this->mTmplFiller->fill("product_options/", "result-message.tpl.html",array());
        }
        elseif(modApiFunc("Session","is_set","Errors"))
        {
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
        };

       return $return_html_code;
    }

    function outputValuesHeader($option_type)
    {
        global $application;

        $modifiers=modApiFunc("Product_Options","__getInnerVar","_MODIFIERS");
        $headers=array();
        for($i=0;$i<count($modifiers);$i++)
            $headers[]='MOD_'._ml_strtoupper($modifiers[$i]);

        $header_width = round(70 / count($headers)).'%';

        $tpl_contents = array(
            "modsCount" => count($modifiers)
           ,"modsNames" => '<th class="text-center" width="'.$header_width.'">'.implode('</th><th class="text-center" width="'.$header_width.'">',array_map(array(&$this,"_var2msg"),$headers)).'</th>'
        );

        $this->_Template_Contents=$tpl_contents;
        $application->registerAttributes($this->_Template_Contents);
        $this->mTmplFiller = &$application->getInstance('TmplFiller');
        return $this->mTmplFiller->fill("product_options/misc/", "values-header.tpl.html",array());
    }

    function outputModifiersList($value_data)
    {
        $return_html_code="";

        $modifiers=modApiFunc("Product_Options","__getInnerVar","_MODIFIERS");
        for($i=0;$i<count($modifiers);$i++)
        {
            $mval=$value_data[$modifiers[$i]."_modifier"];
            if($mval>0)
                $dspl_val="+";
            else
                $dspl_val="";

            if($modifiers[$i]=="weight")
                $dspl_val.=modApiFunc("Localization","format",$mval,"weight")." ".modApiFunc("Localization","getValue","WEIGHT_UNIT");
            else
                $dspl_val.=modApiFunc("Localization","format",$mval,"currency");

            $return_html_code.="<td>".$dspl_val."</td>";
        };

        return $return_html_code;
    }

    function outputValuesList($option_data)
    {
        global $application;
        $return_html_code="";

        if(count($option_data['values'])>0)
        {
            for($i=0;$i<count($option_data['values']);$i++)
            {
                $template_contents=array(
                    "CycleColor" => (($i%2)==0?"#FFFFFF":"#EEF2F8")
                   ,"ValueName" => $option_data['values'][$i]['value_name'] . ($option_data['values'][$i]['is_default']=='Y'?" (".$this->_var2msg('LBL_DEFAULT').")":'')
                   ,"ModifiersList" => $this->outputModifiersList($option_data['values'][$i])
                );

                $this->_Template_Contents=$template_contents;
                $application->registerAttributes($this->_Template_Contents);
                $this->mTmplFiller = &$application->getInstance('TmplFiller');
                $return_html_code.=$this->mTmplFiller->fill("product_options/", "options-list-value-row.tpl.html",array());
            }
        }
        else
            $return_html_code.="<tr><td colspan='5' align='center' style='background: white;'><i>".$this->MessageResources->getMessage('NO_VALUES_DEFINED')."</i></td></tr>";

        return $return_html_code;
    }

    function outputOInfDiv($option_data)
    {
        global $application;
        $template_contents=array(
                "_option_id" => $option_data['option_id']
               ,"OptionName" => $option_data['option_name']
               ,"DisplayName" => $option_data['display_name']
               ,"OptionType" => $this->MessageResources->getMessage('OT_'.$option_data['option_type'])
               ,"OptionShowType" => getMsg('PO','ST_'.$option_data['option_type'].'_'.$option_data['show_type'])
               ,"da_display" => (in_array($option_data['option_type'],array('SS','UF'))) ? '' : 'none'
               ,"DiscardAvail" => ($option_data['discard_avail']=='Y') ? getMsg('PO','LBL_YES') : getMsg('PO','LBL_NO')
               ,"cbt_display" => ($option_data['option_type']=='CI' and preg_match("/^CB/",$option_data['show_type'])) ? '' : 'none'
               ,"CheckBoxText" => $option_data['checkbox_text']
               ,"it_display" => (in_array($option_data['option_type'],array('CI','UF'))) ? 'none' : ''
               ,"UsedForInv" => ($option_data['use_for_it']=='Y') ? getMsg('PO','LBL_YES') : getMsg('PO','LBL_NO')
            );

        $this->_Template_Contents=$template_contents;
        $application->registerAttributes($this->_Template_Contents);
        $this->mTmplFiller = &$application->getInstance('TmplFiller');
        return $this->mTmplFiller->fill("product_options/misc/", "soft-div-oinf.tpl.html",array());
    }

    function outputOneOption($option_data)
    {
        global $application;

        $request_to_edit = new Request();
        $request_to_edit->setView('PO_EditOption');
        $request_to_edit->setKey("option_id",$option_data["option_id"]);

        $template_contents=array(
                "_option_id" => $option_data['option_id']
               ,"EditLink" => $request_to_edit->getURL()
               ,"OptionName" => $option_data['option_name']
               ,"ValuesHeader" => $this->outputValuesHeader($option_data['option_type'])
               ,"ValuesList" => $this->outputValuesList($option_data)
               ,"OInfDiv" => $this->outputOInfDiv($option_data)
               ,"jsDelOptFunc" => ($option_data['use_for_it']=='Y') ? 'tryToDelteOptionWithInventory' : 'tryToDelteOption'
            );

        $this->_Template_Contents=$template_contents;
        $application->registerAttributes($this->_Template_Contents);
        $this->mTmplFiller = &$application->getInstance('TmplFiller');
        return $this->mTmplFiller->fill("product_options/", "options-list-one-option.tpl.html",array());

    }

    function outputOptionsList()
    {
        $return_html_code="";
        if(count($this->Options)>0)
        {
            foreach($this->Options as $key => $option_data)
                $return_html_code.=$this->outputOneOption($option_data);
        }
        else
            $return_html_code.="<div style='padding-top: 150px; padding-bottom: 150px;'>".str_replace("%ENTITY_NAME%",$this->MessageResources->getMessage(_ml_strtoupper('entity_'.$this->parent_entity)),$this->MessageResources->getMessage('NO_OPTIONS_DEFINED'))."</div>";

        return $return_html_code;
    }

    function outputOptionsAsSortItems()
    {
        $return_html_code="";

        for($i=0;$i<count($this->Options);$i++)
            $return_html_code.="<option value=".$this->Options[$i]['option_id'].">".$this->Options[$i]['option_name']."</option>";

        return $return_html_code;
    }

    function outputOptionsSortForm()
    {
        $return_html_code="";

        global $application;

        if(count($this->Options)>0)
        {
            $template_contents=array(
                "WhatSort" => "options"
               ,"SortItems" => $this->outputOptionsAsSortItems()
               ,"ParentEntityName" => $this->parent_entity
               ,"ParentIdName" => "entity_id"
               ,"ParentIdValue" => $this->entity_id
            );

            $this->_Template_Contents=$template_contents;
            $application->registerAttributes($this->_Template_Contents);
            $this->mTmplFiller = &$application->getInstance('TmplFiller');
            $return_html_code.=$this->mTmplFiller->fill("product_options/", "sort-form.tpl.html",array());
        };

        return $return_html_code;
    }

    function outputSettingsForm()
    {
        $return_html_code="";

        global $application;
        $options_settings=modApiFunc("Product_Options","getOptionsSettingsForEntity",$this->parent_entity,$this->entity_id);

        if(count($this->Options)>0)
        {
            $aawd_select = array(
                "select_name" => "os[AAWD]"
                ,"class" => "from-control input-sm input-xsmall"
                ,"selected_value" => $options_settings['AAWD']
               ,"values" => array(
                    array("value" => "Y", "contents" => $this->MessageResources->getMessage('LBL_YES'))
                   ,array("value" => "N", "contents" => $this->MessageResources->getMessage('LBL_NO'))
               )
            );

            $aanic_select = array(
                "select_name" => "os[AANIC]"
               ,"class" => "from-control input-sm input-xsmall"
               ,"selected_value" => $options_settings['AANIC']
               ,"values" => array(
                    array("value" => "Y", "contents" => $this->MessageResources->getMessage('LBL_YES'))
                   ,array("value" => "N", "contents" => $this->MessageResources->getMessage('LBL_NO'))
               )
            );

            $aanis_select = array(
                "select_name" => "os[AANIS]"
                ,"class" => "from-control input-sm input-xsmall"
                ,"selected_value" => $options_settings['AANIS']
               ,"values" => array(
                    array("value" => "Y", "contents" => $this->MessageResources->getMessage('LBL_YES'))
                   ,array("value" => "N", "contents" => $this->MessageResources->getMessage('LBL_NO'))
               )
            );

            $template_contents = array(
                "_parent_entity" => $this->parent_entity
               ,"_entity_id" => $this->entity_id
               ,"inv_sets_display" => (count($this->__for_it_opts) > 0) ? '' : 'none'
               ,"setAAWDfield" => HtmlForm::genDropdownSingleChoice($aawd_select)
               ,"setAANICfield" => HtmlForm::genDropdownSingleChoice($aanic_select)
               ,"WRN_ONS_text" => $options_settings["WRN_ONS"]
               ,"WRN_CI_CR_text" => $options_settings["WRN_CI_CR"]
               ,"WRN_CI_INV_text" => $options_settings["WRN_CI_INV"]
               ,"HintLink_AAWD" => $this->Hints->getHintLink(array('SETTING_AAWD','product-options-messages'))
               ,"HintLink_ANIC" => $this->Hints->getHintLink(array('SETTING_AANIC','product-options-messages'))
               ,"HintLink_WRN_ONS" => $this->Hints->getHintLink(array('SETTING_WRN_ONS','product-options-messages'))
               ,"HintLink_WRN_CI_CR" => $this->Hints->getHintLink(array('SETTING_WRN_CI_CR','product-options-messages'))
               ,"HintLink_WRN_CI_INV" => $this->Hints->getHintLink(array('SETTING_WRN_CI_INV','product-options-messages'))
               ,"LL_NTF_value" => $options_settings['LL_NTF']
               ,"setAANISfield" => HtmlForm::genDropdownSingleChoice($aanis_select)
            );
            $this->_Template_Contents=$template_contents;
            $application->registerAttributes($this->_Template_Contents);
            $this->mTmplFiller = &$application->getInstance('TmplFiller');
            $return_html_code.=$this->mTmplFiller->fill("product_options/settings-form/", "container.tpl.html",array());
        };

        return $return_html_code;
    }

    function outputCRulesSection()
    {
        global $application;

        $req_to_cre = new Request();
        $req_to_cre->setView('PO_CRulesEditor');
        $req_to_cre->setKey('parent_entity',$this->parent_entity);
        $req_to_cre->setKey('entity_id',$this->entity_id);

        $template_contents=array(
               "CRulesEditorLink" => $req_to_cre->getURL()
            );

        $this->_Template_Contents=$template_contents;
        $application->registerAttributes($this->_Template_Contents);
        $this->mTmplFiller = &$application->getInstance('TmplFiller');
        return $this->mTmplFiller->fill("product_options/", "options-list-crules-container.tpl.html",array());
    }

    function outputInventorySection()
    {
        global $application;

        $req_to_inve = new Request();
        $req_to_inve->setView('PO_InvEditor');
        $req_to_inve->setKey('parent_entity',$this->parent_entity);
        $req_to_inve->setKey('entity_id',$this->entity_id);

        $inv_stats = modApiFunc("Product_Options","getInventoryStatsForEntity",$this->parent_entity,$this->entity_id);

        $used_options_html = '<span style="font-weight: normal">';
        foreach($this->Options as $oinf)
            if($oinf['use_for_it']=='Y')
                $used_options_html .= $oinf['option_name'].', ';
        $used_options_html = _ml_substr($used_options_html,0,-2).'</span><br>';

        $template_contents=array(
               "InvEditorLink" => $req_to_inve->getURL()
//              ,"InvStat_ActualValue" => ($inv_stats['IT_ACTUAL']=='Y')?$this->MessageResources->getMessage('LBL_YES'):$this->MessageResources->getMessage('LBL_NO')
              ,"InvStat_CountValue" => $inv_stats['it_count']
              ,"InvStat_AANICValue" => ($inv_stats['AANIC']=='Y')?$this->MessageResources->getMessage('LBL_YES'):$this->MessageResources->getMessage('LBL_NO')
              ,"InvStat_UsedOptions" => $used_options_html
            );

        $this->_Template_Contents=$template_contents;
        $application->registerAttributes($this->_Template_Contents);
        $this->mTmplFiller = &$application->getInstance('TmplFiller');
        return $this->mTmplFiller->fill("product_options/", "options-list-inv-section.tpl.html",array());
    }

    function outputActualHeader()
    {
        global $application;

        if($this->parent_entity=='product')
        {
            $request_to_no = new Request();
            $request_to_no->setView('PO_AddOption');
            $request_to_no->setKey('parent_entity',$this->parent_entity);
            $request_to_no->setKey('entity_id',$this->entity_id);

            $prdobj = &$application->getInstance('CProductInfo',$this->entity_id);

            $template_contents=array(
                "AddNewOptionLink" => $request_to_no->getURL()
               ,"ProductName" => $prdobj->getProductTagValue("Name")
               ,"Local_ProductBookmarks" => getProductBookmarks('options', $this->entity_id)
               ,"settingsButtonDisplay" => (count($this->Options)>0?'':'none')
               ,"sortButtonDisplay" => (count($this->Options)>1?'':'none')
            );

            $this->_Template_Contents=$template_contents;
            $application->registerAttributes($this->_Template_Contents);
            $this->mTmplFiller = &$application->getInstance('TmplFiller');
            return $this->mTmplFiller->fill("product_options/", "header-for-product-on-options-list.tpl.html",array());
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
            return $this->mTmplFiller->fill("product_options/", "header-for-ptype-on-options-list.tpl.html",array());
        };
    }

    function output()
    {
        global $application;
        $request = &$application->getInstance('Request');
        $this->parent_entity=$request->getValueByKey("parent_entity");
        $this->entity_id=$request->getValueByKey("entity_id");

        $this->_osets = modApiFunc('Product_Options','getOptionsSettingsForEntity',$this->parent_entity,$this->entity_id);

        $this->Options=modApiFunc("Product_Options","getOptionsWithValues",$this->parent_entity,$this->entity_id);

        $this->__for_it_opts = modApiFunc("Product_Options","getOptionsList",$this->parent_entity,$this->entity_id,USED_FOR_INV);

        $request_to_no = new Request();
        $request_to_no->setView('PO_AddOption');
        $request_to_no->setKey('parent_entity',$this->parent_entity);
        $request_to_no->setKey('entity_id',$this->entity_id);

        $template_contents = array(
                "ActualHeader" => $this->outputActualHeader()
               ,"ResultMessage" => $this->outputResultMessage()
               ,"OptionsList" => $this->outputOptionsList()
               ,"AddNewOptionLink" => $request_to_no->getURL()
               ,"_parent_entity" => $this->parent_entity
               ,"_entity_id" => $this->entity_id
               ,"OptionsSortForm" => $this->outputOptionsSortForm()
               ,"SettingsForm" => $this->outputSettingsForm()
               ,"JSforIE6" => $this->outputJSforIE6()
               ,"CRulesSection" => (count($this->Options)>0 and modApiFunc("Product_Options","__hasEntityPrivilegesFor",$this->parent_entity,'crules'))?$this->outputCRulesSection():''
               ,"InventorySection" => (count($this->__for_it_opts)>0 and modApiFunc("Product_Options","__hasEntityPrivilegesFor",$this->parent_entity,'inventory'))?$this->outputInventorySection():''
        );

        $this->_Template_Contents=$template_contents;
        $application->registerAttributes($this->_Template_Contents);
        $this->mTmplFiller = &$application->getInstance('TmplFiller');
        return $this->mTmplFiller->fill("product_options/", "options-list-container.tpl.html",array());
    }

    function getTag($tag)
    {
        if ($tag == 'ProductInfoLink') {
            $cz_layouts = LayoutConfigurationManager::static_get_cz_layouts_list();
            LayoutConfigurationManager::static_activate_cz_layout(array_shift(array_keys($cz_layouts)));
            $request = new CZRequest();
            $request->setView  ( 'ProductInfo' );
            $request->setAction( 'SetCurrentProduct' );
            $request->setKey   ( 'prod_id', $this->entity_id);
            $request->setProductID($this->entity_id);
            return $request->getURL();
        }
        return getKeyIgnoreCase($tag, $this->_Template_Contents);
    }

    function _var2msg($var)
    {
        return $this->MessageResources->getMessage($var);
    }

    function _ids2strings($var)
    {
        $return = array('option_name'=>'','value_name'=>array());
        $oid = $var[0];
        $vid = is_array($var[1])?array_keys($var[1]):array($var[1]);
        foreach($this->Options as $ok => $odata)
        {
            if($odata['option_id']==$oid)
            {
                $return['option_name']=$odata['option_name'];
                foreach($odata['values'] as $vk => $vdata)
                    if(in_array($vdata['value_id'],$vid))
                        $return['value_name'][]=$vdata['value_name'];
                $return['value_name']=implode(', ',$return['value_name']);
                break;
            };
        };
        return $return;
    }

    var $_Template_Contents;
    var $MessageResources;
    var $Options;
    var $parent_entity;
    var $entity_id;
    var $_osets;
};

?>