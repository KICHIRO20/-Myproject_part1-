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

class PO_CRulesList
{
    function PO_CRulesList()
    {
        global $application;
        $this->MessageResources = &$application->getInstance("MessageResources","product-options-messages", "AdminZone");
        $this->NoView = false;
    }

    function prepareValuesList($side_content)
    {
        if($side_content=='')
            return $this->MessageResources->getMessage('ANY_WORD');

        $text = array();
        $combination = modApiFunc("Product_Options","_unserialize_combination",$side_content);
        foreach($combination as $oid => $vdata)
        {
            foreach($this->Options as $option_info)
                if($option_info['option_id']==$oid)
                    foreach($option_info['values'] as $value_info)
                        if(array_key_exists($value_info['value_id'],$vdata))
                            $text[]=$value_info['value_name'];
        };
        return implode(", ",$text);
    }

    function outputRulesList()
    {
        global $application;

        $html_code = "";

        $tmpl_suffix = ($this->edit_avail!='yes') ? "-read-only" : "";

        if(empty($this->crules))
        {
            $html_code .= $this->MessageResources->getMessage('NO_CRULES_DEFINED');
        }
        else
        {
            foreach($this->crules as $crule)
            {
                $replacments=array(
                    "%SINGLE_ODIV_LINK%" => "<a>".$this->prepareValuesList($crule['sside'])."</a>"
                   ,"%LSIDE_ODIV_LINK%" => "<a>".$this->prepareValuesList($crule['lside'])."</a>"
                   ,"%RSIDE_ODIV_LINK%" => "<a>".$this->prepareValuesList($crule['rside'])."</a>"
                );

                $template_contents = array(
                    "CRuleID" => $crule['crule_id']
                   ,"CRuleString" => str_replace(array_keys($replacments),array_values($replacments),$this->MessageResources->getMessage($this->templates_names[$crule['tpl_index']-1]))
                );

                $this->_Template_Contents=$template_contents;
                $application->registerAttributes($this->_Template_Contents);
                $this->mTmplFiller = &$application->getInstance('TmplFiller');
                $html_code .= $this->mTmplFiller->fill("product_options/", "crules-list-one-crule".$tmpl_suffix.".tpl.html",array());
            };
        };

        return $html_code;
    }

    function output()
    {
        global $application;
        global $_RESULT;
        $request = &$application->getInstance('Request');
        $parent_entity = $request->getValueByKey('parent_entity');
        $entity_id = $request->getValueByKey('entity_id');
        $this->edit_avail = $request->getValueByKey('edit_avail');
        $sets = modApiFunc("Product_Options","getOptionsSettingsForEntity",$parent_entity,$entity_id);

        $this->crules = modApiFunc("Product_Options","getCRulesForEntity",$parent_entity,$entity_id);
        $this->Options=modApiFunc("Product_Options","getOptionsWithValues",$parent_entity,$entity_id);

        $template_contents = array(
            "RulesList" => $this->outputRulesList()
        );

        $_RESULT['crules_count']=count($this->crules);

        $this->_Template_Contents=$template_contents;
        $application->registerAttributes($this->_Template_Contents);
        $this->mTmplFiller = &$application->getInstance('TmplFiller');
        return $this->mTmplFiller->fill("product_options/", "crules-list-container.tpl.html",array());
    }

    function getTag($tag)
    {
        return getKeyIgnoreCase($tag, $this->_Template_Contents);
    }

    var $_Template_Contents;
    var $MessageResources;
    var $crules;
    var $Options;
    var $templates_names = array('TPL_01_EXCEPTION','TPL_02_EXISTENT','TPL_03_XEXCEPTION','TPL_04_XEXISTENT');
}

?>