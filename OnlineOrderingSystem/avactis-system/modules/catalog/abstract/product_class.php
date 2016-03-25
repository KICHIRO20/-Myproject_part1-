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
loadModuleFile('catalog/catalog_api.php');

define ('PRODUCTINFO_DEFAULT_LOCALIZED_MODE', 0);
define ('PRODUCTINFO_LOCALIZED_DATA', 1);
define ('PRODUCTINFO_NOT_LOCALIZED_DATA', 2);

define ('PRODUCTINFO_SHORT_TAG_NAMES', 'productinfoshorttagnames');
define ('PRODUCTINFO_LONG_TAG_NAMES', 'productinfolongtagnames');

define ('PRODUCTINFO_WITH_TAG_TYPES', 'productinfowithtagtypes');
define ('PRODUCTINFO_NO_TAG_TYPES', 'productinfonotagtypes');

/**
 * The class gives complete information about category.
 *
 * The class interface:
 * CProductInfo::CProductInfo($product_id, $localized = PRODUCTINFO_LOCALIZED_DATA)
 * CProductInfo::turnOnDebugMode()
 * CProductInfo::turnOffDebugMode()
 * CProductInfo::getProductTagValue($tag, $localized = PRODUCTINFO_DEFAULT_LOCALIZED_MODE)
 * CProductInfo::setAdditionalProductTag($key, $value)
 * CProductInfo::getAdditionalProductTagList()
 * CProductInfo::isTagExists($tag)
 * CProductInfo::clone($newParentCatId)
 *
 *
 * @package Catalog
 * @author Alexey Florinsky
 */
class CProductInfo
{

    /**#@+
     * @access public
     */

    /**
	 * The class constructor.
	 * The object will be created only if the specified $pid (Product ID)
	 * is a correct value and such category exists in the DB.
	 *
	 * @param int $pid Category ID
	 * @param int $localized The parameter defines, whether to return localized
	 * values or to send as values the constants PRODUCTINFO_LOCALIZED_DATA or
	 * PRODUCTINFO_NOT_LOCALIZED_DATA directly from the DB
     * @return CProductInfo object or null
	 */
    function CProductInfo($pid, $localized = PRODUCTINFO_LOCALIZED_DATA)
    {
        /*
         If the product id is not correct, then the object will return
         the empty tag values
         */
        if ($pid == null || !is_numeric($pid) || !modApiFunc('Catalog', 'isCorrectProductId', $pid))
        {
            $this->_fProductIDIsIncorrect = true;
        }

        $this->_fProductID = $pid;

        if  ($localized == PRODUCTINFO_LOCALIZED_DATA)
        {
            $this->localized = true;
        }
        else
        {
            $this->localized = false;
        }

        $this->debugmode = false;
        $this->_loadBaseProductInfo();

        # Flip the keys of the array _fArtificialTagList, i.e.
	    # the key values will equal the key itself.
	    # It is necessary to make it easier to use the array
        # The keys of the array should be in the lower case
        $_tmp = $this->_fArtificialTagList;
        $this->_fArtificialTagList = array();
        foreach ($_tmp as $tag)
        {
            $this->_fArtificialTagList[strtolower($tag)] = $tag;
        }
    }

    /**
     * Turns on the debug mode. For the attributes whose values are not defined
     * will be returned a text message instead of empty string, explaining why
     * the values are not defined.
     *
     * @see CProductInfo::turnOffDebugMode()
     *
     */
    function turnOnDebugMode()
    {
        $this->debugmode = true;
    }

    /**
     * Turns off the debug mode.
     *
     * @see CProductInfo::turnOnDebugMode()
     *
     */
    function turnOffDebugMode()
    {
        $this->debugmode = false;
    }


	function isProductIdCorrect()
	{
		return !$this->_fProductIDIsIncorrect;
	}

