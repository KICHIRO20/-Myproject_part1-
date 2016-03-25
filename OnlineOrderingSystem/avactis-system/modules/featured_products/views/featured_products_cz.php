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
 * @package FeaturedProducts
 * @author Egor V. Derevyankin
 *
 */

class FeaturedProducts
{
    function FeaturedProducts()
    {
        $this->currentFP_obj = null;
    }

    function getTemplateFormat()
    {
        $format = array(
            'layout-file'        => 'featured-products-block.ini'
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

    function outputOneFP($fp_id)
    {
        if($this->currentFP_obj != null)
        {
            $this->currentFP_obj->_destruct();
            $this->currentFP_obj = null;
        };

        $this->currentFP_id = $fp_id;
        modApiFunc("tag_param_stack", "push", __CLASS__, array(array("key" => TAG_PARAM_PROD_ID, "value" => $fp_id)));
        $this->currentFP_obj = new CProductInfo($fp_id);
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
            $request->setKey('fp_no_rand', 1);
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
        $this->_loadFPLinks($params);
        $this->_filterFPLinks();
        $this->_randomizeFPLinks();

        if(empty($this->FPLinks))
        {
            return '';
        };

        $_tags = array(
            'Local_FeaturedList'
           ,'Local_FPViewAllLink'
           ,'Local_Columns'
           ,'Local_ThumbnailSide'
       );

        $application->registerAttributes($_tags);
        $this->templateFiller = new TemplateFiller();
        $this->template = $application->getBlockTemplate('FeaturedProducts');
        $this->templateFiller->setTemplate($this->template);

        return $this->templateFiller->fill('BlockContainer');

    }

    function getTag($tag)
    {
        global $application;

        $value = null;

        switch($tag)
        {
            case 'Local_FeaturedList':
                $disable_trtd = $application->getAppIni('PRODUCT_LIST_DISABLE_TR_TD');
                if ($disable_trtd != null && strtolower($disable_trtd) === 'yes')
                    $disable_trtd = true;
                else
                    $disable_trtd = false;
                $value = '';
                $outed = 0;
                $per_line = modApiFunc('Configuration','getValue',SYSCONFIG_FP_PER_LINE);
                foreach($this->FPLinks as $fp_id)
                {
                    if($outed == 0 && $disable_trtd == false)
                        $value .= '<tr>';

                    $value .= $this->outputOneFP($fp_id);

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

            case 'Local_FPViewAllLink';
                $value = $this->outputViewAllLink();
                break;

            case 'Local_Columns':
                $value = modApiFunc('Configuration','getValue',SYSCONFIG_FP_PER_LINE);
                break;

            case 'Local_ThumbnailSide':
                $pi_settings = modApiFunc('Product_Images','getSettings');
                $value = $pi_settings['MAIN_IMAGE_SIDE'];
                break;

            case 'ProductLargeImageWidth':
                $value = (string) $this->currentFP_obj->getProductTagValue('LargeImageWidth');
                break;

            case 'ProductSmallImageWidth':
                $value = (string) $this->currentFP_obj->getProductTagValue('SmallImageWidth');
                break;

            default:
                if(preg_match("/Product(.+)/",$tag,$matches))
                {
                    $value = $this->currentFP_obj->getProductTagValue($matches[1]);
                };
                break;
        };

        return $value;
    }

    function _loadFPLinks($params)
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

        $this->FPLinks = modApiFunc('Featured_Products','getFPIDsForCategories',$_ids);
    }

    function _filterFPLinks()
    {
        if(!empty($this->FPLinks))
        {
            $this->FPLinks = modApiFunc('Catalog','filterProductIdListByGlobalFilter',$this->FPLinks);
        };
    }

    function _randomizeFPLinks()
    {
        global $application;

        if (empty($this->FPLinks))
            return;

        $threshold = modApiFunc('Configuration', 'getValue', SYSCONFIG_FP_RANDOM_THRESHOLD);
        if (modApiFunc('Configuration', 'getValue', SYSCONFIG_FP_RANDOM_CHECKBOX) == 0)
        {

            if ($threshold >= count($this->FPLinks))
                return;
            $this->FPLinks = array_slice($this->FPLinks, $threshold);
            $this->was_randomized = true;
            return;
        }

        $request = $application->getInstance('Request');
        $no_rand = $request->getValueByKey('fp_no_rand');
        if ($no_rand == 1)
            return;

        $newarr = array();
        $apples = array_rand($this->FPLinks, $threshold);
        if (!is_array($apples))
            $apples = array($apples);
        foreach ($apples as $apple)
        {
            $newarr[] = $this->FPLinks[$apple];
        }
        $this->FPLinks = $newarr;
        $this->was_randomized = true;
    }

    var $was_randomized;
    var $FPLinks;
    var $currentFP_id;
    var $currentFP_obj;
};

?>