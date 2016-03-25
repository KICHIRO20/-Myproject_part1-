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

class PO_CheckCRules
{
    function PO_CheckCRules()
    {
    }

    function outputCombinations($cmbs)
    {
        $html_code = "";

        if(empty($cmbs))
            return '<tr><td colspan="2" class="text-center bold"><i>'.getMsg('PO','NOTHING_COMBINATIONS').'</i></td></tr>';

        $i=0;
        $j=0;
        foreach($cmbs as $_combination)
        {
            if(($i%2)==0)
                $html_code .= '<tr><td>';

            foreach($_combination as $oid => $vdata)
            {
                $oinf = modApiFunc("Product_Options","getOptionInfo",$oid);
                $vname="--";
                if(is_numeric($vdata))
                {
                    $vinf = modApiFunc("Product_Options","getValueInfo",$vdata);
                    $vname = $vinf['value_name'];
                }
                elseif(is_array($vdata) and !empty($vdata))
                {
                    $vnames=array();
                    foreach($vdata as $vid => $vvv)
                    {
                        $vinf=modApiFunc("Product_Options","getValueInfo",$vid);
                        $vnames[]=$vinf['value_name'];
                    };
                    $vname = implode(", ",$vnames);
                }
                $html_code .= $oinf['option_name'].': <span class="bold">'.$vname.'</span>';
            }
            if(($i%2)==0)
                $html_code .= '</td><td>';
            else
            {
                $html_code .= '</td></tr>';
                $j++;
            };
            $i++;
        }

        return $html_code;
    }

    function output()
    {
        global $application;
        $request = &$application->getInstance('Request');
        $parent_entity = $request->getValueByKey('parent_entity');
        $entity_id = $request->getValueByKey('entity_id');

        $sets = modApiFunc("Product_Options","getOptionsSettingsForEntity",$parent_entity,$entity_id);
        $frm = $sets['CR_FORMULA'];
        $result=(modApiFunc("Product_Options","__tmp_logic_filter",$frm,$parent_entity,$entity_id));

        switch($parent_entity)
        {
            case 'product':
                $prod_obj = &$application->getInstance('CProductInfo',$entity_id);
                $entity_name = $prod_obj->getProductTagValue("Name");
                break;
            case 'ptype':
                $ptinfo = modApiFunc("Catalog","getProductType",$entity_id);
                $entity_name = $ptinfo['name'];
                break;
        };

        $template_contents = array(
            'EntityType' => ($parent_entity == 'product') ? 'Product' : 'Product Type'
           ,'EntityName' => $entity_name
           ,'_parent_entity' => $parent_entity
           ,'_entity_id' => $entity_id
           ,'CorrectCombinations' => $this->outputCombinations($result['correct'])
           ,'InCorrectCombinations' => $this->outputCombinations($result['incorrect'])
        );

        $this->_Template_Contents=$template_contents;
        $application->registerAttributes($this->_Template_Contents);
        $this->mTmplFiller = &$application->getInstance('TmplFiller');
        return $this->mTmplFiller->fill("product_options/check_crules/", "container.tpl.html",array());
    }

    function getTag($tag)
    {
        return getKeyIgnoreCase($tag, $this->_Template_Contents);
    }
};

?>