    /**
     * Returns Product info tag value.
     *
     * @param string $tag Short tag name, the tag can be written in any case
	 * @param int $localized The parameter defines, whether to return localized
	 * values or to send as values the constants PRODUCTINFO_LOCALIZED_DATA or
	 * PRODUCTINFO_NOT_LOCALIZED_DATA directly from the DB
	 * @return string Tag value
	 **/
    function getProductTagValue($tag, $localized = PRODUCTINFO_LOCALIZED_DATA)
    {
        global $application;

      /*
        If creating an object the incorrect id was passed to the category, then
        it will return an empty value of any tag.
       */
       if ($this->_fProductIDIsIncorrect == true)
       {
           return ($this->debugmode ? 'Product ID is incorrect' : '');
       }

       # Load attrubute values
       if ($this->_fProductAttributesInfo == null)
       {
           $this->_loadProductAttributesInfo();
       }

       # Check additional tags
       if (isset($this->_fAdditionalTagList[$tag]))
       {
           return $this->_fAdditionalTagList[$tag];
       }

       # Check localization request
       if  ($localized == PRODUCTINFO_LOCALIZED_DATA)
       {
           $this->localized = true;
       }
       elseif ($localized == PRODUCTINFO_NOT_LOCALIZED_DATA)
       {
           $this->localized = false;
       }

       # check, if the attribute associated with the tag is visible in the
       # product type
       if (!$this->isProductTagVisible($tag))
       {
           return $this->debugmode ? 'Undefined tag or attribute is invisible: '.$tag : false;
       }

       # Get Tag value
       $tag = strtolower(trim($tag));

       # check, if the tag is custom product tag
       if(preg_match("/(.+)custom$/",$tag, $matches))
       {
           return $this->_getTagValueFromAttributeList($matches[1]);
       }

       switch ($tag)
       {
          #
          # Process artificial tags
          #
          case strtolower($this->_fArtificialTagList['id']):
              return $this->_fBaseProductInfo[0]['p_id'];
              break;

          case strtolower($this->_fArtificialTagList['name']):
              return $this->localized ? prepareHTMLDisplay($this->_fBaseProductInfo[0]['p_name'])
                                      : $this->_fBaseProductInfo[0]['p_name'];
              break;

          case strtolower($this->_fArtificialTagList['typeid']):
              return $this->_fBaseProductInfo[0]['p_type_id'];
              break;

          case strtolower($this->_fArtificialTagList['typename']):
              return $this->localized ? prepareHTMLDisplay($this->_fBaseProductInfo[0]['p_type_name'])
                                      : $this->_fBaseProductInfo[0]['p_type_name'];
              break;

          case strtolower($this->_fArtificialTagList['updated']):
              return $this->localized ? modApiFunc("Localization", "SQL_date_format", $this->_fBaseProductInfo[0]['p_date_updated'])
                                      : $this->_fBaseProductInfo[0]['p_date_updated'];
              break;

          case strtolower($this->_fArtificialTagList['added']):
              return $this->localized ? modApiFunc("Localization", "SQL_date_format", $this->_fBaseProductInfo[0]['p_date_added'])
                                      : $this->_fBaseProductInfo[0]['p_date_added'];
              break;

          case strtolower($this->_fArtificialTagList['infolink']):
              return $this->getProductInfoLink($this->_fBaseProductInfo[0]['p_id'], $this->chooseCategoryID());
              break;

          case strtolower($this->_fArtificialTagList['buylink']):
              $request = new Request();
              modApiFunc("Configuration", "getValue", "store_show_cart") ? $request->setView('CartContent') : $request->setView(CURRENT_REQUEST_URL);
              $request->setAction( 'AddToCart' );
              $request->setKey   ( 'prod_id', $this->_fBaseProductInfo[0]['p_id']);
              $request->setCategoryID($this->chooseCategoryID());
              $request->setProductID($this->_fBaseProductInfo[0]['p_id']);
              return $request->getURL();
              break;

          case strtolower($this->_fArtificialTagList['categorylink']):
              $target_category_id = $this->chooseCategoryID();
              $catObj = &$application->getInstance('CCategoryInfo',$target_category_id);
              return $catObj->getCategoryTagValue('link');

              break;

          case strtolower($this->_fArtificialTagList['categoryid']):
              return $this->chooseCategoryID();
              break;

          case strtolower($this->_fArtificialTagList['categorypath']):
              return modApiFunc('Catalog','getCategoryFullPath',$this->chooseCategoryID());
              break;

          case strtolower($this->_fArtificialTagList['allcategorypath']):
              $category_pathes = array();
              foreach ($this->getCategoriesIDs() as $cid)
              {
                  $category_pathes[] = modApiFunc('Catalog','getCategoryFullPath',$cid);
              }
              return $category_pathes;
              break;

          case strtolower('quantityinstock'):
              $value = "";
              if ($this->whichStockControlMethod()==PRODUCT_OPTIONS_INVENTORY_TRACKING)
              {
                  $value = modApiFunc('Product_Options','getQuantityInStockByInventoryTable','product', $this->getProductTagValue('ID'));
              }
              else
              {
                  $value = $this->_getTagValueFromAttributeList($tag);
              }
              return $value;
              break;

          case strtolower($this->_fArtificialTagList['stockmessage']):
              $store_show_absent = modApiFunc("Configuration", "getValue", "store_show_absent");
              $qty_in_stock = $this->getProductTagValue("QuantityInStock", PRODUCTINFO_NOT_LOCALIZED_DATA);
              $low_stock_level = $this->getProductTagValue("LowStockLevel", PRODUCTINFO_NOT_LOCALIZED_DATA);

              if($low_stock_level === "" || $this->whichStockControlMethod()==PRODUCT_OPTIONS_INVENTORY_TRACKING)
              {
                  //                  ProductType'                   LowStockLevel,
                  //    low stock.
                  return "";
              }
              else
              {
                  switch($store_show_absent)
                  {
                      case STORE_SHOW_ABSENT_SHOW_BUY:
                          //                                  ,                     -
                          //
                          return "";
                          break;
                      case STORE_SHOW_ABSENT_SHOW_NOT_BUY:
                      case STORE_SHOW_ABSENT_NOT_SHOW_NOT_BUY:
                          //                        ,
                          //            LowStock -            Low Stock! Buy Now!
                          if($qty_in_stock  !== "" &&
                             $qty_in_stock < $low_stock_level &&
                             $qty_in_stock > 0)
                          {
                              $MessageResources = &$application->getInstance('MessageResources',"messages");
                              return $MessageResources->getMessage('LOW_STOCK_LEVEL_MESSAGE');
                          }
                          else
                          {
                              return "";
                          }
                          break;
                      default:
                          //STORE_SHOW_ABSENT_SHOW_BUY
                          return "";
                          break;
                  }
              }
              break;

          case strtolower($this->_fArtificialTagList['largeimagefile']):
              $_img_name = $this->_findEntityValueInAtributeList('a_view_tag', 'largeimage', 'image_name');
              return $_img_name;
              break;

          case strtolower($this->_fArtificialTagList['largeimagefilepath']):
              $_img_name = $this->_findEntityValueInAtributeList('a_view_tag', 'largeimage', 'image_name');
              $_img_path = $application->getAppIni('PATH_IMAGES_DIR') . $_img_name;
              return $_img_path;
              break;

          case strtolower($this->_fArtificialTagList['smallimagefile']):
              $_img_name = $this->_findEntityValueInAtributeList('a_view_tag', 'smallimage', 'image_name');
              return $_img_name;
              break;

          case strtolower($this->_fArtificialTagList['smallimagefilepath']):
              $_img_name = $this->_findEntityValueInAtributeList('a_view_tag', 'smallimage', 'image_name');
              $_img_path = $application->getAppIni('PATH_IMAGES_DIR') . $_img_name;
              return $_img_path;
              break;

          case strtolower($this->_fArtificialTagList['largeimagesrc']):
              $imagesUrl = $application->getAppIni('URL_IMAGES_DIR');
              if ($application->getCurrentProtocol() == "https" && $application->getAppIni('HTTPS_URL_IMAGES_DIR'))
              {
                  $imagesUrl = $application->getAppIni('HTTPS_URL_IMAGES_DIR');
              }
              $_img_name = $this->_findEntityValueInAtributeList('a_view_tag', 'largeimage', 'image_name');
              return $imagesUrl.$_img_name;
              break;

          case strtolower($this->_fArtificialTagList['largeimagewidth']):
              return $this->_findEntityValueInAtributeList('a_view_tag', 'largeimage', 'image_width');
              break;

          case strtolower($this->_fArtificialTagList['largeimageheight']):
              return $this->_findEntityValueInAtributeList('a_view_tag', 'largeimage', 'image_height');
              break;

          case strtolower($this->_fArtificialTagList['smallimagesrc']):
              $imagesUrl = $application->getAppIni('URL_IMAGES_DIR');
              if ($application->getCurrentProtocol() == "https" && $application->getAppIni('HTTPS_URL_IMAGES_DIR'))
              {
                  $imagesUrl = $application->getAppIni('HTTPS_URL_IMAGES_DIR');
              }
              $_img_name = $this->_findEntityValueInAtributeList('a_view_tag', 'smallimage', 'image_name');
              if(empty($_img_name)) $_img_name = 'noimage.png';
              return $imagesUrl.$_img_name;
              break;

          case strtolower($this->_fArtificialTagList['smallimagewidth']):
              return $this->_findEntityValueInAtributeList('a_view_tag', 'smallimage', 'image_width');
              break;

          case strtolower($this->_fArtificialTagList['smallimageheight']):
              return $this->_findEntityValueInAtributeList('a_view_tag', 'smallimage', 'image_height');
              break;

          #
          # Process real tags from attributes
          #
          default:
              return $this->_getTagValueFromAttributeList($tag);
              break;
       }
    }

