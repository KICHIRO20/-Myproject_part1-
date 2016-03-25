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

class PO_InvPage
{
    function PO_InvPage()
    {
        $this->NoView = false;
        loadCoreFile('html_form.php');
    }

    function outputSectorPages($res_arr)
    {
        $sectors_count = ceil($res_arr['pages_count']  / $this->pages_per_sector);
        $sector_number = ceil($res_arr['page_number'] / $this->pages_per_sector);

        $start_page_number = ($sector_number - 1) * $this->pages_per_sector + 1;
        $end_page_number = ($sector_number) * $this->pages_per_sector;

        if($start_page_number<0)
            $start_page_number=0;

        if($end_page_number>$res_arr['pages_count'])
            $end_page_number=$res_arr['pages_count'];

        $html_code = '<table class="paginator" align="center" cellpadding="0" cellspacing="0"><tr>';

        if($sector_number > 1)
            $html_code .= '<td style="font-weight: normal; padding-left: 3px; padding-right: 2px; color: #666666;" align="left">...</td>';

        for($i=$start_page_number;$i<=$end_page_number;$i++)
        {
            if($res_arr['page_number']!=$i)
            {
                $html_code .= '<td><a href="javascript: void(0);" onClick="loadInvPage('.$i.')">'.$i.'</a><span style="font-weight: normal; color: #666666;">&nbsp;|</span></td>';
            }
            else
            {
                $html_code .= '<td><span style="color: red;">'.$i.'</span><span style="font-weight: normal; color: #666666;">&nbsp;|</span></td>';
            }
        };

        if($sector_number < $sectors_count)
            $html_code .= '<td style="font-weight: normal; padding-left: 2px; padding-right: 3px; color: #666666;">...</td>';

        $html_code .= '</tr></table>';

        return $html_code;
    }

    function outputSectorSizeChoiser($current_active)
    {
        global $application;

        $html_code = "";
        foreach($this->sector_sizes as $sector_size)
        {
            $template_contents = array(
                "SSID" => "ss".$sector_size
               ,"SSClass" => ($sector_size == $current_active) ? 'ss_active' : 'ss_inactive'
               ,"SSText" => $sector_size
            );
            $this->_Template_Contents=$template_contents;
            $application->registerAttributes($this->_Template_Contents);
            $this->mTmplFiller = &$application->getInstance('TmplFiller');
            $html_code .= $this->mTmplFiller->fill("product_options/paginator/", "sector-size-element.tpl.html",array());
        };
        return $html_code;
    }

    function output_jsDDforSSCvalues($current_active)
    {
        global $application;
        $html_code = "";
        foreach($this->sector_sizes as $sector_size)
        {
            $template_contents = array(
                "OptionValue" => $sector_size
               ,"OptionText" => $sector_size
               ,"OptionClass" => ($sector_size == $current_active) ? 'js_drop_down_option_hover' : 'js_drop_down_option'
            );
            $this->_Template_Contents=$template_contents;
            $application->registerAttributes($this->_Template_Contents);
            $this->mTmplFiller = &$application->getInstance('TmplFiller');
            $html_code .= $this->mTmplFiller->fill("product_options/misc/", "js-drop-down-option.tpl.html",array());
        };
        return $html_code;
    }

    function outpur_jsDDforSSC($current_active)
    {
        global $application;
        $template_contents = array(
            "SelectedValueText" => $current_active
           ,"Values" => $this->output_jsDDforSSCvalues($current_active)
        );
        $this->_Template_Contents=$template_contents;
        $application->registerAttributes($this->_Template_Contents);
        $this->mTmplFiller = &$application->getInstance('TmplFiller');
        return $this->mTmplFiller->fill("product_options/misc/", "js-drop-down.tpl.html",array());
    }

