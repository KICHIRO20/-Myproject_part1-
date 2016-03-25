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
 * @author Egor V. Derevyankin
 *
 */

loadClass('DataFilterDefault');
loadCoreFile('Tar.php');

class DataFilterProductsCSVDB extends DataFilterDefault
{
    function DataFilterProductsCSVDB()
    {
        $this->_pseudo_attrs = array(
            'ID'
           ,'Category'
           ,'Name'
        );

        global $application;
        $this->MR = &$application->getInstance('MessageResources','catalog-messages','AdminZone');
    }

    function initWork($settings)
    {
        $this->clearWork();

        if($settings['script_step']==1)
        {
            $attrs = array();
            $csv_headers = @func_get_arg(1);
            if(!is_array($csv_headers))
            {
                $this->_errors = $this->MR->getMessage('DFPCSVDB_CSV_HDRS_NOT_DEFINED');//'csv headers not defined';
            }
            else
            {
                $__illegal_count = 0;
                foreach($csv_headers as $header)
                {
                    $aname = preg_replace("/^Product/","",$header);
                    $aname = preg_replace("/Custom$/","",$aname);
                    if(in_array($aname,$this->_pseudo_attrs))
                        $attrs[$aname] = array('input_type_name'=>'text');
                    else
                    {
                        $__tmp = modApiFunc('Catalog','_getAttrInfoByViewTag',$aname,true);
                        if(!empty($__tmp))
                            $attrs[$aname] = $__tmp;
                        else
                        {
                            $attrs[$aname] = array('input_type_name'=>'illegal');
                            $__illegal_count++;
                        };
                    };
                };
                if($__illegal_count==count($csv_headers))
                {
                    $this->_errors = $this->MR->getMessage('NOT_FOUND_LEGAL_ATTRS');//'Not found legal attributes in the csv-headers. Import is impossible.';
                };
            };
            $this->_process_info['global']['attrs'] = $attrs;
            $this->_settings = array('script_step' => 1);
        }
        else
        {
            $this->_settings = array(
                'target_ptype_id' => intval(@$settings['target_ptype'])
               ,'target_category_id' => intval(@$settings['target_category'])
               ,'src_images_dir' => strval(@$settings['src_images_dir'])
               ,'src_images_tar' => strval(@$settings['src_images_tar'])
               ,'target_category_info' => null
               ,'script_step' => $settings['script_step']
               ,'ptypes_attrs' => array()
            );

            $this->_fetch_catgeory_info();
            $this->_check_src_images_dir();
            $this->_check_src_images_tar();
            $this->_fetch_home_cat_info();
        };

        $this->_process_info['status']='INITED';
    }