    function getProductInfoLink($product_id, $category_id)
    {
		global $zone;
		if ($zone == 'CustomerZone')
		{
			$request = new Request();
            $product_type_id = $this->getProductTagValue("TypeID");
            if ($product_type_id == GC_PRODUCT_TYPE_ID)
            {
                $request->setView('GiftCertificate');
                return $request->getURL();
            }
			$request->setView  ( 'ProductInfo' );
			$request->setAction( 'SetCurrentProduct' );
			$request->setKey   ( 'prod_id', $product_id);
			$request->setCategoryID($category_id);
			$request->setProductID($product_id);
			return $request->getURL();
		}
		else
		{
            $request = new Request();
            $request->setView  ( 'Catalog_EditProduct' );
            $request->setAction( 'SetCurrentProduct' );
            $request->setKey   ( 'prod_id', $product_id );
			$request->setCategoryID($category_id);
			$request->setProductID($product_id);
            return $request->getURL();
		}
    }

    function whichStockControlMethod()
    {
        $opts = modApiFunc("Product_Options", "getOptionsList", 'product', $this->_fProductID, USED_FOR_INV);
        if (count($opts)>0 and modApiFunc("Product_Options","__hasEntityPrivilegesFor",'product','inventory'))
        {
            return PRODUCT_OPTIONS_INVENTORY_TRACKING;
        }
        else
        {
            return PRODUCT_QUANTITY_IN_STOCK_ATTRIBUTE;
        }
    }

    function getCategoriesIDs()
    {
        if (! isset($this->_categoriesIDs)) {
            $this->_categoriesIDs = array();
            $res = execQuery('SELECT_ALL_PRODUCT_CATEGORIES', array('product_id' => $this->_fProductID));
            foreach ($res as $row)
            {
                $this->_categoriesIDs[] = $row['category_id'];
            }
        }
        return $this->_categoriesIDs;
    }

    function chooseCategoryID()
    {
         global $application;
         $cats_ids = $this->getCategoriesIDs();
         $online_cats = array();
         foreach($cats_ids as $cat_id)
         {
            $cat = &$application->getInstance('CCategoryInfo', $cat_id);
            if($cat->getCategoryTagValue('RecursiveStatus') == CATEGORY_STATUS_ONLINE)
                $online_cats[] = $cat_id;
         };

         $target_category_id = null;
         if(count($online_cats) == 1)
         {
            $target_category_id = array_shift($online_cats);
         }
         elseif(count($online_cats) == 0)
         {
            $target_category_id = modApiFunc('CProductListFilter','getCurrentCategoryId');
         }
         else
         {
            $cur_cat_id = modApiFunc('CProductListFilter','getCurrentCategoryId');
            if($cur_cat_id!=null and in_array($cur_cat_id,$online_cats))
                $target_category_id = $cur_cat_id;
            else
                $target_category_id = array_shift($online_cats);
         };

         if($target_category_id == null)
         {
            $target_category_id = 1;
         };

         return $target_category_id;
    }

    /**
     *                                 online          .
     */
    function haveOnlineCategory()
    {
        global $application;

        #Product is of gift certificate product type (should not be added to any of available categories) so it is added to non-existant category
        if ($this->_fBaseProductInfo[0]['p_type_id'] == GC_PRODUCT_TYPE_ID)
        {
            return true;
        }

        $have_online = false;
        foreach($this->getCategoriesIDs() as $cat_id)
        {
            $cat = &$application->getInstance('CCategoryInfo', $cat_id);
            if($cat->getCategoryTagValue('RecursiveStatus') == CATEGORY_STATUS_ONLINE)
            {
                $have_online = true;
                break;
            };
        };

        return $have_online;
    }

    /**
     * Determines, if the attribute is visible for the specified tag in the product type.
     *
     * @param string $tag short tag name
     * @return boolean returns true if the attribute is visible for the specified tag,
     * specified tag, false otherwise.
     */
    function isProductTagVisible($tag)
    {
        # see the additional tags
        if (isset($this->_fAdditionalTagList[$tag]))
        {
            return true;
        }

    	$tag = strtolower($tag);

        # first see the base attributes
        if ($this->_findEntityValueInAtributeList('a_view_tag', $tag, 'a_visibility') == 1)
        {
            # find a precise visibility specification
            return true;
        }

        # then see the artificial attributes, they are all visible, except the derived ones
        switch($tag)
        {
            case strtolower($this->_fArtificialTagList['smallimagewidth']):
            case strtolower($this->_fArtificialTagList['smallimageheight']):
            case strtolower($this->_fArtificialTagList['smallimagesrc']):
            case strtolower($this->_fArtificialTagList['smallimagefilepath']):
            case strtolower($this->_fArtificialTagList['smallimagefile']):
                if ($this->_findEntityValueInAtributeList('a_view_tag', 'smallimage', 'a_visibility') == 1)
                {
                    return true;
                }
                break;

            case strtolower($this->_fArtificialTagList['largeimagewidth']):
            case strtolower($this->_fArtificialTagList['largeimageheight']):
            case strtolower($this->_fArtificialTagList['largeimagesrc']):
            case strtolower($this->_fArtificialTagList['largeimagefilepath']):
            case strtolower($this->_fArtificialTagList['largeimagefile']):
                if ($this->_findEntityValueInAtributeList('a_view_tag', 'largeimage', 'a_visibility') == 1)
                {
                    return true;
                }
                break;

            default:
                return true;
        }
    }

