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



/**
 * The class gives complete information about category.
 * To get this information, it's necessary to create the class object
 * and invoke the method getCategoryTagValue, specifying the tag.
 *
 * The class interface:
 * CCategoryInfo::CCategoryInfo($cid, $localized = CATEGORYINFO_DEFAULT_LOCALIZED_MODE)
 * CCategoryInfo::turnOnDebugMode()
 * CCategoryInfo::turnOffDebugMode()
 * CCategoryInfo::getCategoryTagValue($tag, $localized = CATEGORYINFO_DEFAULT_LOCALIZED_MODE)
 *
 * Example of usage:
 * <code>
 *  $category = new CCategoryInfo(8);
 *  $category_name = $category->getCategoryTagValue('name');
 * </code>
 *
 * @package Catalog
 * @author Alexey Florinsky
 */
class CCategoryInfo
{

    /**#@+
     * @access public
     */


    /**
	 * The class constructor.
	 * The object will be created only if the specified $cid (Category ID)
	 * is a correct value and such category exists in the DB.
	 *
	 * @param int $cid Category ID
	 * @param int $localized The parameter defines, whether to return localized
	 * values or to send as values the constants CATEGORYINFO_LOCALIZED_DATA or
	 * CATEGORYINFO_NOT_LOCALIZED_DATA directly from the DB
     * @return CCategoryInfo object or null
	 */
    function CCategoryInfo($cid, $localized = CATEGORYINFO_DEFAULT_LOCALIZED_MODE)
    {
		# If the product id is not correct, then the object will return the empty tag values
	    if ($cid == null || !is_numeric($cid) || !modApiFunc('Catalog', 'isCorrectCategoryId', $cid))
		{
		    $this->_fCategoryIDIsIncorrect = true;
		}

		$this->_fCategoryID = $cid;

		if  ($localized == CATEGORYINFO_LOCALIZED_DATA)
	    {
	        $this->localized = true;
	    }
	    else
	    {
	        $this->localized = false;
	    }

	    $this->debugmode = false;

	    # Flip the keys of the array _fArtificialTagList, i.e.
	    # the key values will equal the key itself.
	    # It is necessary to make it easier to use the array
	    $_tmp = $this->_fArtificialTagList;
	    $this->_fArtificialTagList = array();
	    foreach ($_tmp as $tag)
	    {
	        $this->_fArtificialTagList[$tag] = $tag;
	    }

        # Load base category info.
        $this->_fBaseCategoryInfo = array(modapiFunc('Catalog', 'fetchBaseCategoryInfo', $this->_fCategoryID));
    }

    /**
	 * Turns on the debug mode. For the attributes whose values are not defined
	 * will be returned a text message instead of empty string, explaining why
	 * the values are not defined.
	 *
	 * @see CCategoryInfo::turnOffDebugMode()
	 *
	 */
	function turnOnDebugMode()
	{
	    $this->debugmode = true;
	}

    /**
	 * Turns off the debug mode.
	 *
	 * @see CCategoryInfo::turnOnDebugMode()
	 *
	 */
	function turnOffDebugMode()
	{
	    $this->debugmode = false;
	}