    function doWork($data)
    {
        $filtered = $data;

        if($filtered['item_data']==null)
            return $filtered;

        if(!isset($filtered['fine_data']))
            $filtered['fine_data'] = array();
        if(!isset($filtered['item_status']))
            $filtered['item_status'] = 'illegal';

        $this->_current_item_number = $data['item_number'];

        if($this->_settings['script_step']>1 and $filtered['item_status']=='illegal')
            return $filtered;

        if($filtered['item_status']!='illegal')
        {
            if($this->_settings == null)
            {
                $this->_errors = $this->MR->getMessage('SETS_NOT_DEF');//'can not filter data. settinges not defined';
                return $filtered;
            };

            if($filtered['item_status']=='new')
            {
                if($this->_settings['target_category_info'] == null)
                {
                    $this->_errors = $this->MR->getMessage('TARGET_CAT_NOT_CORRECT');//'can not filter data. target category is not correct';
                    return $filtered;
                };
            };
        };

        if($data['item_data'] == null or !is_array($data['item_data']) or empty($data['item_data']))
        {
            $this->_warnings = str_replace('%01%',($data['item_number']+1),$this->MR->getMessage('ILLEGAL_RECORD_01'));//'illegal record on file line '.($data['item_number']+1).' record doesn\'t contain information';
            return $filtered;
        };

        foreach($this->_pseudo_attrs as $pa_name)
        {
            if($this->_settings['script_step']==1 and $pa_name=='Category')
                continue;
            if($this->_settings['script_step']>1 and $pa_name!='Category')
                continue;

            $full_name = 'Product'.$pa_name;
            $for_check = isset($data['item_data'][$full_name]) ? $data['item_data'][$full_name] : null;
            $checked_value = $this->_check_pseudo_attr_value($pa_name,$for_check,$filtered['item_status']);
            if($checked_value !== null)
            {
                $filtered['fine_data'][$pa_name] = $checked_value;
            };
            unset($data['item_data'][$full_name]);
        }

        if($this->_settings['script_step']==1)
        {
            $filtered['item_status'] = $this->_get_item_status($filtered['fine_data']);
            if($filtered['item_status']=='illegal')
            {
                $this->_warnings = str_replace('%01%',($data['item_number']+1),$this->MR->getMessage('ILLEGAL_RECORD_02'));//'illegal record on file line '.($data['item_number']+1).' - incorrect `ProductID` and/or `ProductName`';
            };
        };

        if($this->_settings['script_step']>1)
        {
            $_pt_id = null;
            if($filtered['item_status']=='new')
            {
                $_pt_id = $this->_settings['target_ptype_id'];
            }
            elseif($filtered['item_status']=='exist')
            {
                $_pt_id = modApiFunc('Catalog','_getPTypeIDByProductID',$filtered['fine_data']['ID']);
            };

            if($_pt_id == null)
            {
                //$this->_warnings = 'cannot fecth product type info for record with status = `'.$filtered['item_status'].'`';
                return $filtered;
            };

            $_pt_attrs = $this->_fetch_ptype_info($_pt_id);

            foreach($_pt_attrs as $attr_name => $attr_info)
            {
                if(in_array($attr_name, $this->_pseudo_attrs))
                    continue;

                $full_name = $attr_info['type'] == 'custom' ? 'Product'.$attr_name.'Custom' : 'Product'.$attr_name;
                if(array_key_exists($full_name,$data['item_data']))
                {
                    $aVal = $this->_check_attr_value($attr_info,$data['item_data'][$full_name]);
                }
                elseif($filtered['item_status']=='new')
                {
                    if(in_array($attr_info['patt_type'],array('currency','item','weight')))
                        $aVal = 0;
                    else
                        $aVal = $attr_info['default'];
                }
                else
                    continue;

                $filtered['fine_data'][$attr_name] = $aVal;
            };

            $filtered['fine_data']['ptype_id'] = $_pt_id;
        }
        else
            $this->_process_info['global']['items_statuses'][$filtered['item_status']]++;

        return $filtered;
    }

    function finishWork()
    {
        if($this->_settings['script_step']>1 and $this->_settings['src_images_tar']!=null)
        {
            modApiFunc('Shell','removeDirectory',$this->_settings['src_images_dir']);
        };
    }

    function saveWork()
    {
        modApiFunc('Session','set','DataFilterProductsCSVDBSettings',$this->_settings);
    }

    function loadWork()
    {
        if(modApiFunc('Session','is_set','DataFilterProductsCSVDBSettings'))
            $this->_settings = modApiFunc('Session','get','DataFilterProductsCSVDBSettings');
        else
            $this->_settings = null;

        $this->_process_info['global']['items_statuses'] = array(
            'new' => 0
           ,'exist' => 0
           ,'illegal' => 0
        );

        $this->_process_info['global']['attrs'] = array();
    }

    function clearWork()
    {
        modApiFunc('Session','un_set','DataFilterProductsCSVDBSettings');
        $this->_settings = null;
        $this->_process_info['global']['items_statuses'] = array(
            'new' => 0
           ,'exist' => 0
           ,'illegal' => 0
        );
    }

    function _fetch_ptype_info($ptype_id)
    {
        if(isset($this->_settings['ptypes_attrs'][$ptype_id]))
            return $this->_settings['ptypes_attrs'][$ptype_id];

        $ptype_attrs = modApiFunc('Catalog','getProductTypeAttributes',$ptype_id);

        foreach($ptype_attrs as $aName => $aInfo)
            if($aInfo['visible']!=1)
                unset($ptype_attrs[$aName]);

        $this->_settings['ptypes_attrs'][$ptype_id] = $ptype_attrs;
        return $ptype_attrs;
    }