    /**
     * Returns the array of all tags and their values for the product.
     *
     * The array consisits only of those tags, for which product attributes
     * are visible in the product type.
     *
     * Besides, the returned array can be completed with the info about value
     * types of the tags. For each tag is defined two types: simple type and
     * semantic type. The simple type is a format for storing tag values,
     * for example, integer number, a string etc. The semantic type is everything,
     * that indicates the value of the tag, for example, currency, date etc.
     *
     * This method receives three in-parameters.
     *
     * $localized - whether to return tag values in the localized format.
     * Possible parameter values:
     * PRODUCTINFO_LOCALIZED_DATA - the values will be formatted according to
     * current application settings,
     * PRODUCTINFO_NOT_LOCALIZED_DATA - the values will be "as is" in the database.
     *
     * $tagNamesFormat - whether to use long tag names.
     * Possible values:
     * PRODUCTINFO_LONG_TAG_NAMES - use long tag names with the prifix Product,
     * PRODUCTINFO_SHORT_TAG_NAMES - use short tag names without any prefix.
     *
     * $addTagTypesInfo - whether to add the info about the types of tag values
     * to the result array.
     * POssible values:
     * PRODUCTINFO_WITH_TAG_TYPES - add,
     * PRODUCTINFO_NO_TAG_TYPES - do not add.
     *
     * The returnes value has the following format:
     * array
     * (
     *     '<tag name>' => '<tag value>',
     *     '<tag name>' => '<tag value>',
     *     ...
     *     '<tag name>' => '<tag value>',
     * )
     *
     * @param const $localized should the tag values be localized?
     * @param const $tagNamesFormat Should long and short tags be used?
     * @param const $addTagTypesInfo Should the type info be included in the
     * @return array Product tag hash (tags and values)
     */
    function getProductTagValuesHash($localized = PRODUCTINFO_DEFAULT_LOCALIZED_MODE,
                                     $tagNamesFormat = PRODUCTINFO_LONG_TAG_NAMES)
    {
        # Load attrubute values
        if ($this->_fProductAttributesInfo == null)
        {
            $this->_loadProductAttributesInfo();
        }

        $full_tags_list = array();

        # whether to use long tag names
        if ($tagNamesFormat == PRODUCTINFO_LONG_TAG_NAMES)
        {
        	$tag_prefix = 'Product';
        }
        else
        {
            $tag_prefix = '';
        }

        # Take the values of the standard visible attributes for this product
        foreach($this->_fProductAttributesInfo as $val)
        {
            if ($val['a_visibility'] == 1)
            {
                $full_tags_list[$tag_prefix.$val['a_view_tag']] = $this->getProductTagValue($val['a_view_tag'], $localized);
            }
        }

        # Take the artificial attribute values
        foreach($this->_fArtificialTagList as $tag)
        {
            if ($this->isProductTagVisible($tag))
            {
                $full_tags_list[$tag_prefix.$tag] = $this->getProductTagValue($tag, $localized);
            }
        }

        return $full_tags_list;
    }

    function getProductTagValuesExportHash($localized = PRODUCTINFO_DEFAULT_LOCALIZED_MODE, $artificial_tags = null)
    {
        # Load attrubute values
        if ($this->_fProductAttributesInfo == null)
        {
            $this->_loadProductAttributesInfo();
        }

        $full_tags_list = array();

       	$tag_prefix = 'Product';

        # Take the values of the standard visible attributes for this product
        foreach($this->_fProductAttributesInfo as $val)
        {
            if ($val['a_visibility'] == 1)
            {
                $full_tags_list[$tag_prefix.$val['a_view_tag']] = $this->getProductTagValue($val['a_view_tag'], $localized);
            }
        }

        # Take the artificial attribute values
        if ($artificial_tags === null)
            $artificial_tags = $this->_fArtificialTagList;
        foreach($artificial_tags as $tag)
        {
            $tag_value = $this->getProductTagValue($tag, $localized);
            if ($tag_value !== false)
                $full_tags_list[$tag_prefix.$tag] = $tag_value;
        }

        return $full_tags_list;
    }

    /**
     * Defines additional tags and their values in this object.
     * To get a specified value, it is used getProductTagValue(). The tags, defined
     * by this method are more priority, then the others, i.e. the tag values
     * can be overridden.
     *
     * Example of usage:
     * <code>
     *
     * // Create a product object with ID=1
     * $prd = new CProductInfo(1);
     *
     * // Get a product name
     * $product_name = $prd->getProductTagValue('Name');
     *
     * // Create a new tag 'Number' in the product with value '1'
     * $prd->setAdditionalProductTag('Number','1');
     *
     * // Override the product name
     * $prd->setAdditionalProductTag('Name','Product with ID = 1');
     *
     * </code>
     *
     * @param string $key Tag name
     * @param string $value Tag value
     */
    function setAdditionalProductTag($key, $value)
    {
        $this->_tag_value_cache = array();
        $this->_fAdditionalTagList[$key] = $value;
    }

    /**
     *                                                           :
     * array( 'TagName' => 'TagValue', ...)
     *
     *                                                                    .
     *
     *       :
     * <code>
     * //
     * $productInfo->setAdditionalProductTag('Checked', true);
     * $productInfo->setAdditionalProductTag('Number', $i);
     *
     * //                                        ,
     * //                                                               PHP
     * $application->registerAttributes( $productInfo->getAdditionalProductTagList() );
     * </code>
     *
     * @return array array( 'TagName' => 'TagValue', ...)
     */
    function getAdditionalProductTagList()
    {
        return $this->_fAdditionalTagList;
    }

    /**
     * Returns true if the specified tag exists in the given product object.
     * It searches among all the available tags:
     * - additional
     * - artificial
     * - in the attribute database
     *
     * @param string $tag Tag name
     * @return boolean Return true if tag name exists in the object
     */
    function isTagExists($tag)
    {
      /*
        If creating an object the incorrect id was passed in to the category, then
        this method will return false of any tag.
       */
       if ($this->_fProductIDIsIncorrect == true)
       {
           return ($this->debugmode ? 'Product ID is incorrect' : false);
       }

       if ($this->_fProductAttributesInfo == null)
       {
           $this->_loadProductAttributesInfo();
       }

       # Check additional tag list
       if ( array_key_exists( $tag, $this->_fAdditionalTagList ) )
       {
           return true;
       }

       # Check artificial tag list
       if ( array_key_exists( strtolower($tag), $this->_fArtificialTagList ) )
       {
           return true;
       }

       # Check database attributes
       if ( strtolower($this->_findEntityValueInAtributeList('a_view_tag',$tag,'a_view_tag')) == strtolower($tag))
       {
           return true;
       }

       return false;
    }

