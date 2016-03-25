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

loadClass('DataReaderDefault');


/**
 *                                .
 *
 * @package Catalog
 * @author Oleg F. Vlasenko, Egor V. Derevyankin
 */
class DataReaderProductsDB extends DataReaderDefault
{

//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------


	function DataReaderProductsDB()
	{

	}

	/**
	 *               -
	 *
	 * $settings
	 * 	array (
	 *  	'product_type_id' 				=> <id              >
	 * 		'product_category_id'			=> <id          >
	 * 		'categories_export_recursively'	=> 'RECURSIVELY'     'NONRECURSIVELY'
	 *  )
	 *
	 * @param array $settings -        settings
	 * @param ref array of strings $warnings -                      ,
     * @param ref array of strings $errors -              ,
	 */
	function initWork($settings)
	{
		$this->clearWork();

		$this->_settings['type_id'] = $settings['product_type_id'];
		$this->_settings['category_id'] = $settings['product_category_id'];
		$this->_settings['categories_export_recursively'] = $settings['categories_export_recursively'];
                $this->_settings['bulk_number'] = 50;

		if ($this->_settings['type_id'] == 0)
		{
			$this->_settings['type_id'] = 'ALL_PRODUCT_TYPES';
		}

		// get product id list from DB
		$_select_params = new PRODUCT_LIST_PARAMS();
        if ($this->_settings['category_id'] == 'ALL_CATEGORIES')
        {
            $_select_params->category_id = 1;
            $_select_params->select_mode_recursiveness = IN_CATEGORY_RECURSIVELY;
        }
        else
        {
            $_select_params->category_id = $this->_settings['category_id'];
            if ($this->_settings['categories_export_recursively'] == 'RECURSIVELY')
            {
                $_select_params->select_mode_recursiveness = IN_CATEGORY_RECURSIVELY;
            }
            else
            {
                $_select_params->select_mode_recursiveness = IN_CATEGORY_ONLY;
            }
        }
        $_select_params->select_mode_uniqueness = UNIQUE_PRODUCTS;
		$_product_id_list = modApiFunc('Catalog','getProductListByFilter',$_select_params, RETURN_AS_ID_LIST);

        // Let's make plain array of Id
		$this->_product_ids = array();
        foreach($_product_id_list as $db_item)
        {
            $this->_product_ids[] = $db_item['product_id'];
        }

        //dirty hack
        $attributes_not_for_export = array('Manufacturer');

        if (modApiFunc('Catalog','imagesPresent') == false) // no images attached to products
        {
            $attributes_not_for_export[] = 'LargeImage';
            $attributes_not_for_export[] = 'SmallImage';
        }
        if(@$settings['need_attrs'] === true)
        {
		    $_attrs = modApiFunc('Catalog','_getPtypesAttrsByPIDs',$this->_product_ids);
            $short_attrs = array();
            if(!empty($_attrs))
            {
                foreach($_attrs as $aname => $ainfo)
                {
                    if(!in_array($aname, $attributes_not_for_export))
                    {
                        $short_attrs[$aname] = array('name' => $ainfo['name'], 'type' => $ainfo['type'], 'input_type' => $ainfo['input_type_name']);
                    }
                }

                $_id = $short_attrs['ID'];
                $_name = $short_attrs['Name'];
                $_cat = array('name' => 'Category', 'type' => 'artifical', 'input_type' => 'text');
                unset($short_attrs['ID'],$short_attrs['Name']);
                $short_attrs = array_merge(array('ID'=>$_id,'Name'=>$_name,'Category'=>$_cat),$short_attrs);
            };
            $this->_process_info['global']['ptypes_attrs'] = $short_attrs;
        }
        else
            $this->_process_info['global']['ptypes_attrs'] = array();

		$this->_count_exported_products = 0;

        $this->_process_info['status']='INITED';
        $this->_process_info['items_count'] = count($this->_product_ids);
        $this->_process_info['items_processing']=0;
	}