    function _fetch_catgeory_info()
    {
        if($this->_settings == null or !isset($this->_settings['target_category_id']) or !is_numeric($this->_settings['target_category_id']))
        {
            $this->_errors = $this->MR->getMessage('TARGET_CAT_NOT_DEFINED');//'can not fetch category info. target category not defined';
            return;
        };

        if(!modApiFunc('Catalog','isCorrectCategoryId',$this->_settings['target_category_id']))
        {
            $this->_errors = $this->MR->getMessage('TARGET_CAT_NOT_CORRECT');//'can not fetch category info. target category is not correct';
            return;
        };

        $cat_path_arr = modApiFunc('Catalog','getCategoryFullPath',$this->_settings['target_category_id'],false);
        $cat_path_str = '';
        foreach($cat_path_arr as $k => $v)
            $cat_path_str .= '/'.$v['name'];
        $this->_settings['target_category_info'] = _ml_substr($cat_path_str,1);
    }

    function _check_src_images_dir()
    {
        if($this->_settings == null or !isset($this->_settings['src_images_dir']) or $this->_settings['src_images_dir']=='')
        {
            $this->_errors = $this->MR->getMessage('CANT_CHECK_IMGS_DIR_01');//'can not check source images folder. folder not defined';
            $this->_settings['src_images_dir'] = null;
            return;
        };
        if($this->_settings['src_images_dir']=='NONE')
        {
            $this->_settings['src_images_dir'] = null;
            return;
        };
        if(!is_dir($this->_settings['src_images_dir']))
        {
            $this->_warnings = $this->MR->getMessage('IMGS_DIR_ISNT_DIR');//'source images folder is not folder. images will be ignored';
            $this->_settings['src_images_dir'] = null;
            return;
        };
        if(!is_readable($this->_settings['src_images_dir']))
        {
            $this->_warnings = $this->MR->getMessage('IMGS_DIR_ISNT_READ');//'source images folder is not readable. images will be ignored';
            $this->_settings['src_images_dir'] = null;
            return;
        };
    }

    function _check_src_images_tar()
    {
        if($this->_settings == null or !isset($this->_settings['src_images_tar']) or $this->_settings['src_images_tar']=='')
        {
            $this->_errors = $this->MR->getMessage('CANT_CHECK_IMGS_ARC_01');//'can not check source images archive. archive not defined';
            $this->_settings['src_images_tar'] = null;
            return;
        };
        if($this->_settings['src_images_tar']=='NONE')
        {
            $this->_settings['src_images_tar'] = null;
            return;
        };
        if(!is_file($this->_settings['src_images_tar']))
        {
            $this->_warnings = $this->MR->getMessage('IMGS_ARC_ISNT_FILE');//'source images archive is not file. images will be ignored';
            $this->_settings['src_images_tar'] = null;
            return;
        };
        if(!is_readable($this->_settings['src_images_tar']))
        {
            $this->_warnings = $this->MR->getMessage('IMGS_ARC_ISNT_READ');//'source images archive is not readable. images will be ignored';
            $this->_settings['src_images_tar'] = null;
            return;
        };

        global $application;
        $dp = $application->getAppIni('PATH_CACHE_DIR').'__dfpcsvdb_imgs/';
        if(!is_dir($dp))
            mkdir($dp);
        else
            modApiFunc('Shell','clearFolderContent',$dp);
        $tar_obj = new Archive_Tar($this->_settings['src_images_tar']);
        if($tar_obj->extract($dp))
        {
            $this->_settings['src_images_dir'] = $dp;
        }
        else
        {
            $this->_warnings = str_replace('%01%',basename($this->_settings['src_images_tar']),$this->MR->getMessage('CANT_EXTRACT_CONTENT'));//'Cannot extract content from archive `'.basename($this->_settings['src_images_tar']).'`. Images will be ignored.';
            $this->_settings['src_images_dir'] = null;
            $this->_settings['src_images_tar'] = null;
        };
    }