    /**
     * Creates a clone of the current product, but the object itself doesn't
     * switch to the cloned one.
     * It returns the created product id.
     *
     * @param int $newParentCatId Category ID where product should be cloned
     * @return int ID cloned product
     */
    function clone_to_category($newParentCatId)
    {
        global $application;


       /*
         If creating an object the incorrect id was passed, then
         this method will not do anything.
        */
        if ($this->_fProductIDIsIncorrect == true)
        {
            return ($this->debugmode ? 'Product ID is incorrect' : null);
        }

        if ($this->_fProductAttributesInfo == null)
        {
            $this->_loadProductAttributesInfo();
        }

        # check $newParentCatId

        # First create a new product in the table products
        modApiFunc('Catalog', 'insertNewProduct',
                   $this->_fBaseProductInfo[0]['p_type_id'],
                   $this->_fBaseProductInfo[0]['p_name']);
        $new_product_id = $application->db->DB_Insert_Id();

        // copy multilang product names
        $p_ml = modApiFunc('MultiLang', 'getMLTableData', 'Catalog', 'products', $this->_fBaseProductInfo[0]['p_id']);
        modApiFunc('MultiLang', 'addMLTableData', 'Catalog', 'products', $new_product_id, $p_ml);

        //<add_link>
        modApiFunc('Catalog','addProductLinkToCategory',$new_product_id,$newParentCatId);
        //</add_link>

        # Copies all the current product attributes
        # to the created product
        foreach ($this->_fProductAttributesInfo as $attribute)
        {
            //                          -       ,              $this->_fProductAttributesInfo
            //                               ,                                ,
            //                      product_attributes.
            if ($attribute['pa_id'] === null)
            {
                continue;
            }

            modApiFunc('Catalog', 'insertNewProductAttribute', $new_product_id, $attribute['a_id'], $attribute['pa_value']);
            $new_pa_id = $application->db->DB_Insert_Id();

            // copy multilang attribute values
            $pa_ml = modApiFunc('MultiLang', 'getMLTableData', 'Catalog', 'product_attributes', $attribute['pa_id']);
            modApiFunc('MultiLang', 'addMLTableData', 'Catalog', 'product_attributes', $new_pa_id, $pa_ml);

            //  if it is an image, save it to the separate table
            if (strtoupper($attribute['a_input_type_name']) == 'IMAGE')
            {
                if ($attribute['image_name'] &&
                    is_readable($application->getAppIni('PATH_IMAGES_DIR').$attribute['image_name']))
                {
                    # a temporary image name
                    $source_name = $attribute['image_name'];
                    $clone_name = basename($application->getUploadImageName($source_name));
                    # copy the current imge to the temporary file
                    $cr = copy($application->_img_path($source_name), $application->_img_path($clone_name));

                    # Clone the image (and delete a temporary one)
                    $image_info = $application->saveUploadImage("", $clone_name);

                    # create image info
                    modApiFunc('Catalog', 'insertNewProductImage', $new_pa_id, $image_info['name'], $image_info['width'], $image_info['height']);
                }
            }
        }
        modApiFunc('EventsManager','throwEvent','ProductCloned', array('pid'=>$this->_fProductID, 'new_pid'=>$new_product_id, 'cid'=>$newParentCatId));
        return $new_product_id;
    }


    /**#@-*/



    /**#@+
     * @access private
     */

    /**
     * Loads base product info from the DB, which contains the following fields:
     *
     * p_id - Product ID
     * p_name - Product Name
     * p_category_id - Product Category ID
     * p_date_updated - Product last date updated
     * p_date_added - Product date added to aatalog
     * p_category_name - Product Category Name
     * p_type_id - Product Type Id
     * p_type_name - Product Type Name
     *
     * After executing this method, the specified fields and their values
     * will be saved to the associative array $this->_fBaseProductInfo.
     *
     * Example of usage:
     * <code>
     *     $this->_loadBaseProductInfo()
     *     ...
     *     $product_name = $this->_fBaseProductInfo['p_name'];
     *     $product_category_id = $this->_fBaseProductInfo['p_category_id'];
     *     ...
     * </code>
     *
     */
    function _loadBaseProductInfo()
    {
        $a = (int)$this->_fProductID;
        if (isset(self::$_cache_base[$a])) {
            $this->_fBaseProductInfo = self::$_cache_base[$a];
            return;
        }
        $params = array('product_id'=>(int)$this->_fProductID);
        $this->_fBaseProductInfo = execQuery('SELECT_BASE_PRODUCT_INFO', $params);
        self::$_cache_base[$a] = $this->_fBaseProductInfo;
    }

