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
 * @package RelatedProducts
 * @author Egor V. Derevyankin
 *
 */

class RelatedProducts
{
    function RelatedProducts()
    {
        $this->currentRP_obj = null;
        $this->was_randomized = false;
    }

    function getTemplateFormat()
    {
        $format = array(
            'layout-file'        => 'related-products-block.ini'
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

    function outputOneRP($rp_id)
    {
        if($this->currentRP_obj != null)
        {
            $this->currentRP_obj->_destruct();
            $this->currentRP_obj = null;
        };

        $this->currentRP_id = $rp_id;
        modApiFunc("tag_param_stack", "push", __CLASS__, array(array("key" => TAG_PARAM_PROD_ID, "value" => $rp_id)));
        $this->currentRP_obj = new CProductInfo($rp_id);
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
            $request->setKey('rp_no_rand', 1);
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
        $this->_loadRPLinks($params);
        $this->_filterRPLinks();
        $this->_randomizeRPLinks();

        if(empty($this->RPLinks))
        {
            return '';
        };

        $_tags = array(
            'Local_RelatedList'
           ,'Local_RPViewAllLink'
           ,'Local_Columns'
           ,'Local_ThumbnailSide'
       );

        $application->registerAttributes($_tags);
        $this->templateFiller = new TemplateFiller();
        $this->template = $application->getBlockTemplate('RelatedProducts');
        $this->templateFiller->setTemplate($this->template);

        return $this->templateFiller->fill('BlockContainer');
    }

    function getTag($tag)
    {
        global $application;

        $value = null;

        switch($tag)
        {
            case 'RelatedProducts':
            case 'relatedproducts':
                //                        ,                                  RelatedProduct               RelatedProduct
                //                 ,                  ProductList                         RelatedProduct
                break;

            case 'Local_RPViewAllLink':
                $value = $this->outputViewAllLink();
                break;

            case 'Local_RelatedList':
                $disable_trtd = $application->getAppIni('PRODUCT_LIST_DISABLE_TR_TD');
                if ($disable_trtd != null && strtolower($disable_trtd) === 'yes')
                    $disable_trtd = true;
                else
                    $disable_trtd = false;
                $value = '';
                $outed = 0;
                $per_line = modApiFunc('Configuration','getValue',SYSCONFIG_RP_PER_LINE);
                foreach($this->RPLinks as $rp_id)
                {
                    if($outed == 0 && $disable_trtd == false)
                        $value .= '<tr>';

                    $value .= $this->outputOneRP($rp_id);

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
            case 'Local_Columns':
                $value = modApiFunc('Configuration','getValue',SYSCONFIG_RP_PER_LINE);
                break;

            case 'Local_ThumbnailSide':
                $pi_settings = modApiFunc('Product_Images','getSettings');
                $value = $pi_settings['MAIN_IMAGE_SIDE'];
                break;

            case 'ProductLargeImageWidth':
                $value = (string) $this->currentRP_obj->getProductTagValue('LargeImageWidth');
                break;

            case 'ProductSmallImageWidth':
                $value = (string) $this->currentRP_obj->getProductTagValue('SmallImageWidth');
                break;

            default:
                if(preg_match("/Product(.+)/",$tag,$matches))
                {
                    $value = $this->currentRP_obj->getProductTagValue($matches[1]);
                };
                break;
        };

        return $value;
    }

    function _loadRPLinks($params)
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
            if(in_array('Checkout',$sections) or in_array('Cart',$sections)) // checkout or cart
            {
                $_ids = modApiFunc('Cart','getUniqueProductsIDsInCart');
            }
            elseif(in_array('ProductList',$sections)) // product list
            {
                $_ids = array();
                $_pids = modApiFunc('Catalog', 'getProductListByGlobalFilter', PAGINATOR_ENABLE, RETURN_AS_ID_LIST);
                if(is_array($_pids) and !empty($_pids))
                {
                    foreach($_pids as $_pinfo)
                    {
                        $_ids[] = $_pinfo['product_id'];
                    };
                };
            }
            else // product info
            {
                $_id = modApiFunc('Catalog','getCurrentProductId');
                if ($_id != NULL)
                {
                    $_ids = array($_id);
                }
                else
                {
                    $_ids = array();
                }
            };
        };

        $this->RPLinks = modApiFunc('Related_Products','getRPIDsForProducts',$_ids);
    }

    function _filterRPLinks()
    {
        if(!empty($this->RPLinks))
        {
            $this->RPLinks = modApiFunc('Catalog','filterProductIdListByGlobalFilter',$this->RPLinks);
        };
    }

    function _randomizeRPLinks()
    {
        global $application;

        if (empty($this->RPLinks))
            return;

        $threshold = modApiFunc('Configuration', 'getValue', SYSCONFIG_RP_RANDOM_THRESHOLD);
        if (modApiFunc('Configuration', 'getValue', SYSCONFIG_RP_RANDOM_CHECKBOX) == 0)
        {

            if ($threshold >= count($this->RPLinks))
                return;
            $this->RPLinks = array_slice($this->RPLinks, $threshold);
            $this->was_randomized = true;
            return;
        }

        $request = $application->getInstance('Request');
        $no_rand = $request->getValueByKey('rp_no_rand');
        if ($no_rand == 1)
            return;

        $newarr = array();
        $apples = array_rand($this->RPLinks, $threshold);
        if (!is_array($apples))
            $apples = array($apples);
        foreach ($apples as $apple)
        {
            $newarr[] = $this->RPLinks[$apple];
        }
        $this->RPLinks = $newarr;
        $this->was_randomized = true;
    }

    var $product_id;
    var $RPLinks;
    var $was_randomized;
    var $currentRP_id;
    var $currentRP_obj;
};

?>