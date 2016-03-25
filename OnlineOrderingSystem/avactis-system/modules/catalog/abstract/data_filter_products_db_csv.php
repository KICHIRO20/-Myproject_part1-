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

loadClass('DataFilterDefault');
loadCoreFile('Tar.php');

/**
 *                                                 CSV     .
 *
 * @package Catalog
 * @author Oleg F. Vlasenko, Egor V. Derevyankin
 */
class DataFilterProductsDBCSV extends DataFilterDefault
{


//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------


	function DataFilterProductsDBCSV()
	{
        global $application;
        $this->MR = &$application->getInstance('MessageResources','catalog-messages','AdminZone');
	}

	/**
	 *               -
	 *
	 * @param array $settings -        settings
	 * @param ref array of strings $warnings -                      ,
     * @param ref array of strings $errors -              ,
	 */
	function initWork($settings)
	{
        $this->clearWork();
        $this->_settings = array(
            'attrs' => $settings['headers']
           ,'images_processing' => $settings['images_processing']
           ,'images_action' => $settings['images_action']
           ,'images_tar_file' => $settings['images_tar_file']
           ,'images_dir_path' => $settings['images_dir_path']
        );
        @unlink($this->_settings['images_tar_file']);
        $this->_build_attrs_info();
        $this->_check_images_processing_avail();
        $this->_process_info['status']='INITED';
	}



     /**
     *                                              .                                   :
     *
     * array (
     * 		0 =>  array (
	 *		          '<tag name>' => '<tag value>',
	 *		          '<tag name>' => '<tag value>',
	 *		          ...
	 *		          '<tag name>' => '<tag value>',
	 *				  'ProductCategoryPath' => array (
 	 *									           	0 => Array (
     *        	   										    'id'	=> 	<id          >
     *               										'name' 	=>	<             >
     *           										),
     * 												...
     * 												N => Array (
     *        	   										    'id'	=> 	<id          >
     *               										'name' 	=>	<             >
     *           										),
     * 											)
	 *		      )
	 *		...
	 * )
     *
     *                  ,                                                          .
     *                                      -                     .
     *
     * @param array $data array of arrays of     '<tag name>' => '<tag value>'
     * @param ref array of strings $warnings -                      ,
     * @param ref array of strings $errors -              ,
     * @return array of arrays of     '<tag name>' => '<tag value>'
     */
    function doWork($dataArray)
    {
        if ($dataArray === null)
        {
            return null;
        };

        $filteredArray = array();

        foreach($dataArray as $data)
        {
            if ($data === null)
                continue;

            $filtered = array();
            foreach($this->_settings['attrs'] as $attr_name => $attr_info)
            {
                $attr_name_for_header = $attr_info['attribute_type']=='custom' ? $attr_name."Custom" : $attr_name;
                if($attr_name=='ProductID' or $attr_name=='ProductName')
                {
                    $filtered[$attr_name_for_header] = $data[$attr_name];
                    continue;
                };
                if($attr_name=='ProductCategory')
                {
                    for ($i=0; $i<count($data['ProductAllCategoryPath']); $i++)
                    {
                        $k = ($i==0 ? 'ProductCategory' : 'ProductCategory'.$i);
                        $filtered[$k] = $this->_cat_path_to_str(array($data['ProductAllCategoryPath'][$i]));
                    }
                    continue;
                };
                if(!isset($data[$attr_name]) or $data[$attr_name]=='')
                {
                    $filtered[$attr_name_for_header]='';
                    continue;
                };
                if($attr_info['input_type_name']=='image')
                {
                    $filtered[$attr_name_for_header] = $data[$attr_name.'File'];
                    $this->_process_image($data[$attr_name.'FilePath']);
                    continue;
                };

                $filtered[$attr_name_for_header] = $this->_prepare_value($attr_info,$data[$attr_name]);
            }
            $filteredArray[] = $filtered;
        }

        return $filteredArray;
    }