    /**
     * Loads complete info about product attributes from the DB.
     * Th following fields are loaded:
     *
     * pa_id             - Product Attribute ID (from 'product_attributes' table)
     * pa_value          - Product Attribute Value (from 'product_attributes' table)
     * a_id              - Attribute ID (from 'attributes' table)
     * a_view_tag        - Attribute View Tag (from 'attributes' table)
     * a_allow_html      - Attribute Allow HTML Flag, 1 - allow, 0 - do not allow (from 'attributes' table)
     * a_input_type_id   - Attribute Input Type ID (from 'attributes' table)
     * a_input_type_name - Attribute Input Type Name (from 'input_types' table)
     * a_type            - Attribute Type, standard or custom (from 'attributes' table)
     * a_unit_type       - Attribute Unit Type (from 'attributes' table)
     * a_name            - Attribute Name (from 'attributes' table)
     * a_descr           - Attribute Description (from 'attributes' table)
     * a_min_value       - Attribute Min Value (from 'attributes' table)
     * a_max_value       - Attribute Max Value (from 'attributes' table)
     * a_html_size       - Attribute Html Size (from 'attributes' table)
     * a_visibility      - Attribute Visibility flag, 1 - visible (from 'product_type_attributes' table)
     * a_required        - Attribute Required flag, 1 - required (from 'product_type_attributes' table)
     * a_default_value   - Attribute Default Value (from 'product_type_attributes' table)
     * image_id          - Image Id
     * image_name        - Image Name
     * image_width       - Image Width
     * image_height      - Image Height
     *
     * All attribute info is entered in the array $_fProductAttributesInfo,
     * where each element is an associative array with specified keys.
     * As in the SQL is used LEFT JOIN, then some fields can be undefined.
     * E.g. the fields image_* will be defined only of such attribute, which
     * has a_input_type_name == 'IMAGE' and only if the product has images.
     *
     */
    function _loadProductAttributesInfo()
    {
        global $zone;

        if ($this->_fBaseProductInfo == null)
        {
            $this->_loadBaseProductInfo();
        }
        $display_product_price_including_taxes = modApiFunc('Settings', 'getParamValue', 'TAXES_PARAMS', "DISPLAY_PRICES_W_INCLUDED_TAXES");

        $a = (int)$this->_fProductID;
        $b = (int)$this->_fBaseProductInfo[0]['p_type_id'];
        $c = $display_product_price_including_taxes == DB_TRUE ? 1 : 0;
        if (isset(self::$_cache_attr[$a][$b][$c])) {
            $this->_fProductAttributesInfo = self::$_cache_attr[$a][$b][$c];
            return;
        }

        $params = array('product_id' => (int)$this->_fProductID,
                        'product_type_id' => (int)$this->_fBaseProductInfo[0]['p_type_id']);
        $this->_fProductAttributesInfo = execQuery('SELECT_PRODUCT_ATTRIBUTES_INFO', $params);
        //          SalePrice,
        //                                TaxClass.
        $tax_class_id = NULL;
        foreach ($this->_fProductAttributesInfo as $index => $attribute)
        {
            if ( strtolower($attribute['a_view_tag']) == 'taxclass' )
            {
                $tax_class_id = $attribute['pa_value'];
                break;
            }
        }

        $price_including_included_taxes_if_any = NULL;
        foreach ($this->_fProductAttributesInfo as $index => $attribute)
        {
            if ( strtolower($attribute['a_view_tag']) == 'saleprice' )
            {
                if($zone == 'CustomerZone')
                {
                    $membership = modApiFunc('Customer_Account','getCurrentSignedCustomerGroupID');
                    $attribute['pa_value'] = modApiFunc('Quantity_Discounts','getFixedPrice',$this->_fProductID,1,$attribute['pa_value'],$membership);
                }

                $price_including_included_taxes_if_any = $attribute['pa_value'];
                $this->_fProductAttributesInfo['salepriceexcludingtaxes'] = $this->_fProductAttributesInfo[$index];
                $this->_fProductAttributesInfo['salepriceexcludingtaxes']['pa_id'] = NULL;
                $this->_fProductAttributesInfo['salepriceexcludingtaxes']['a_view_tag'] = NULL;

                //     TaxClass             -        ,                     .
                if($tax_class_id !== NULL)
                {
                    //                                         .
                    $price_excluding_taxes = modApiFunc("Catalog", "computePriceExcludingTaxes", $attribute['pa_value'], $tax_class_id);
                    $this->_fProductAttributesInfo[$index]['pa_value'] = $price_excluding_taxes;

                    //              _   _                         'salepriceexcludingtaxes':
                    $this->_fProductAttributesInfo['salepriceexcludingtaxes']['pa_value'] =  $price_excluding_taxes;
                }

                $this->_fProductAttributesInfo['salepriceincludingtaxes'] = $this->_fProductAttributesInfo[$index];
                $this->_fProductAttributesInfo['salepriceincludingtaxes']['pa_id'] = NULL;
                $this->_fProductAttributesInfo['salepriceincludingtaxes']['a_view_tag'] = NULL;

                if($tax_class_id !== NULL)
                {
                    //                                         .
                    //              _c_                         'salepriceexcludingtaxes':
                    $price_including_taxes = $price_including_included_taxes_if_any;
                    //                                             ,
                    //        ,             "          "
                    //modApiFunc("Catalog", "computePriceIncludingTaxes", $attribute['pa_value'], $tax_class_id);

                    $this->_fProductAttributesInfo['salepriceincludingtaxes']['pa_value'] =  $price_including_taxes;

                    if($display_product_price_including_taxes == DB_TRUE)
                    {
                        $this->_fProductAttributesInfo[$index]['pa_value'] = $price_including_taxes;
                    }
                }

                break;
            }
        }
        self::$_cache_attr[$a][$b][$c] = $this->_fProductAttributesInfo;
    }

    function resetCache()
    {
        self::$_cache_base = array();
        self::$_cache_attr = array();
    }