    /**
     *                                                                          .
     *
     *                                           ,
     *                                  .
     *
     *                                      .
     *
     * $warnings -                      ,
     * $errors -              ,
     *
     *                                             :
     * array (
     * 		0 =>  array (
     *		          '<tag name>' => '<tag value>',
     *		          '<tag name>' => '<tag value>',
     *		          ...
     *		          '<tag name>' => '<tag value>',
     *                    'ProductCategoryPath' => array (
     *                        0 => Array (
     *                                 'id'   => <id          >
     *                                 'name' => <             >
     *                             ),
     *                        ...
     *                        N => Array (
     *                                 'id'   => <id          >
     *                                 'name' => <             >
     *                        )
     *                    )
     *                )
     *          ...
     * )
     *
     * @param ref array of strings $warnings -                      ,
     * @param ref array of strings $errors -              ,
     * @return array of arrays of     '<tag name>' => '<tag value>'
     */
    function doWork()
    {
        $total_products = count($this->_product_ids);
        $this->_process_info['items_count'] = $total_products;
        $this->_process_info['status']='HAVE_MORE_DATA';
        if ($this->_product_ids === null)
        {
            $this->_process_info['items_count'] = 0;
            $this->_process_info['items_processing']=$this->_count_exported_products;
            $this->_process_info['status']='NO_MORE_DATA';
            return;
        }

        if ($this->_count_exported_products >= $total_products)
        {
            $this->_process_info['items_processing']=$this->_count_exported_products;
            $this->_process_info['status']='NO_MORE_DATA';
            return;
        }

        $result = array();

        // preparing the bulk product ids
	$bulk_productids = array();
	for ($i = 0; $i < $this -> _settings['bulk_number'] && $this->_count_exported_products < $total_products; $i++)
            $bulk_productids[] = $this->_product_ids[$this->_count_exported_products++];

        foreach($bulk_productids as $id)
        {
            $prodObj = new CProductInfo($id);
            $prodObj->setForceLocalizeAttrTypes(array('select'));
            $result[] = $prodObj->getProductTagValuesExportHash(PRODUCTINFO_NOT_LOCALIZED_DATA, $this -> _TagList);
            unset($prodObj);
        }

        $this->_process_info['items_processing']=$this->_count_exported_products;

        if($this->_count_exported_products>=$total_products)
             $this->_process_info['status']='NO_MORE_DATA';

        return $result;
    }

    function finishWork()
    {
        $this->clearWork();
    }

    /**
	 *                                            -                           data reader.
     */
    function loadWork()
    {
        if(modApiFunc('Session', 'is_Set', 'DataReaderProductsDBSettings'))
        {
            $this->_settings = modApiFunc('Session', 'get', 'DataReaderProductsDBSettings');
        }
        else
        {
            $this->_settings = NULL;
        }

       	if(modApiFunc('Session', 'is_Set', 'DataReaderProductsDBIds'))
        {
            $this->_product_ids = modApiFunc('Session', 'get', 'DataReaderProductsDBIds');
        }
        else
        {
            $this->_product_ids = NULL;
        }

        if(modApiFunc('Session', 'is_Set', 'DataReaderProductsDBCountExportedProduct'))
        {
            $this->_count_exported_products = modApiFunc('Session', 'get', 'DataReaderProductsDBCountExportedProduct');
        }
        else
        {
            $this->_count_exported_products = NULL;
        }
    }

	/**
	 *
	 */
    function clearWork()
	{
        modApiFunc('Session', 'un_Set', 'DataReaderProductsDBSettings');
        modApiFunc('Session', 'un_Set', 'DataReaderProductsDBIds');
        modApiFunc('Session', 'un_Set', 'DataReaderProductsDBCountExportedProduct');
		$this->_settings = NULL;
		$this->_product_ids = NULL;
		$this->_count_exported_products = NULL;
	}


	/**
	 *
	 */
    function saveWork()
    {

        if (!($this->_settings === NULL))
        {
            modApiFunc('Session', 'set', 'DataReaderProductsDBSettings', $this->_settings);
        }
        elseif (modApiFunc('Session', 'is_Set', 'DataReaderProductsDBSettings'))
        {
            modApiFunc('Session', 'un_Set', 'DataReaderProductsDBSettings');
        }

        if (!($this->_product_ids === NULL))
        {
            modApiFunc('Session', 'set', 'DataReaderProductsDBIds', $this->_product_ids);
        }
        elseif (modApiFunc('Session', 'is_Set', 'DataReaderProductsDBIds'))
        {
            modApiFunc('Session', 'un_Set', 'DataReaderProductsDBIds');
        }

        if (!($this->_count_exported_products === NULL))
        {
            modApiFunc('Session', 'set', 'DataReaderProductsDBCountExportedProduct', $this->_count_exported_products);
        }
        elseif (modApiFunc('Session', 'is_Set', 'DataReaderProductsDBCountExportedProduct'))
        {
            modApiFunc('Session', 'un_Set', 'DataReaderProductsDBCountExportedProduct');
        }
    }

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------


	/**
	 * Settings     datareader.
	 *
	 * array (
	 * 		'type_id' 							=> <id                               >
	 * 		'category_id' 						=> <id                                   >
	 * 		'categories_export_recursively'		=> 'RECURSIVELY'     'NONRECURSIVELY'
	 * )
	 */
	var $_settings;

	/**
	 * Id               ,                               .
	 *
	 * array (
	 * 	0 => <id                 >
	 * ...
	 *  N => <id                    >
	 * )
	 */
	var $_product_ids;

	/**
	 *                                          .
	 *                            ,                            getNextPortion
	 */
	var $_count_exported_products;

    /**
     * The list of tags for export
     * Note: it MUST be a subset of CProductInfo :: _fArtificialTagList
     */
    var $_TagList = array(
        'id'                 => 'ID',
        'name'               => 'Name',
        'allcategorypath'    => 'AllCategoryPath',
        'largeimagefile'     => 'LargeImageFile',
        'largeimagefilepath' => 'LargeImageFilePath',
        'largeimagesrc'      => 'LargeImageSrc',
        'typename'           => 'TypeName',
        'smallimagefile'     => 'SmallImageFile',
        'smallimagefilepath' => 'SmallImageFilePath'
    );

}



?>