         /**
	 *                                            -                           filter
	 */
    function loadWork()
    {
        if(modApiFunc('Session','is_set','DataFilterProductsDBCSVsettings'))
            $this->_settings = modApiFunc('Session','get','DataFilterProductsDBCSVsettings');
        else
            $this->_settings = null;

        $this->_make_tar_obj();
    }

    function finishWork()
    {
        $this->clearWork();
    }

         /**
	 *                 -
	 */
	function clearWork()
	{
        modApiFunc('Session','un_set','DataFilterProductsDBCSVsettings');
        $this->_settings = null;
        $this->_tar_obj = null;
	}

	/**
	 *                                             (   time-out- )
	 */
	function saveWork()
	{
		if($this->_settings != null)
        {
            modApiFunc('Session','set','DataFilterProductsDBCSVsettings',$this->_settings);
        };
	}

    function _prepare_value($attr_info,$value)
    {
        switch($attr_info['unit_type'])
        {
            case 'currency':   $value = sprintf("%.2f",$value); break;
            case 'item':       $value = intval($value); break;
            case 'weight':     $value = floatval($value); if($value < 0) $value = 0; $value = sprintf("%.2f",$value); break;
            case 'string256':  $value = strval($value); if(_ml_strlen($value) > 255) $value = _ml_substr($value,0,255); break;
            case 'string1024': $value = strval($value); if(_ml_strlen($value) > 1024) $value = _ml_substr($value,0,1024); break;
        };
        return $value;
    }

    function _build_attrs_info()
    {
        $_attrs = array();

        foreach($this->_settings['attrs'] as $aname)
            $_attrs[$aname] = modApiFunc('Catalog','_getAttrInfoByViewTag',preg_replace("/^Product/","",$aname),true);

        $this->_settings['attrs'] = $_attrs;
    }

    function _cat_path_to_str($categories_array)
    {
        $pathes = array();
        foreach ($categories_array as $path_arr)
        {
            $str = '';
            for($i=1;$i<count($path_arr);$i++)
            {
                $str .= '/'.$path_arr[$i]['name'];
            }
            if (!empty($str))
            {
                $pathes[] = _ml_substr($str,1);
            }
        }
        return implode(';', $pathes);
    }

    function _make_tar_obj()
    {
        $this->_tar_obj = null;
        if($this->_settings == null)
            return;
        if(!$this->_settings['images_processing'] or $this->_settings['images_action']==3)
            return;

        $this->_tar_obj = new Archive_Tar($this->_settings['images_tar_file']);
        $this->_tar_obj->setErrorHandling(PEAR_ERROR_PRINT);
    }

    function _check_images_processing_avail()
    {
        if(!$this->_settings['images_processing'])
            return;

        switch($this->_settings['images_action'])
        {
            case '1':
            case '2':
                if(!is_writable(dirname($this->_settings['images_tar_file'])))
                    $this->_errors = str_replace('%01%',basename($this->_settings['images_tar_file']),$this->MR->getMessage('DFPDBCSV_CANT_WRITE_TO_ARCHIVE'));//'can not write to archive `'.dirname($this->_settings['images_tar_file']).'`';
                break;
            case '3':
                if(!is_writable($this->_settings['images_dir_path']))
                    $this->_errors = str_replace('%01%',$this->_settings['images_dir_path'],$this->MR->getMessage('DFPDBCSV_CANT_WRITE_TO_DIR'));//'can not write to directory `'.$this->_settings['images_dir_path'].'`';
                break;
        };

    }

    function _process_image($image_path)
    {
        if(!$this->_settings['images_processing'])
            return;

        switch($this->_settings['images_action'])
        {
            case '1':
            case '2':
                $this->_tar_obj->addModify($image_path,'',dirname($image_path));
                break;
            case '3':
                copy($image_path,$this->_settings['images_dir_path'].basename($image_path));
                break;
        };
    }

    var $_settings;
    var $_tar_obj;

}



?>