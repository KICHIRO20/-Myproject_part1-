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
 * @package Bestsellers
 * @author Egor V. Derevyankin
 *
 */

class Bestsellers
{
    function Bestsellers()
    {
        $this->currentBS_obj = null;
    }

    function getTemplateFormat()
    {
        $format = array(
            'layout-file'        => 'bestsellers-block.ini'
           ,'files' => array(
                'BlockContainer' => TEMPLATE_FILE_SIMPLE
               ,'BlockItem' => TEMPLATE_FILE_SIMPLE
               ,'Separator' => TEMPLATE_FILE_SIMPLE
            )
           ,'options' => array(
            )
        );
        return $format;
    }

    function outputOneBS($bs_id)
    {
        if($this->currentBS_obj != null)
        {
            $this->currentBS_obj->_destruct();
            $this->currentBS_obj = null;
        };

        $this->currentBS_id = $bs_id;
        modApiFunc("tag_param_stack", "push", __CLASS__, array(array("key" => TAG_PARAM_PROD_ID, "value" => $bs_id)));
        $this->currentBS_obj = new CProductInfo($bs_id);
        $ret = $this->templateFiller->fill('BlockItem');
        modApiFunc("tag_param_stack", "pop", __CLASS__);
        return $ret;
    }

    function outputViewAllLink()
    {
        $value = '';
        if ($this->was_randomized)
        {
            $request = new Request();
            $request->setView(CURRENT_REQUEST_URL);
            $request->setKey('bs_no_rand', 1);
            $url = $request->getURL();
            $msg = cz_getMsg('VIEW_ALL_LABEL');
            $value = str_replace('{URL}', $url, $msg);
        }
        return $value;
    }

    function output()
    {
        global $application;

        $params = func_get_args();
        $this->_loadBSLinks($params);
        $this->_filterBSLinks();
        $this->_randomizeBSLinks();

        if(empty($this->BSLinks))
        {
            return '';
        };

        $_tags = array(
            'Local_BestsellersList'
           ,'Local_BSViewAllLink'
           ,'Local_Columns'
           ,'Local_ThumbnailSide'
        );

        $application->registerAttributes($_tags);
        $this->templateFiller = new TemplateFiller();
        $this->template = $application->getBlockTemplate('Bestsellers');
        $this->templateFiller->setTemplate($this->template);

        return $this->templateFiller->fill('BlockContainer');

    }

    function getTag($tag)
    {
        global $application;

        $value = null;

        switch($tag)
        {
            case 'Local_BestsellersList':
                $disable_trtd = $application->getAppIni('PRODUCT_LIST_DISABLE_TR_TD');
                if ($disable_trtd != null && strtolower($disable_trtd) === 'yes')
                    $disable_trtd = true;
                else
                    $disable_trtd = false;
                $value = '';
                $outed = 0;
                $per_line = modApiFunc('Configuration','getValue',SYSCONFIG_BS_PER_LINE);
                foreach($this->BSLinks as $bs_id)
                {
                    if($outed == 0 && $disable_trtd == false)
                        $value .= '<tr>';

                    $value .= $this->outputOneBS($bs_id);

                    $outed++;
                    if($outed == $per_line)
                    {
                        if ($disable_trtd == false)
                            $value .= '</tr>';
                        else
                            $value .= $this->templateFiller->fill('Separator');
                        $outed = 0;
                    };
                };

                if($outed != 0 && $disable_trtd == false)
                {
                    $value .= str_repeat('<td></td>',($per_line - $outed)) . '</tr>';
                };

                break;

            case 'Local_BSViewAllLink';
                $value = $this->outputViewAllLink();
                break;

            case 'Local_Columns':
                $value = modApiFunc('Configuration','getValue',SYSCONFIG_BS_PER_LINE);
                break;

            case 'Local_ThumbnailSide':
                $pi_settings = modApiFunc('Product_Images','getSettings');
                $value = $pi_settings['MAIN_IMAGE_SIDE'];
                break;

            case 'ProductLargeImageWidth':
                $value = (string) $this->currentBS_obj->getProductTagValue('LargeImageWidth');
                break;

            case 'ProductSmallImageWidth':
                $value = (string) $this->currentBS_obj->getProductTagValue('SmallImageWidth');
                break;

            default:
                if(preg_match("/Product(.+)/",$tag,$matches))
                {
                    $value = $this->currentBS_obj->getProductTagValue($matches[1]);
                };
                break;
        };

        return $value;
    }

    function _loadBSLinks($params)
    {
        // load by products ids
        if(!empty($params))
        {
            $_ids = array_filter(array_map("abs",array_map("intval",$params)));
        }
        else // load by current section
        {
            global $application;
            $sections = $application->getSectionByCurrentPagename();

            if(in_array('ProductList',$sections)) // product list
            {
                $_cid = modApiFunc('CProductListFilter','getCurrentCategoryId');
                $_ids = array($_cid);
            }
            else // all other pages
            {
                $_cid = modApiFunc('Catalog','getHomeCategoryID');
                $_ids = array($_cid);
            };
        };

        $hard_links = modApiFunc('Bestsellers_API','getHardBSLinksForCategories',$_ids);
        $stat_links = modApiFunc('Bestsellers_API','getStatBSLinksForCategories',$_ids);
        $this->BSLinks = array_values(array_unique(array_merge($hard_links,$stat_links)));
    }

    function _filterBSLinks()
    {
        if(!empty($this->BSLinks))
        {
            $this->BSLinks = modApiFunc('Catalog','filterProductIdListByGlobalFilter',$this->BSLinks);
        };
    }

    function _randomizeBSLinks()
    {
        global $application;

        if (empty($this->BSLinks))
            return;

        $threshold = modApiFunc('Configuration', 'getValue', SYSCONFIG_BS_RANDOM_THRESHOLD);

        if ($threshold >= count($this->BSLinks))
        	$threshold= count($this->BSLinks)-1;

        if (modApiFunc('Configuration', 'getValue', SYSCONFIG_BS_RANDOM_CHECKBOX) == 0)
        {
            $this->BSLinks = array_slice($this->BSLinks, $threshold);
            $this->was_randomized = true;
            return;
        }

        $request = $application->getInstance('Request');
        $no_rand = $request->getValueByKey('bs_no_rand');
        if ($no_rand == 1)
            return;

        $newarr = array();
        $apples = array_rand($this->BSLinks, $threshold);
        if (!is_array($apples))
            $apples = array($apples);
        foreach ($apples as $apple)
        {
            $newarr[] = $this->BSLinks[$apple];
        }
        $this->BSLinks = $newarr;
        $this->was_randomized = true;
    }

    var $BSLinks;
    var $was_randomized;
    var $currentBS_id;
    var $currentBS_obj;
};

?>