    /**
     * Searches the specified tag in the list of product attributes and returns
     * its value. If the tag is not found, then an empty string is returned.
     * If the debug mode is on, then will be returned a string explainig why the
     * tag value is not defined.
     *
     * @param string $tag Short tag name
     * @return string Tag value
     */
    function _getTagValueFromAttributeList($tag)
    {
        global $application;

        /*
         Search the specified tag in the list of attributes
         search by the field a_view_tag
         */
        foreach ($this->_fProductAttributesInfo as $attribute)
        {
            if ( strtolower($attribute['a_view_tag']) == strtolower($tag) )
            {
                /*
                 First check if the attribute is visible in the product type,
                 if it is not, output an empty string
                 */
                if ( $attribute['a_visibility'] != 1 )
                {
                    if ( strtoupper($attribute['a_input_type_name']) == 'SELECT' )
                    {
                        /*
                         : Magic Number Present
                         Number 3 means in this case, that the value of this attribute
                         should be selected from the table input_type_values
                        */
                        if (($attribute['a_input_type_id'] == 3 ||
                             $attribute['a_input_type_id'] == 7))
                        {
                            if(!$this->localized and !in_array('select',$this->_force_localize_attr_types))
                            {
                                switch($attribute['a_input_type_id'])
                                {
                                    case "3":
                                        return PRODUCT_FREESHIPPING_YES;
                                        break;
                                    case "7":
                                        return PRODUCT_STATUS_ONLINE;
                                        break;
                                }
                            }
                            else
                            {
                                switch($attribute['a_input_type_id'])
                                {
                                    case "3":
                                        return getMsg('SYS','PRDTYPE_VALUE_YES');
                                        break;
                                    case "7":
                                        return getMsg('SYS','PRDTYPE_VALUE_STATUS_ONLINE');
                                        break;
                                }
                            }
                        }
                       /*
                         : Magic Number Present
                         Number 6 means here, that the value of this attribute
                         should be selected from the module Taxes
                        */
                        elseif ($attribute['a_input_type_id'] == 6)
                        {
                            return 0.0;
                        }
                       /*
                         : Magic Number Present
                         Number 9 means here, that the value of this attribute
                         should be selected from customer reviews
                        */
                        elseif ($attribute['a_input_type_id'] == 9)
                        {
                            return '';
                        }
                        else
                        {
                            return $this->debugmode ? 'Attribute is SELECT type, but the value is NULL or undefined in the database' : '';
                        }
                    }
                    else
                    {
                        return $this->debugmode ? 'Attribute is hidden in the product type: '.$tag : '';
                    }
                }

                /*
                 If it is an attribute of SELECT type, then its value
                 should be procesed differently
                  - finish the processing. The code is commented because,
                 the current value for the SELECT type attributes is saved to $attribute['pa_value']
                 and it will be got then by algorithm. Besides, id values are saved too, i.e.
                 get a real value from this id .
                 */
                if ( strtoupper($attribute['a_input_type_name']) == 'SELECT' )
                {
                    /*
                     : Magic Number Present
                     Number 3 means in this case, that the value of this attribute
                     should be selected from the table input_type_values
                    */
                    if (($attribute['a_input_type_id'] == 3 ||
                         $attribute['a_input_type_id'] == 7 ||
                         $attribute['a_input_type_id'] == 9 ||
                         $attribute['a_input_type_id'] == CTLG_INPUT_TYPE_MANUFACTURER)
                         && $attribute['pa_value'])
                    {
                        if(!$this->localized and !in_array('select',$this->_force_localize_attr_types))
                        {
                            return $attribute['pa_value'];
                        }
                        else
                        {
                            $params = array('a_input_type_id'=>$attribute['a_input_type_id'],
                                            'pa_value'=>$attribute['pa_value']);
                            $r = execQuery('SELECT_INPUT_TYPE_VALUES_BY_ATTRIBUTE',$params);
                            if (count($r) == 0)
                                return '';
                            return modApiFunc('Catalog', 'getInputTypeActualValue', $r[0]['value']);
                        }

                    }
                    //                  ,         ProductType Available      Invisible,
                    //                 ,
                    //          Available         (Visible).
                    //                   .            select'   3   7. (FreeShipping   Available)
                    //                        AZ ProdList
                    elseif (($attribute['a_input_type_id'] == 3 ||
                             $attribute['a_input_type_id'] == 7 ||
                             $attribute['a_input_type_id'] == CTLG_INPUT_TYPE_MANUFACTURER) &&
                             !$attribute['pa_value'])
                    {
                        if(!$this->localized)
                        {
                            switch($attribute['a_input_type_id'])
                            {
                                case "3":
                                    return PRODUCT_FREESHIPPING_YES;
                                    break;
                                case "7":
                                    return PRODUCT_STATUS_ONLINE;
                                    break;
                                case "". CTLG_INPUT_TYPE_MANUFACTURER:
                                	return MANUFACTURER_NOT_DEFINED;
                                	break;
                            }
                        }
                        else
                        {
                            switch($attribute['a_input_type_id'])
                            {
                                case "3":
                                    return getMsg('SYS','PRDTYPE_VALUE_YES');
                                    break;
                                case "7":
                                    return getMsg('SYS','PRDTYPE_VALUE_STATUS_ONLINE');
                                    break;
                                case "". CTLG_INPUT_TYPE_MANUFACTURER:
                                    return getMsg('MNF', 'MANUFACTURER_NOT_DEFINED');
                                    break;
                            }
                        }
                    }
                   /*
                     : Magic Number Present
                     Number 6 means here, that the value of this attribute
                     should be selected from the module Taxes
                    */
                    elseif ($attribute['a_input_type_id'] == 6 && $attribute['pa_value'])
                    {
                        $tax_class_info = modApiFunc('Taxes','getProductTaxClassInfo', $attribute['pa_value']);
                        return $tax_class_info['value'];
                    }
                   /*
                     : Magic Number Present
                     Number 9 means here, that the value of this attribute
                     should be selected from customer reviews
                    */
                    elseif ($attribute['a_input_type_id'] == 9 && $attribute['pa_value'])
                    {
                        switch($attribute['pa_value'])
                        {
                            case PRODUCT_CUSTOMER_REVIEWS_MESSAGE_RATE:
                                return getMsg('SYS', 'PRDTYPE_VALUE_REVIEW_RATE');
                                break;
                            case PRODUCT_CUSTOMER_REVIEWS_MESSAGE:
                                return getMsg('SYS', 'PRDTYPE_VALUE_REVIEW');
                                break;
                            case PRODUCT_CUSTOMER_REVIEWS_RATE:
                                return getMsg('SYS', 'PRDTYPE_VALUE_RATE');
                                break;
                            case PRODUCT_CUSTOMER_REVIEWS_NOREVIEW:
                                return getMsg('SYS', 'PRDTYPE_VALUE_NOREVIEW');
                                break;
                        }
                    }
                    else
                    {
                        $params = array('a_input_type_id'=>$attribute['a_input_type_id'], 'pa_value'=>$attribute['pa_value']);
                        $r = execQuery('SELECT_INPUT_TYPE_VALUES_BY_ATTRIBUTE',$params);
                        if (count($r) == 0)
                            {
                            return $this->debugmode ? 'Attribute is SELECT type, but the value is NULL or undefined in the database' : '';
                            }
                        return modApiFunc('Catalog', 'getInputTypeActualValue', $r[0]['value']);
                    }

                }

                /*
                                       IMAGE,
                                                 :
                                     HTML     <IMG>
                 */
                if ( strtoupper($attribute['a_input_type_name']) == 'IMAGE' )
                {
                    $imagesUrl = $application->getAppIni('URL_IMAGES_DIR');
                    if ($application->getCurrentProtocol() == "https" && $application->getAppIni('HTTPS_URL_IMAGES_DIR'))
                    {
                        $imagesUrl = $application->getAppIni('HTTPS_URL_IMAGES_DIR');
                    }
                    $_src    = $imagesUrl.$attribute['image_name'];
                    $_width  = $attribute['image_width'];
                    $_height = $attribute['image_height'];
                    $_alt    = prepareHTMLDisplay($this->_getTagValueFromAttributeList('ImageAltText'));

                    if ($attribute['image_name'] != null && $application->isImageFileValid($attribute['image_name']) )
                    {
                        $_res = 'img src="'.$_src.'" height="'.$_height.'" width="'.$_width.'" alt="'.$_alt.'"';
                        return $this->debugmode ? '['.$_res.']' : '<'.$_res.'/>';
                    }
                    else
                    {
                        return $this->debugmode ? 'Image Does Not Exist' : '';
                    }
                }

                # If the tag value is specified, then process it
                if (!($attribute['pa_value'] === NULL))
                {
                    /*
                     If the output of pure HTML code is inhibited
                     and the localization is turned on
                     */
                    if ($attribute['a_allow_html'] != 1 && $this->localized)
                    {
                        $_val = modApiFunc("Localization", "format", $attribute['pa_value'], $attribute['a_unit_type']);

                        /*
                          change
                         Localization::format() itself should substitute a unit
                         */
                        global $__localization_disable_formatting__;
                        if ($attribute['a_unit_type']!='currency' && $__localization_disable_formatting__ == false)
                        {
                            $_val .= rtrim(' '.modApiFunc("Localization", "getUnitTypeValue", $attribute['a_unit_type']));
                            //patch to allow single unicode character as currency symbol
                            $_val = prepareHTMLDisplay($_val);
                        }

                        return $_val;
                    }
                    # Otherwise output the attribute value as is
                    else
                    {
                        return $attribute['pa_value'];
                    }
                }
                # If the tag value is undefined, return an empty string
                else
                {
                    return $this->debugmode ? 'Tag value is NULL or undefined: '.$tag : '';
                }

                # Finish the cycle
                break;
            }
        }

        /*
         If the attribute with the specified tag is not found, return an empty
         string, i.e. the tag will output null
         */
        return $this->debugmode ? 'Tag Value Not Found: '.$tag : null;
    }