    /**
     * Returns Category info tag value.
     *
     * @param string $tag Short tag name
	 * @param int $localized The parameter defines, whether to return localized
	 * values or to send as values the constants CATEGORYINFO_LOCALIZED_DATA or
	 * CATEGORYINFO_NOT_LOCALIZED_DATA directly from the DB
	 * @return string Tag value
	 **/
    function getCategoryTagValue($tag, $localized = CATEGORYINFO_DEFAULT_LOCALIZED_MODE)
    {
	   global $application;

      /*
        If creating an object the incorrect id was passed to the category, then
        it will return an empty value of any tag.
       */
       if ($this->_fCategoryIDIsIncorrect == true)
       {
           return ($this->debugmode ? 'Category ID is incorrect' : '');
       }

       # Check additional tags
       if (array_key_exists($tag, $this->_fAdditionalTagList))
       {
           return $this->_fAdditionalTagList[$tag];
       }

       # Check localization request
       if  ($localized == CATEGORYINFO_LOCALIZED_DATA)
	   {
	       $this->localized = true;
	   }
	   elseif ($localized == CATEGORYINFO_NOT_LOCALIZED_DATA)
	   {
	       $this->localized = false;
	   }

       # Get Tag value
	   $tag = _ml_strtolower(trim($tag));
       switch ($tag)
       {
          #
          # Process artificial tags
          #
          case $this->_fArtificialTagList['id']:
              return $this->_fCategoryID;
              break;

          case $this->_fArtificialTagList['name']:
              return $this->localized ? prepareHTMLDisplay($this->_fBaseCategoryInfo[0]['name']) : $this->_fBaseCategoryInfo[0]['name'];
              break;

          case $this->_fArtificialTagList['status']:
              $cat_status = "";
              $MessageResources = &$application->getInstance('MessageResources');
              switch($this->_fBaseCategoryInfo[0]['status'])
              {
                  case CATEGORY_STATUS_ONLINE:
                      $cat_status = $MessageResources->getMessage('CAT_STATUS_ONLINE');
                      break;
                  case CATEGORY_STATUS_OFFLINE:
                      $cat_status = $MessageResources->getMessage('CAT_STATUS_OFFLINE');
                      break;
                  default:
                      //: report error
                      $cat_status = "";
              }
              return $this->localized ? prepareHTMLDisplay($cat_status) : $this->_fBaseCategoryInfo[0]['status'];

              break;

          case $this->_fArtificialTagList['description']:
              return $this->localized ? prepareHTMLDisplay($this->_fBaseCategoryInfo[0]['descr']) : $this->_fBaseCategoryInfo[0]['descr'];
              break;

          case $this->_fArtificialTagList['largeimage']:
              if ($this->_fImagesData === null)
              {
                $this->_getImageInfo();
              }
              $_src = $this->_fImagesData['largeimage_file']['URL'];
              $_width = $this->_fImagesData['largeimage_file']['WIDTH'];
              $_height = $this->_fImagesData['largeimage_file']['HEIGHT'];
              $_alttext = $this->getCategoryTagValue('imagealttext');
              return '<img src="'.$_src.'" width="'.$_width.'" height="'.$_height.'" alt="'.$_alttext.'" />';
              break;

          case $this->_fArtificialTagList['largeimagesrc']:
              if ($this->_fImagesData === null)
              {
                $this->_getImageInfo();
              }
              return $this->_fImagesData['largeimage_file']['URL'];
              break;

          case $this->_fArtificialTagList['largeimagewidth']:
              if ($this->_fImagesData === null)
              {
                $this->_getImageInfo();
              }
              return $this->_fImagesData['largeimage_file']['WIDTH'];
              break;

          case $this->_fArtificialTagList['largeimageheight']:
              if ($this->_fImagesData === null)
              {
                $this->_getImageInfo();
              }
              return $this->_fImagesData['largeimage_file']['HEIGHT'];
              break;

          case $this->_fArtificialTagList['smallimage']:
              if ($this->_fImagesData === null)
              {
                $this->_getImageInfo();
              }
              $_src = $this->_fImagesData['smallimage_file']['URL'];
              $_width = $this->_fImagesData['smallimage_file']['WIDTH'];
              $_height = $this->_fImagesData['smallimage_file']['HEIGHT'];
              $_alttext = $this->getCategoryTagValue('imagealttext');
              return '<img src="'.$_src.'" width="'.$_width.'" height="'.$_height.'" alt="'.$_alttext.'" />';
              break;

          case $this->_fArtificialTagList['smallimagesrc']:
              if ($this->_fImagesData === null)
              {
                $this->_getImageInfo();
              }
              return $this->_fImagesData['smallimage_file']['URL'];
              break;

          case $this->_fArtificialTagList['smallimagewidth']:
              if ($this->_fImagesData === null)
              {
                $this->_getImageInfo();
              }
              return $this->_fImagesData['smallimage_file']['WIDTH'];
              break;

          case $this->_fArtificialTagList['smallimageheight']:
              if ($this->_fImagesData === null)
              {
                $this->_getImageInfo();
              }
              return $this->_fImagesData['smallimage_file']['HEIGHT'];
              break;

          case $this->_fArtificialTagList['imagealttext']:
              return $this->localized ? prepareHTMLDisplay($this->_fBaseCategoryInfo[0]['image_descr']) : $this->_fBaseCategoryInfo[0]['image_descr'];
              break;

          case $this->_fArtificialTagList['pagetitle']:
              return $this->localized ? prepareHTMLDisplay($this->_fBaseCategoryInfo[0]['page_title']) : $this->_fBaseCategoryInfo[0]['page_title'];
              break;

          case $this->_fArtificialTagList['metakeywords']:
              return $this->localized ? prepareHTMLDisplay($this->_fBaseCategoryInfo[0]['meta_keywords']) : $this->_fBaseCategoryInfo[0]['meta_keywords'];
              break;

          case $this->_fArtificialTagList['metadescription']:
              return $this->localized ? prepareHTMLDisplay($this->_fBaseCategoryInfo[0]['meta_descr']) : $this->_fBaseCategoryInfo[0]['meta_descr'];
              break;

          case $this->_fArtificialTagList['productsnumber']:
              //               :                                     ,
              //
              if ($this->getShowProductsRecursivelyStatus() == CATEGORY_SHOW_PRODUCTS_RECURSIVELY)
              {
                  $select_mode_recursiveness = IN_CATEGORY_RECURSIVELY;
              }
              else
              {
                  $select_mode_recursiveness = IN_CATEGORY_ONLY;
              }
              return $this->_getProductsNumberInCategory($select_mode_recursiveness, UNIQUE_PRODUCTS);
              break;

          case $this->_fArtificialTagList['productsnumberrecursively']:
              return $this->_getProductsNumberInCategory(IN_CATEGORY_RECURSIVELY, UNIQUE_PRODUCTS);
              break;

          case $this->_fArtificialTagList['productsnumber_non_recursively']:
              return $this->_getProductsNumberInCategory(IN_CATEGORY_ONLY, UNIQUE_PRODUCTS);
              break;

          case $this->_fArtificialTagList['productsnumberrecursively_all_product_links']:
              return $this->_getProductsNumberInCategory(IN_CATEGORY_RECURSIVELY, ALL_PRODUCT_LINKS);
              break;

          case $this->_fArtificialTagList['offlineandonlinesubcategoriesnumber']:
              if ($this->_fSubcategoriesNumber == null)
              {
                  $this->_fSubcategoriesNumber =
                      sizeof(modApiFunc('Catalog', 'getSubcategoriesFullListWithParent', $this->_fBaseCategoryInfo[0]['id'], false)) - 1;
              }
              return $this->_fSubcategoriesNumber;

          case $this->_fArtificialTagList['hasonlinesubcategories']:
              return modApiFunc('Catalog', 'hasOnlineSubcategories', $this->_fBaseCategoryInfo[0]['id']);

          case $this->_fArtificialTagList['subcategoriesnumber']:
              $OnlineSubCategories = $this->_getOnlineSubcategories();
              $n_subcategories_including_current = sizeof($OnlineSubCategories);
              //                 (Navigator AZ)                                      ,
              //                              .
              //             _loadOnlineSubCategories               Id      Online             ,
              //       id                  ,          Online,        .
              return $n_subcategories_including_current > 0 ?
                  $n_subcategories_including_current - 1 : // current is online
                  $n_subcategories_including_current; // current is itself offline

              break;

          case $this->_fArtificialTagList['left']:
              return $this->_fBaseCategoryInfo[0]['left1'];
              break;

          case $this->_fArtificialTagList['right']:
              return $this->_fBaseCategoryInfo[0]['right1'];
              break;

          case $this->_fArtificialTagList['level']:
              return $this->_fBaseCategoryInfo[0]['level'];
              break;

          case $this->_fArtificialTagList['parentid']:
              if ($this->_fParentCategoryID == null)
              {
                  $this->_fParentCategoryID = modApiFunc('Catalog', 'getParentCategoryId', $this->_fCategoryID);
              }
              return $this->_fParentCategoryID;
              break;

          case $this->_fArtificialTagList['recursivestatus']:
              $this->_loadRecursiveStatus();
              $cat_status = "";
              $MessageResources = &$application->getInstance('MessageResources');
              switch($this->_fRecursiveStatus)
              {
                  case CATEGORY_STATUS_ONLINE:
                      $cat_status = $MessageResources->getMessage('CAT_STATUS_ONLINE');
                      break;
                  case CATEGORY_STATUS_OFFLINE:
                      $cat_status = $MessageResources->getMessage('CAT_STATUS_OFFLINE');
                      break;
                  default:
                      //: report error
                      $cat_status = "";
              }
              return $this->localized ? prepareHTMLDisplay($cat_status) : $this->_fRecursiveStatus;
              break;

          case $this->_fArtificialTagList['link']:
              $_request = new Request();
              $_request->setView  ( 'NavigationBar' );
              $_request->setAction( "SetCurrCat" );
              $_request->setKey   ( "category_id", $this->_fCategoryID );
              $_request->setCategoryID($this->_fCategoryID);
              $_url = $_request->getURL();
              return $_url;
              break;

          case $this->_fArtificialTagList['seo_url_prefix']:
              return $this->_fBaseCategoryInfo[0]['seo_url_prefix'];
              break;

          case $this->_fArtificialTagList['showproductsrecursivelystatus']:
              $raw_status = $this->_fBaseCategoryInfo[0]['show_prod_recurs'];
              $obj = &$application->getInstance('MessageResources');
              if ($this->localized )
              {
                  if ($raw_status == CATEGORY_DONOTSHOW_PRODUCTS_RECURSIVELY)
                  {
                      return $obj->getMessage('CATEGORY_DONOTSHOW_PRODUCTS_RECURSIVELY');
                  }
                  else
                  {
                      return $obj->getMessage('CATEGORY_SHOW_PRODUCTS_RECURSIVELY');
                  }
              }
              else
              {
                  return $raw_status;
              }
              break;

          # If the tag is not defined, return an empty string.
          default:
              return $this->debugmode ? 'The tag is undefined:'.$tag : '';
       }

    }