    function _check_attr_value($aInfo,$value)
    {
        switch($aInfo['patt_type'])
        {
            case 'currency':   $value = round(floatval($value),2); break;
            case 'item':       $value = intval($value); break;
            case 'weight':     $value = round(floatval($value),2); if($value < 0) $value = 0; break;
            case 'string256':  $value = strval($value); if(_ml_strlen($value) > 255) $value = _ml_substr($value,0,255); break;
            case 'string1024': $value = strval($value); if(_ml_strlen($value) > 1024) $value = _ml_substr($value,0,1024); break;
            case '':
                switch($aInfo['input_type_name'])
                {
                    case 'select':
                        $tmp_val = _ml_strtolower($value);
                        $tmp_arr = array_map("_ml_strtolower",$aInfo['input_type_values']);
                        if(!in_array($tmp_val,$tmp_arr))
                            {
                            if($aInfo['type'] == 'custom' && $tmp_val!='')
                                {
                                global $application;
                                execQuery('INSERT_INPUT_TYPE_VALUE', array('input_type_id'=>$aInfo['input_type_id'], 'input_type_value'=>$value));
                                $value = $application->db->DB_Insert_Id();
                                }
                            else
                                $value = $aInfo['default'];
                            }
                        else
                            $value = array_search($tmp_val,$tmp_arr);
                        break;
                    case 'image':
                        if($this->_settings['src_images_dir'] == null or $value=='')
                            $value = '';
                        else
                        {
                            if(!file_exists($this->_settings['src_images_dir'].$value))
                            {
                                if($this->_settings['src_images_tar']!=null)
                                    //$this->_warnings = 'Line '.($this->_current_item_number+1).'. Attribute `'.$aInfo['view_tag'].'`. Image `'.$value.'` not found in archive `'.basename($this->_settings['src_images_tar']).'`';
                                    $this->_warnings = str_replace(array('%01%','%02%'),array($this->_current_item_number+1,$aInfo['view_tag']),$this->MR->getMessage('LINE_ATTR_WARN')).' '.str_replace(array('%01%','%02%'),array($value,basename($this->_settings['src_images_tar'])),$this->MR->getMessage('IMG_NOT_FOUND_IN_ARC'));
                                else
                                    //$this->_warnings = 'Line '.($this->_current_item_number+1).'. Attribute `'.$aInfo['view_tag'].'`. Image `'.$value.'` not found in folder `'.$this->_settings['src_images_dir'].'`';
                                    $this->_warnings = str_replace(array('%01%','%02%'),array($this->_current_item_number+1,$aInfo['view_tag']),$this->MR->getMessage('LINE_ATTR_WARN')).' '.str_replace(array('%01%','%02%'),array($value,$this->_settings['src_images_dir']),$this->MR->getMessage('IMG_NOT_FOUND_IN_DIR'));
                                $value = '';
                            }
                            elseif(!is_readable($this->_settings['src_images_dir'].$value))
                            {
                                //$this->_warnings = 'Line '.($this->_current_item_number+1).'. Attribute `'.$aInfo['view_tag'].'`. Image `'.$value.'` not readable in folder `'.$this->_settings['src_images_dir'].'`';
                                $this->_warnings = str_replace(array('%01%','%02%'),array($this->_current_item_number+1,$aInfo['view_tag']),$this->MR->getMessage('LINE_ATTR_WARN')).' '.str_replace(array('%01%','%02%'),array($value,$this->_settings['src_images_dir']),$this->MR->getMessage('IMG_NOT_READ_IN_DIR'));
                                $value = '';
                            }
                            else
                            {
                                $value = $this->_settings['src_images_dir'].$value;
                            };
                        };
                        break;
                }
        };

        return $value;
    }

    function _check_pseudo_attr_value($name,$value)
    {
        switch($name)
        {
            case 'Category':
                if($value === null) $value = '';
                $sub_path = implode('/',array_filter(explode('/',$value)));
                if(func_get_arg(2)=='new')
                {
                    $value = implode('/',array_filter(array($this->_settings['target_category_info'],$sub_path)));
                }
                elseif(func_get_arg(2)=='exist')
                {
                   $value = implode('/',array_filter(array($this->_settings['home_cat_name'],$sub_path)));
                };
                break;
            case 'ID':
                if($value !== null and $value!='')
                    $value = intval($value);
                else
                    $value = null;
                break;
            case 'Name':
                if($value !== null and $value!='')
                    $value = $this->_check_attr_value($name,$value);
                else
                    $value = null;
                break;
        }

        return $value;
    }

    function _get_item_status($data)
    {
        if(!isset($data['ID']) and isset($data['Name']))
            return 'new';

        if(!isset($data['ID']) and !isset($data['Name']))
            return 'illegal';

        if(modApiFunc('Catalog','isCorrectProductId',$data['ID']))
            return 'exist';

        if(!isset($data['ID']) and isset($data['Name']) and $data['Name']!='')
            return 'new';

        return 'illegal';
    }

    function _fetch_home_cat_info()
    {
        $home_cat_info = modApiFunc('Catalog','getCategoryFullPath',1);
        $home_cat_info = array_shift($home_cat_info);
        $this->_settings['home_cat_name'] = $home_cat_info['name'];
    }

    var $_settings;
}

?>