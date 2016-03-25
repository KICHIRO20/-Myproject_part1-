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
 * @package Catalog
 * @author Sergey Galanin
 */

define('MAX_PROD_IMG_SIZE', 120);

/**
 * This class generates a short category review.
 */
class CategoryReview
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

	function CategoryReview()
	{
		global $application;
		$images_url = $application->getAppIni('SITE_AZ_URL');
		if ($application->getCurrentProtocol() == "https" && $application->getAppIni('SITE_AZ_HTTPS_URL'))
		{
			$images_url = $application->getAppIni('SITE_AZ_HTTPS_URL');
		}
		$this->no_image_url = $images_url . 'images/no-image.gif';
	}

	function output()
	{
		global $application;
        $this->msg_res = &$application->getInstance('MessageResources');

		$this->category_id = modApiFunc('CProductListFilter', 'getCurrentCategoryId');
        loadClass('CCategoryInfo');
        $cat_info = new CCategoryInfo($this->category_id);
        $this->_Current_Category = &$cat_info;

        $i_w = $cat_info->getCategoryTagValue('SmallImageWidth' );
        $i_h = $cat_info->getCategoryTagValue('SmallImageHeight');
        $i_url = $cat_info->getCategoryTagValue('SmallImageSrc');
        if ($i_url == '' || $i_w == 0 || $i_h == 0) {
        	$i_w = 100;
        	$i_h = 100;
        	$i_url = $this->no_image_url;
        }
        $cat_info->setAdditionalCategoryTag('AttributeImageWidth', $i_w);
        $cat_info->setAdditionalCategoryTag('AttributeImageHeight', $i_h);
        $cat_info->setAdditionalCategoryTag('AttributeImageURL', $i_url);
        $cat_info->setAdditionalCategoryTag('CategoryStatus', $cat_info->getCategoryTagValue('status', CATEGORYINFO_LOCALIZED_DATA));
        $application->registerAttributes($cat_info->getAdditionalCategoryTagList());
        $application->registerAttributes(array('CategoryProducts', 'CategoryProductsRec',
                'FeaturedProducts', 'BestsellersProducts',
        ));
        return modApiFunc('TmplFiller', 'fill', "catalog/category_review/", "review.tpl.html", array());
	}

	function outputFeaturedProducts()
	{
        $fp_links = modApiFunc('Featured_Products', 'getFPIDsForCategory', $this->category_id);
        if (! is_array($fp_links) || sizeof($fp_links) == 0) {
        	return '<p class="text-center bold">'.getMsg('CTL','CTG_RVW_NO_FEAT').'</p>';
        }
        $per_line = modApiFunc('Configuration','getValue',SYSCONFIG_FP_PER_LINE);
        return $this->outputLinkedProducts($fp_links, $per_line);
	}

    function outputBestsellersProducts()
    {
        $hbs_links = modApiFunc('Bestsellers_API', 'getHardBSLinksForCategory', $this->category_id);
        $sbs_links = modApiFunc('Bestsellers_API', 'getStatBSLinksForCategory', $this->category_id);
        $links = array_unique(array_merge($hbs_links, $sbs_links));
        if (sizeof($links) == 0) {
            return '<p class="text-center bold">'.getMsg('CTL','CTG_RVW_NO_BSTS').'</p>';
        }
        $per_line = modApiFunc('Configuration', 'getValue', SYSCONFIG_BS_PER_LINE);
        return $this->outputLinkedProducts($links, $per_line);
    }

    function outputLinkedProducts($links, $per_line)
    {
        $value = '<table class="table borderless linked_products">';
        $on_line = 0;
        foreach ($links as $p_id) {
            if ($on_line == 0) {
                $value .= '<tr>';
            }
            $value .= $this->outputLinkedProduct($p_id);
            $on_line ++;
            if ($on_line >= $per_line) {
                $value .= '</tr>';
                $on_line = 0;
            };
        };
        if ($on_line > 0) {
            $value .= str_repeat('<td width="33%">&nbsp;</td>',($per_line - $on_line)) . '</tr>';
        };
        $value .= '</table>';
        return $value;
    }

	function outputLinkedProduct($p_id)
	{
		global $application;
		$prod = new CProductInfo($p_id);
		$tags = array(
              'ProductID' => $p_id,
              'ProductName' => $prod->getProductTagValue('Name'),
              'ProductImageFile' => $prod->getProductTagValue('smallimagefile'),
		      'ProductImage' => $prod->getProductTagValue('smallimagesrc'),
              'ProductImageWidth' => $prod->getProductTagValue('smallimagewidth'),
              'ProductImageHeight' => $prod->getProductTagValue('smallimageheight'),
		);
		if ($tags['ProductImageFile'] == '' ||
		        $tags['ProductImageWidth'] == 0 ||
		        $tags['ProductImageHeight'] == 0) {

            $imagesUrl = $application->getAppIni('SITE_AZ_URL');
            if ($application->getCurrentProtocol() == "https" && $application->getAppIni('SITE_AZ_HTTPS_URL'))
            {
                $imagesUrl = $application->getAppIni('SITE_AZ_HTTPS_URL');
            }
            $tags['ProductImage'] = $this->no_image_url;
            $tags['ProductImageWidth'] = 100;
            $tags['ProductImageHeight'] = 100;
		}
		return modApiFunc('TmplFiller', 'fill', "catalog/category_review/", "linked_product.tpl.html", $tags);
	}

	function adjustImageSize(&$tags)
	{
		if ($tags['ProductImageWidth'] >= $tags['ProductImageHeight']) {
			if ($tags['ProductImageWidth'] > MAX_PROD_IMG_SIZE) {
				$tags['ProductImageHeight'] =
				    round($tags['ProductImageHeight'] * MAX_PROD_IMG_SIZE / $tags['ProductImageWidth']);
				$tags['ProductImageWidth'] = MAX_PROD_IMG_SIZE;
			}
		}
		else {
            if ($tags['ProductImageHeight'] > MAX_PROD_IMG_SIZE) {
                $tags['ProductImageWidth'] =
                    round($tags['ProductImageWidth'] * MAX_PROD_IMG_SIZE / $tags['ProductImageHeight']);
                $tags['ProductImageHeight'] = MAX_PROD_IMG_SIZE;
            }
		}
	}

	function getTag($tag)
	{
        global $application;
        $value = null;
        //$CatID = $this->_Current_Category->getCategoryTagValue('ID');
        switch ($tag)
        {

            case 'CategoryProducts':
                $value = $this->_Current_Category->getCategoryTagValue('productsnumber_non_recursively');
                break;

            case 'CategoryProductsRec':
                $value = $this->_Current_Category->getCategoryTagValue('productsnumberrecursively_all_product_links');
                break;

            case 'FeaturedProducts':
            	$value = $this->outputFeaturedProducts();
            	break;

            case 'BestsellersProducts':
                $value = $this->outputBestsellersProducts();
                break;

            default:
                if (is_object($this->_Current_Category) && $this->_Current_Category->isTagExists($tag))
                {
                    $value = $this->_Current_Category->getCategoryTagValue($tag);
                }
                else
                {
                    if (_ml_strpos($tag, 'Category') === 0)
                    {
                        $tag = _ml_substr($tag, _ml_strlen('Category'));
                        if (is_object($this->_Current_Category) && $this->_Current_Category->isTagExists($tag))
                        {
                            $value = $this->_Current_Category->getCategoryTagValue($tag);
                        }
                    }
                }
                break;
        }
        return $value;
	}
    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */

}

?>