    /**
     *                           :
     * CATEGORY_SHOW_PRODUCTS_RECURSIVELY     CATEGORY_DONOTSHOW_PRODUCTS_RECURSIVELY.
     *
     *           CATEGORY_DONOTSHOW_PRODUCTS_RECURSIVELY         ,
     *                           "
     *                         ,                                           ".
     *
     *            CATEGORY_SHOW_PRODUCTS_RECURSIVELY         ,
     *                           "
     *                            ".
     *
     *                   (18      2007)                                    storefront
     *                               .
     */
    function getShowProductsRecursivelyStatus()
    {
        return $this->_fBaseCategoryInfo[0]['show_prod_recurs'];
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
     * if ($prd === null) die('Product does not exists');
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
    function setAdditionalCategoryTag($key, $value)
    {
        $this->_fAdditionalTagList[$key] = $value;
    }

    /**
     * Returns the associative array of the additional tags:
     * array( 'TagName' => 'TagValue', ...)
     *
     * This method is used to log the additional tags in the system.
     *
     * An example:
     * <code>
     * // Define additional tags
     * $productInfo->setAdditionalProductTag('Checked', true);
     * $productInfo->setAdditionalProductTag('Number', $i);
     *
     * // Log the additional tags, after logging they are available in the
     * // templates as PHP functions.
     * $application->registerAttributes( $productInfo->getAdditionalProductTagList() );
     * </code>
     *
     * @return array array( 'TagName' => 'TagValue', ...)
     */
    function getAdditionalCategoryTagList()
    {
        return $this->_fAdditionalTagList;
    }

    /**
     * Returns true if the specified tag exists in the given product object.
     * It searches among all the available tags:
     * - additional
     * - artificial
     * - in the attribute database.
     *
     * @param string $tag Tag name
     * @return boolean Return true if tag name exists in the object
     */
    function isTagExists($tag)
    {
       /*
        If creating an object the incorrect id was passed to the category, then
        the method will return false for any tag.
       */
       if ($this->_fCategoryIDIsIncorrect == true)
       {
           return ($this->debugmode ? 'Category ID is incorrect' : false);
       }

       # Check additional tag list
       if ( array_key_exists( $tag, $this->_fAdditionalTagList ) )
       {
           return true;
       }

       # Check artificial tag list
       if ( array_key_exists( _ml_strtolower($tag), $this->_fArtificialTagList ) )
       {
           return true;
       }

       return false;
    }


    /**#@-*/



    /**#@+
     * @access private
     */

    /**
     * Initializes the field of the class _fImagesData.
     * After executing this method the field $this->_fImagesData looks as follows:
     *
     * $this->_fImagesData = array('largeimage_file' => array('WIDTH' => XXX,
     *                                                        'HEIGHT' => XXX,
     *                                                        'URL' => '[url]'),
     *                             'smallimage_file' => array('WIDTH' => XXX,
     *                                                        'HEIGHT' => XXX,
     *                                                        'URL' => '[url]')
     *                                  );
     */
    function _getImageInfo()
    {
        global $application;

        if ($this->_fImagesData === null)
        {

            /* Define default values, in case the category doesn't have
               any images   */
            $this->_fImagesData = array('largeimage_file' => array('WIDTH' => 1,
                                                                   'HEIGHT' => 1,
                                                                   'URL' => ''),
                                        'smallimage_file' => array('WIDTH' => 1,
                                                                   'HEIGHT' => 1,
                                                                   'URL' => '')
                                       );

            /* Create a cycle, not to duplicate the following code*/
            $_cycle = array('largeimage_file', 'smallimage_file');

            $imagesUrl = $application->getAppIni('URL_IMAGES_DIR');
            if ($application->getCurrentProtocol() == "https" && $application->getAppIni('HTTPS_URL_IMAGES_DIR'))
            {
                $imagesUrl = $application->getAppIni('HTTPS_URL_IMAGES_DIR');
            }
            foreach ($_cycle as $_image_file_type)
            {
                if ( !empty($this->_fBaseCategoryInfo[0][$_image_file_type])
                     &&
                     $application->isImageFileValid($this->_fBaseCategoryInfo[0][$_image_file_type]) )
                {
                    $size = @getimagesize($application->getAppIni('PATH_IMAGES_DIR')
                            .$this->_fBaseCategoryInfo[0][$_image_file_type]);

                    $this->_fImagesData[$_image_file_type]['WIDTH']  = $size[0];
                    $this->_fImagesData[$_image_file_type]['HEIGHT'] = $size[1];
                    $this->_fImagesData[$_image_file_type]['URL']    = $imagesUrl.$this->_fBaseCategoryInfo[0][$_image_file_type];
                }
            }
        }
    }

    /**
     * Returns the product number in the category.
     * The parameter $calc_mode defines the method of calculating the product number:
     * IN_CATEGORY_ONLY - calculating the product number in the current category only,
     * IN_CATEGORY_RECURSIVELY - calculating the product number in the current
     * category and in all the nested ones
     * IN_CATEGORY_RECURSIVELY_WITHOUT_CURRENT - calculating the product number
     * in the nested categories only
     *
     * It invokes the method $this->_loadProductsNumberInCategory() to execute
     * SQL queries. The data are saved in the field $this->_fProductsNumberInCategory.
     * That means the next invocations of this method will not call the database
     * access.
     *
     * The product number, which will be returned, depends on the current zone.
     * If the current zone is Customer Zone, then it will be calculated only the
     * products, which:
     * - attribute Available is installed in Yes,
     * - attribute Available is installed in the empty string,
     * - attribute Available can't be seen in the product type.
     *
     * If the current zone is Admin Zone, then all products will be calculated.
     *
     * @see CCategoryInfo::_loadProductsNumberInCategory()
     * @param int $calc_mode The constant IN_CATEGORY_ONLY, IN_CATEGORY_RECURSIVELY
     *        or IN_CATEGORY_RECURSIVELY_WITHOUT_CURRENT
     * @return int the number of the products in the current category
     */
    function _getProductsNumberInCategory($select_mode_recursiveness = IN_CATEGORY_ONLY, $select_mode_uniqueness = UNIQUE_PRODUCTS)
    {
        switch ($select_mode_recursiveness)
        {
            case IN_CATEGORY_RECURSIVELY:
                if ( !isset($this->_fProductsNumberInCategory[IN_CATEGORY_RECURSIVELY.'-'.$select_mode_uniqueness]) )
                {
                    $this->_fProductsNumberInCategory[IN_CATEGORY_RECURSIVELY.'-'.$select_mode_uniqueness] = $this->_loadProductsNumberInCategory(IN_CATEGORY_RECURSIVELY, $select_mode_uniqueness);
                }
                return $this->_fProductsNumberInCategory[IN_CATEGORY_RECURSIVELY.'-'.$select_mode_uniqueness];
                break;

            case IN_CATEGORY_RECURSIVELY_WITHOUT_CURRENT:
                if ( !isset($this->_fProductsNumberInCategory[IN_CATEGORY_RECURSIVELY_WITHOUT_CURRENT.'-'.$select_mode_uniqueness]) )
                {
                    $this->_fProductsNumberInCategory[IN_CATEGORY_RECURSIVELY_WITHOUT_CURRENT.'-'.$select_mode_uniqueness] = $this->_loadProductsNumberInCategory(IN_CATEGORY_RECURSIVELY_WITHOUT_CURRENT, $select_mode_uniqueness);
                }
                return $this->_fProductsNumberInCategory[IN_CATEGORY_RECURSIVELY_WITHOUT_CURRENT.'-'.$select_mode_uniqueness];
                break;

            default:
                if ( !isset($this->_fProductsNumberInCategory[IN_CATEGORY_ONLY.'-'.$select_mode_uniqueness]) )
                {
                    $this->_fProductsNumberInCategory[IN_CATEGORY_ONLY.'-'.$select_mode_uniqueness] = $this->_loadProductsNumberInCategory(IN_CATEGORY_ONLY, $select_mode_uniqueness);
                }
                return $this->_fProductsNumberInCategory[IN_CATEGORY_ONLY.'-'.$select_mode_uniqueness];
                break;
        }
    }

    /**
     * Executes SQL query, which recursively computes category status: Online/Offline.
     * If parent category is Offline then child's status will be offline too.
     * $this->_fRecursiveStatus.
     *
     */
    function _loadRecursiveStatus()
    {
        if ($this->_fRecursiveStatus == null) {
            $this->_fRecursiveStatus = modApiFunc('Catalog', 'getCategoryRecursiveStatus', $this->_fBaseCategoryInfo[0]['id']);
        }
    }

    /**
     * Executes SQL query, which returns the product number in the current
     * category.
     *
     * The product number, which will be returned, depends on the current zone.
     * If the current zone is Customer Zone, then it will be calculated only the
     * products, which:
     * - attribute Available is installed in Yes,
     * - attribute Available is installed in the empty string, (                ,
     *                        ,                       ,     Yes)
     * - attribute Available can't be seen in the product type.
     *
     * If the current zone is Admin Zone, then all products will be calculated.
     *
     * @see CCategoryInfo::_loadProductsNumberInCategory()
     * @param int $calc_mode The constant IN_CATEGORY_ONLY, IN_CATEGORY_RECURSIVELY
     *        or IN_CATEGORY_RECURSIVELY_WITHOUT_CURRENT
     * @return int the number of the products in the current category
     */
    function _loadProductsNumberInCategory($select_mode_recursiveness, $select_mode_uniqueness)
    {
        //           default
        //
        loadClass('CProductListFilter');
        $f = new CProductListFilter();
        $params = $f->getProductListParamsObject();

        //                 default            "      "
        $params->category_id = $this->_fCategoryID;
        $params->select_mode_recursiveness = $select_mode_recursiveness;
        $params->select_mode_uniqueness = $select_mode_uniqueness;

        return execQueryCount('SELECT_PRODUCT_LIST', $params->getParams());
    }

    function _getOnlineSubCategories()
    {
        if ($this->OnlineSubCategories == null)
        {
            $this->_loadRecursiveStatus();
            if ($this->_fRecursiveStatus == CATEGORY_STATUS_OFFLINE) {
                $this->OnlineSubCategories = array();
            }
            else {
                $this->OnlineSubCategories = modApiFunc('Catalog', 'fetchOnlineSubcategories', $this->_fBaseCategoryInfo[0]['id']);
            }
        }
        return $this->OnlineSubCategories;
    }


    var $debugmode = false;
    var $_fCategoryID = null;
    var $_fCategoryIDIsIncorrect = false;
    var $_fParentCategoryID = null;
    var $_fRecursiveStatus = null;
    var $localized = true;
    var $_fBaseCategoryInfo = null;
    var $_fProductsNumberInCategory = null;
    var $_fSubcategoriesNumber = null;
    var $_fImagesData = null;

    var $_fAdditionalTagList = array();

    var $_fArtificialTagList = array( 'id'
                                     ,'name'
                                     ,'status'
                                     ,'description'
                                     ,'largeimage'
                                     ,'largeimagesrc'
                                     ,'largeimagewidth'
                                     ,'largeimageheight'
                                     ,'smallimage'
                                     ,'smallimagesrc'
                                     ,'smallimagewidth'
                                     ,'smallimageheight'
                                     ,'imagealttext'
                                     ,'pagetitle'
                                     ,'metakeywords'
                                     ,'metadescription'
                                     ,'productsnumber'
                                     ,'productsnumber_non_recursively'
                                     ,'productsnumberrecursively'
                                     ,'productsnumberrecursively_all_product_links'
                                     ,'offlineandonlinesubcategoriesnumber'
                                     ,'hasonlinesubcategories'
                                     ,'subcategoriesnumber'
                                     ,'left'
                                     ,'right'
                                     ,'level'
                                     ,'parentid'
                                     ,'recursivestatus'
                                     ,'link'
                                     ,'seo_url_prefix'
                                     ,'showproductsrecursivelystatus'
                                    );

    var $OnlineSubCategories = null;

    /**#@-*/

}

?>