    /**
     * Searches the specified key $FindWhatEntityName and its value
     * $FindWhatEntityValue in the attribute list. It returns the value of the
     * specified key $ReturnWhatEntityName for the found attribute.
     *
     * If the specified key $FindWhatEntityName or its value $FindWhatEntityValue,
     * or the key $ReturnWhatEntityName are not found, an empty string will be
     * returned
     *
     * Example:
     * <code>
     *
     *  // Search an attribute in the attribute list, whose key 'a_view_tag'
     *  // equals 'largeimage',
     *  // get from this attribute the key value 'image_width'
     *  $image_width = $this->_findEntityValueInAtributeList('a_view_tag',
     *  'largeimage', 'image_width') ;
     *
     * </code>
     *
     * @param string $FindWhatEntityName The key name to search by
     * @param string $FindWhatEntityValue The key value
     * @param string $ReturnWhatEntityName The key name, whose value will be
     *       returned in the found attribute
     * @return string The key value
     */
    function _findEntityValueInAtributeList($FindWhatEntityName, $FindWhatEntityValue, $ReturnWhatEntityName)
    {
        $product_type_id = $this->_fBaseProductInfo[0]['p_type_id'];
        $r = '';
        if($product_type_id == GC_PRODUCT_TYPE_ID
            && $FindWhatEntityName=='a_view_tag'
            && ($FindWhatEntityValue=='largeimage' || $FindWhatEntityValue=='smallimage'))
        {
            $imageInfo = modApiFunc('GiftCertificateApi', 'getImageInfo');
            if($imageInfo[$FindWhatEntityValue]['is_exist']===true)
            {
                switch($ReturnWhatEntityName)
                {
                    case 'image_name':
                        $r = $imageInfo[$FindWhatEntityValue]['url'];
                    break;
                    case 'image_width':
                        $r = $imageInfo[$FindWhatEntityValue]['width'];
                    break;
                    case 'image_height':
                        $r = $imageInfo[$FindWhatEntityValue]['height'];
                    break;
                }
            }

            return $r;
        }

        foreach ($this->_fProductAttributesInfo as $attribute)
        {
            if (strtolower($attribute[$FindWhatEntityName]) == strtolower($FindWhatEntityValue))
            {
                if (array_key_exists(strtolower($ReturnWhatEntityName), $attribute))
                {
                	$r = $attribute[strtolower($ReturnWhatEntityName)];

                	//                       $r                  null.                                                     .
                	//                                              null                                         .
                	//             :              null,           getTag                                null,      
                	//                   info-                         ,                                               
                	//                                                   null.                          .
                	if ($r === null) return '';
                	else return $r;
                }
                else
                {
                	return '';
                }
            }
        }
        return '';
    }

    function setForceLocalizeAttrTypes($arr)
    {
        $this->_force_localize_attr_types = $arr;
    }

    function _destruct()
    {
        $this->_fProductIDIsIncorrect = null;
        $this->_fBaseProductInfo = null;
        $this->_fProductAttributesInfo = null;
        $this->localized = null;
        $this->debugmode = null;
        $this->_fAdditionalTagList = null;
        $this->_fArtificialTagList = null;
        $this->_categoriesIDs = null;
        $this->_fProductID = null;
    }

    var $_fProductID = null;
    var $_fProductIDIsIncorrect = false;
    var $_fBaseProductInfo = null;
    var $_fProductAttributesInfo = null;
    var $localized = true;
    var $debugmode = false;
    var $_fAdditionalTagList = array();
    var $_force_localize_attr_types = array();

    # : this list should be defined not here, but somewhere else in the module,
    #        because not this class gives the tags, but the whole module.
    #
    # The list of the artificial tags.
    # If change, correct this array and the method getProductTagValue.
    var $_fArtificialTagList = array( 'ID'
                                     ,'Name'
                                     ,'TypeID'
                                     ,'TypeName'
                                     ,'Updated'
                                     ,'Added'
                                     ,'CategoryID'
//                                     ,'CategoryName'
                                     ,'InfoLink'
                                     ,'BuyLink'
                                     ,'CategoryLink'
                                     ,'CategoryPath' // returns the array
                                     ,'AllCategoryPath'
                                     ,'LargeImageFile'
                                     ,'LargeImageFilePath'
                                     ,'LargeImageSrc'
                                     ,'LargeImageWidth'
                                     ,'LargeImageHeight'
                                     ,'SmallImageFile'
                                     ,'SmallImageFilePath'
                                     ,'SmallImageSrc'
                                     ,'SmallImageWidth'
                                     ,'SmallImageHeight'
                                     ,'StockMessage'
                                     );

    var $_categoriesIDs;

    static $_cache_base = array();
    static $_cache_attr = array();

    /**#@-*/

}

?>