    function output()
    {
        global $application;
        $request = &$application->getInstance('Request');

        if($request->getValueByKey('output_not_needed')=='yes')
            return '';

        global $_RESULT;
        if(isset($_RESULT['fault_by']) and $_RESULT['fault_by']!='')
            return '';

        $page_number = $request->getValueByKey('page_number');
        $this->parent_entity=$request->getValueByKey('parent_entity');
        $this->entity_id=$request->getValueByKey('entity_id');
        $this->Options=modApiFunc("Product_Options","getOptionsWithValues",$this->parent_entity,$this->entity_id);

        $res_arr = modApiFunc("Product_Options","getInventoryPage",$this->parent_entity,$this->entity_id,$page_number);

        $_RESULT["inv_pages_count"]=$res_arr["pages_count"];
        $_RESULT["inv_page_number"]=$res_arr["page_number"];
/*
        $template_contents = array(
            "InvPaginatorField" => HtmlForm::genInputTextField('10','InvPaginator','2',$res_arr["page_number"])
           ,"leftImgSuffix" => $res_arr["page_number"]<=1 ? '_na' : ''
           ,"rightImgSuffix" => $res_arr["page_number"]==$res_arr["pages_count"] ? '_na' : ''
           ,"firstOnClick" => $res_arr["page_number"]<=1 ? '' : 'loadInvPage(1);'
           ,"prevOnClick" => $res_arr["page_number"]<=1 ? '' : 'loadInvPage('.($res_arr["page_number"]-1).');'
           ,"nextOnClick" => $res_arr["page_number"]==$res_arr["pages_count"] ? '' : 'loadInvPage('.($res_arr["page_number"]+1).')'
           ,"lastOnClick" => $res_arr["page_number"]==$res_arr["pages_count"] ? '' : 'loadInvPage('.($res_arr["pages_count"]).')'
           ,"directOnClick" => "loadInvPage(document.getElementById('directPageNumber').value);"
           ,"leftStyle" => $res_arr["page_number"]<=1 ? '' : 'style="cursor: pointer;"'
           ,"rightStyle" => $res_arr["page_number"]==$res_arr["pages_count"] ? '' : 'style="cursor: pointer;"'
           ,"PagesCount" => $res_arr["pages_count"]
        );
        $this->_Template_Contents=$template_contents;
        $application->registerAttributes($this->_Template_Contents);
        $this->mTmplFiller = &$application->getInstance('TmplFiller');
        $_RESULT["inv_paginator_html"] = $this->mTmplFiller->fill("product_options/", "inv-paginator.tpl.html",array());
*/
        /*
        if($res_arr['inv_count']!=0)
        {
            $sectors_count = ceil($res_arr['pages_count']  / $this->pages_per_sector);
            $sector_number = ceil($res_arr['page_number'] / $this->pages_per_sector);

            $template_contents = array(
                "leftImgSuffix" => $sector_number<=1 ? '_na' : ''
               ,"rightImgSuffix" => $sector_number==$sectors_count ? '_na' : ''
               ,"firstOnClick" => $sector_number<=1 ? '' : 'loadInvPage('.(($sector_number-1)*($this->pages_per_sector)-($this->pages_per_sector-1)).');'
               ,"lastOnClick" => $sector_number==$sectors_count ? '' : 'loadInvPage('.(($sector_number+1)*($this->pages_per_sector)-($this->pages_per_sector-1)).')'
               ,"leftStyle" => $sector_number<=1 ? '' : 'style="cursor: pointer;"'
               ,"rightStyle" => $sector_number==$sectors_count ? '' : 'style="cursor: pointer;"'
               ,"SectorPages" => $this->outputSectorPages($res_arr)
               ,"SectorSizeChoiser" => $this->outputSectorSizeChoiser($res_arr['inv_per_page'])
            );
            $this->_Template_Contents=$template_contents;
            $application->registerAttributes($this->_Template_Contents);
            $this->mTmplFiller = &$application->getInstance('TmplFiller');
            $_RESULT["inv_paginator_html"] = $this->mTmplFiller->fill("product_options/", "inv-paginator-2.tpl.html",array());
        }
        else
        {
            $_RESULT["inv_paginator_html"] = "";
        };
*/
        if($res_arr['inv_count']!=0 and $res_arr['inv_count']>10)
        {
            $sectors_count = ceil($res_arr['pages_count']  / $this->pages_per_sector);
            $sector_number = ceil($res_arr['page_number'] / $this->pages_per_sector);

            $template_contents = array(
                "leftImgSuffix" => $sector_number<=1 ? '_na' : ''
               ,"rightImgSuffix" => $sector_number==$sectors_count ? '_na' : ''
               ,"firstOnClick" => $sector_number<=1 ? '' : 'loadInvPage('.(($sector_number-1)*($this->pages_per_sector)-($this->pages_per_sector-1)).');'
               ,"lastOnClick" => $sector_number==$sectors_count ? '' : 'loadInvPage('.(($sector_number+1)*($this->pages_per_sector)-($this->pages_per_sector-1)).')'
               ,"leftStyle" => $sector_number<=1 ? 'style="display: none"' : 'style="cursor: pointer;"'
               ,"rightStyle" => $sector_number==$sectors_count ? 'style="display: none"' : 'style="cursor: pointer;"'
               ,"SectorPages" => $this->outputSectorPages($res_arr)
               //,"SectorSizeChoiser" => $this->outputSectorSizeChoiser($res_arr['inv_per_page'])
               ,"SectorSizeChoiser" => $this->outpur_jsDDforSSC($res_arr['inv_per_page'])
            );
            $this->_Template_Contents=$template_contents;
            $application->registerAttributes($this->_Template_Contents);
            $this->mTmplFiller = &$application->getInstance('TmplFiller');
            $_RESULT["inv_paginator_html"] = $this->mTmplFiller->fill("product_options/", "inv-paginator-2.tpl.html",array());
        }
        else
        {
            $_RESULT["inv_paginator_html"] = "";
        };

        $inventory_page = $res_arr["page_content"];

        $return_html_code="";

        for($i=0;$i<count($inventory_page);$i++)
        {
            if(empty($inventory_page[$i]['combination']))
                $inventory_page[$i]['combination'] = "[0]{{0}}";
            $cmb_arr = modApiFunc("Product_Options","_unserialize_combination",$inventory_page[$i]['combination']);
            $counter = 0;
            foreach($cmb_arr as $oid => $vdata)
            {
                $strings = $this->_ids2strings(array($oid,$vdata));
                $template_contents=array(
                        "OptionName" => (empty($strings['option_name']) ? 'not specified' : $strings['option_name'])
                       ,"ValuesField" => (empty($strings['value_name']) ? '' : $strings['value_name'])
                       ,"RowsCount" => count($cmb_arr)
                       ,"CycleColor" => ($i % 2)==0?"#DEE2E8":"#EEF2F8"
                       ,"SKUField" => HtmlForm::genInputTextField('255','Inventory['.$inventory_page[$i]['it_id'].'][sku]','17',$inventory_page[$i]['sku'])
                       ,"QuantityField" => HtmlForm::genInputTextField('255','Inventory['.$inventory_page[$i]['it_id'].'][quantity]','11',$inventory_page[$i]['quantity'])
                       ,"InvRecID" => $inventory_page[$i]['it_id']
                       ,"SortOrderID" => $inventory_page[$i]['sort_order']
                    );

                $fn = ($counter==0)?'first':'other';

                $this->_Template_Contents=$template_contents;
                $application->registerAttributes($this->_Template_Contents);
                $this->mTmplFiller = &$application->getInstance('TmplFiller');
                $return_html_code.=$this->mTmplFiller->fill("product_options/", "it-page-element-$fn-row.tpl.html",array());
                $counter++;
            };
        }

        if($return_html_code=="")
        {
            $MR = &$application->getInstance('MessageResources','product-options-messages','AdminZone');
            $return_html_code = "<tr><td colspan='3'><i>".$MR->getMessage('NO_INVENTORY_DEFINED')."</td></tr></i>";
        }
        else
        {
            $return_html_code .= '<tr bgcolor="#DEE2E8"><td></td><td><input type="checkbox" onClick="setCBstate(\'InventoryForm\',\'_ir_\',this.checked);"></td><td colspan="4"></td></tr>';
        };

        return $return_html_code;
    }

    function getTag($tag)
    {
        return getKeyIgnoreCase($tag, $this->_Template_Contents);
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

    var $parent_entity;
    var $entity_id;
    var $Options;
    var $sector_sizes = array(10,20,30,100);
    var $pages_per_sector = 10;
};

?>