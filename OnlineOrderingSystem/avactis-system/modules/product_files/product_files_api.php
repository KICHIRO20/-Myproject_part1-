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
 * @package ProductFiles
 * @author Egor V. Derevyankin
 *
 */

class Product_Files
{

    function Product_Files()
    {
    }

    function install()
    {
        global $application;
        loadCoreFile('csv_parser.php');
        $csv_parser = new CSV_Parser();

        $tables = Product_Files::getTables();
        $query = new DB_Table_Create($tables);

        $table = 'pf_settings';
        $columns = $tables[$table]['columns'];

        list($flt,$Default_Settings) = $csv_parser->parse_file(dirname(__FILE__)."/includes/default_settings.csv");
        if(count($Default_Settings) > 0)
        {
            foreach($Default_Settings as $key => $setting)
            {
                $query = new DB_Insert($table);
                $query->addInsertValue($setting["key"], $columns['setting_key']);
                $query->addInsertValue($setting["value"], $columns['setting_value']);
                $application->db->getDB_Result($query);
            };
        };

        modApiFunc('EventsManager','addEventHandler','OrdersWereUpdated',__CLASS__,'OnOrdersWereUpdated');
        modApiFunc('EventsManager','addEventHandler','OrdersWillBeDeleted',__CLASS__,'OnOrdersWillBeDeleted');
    }

    function uninstall()
    {
        $query = new DB_Table_Delete(Product_Files::getTables());
        modApiFunc('EventsManager','removeEventHandler','OrdersWereUpdated',__CLASS__,'OnOrdersWereUpdated');
        modApiFunc('EventsManager','removeEventHandler','OrdersWillBeDeleted',__CLASS__,'OnOrdersWillBeDeleted');
    }

    function getTables()
    {
        static $tables;

        if (is_array($tables))
        {
            return $tables;
        }

        $tables=array();

        $table='pf_settings';
        $tables[$table]=array();
        $tables[$table]['columns']=array(
            'setting_id'    => $table.'.setting_id'
           ,'setting_key'   => $table.'.setting_key'
           ,'setting_value' => $table.'.setting_value'
        );
        $tables[$table]['types']=array(
            'setting_id'    => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
           ,'setting_key'   => DBQUERY_FIELD_TYPE_CHAR255.' NOT NULL DEFAULT \'\''
           ,'setting_value' => DBQUERY_FIELD_TYPE_CHAR255.' NOT NULL DEFAULT \'\''
        );
        $tables[$table]['primary']=array(
            'setting_id'
        );

        $table='pf_files';
        $tables[$table]=array();
        $tables[$table]['columns']=array(
            'file_id'       => $table.'.file_id'
           ,'product_id'    => $table.'.product_id'
           ,'file_path'     => $table.'.file_path'
           ,'file_name'     => $table.'.file_name'
           ,'file_descr'    => $table.'.file_descr'
           ,'file_size'     => $table.'.file_size'
           ,'is_uploaded'   => $table.'.is_uploaded'
        );
        $tables[$table]['types']=array(
            'file_id'       => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
           ,'product_id'    => DBQUERY_FIELD_TYPE_INT .' NOT NULL DEFAULT 0'
           ,'file_path'     => DBQUERY_FIELD_TYPE_TEXT
           ,'file_name'     => DBQUERY_FIELD_TYPE_CHAR255
           ,'file_descr'    => DBQUERY_FIELD_TYPE_CHAR255
           ,'file_size'     => DBQUERY_FIELD_TYPE_INT.' NOT NULL DEFAULT 0'
           ,'is_uploaded'   => "ENUM ('Y','N') default 'Y'"
        );
        $tables[$table]['primary']=array(
            'file_id'
        );
        $tables[$table]['indexes']=array(
            'IDX_pid' => 'product_id'
        );

        $table='pf_hotlinks';
        $tables[$table]=array();
        $tables[$table]['columns']=array(
            'hotlink_id'    => $table.'.hotlink_id'
           ,'hotlink_key'   => $table.'.hotlink_key'
           ,'hotlink_value' => $table.'.hotlink_value'
           ,'order_product_id'  => $table.'.order_product_id'
           ,'file_id'       => $table.'.file_id'
           ,'expire_date'   => $table.'.expire_date'
           ,'max_try'       => $table.'.max_try'
           ,'was_try'       => $table.'.was_try'
           ,'status'        => $table.'.status'
        );
        $tables[$table]['types']=array(
            'hotlink_id'    => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
           ,'hotlink_key'   => DBQUERY_FIELD_TYPE_CHAR255.' NOT NULL DEFAULT \'\''
           ,'hotlink_value' => DBQUERY_FIELD_TYPE_TEXT.' NOT NULL '
           ,'order_product_id'  => DBQUERY_FIELD_TYPE_INT.' NOT NULL DEFAULT 0'
           ,'file_id'       => DBQUERY_FIELD_TYPE_INT.' NOT NULL DEFAULT 0'
           ,'expire_date'   => DBQUERY_FIELD_TYPE_INT.' NOT NULL DEFAULT 0'
           ,'max_try'       => DBQUERY_FIELD_TYPE_INT.' NOT NULL DEFAULT 0'
           ,'was_try'       => DBQUERY_FIELD_TYPE_INT.' NOT NULL DEFAULT 0'
           ,'status'        => "ENUM ('L','U') DEFAULT 'L'"
        );
        $tables[$table]['primary']=array(
            'hotlink_id'
        );
        $tables[$table]['indexes']=array(
            'IDX_opid' => 'order_product_id'
        );

        global $application;
        return $application->addTablePrefix($tables);
    }

    function getSettings()
    {
        global $application;

        if(!isset($this->settings))
        {
	        $tables=$this->getTables();

	        $query = new DB_Select();
	        $query->addSelectTable('pf_settings');
	        $query->addSelectField('*');
	        $res=$application->db->getDB_Result($query);

	        $settings=array();

	        foreach($res as $k => $sval)
	            $settings[$sval['setting_key']]=$sval['setting_value'];

	        $this->settings = $settings;
        }
        return $this->settings;
    }

    function updateSettings($settings)
    {
        global $application;
        $tables=$this->getTables();
        $stable=$tables['pf_settings']['columns'];

        foreach($settings as $skey => $sval)
        {
            $query = new DB_Update('pf_settings');
            $query->addUpdateValue($stable['setting_value'],$sval);
            $query->WhereValue($stable['setting_key'], DB_EQ, $skey);
            $application->db->PrepareSQL($query);
            $application->db->DB_Exec();
        };

        return;
    }

    function checkFileUploading()
    {
        if(!ini_get('file_uploads'))
            return 1;

        global $application;

        if(!is_dir($application->getAppIni('PRODUCT_FILES_DIR')))
            return 2;

        if(!is_writable($application->getAppIni('PRODUCT_FILES_DIR')))
            return 3;

        return 0;
    }

    function addFileToProduct($product_id, $file_name, $file_path, $file_descr="", $is_uploaded=true)
    {
        global $application;
        $tables = $this->getTables();
        $file_table = $tables['pf_files']['columns'];

        $query = new DB_Insert('pf_files');
        $query->addInsertValue($product_id, $file_table['product_id']);
        $query->addInsertValue($file_path, $file_table['file_path']);
        $query->addInsertValue($file_name, $file_table['file_name']);
        $query->addInsertValue($file_descr, $file_table['file_descr']);
        $query->addInsertValue(filesize($file_path), $file_table['file_size']);
        $query->addInsertValue($is_uploaded ? 'Y' : 'N', $file_table['is_uploaded']);

        $application->db->PrepareSQL($query);
        $application->db->DB_Exec();

        $file_id = $application->db->DB_Insert_Id();

        return $file_id;
    }

    function delFilesFromProduct($product_id,$files_ids)
    {
        global $application;
        $tables = $this->getTables();
        $file_table = $tables['pf_files']['columns'];

        $query = new DB_Select();
        $query->addSelectField($file_table['file_path']);
        $query->WhereValue($file_table['product_id'],DB_EQ,$product_id);
        $query->WhereAND();
        $query->Where($file_table['file_id'],DB_IN,'(\''.implode('\',\'',$files_ids).'\')');
        $query->WhereAND();
        $query->WhereValue($file_table['is_uploaded'], DB_EQ, 'Y');
        $res = $application->db->getDB_Result($query);
        $this->unlinkFiles($res);

        $query = new DB_Delete('pf_files');
        $query->WhereValue($file_table['product_id'],DB_EQ,$product_id);
        $query->WhereAND();
        $query->Where($file_table['file_id'],DB_IN,'(\''.implode('\',\'',$files_ids).'\')');

        $application->db->PrepareSQL($query);
        $application->db->DB_Exec();

        return;
    }

    function unlinkFiles($paths)
    {
        for($i=0;$i<count($paths);$i++)
            unlink($paths[$i]['file_path']);

        return;
    }

    function getFilesListForProduct($product_id)
    {
        global $application;
        $tables = $this->getTables();
        $file_table = $tables['pf_files']['columns'];

        $query = new DB_Select();
        $query->addSelectTable('pf_files');
        $query->addSelectField('*');
        $query->WhereValue($file_table['product_id'],DB_EQ, $product_id);
        $query->SelectOrder($file_table['file_id'],'ASC');

        return $application->db->getDB_Result($query);
    }

    function getPFileInfo($file_id)
    {
        global $application;
        $tables = $this->getTables();
        $file_table = $tables['pf_files']['columns'];

        $query = new DB_Select();
        $query->addSelectTable('pf_files');
        $query->addSelectField('*');
        $query->WhereValue($file_table['file_id'], DB_EQ, $file_id);

        $res = $application->db->getDB_Result($query);

        if(count($res)!=1)
            return null;
        else
            return array_shift($res);
    }

    /**
     * @return array('error','full_path','base_name','file_size')
     */
    function moveUploadedFileToPFDir($product_id,$src_fname)
    {
        global $application;

        $result = array(
            'error' => ''
           ,'full_path' => ''
           ,'base_name' => ''
           ,'file_size' => ''
        );

        $src_file = $_FILES[$src_fname];
        $result['error'] = $src_file['error'];

        if($result['error'] != UPLOAD_ERR_OK)
        {
            return $result;
        };

        if(!is_uploaded_file($src_file['tmp_name']))
        {
            $result['error'] = UPLOAD_ERR_POSIBLE_ATTACK;
            return $result;
        };

        $dest_dir = $application->getAppIni('PRODUCT_FILES_DIR');

        $i = 0;
        while(file_exists($dest_dir.'pfile_'.$product_id.'_'.sprintf("%04d",$i)))
            $i++;

        if(!move_uploaded_file($src_file['tmp_name'],$dest_dir.'pfile_'.$product_id.'_'.sprintf("%04d",$i)))
        {
            $result['error'] = UPLOAD_ERR_CANT_MOVE_FILE;
            return $result;
        };

        $result['full_path'] = $dest_dir.'pfile_'.$product_id.'_'.sprintf("%04d",$i);
        $result['base_name'] = $src_file['name'];
        $result['file_size'] = $src_file['size'];

        return $result;
    }

    function moveFileToPFDir($product_id, $file_path)
    {
        global $application;

        $result = array(
            'error' => UPLOAD_ERR_OK
           ,'full_path' => ''
           ,'base_name' => basename($file_path)
           ,'file_size' => ''
        );

        $dest_dir = $application->getAppIni('PRODUCT_FILES_DIR');

        $i = 0;
        while(file_exists($dest_dir.'pfile_'.$product_id.'_'.sprintf("%04d",$i)))
            $i++;

        if(!rename($file_path,$dest_dir.'pfile_'.$product_id.'_'.sprintf("%04d",$i)))
        {
            $result['error'] = UPLOAD_ERR_CANT_MOVE_FILE;
            return $result;
        };

        $result['full_path'] = $dest_dir.'pfile_'.$product_id.'_'.sprintf("%04d",$i);
        $result['file_size'] = filesize($dest_dir.'pfile_'.$product_id.'_'.sprintf("%04d",$i));

        return $result;
    }

    /**
     * @deprecated 1.8.x - 09.10.2007 by egor. cause: method was moved to the
     * 'Localization' module.
     */
    function formatFileSize($size)
    {
        $a = array('b','Kb','Mb','Gb','Tb');
        $i = 0;
        $have_mod = false;
        while($size > 1024 and $i < 4)
        {
            $have_mod = ($have_mod or (($size % 1024) > 0));
            $size = $size / 1024;
            $i++;
        };

        $str = '';
        $frm = "%d ";
        if($i)
        {
            if($have_mod)
                $str .= '~';
            $frm = "%.2f ";
        };
        $str .= sprintf($frm,round($size,2)).$a[$i];
        return $str;
    }

    function genHotlinks($product_id,$order_product_id)
    {
        global $application;
        $pfiles = $this->getFilesListForProduct($product_id);

        if(empty($pfiles))
            return;

        $tables = $this->getTables();
        $hl_table = $tables['pf_hotlinks']['columns'];
        $sets = $this->getSettings();

        foreach($pfiles as $k => $pf_info)
        {
            $hl_key = md5($pf_info['file_path'].time());
            $hl_req = new Request();
            $hl_req->setView('Download');
            $hl_req->setKey('key',$hl_key);

            $query = new DB_Insert('pf_hotlinks');
            $query->addInsertValue($hl_key, $hl_table['hotlink_key']);
            $query->addInsertValue($hl_req->getURL(), $hl_table['hotlink_value']);
            $query->addInsertValue($order_product_id, $hl_table['order_product_id']);
            $query->addInsertValue($pf_info['file_id'], $hl_table['file_id']);
            $query->addInsertValue(time()+$sets['HL_TL']*3600, $hl_table['expire_date']);
            $query->addInsertValue($sets['HL_MAX_TRY'], $hl_table['max_try']);

            $application->db->PrepareSQL($query);
            $application->db->DB_Exec();
        }
    }

    function getHotlinksList($order_product_id)
    {
        global $application;
        $tables = $this->getTables();
        $hl_table = $tables['pf_hotlinks']['columns'];

        $query = new DB_Select();
        $query->addSelectTable('pf_hotlinks');
        $query->addSelectField('*');
        $query->WhereValue($hl_table['order_product_id'], DB_EQ, $order_product_id);

        return $application->db->getDB_Result($query);
    }

    function changeHotlinkStatus($opid,$hl_id)
    {
        global $application;
        $tables = $this->getTables();
        $hl_table = $tables['pf_hotlinks']['columns'];

        $query = new DB_Update('pf_hotlinks');
        $query->addUpdateExpression($hl_table['status'],"IF(status='L','U','L')");
        $query->WhereValue($hl_table['order_product_id'], DB_EQ, $opid);
        $query->WhereAND();
        $query->WhereValue($hl_table['hotlink_id'], DB_EQ, $hl_id);

        $application->db->PrepareSQL($query);
        return $application->db->DB_Exec();
    }

    function zeroHotlinkTries($opid,$hl_id)
    {
        global $application;
        $tables = $this->getTables();
        $hl_table = $tables['pf_hotlinks']['columns'];

        $query = new DB_Update('pf_hotlinks');
        $query->addUpdateValue($hl_table['was_try'],'0');
        $query->WhereValue($hl_table['order_product_id'], DB_EQ, $opid);
        $query->WhereAND();
        $query->WhereValue($hl_table['hotlink_id'], DB_EQ, $hl_id);

        $application->db->PrepareSQL($query);
        return $application->db->DB_Exec();
    }

    function updateHotlinkExpireDate($opid,$hl_id,$ts)
    {
        global $application;
        $tables = $this->getTables();
        $hl_table = $tables['pf_hotlinks']['columns'];

        $query = new DB_Update('pf_hotlinks');
        $query->addUpdateValue($hl_table['expire_date'], $ts);
        $query->WhereValue($hl_table['order_product_id'], DB_EQ, $opid);
        $query->WhereAND();
        $query->WhereValue($hl_table['hotlink_id'], DB_EQ, $hl_id);

        $application->db->PrepareSQL($query);
        return $application->db->DB_Exec();
    }

    function getHotlinkInfoByKey($hl_key)
    {
        global $application;
        $tables = $this->getTables();
        $hl_table = $tables['pf_hotlinks']['columns'];

        $query = new DB_Select();
        $query->addSelectTable('pf_hotlinks');
        $query->addSelectField('*');
        $query->WhereValue($hl_table['hotlink_key'],DB_EQ,$hl_key);
        $res = $application->db->getDB_Result($query);

        if(empty($res))
            return null;
        else
            return array_shift($res);
    }

    function getFileInfoByHotlinkKey($hl_key)
    {
        if($hl_key == null or !is_string($hl_key))
            return null;

        $hl_info = $this->getHotlinkInfoByKey($hl_key);
        if($hl_info == null)
            return null;

        $pfile_info = $this->getPFileInfo($hl_info['file_id']);
        if($pfile_info == null)
            return null;

        return $pfile_info;
    }

    function isDownloadAllowed($hl_key)
    {
        if($hl_key == null or !is_string($hl_key))
            return false;

        $hl_info = $this->getHotlinkInfoByKey($hl_key);
        if($hl_info == null)
            return false;

        if($hl_info['was_try'] >= $hl_info['max_try']
            or $hl_info['expire_date'] < time()
            or $hl_info['status']!='U')
            return false;

        $pfile_info = $this->getPFileInfo($hl_info['file_id']);
        if($pfile_info == null)
            return false;

        if(!file_exists($pfile_info['file_path']) or !is_file($pfile_info['file_path']))
            return false;

        return true;
    }

    function sendProductFile($hl_key,$file_id=null)
    {
        if($file_id == null)
            $file_info = $this->getFileInfoByHotlinkKey($hl_key);
        else
            $file_info = $this->getPFileInfo($file_id);

        $from_byte = 0;
        $to_byte = $file_info['file_size'] - 1;

        // required for IE, otherwise Content-disposition is ignored
        if(ini_get('zlib.output_compression'))  ini_set('zlib.output_compression', 'Off');

        if(isset($_SERVER["HTTP_RANGE"]))
        {
            $range_text = $_SERVER["HTTP_RANGE"];
            $range_text = str_replace("bytes=","",$range_text);
            list($from_byte,$to_byte) = explode("-",$range_text);
            if($from_byte == '')
                $from_byte = 0;
            if($to_byte == '')
                $to_byte = $file_info['file_size'] - 1;
        };

        $fh = fopen($file_info['file_path'],'r');
        fseek($fh,$from_byte);

        $partial = false;

        if($from_byte != 0 or $to_byte != ($file_info['file_size']-1))
        {
            $partial = true;
        }

        if($from_byte == 0)
            $this->__increaseHotlinkTries($hl_key);

        if($partial)
        {
            header("HTTP/1.1 206 Partial content");
        }
        else
        {
            header("HTTP/1.1 200 OK\n");
        }

        header("Accept-Ranges: bytes");
        header("Pragma: public");
        header("Expires: " . gmdate("D, d M Y H:i:s", time()) . " GMT");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Content-Type: application/octet-stream");
        header("Content-Disposition: attachment; filename=\"".$file_info['file_name']."\"");

        if($partial)
        {
            header("Content-Range: ".$from_byte."-".$to_byte."/".$file_info['file_size']);
            header("Content-Length: ".($to_byte - $from_byte + 1));
        }
        else
        {
            header("Content-Length: ".$file_info['file_size']);
        };

        while(!feof($fh) and ftell($fh) < $to_byte)
            echo fread($fh,4096);

        fclose($fh);
    }

    function OnOrdersWereUpdated($statusChanged)
    {
        if(isset($statusChanged["payment_status"]) and is_array($statusChanged["payment_status"]))
        {
            foreach($statusChanged["payment_status"] as $order_id => $status_array)
            {
                if($status_array["new_status"] == 2) // Fully Paid
                    $hl_status = 'U';
                else
                    $hl_status = 'L';

                $this->__setStatusForAllHotlinksOfOrder($order_id,$hl_status);
            };
        };

        return;
    }

    function OnOrdersWillBeDeleted($orders_ids)
    {
        $opids = array();
        for($i=0;$i<count($orders_ids);$i++)
            $opids = array_merge($opids,modApiFunc('Checkout','getOrderProductsIDs',$orders_ids[$i]));

        global $application;
        $tables = $this->getTables();
        $hl_table = $tables['pf_hotlinks']['columns'];

        $query = new DB_Delete('pf_hotlinks');
        $query->Where($hl_table['order_product_id'], DB_IN, "('".implode("','",$opids)."')");
        $application->db->PrepareSQL($query);
        return $application->db->DB_Exec();
    }

    function delAllFilesFromProducts($products_ids)
    {
        global $application;
        $tables = $this->getTables();
        $files_table = $tables['pf_files']['columns'];

        $query = new DB_Select();
        $query->addSelectField($files_table['file_path']);
        $query->Where($files_table['product_id'], DB_IN, "('".implode("','",$products_ids)."')");
        $query->WhereAND();
        $query->WhereValue($files_table['is_uploaded'], DB_EQ, 'Y');
        $res = $application->db->getDB_Result($query);
        $this->unlinkFiles($res);

        $query = new DB_Delete('pf_files');
        $query->Where($files_table['product_id'], DB_IN, "('".implode("','",$products_ids)."')");
        $application->db->PrepareSQL($query);
        $application->db->DB_Exec();

        return;
    }

    function copyAllFilesFromProductToProduct($from_pid,$to_pid)
    {
        $src_files = $this->getFilesListForProduct($from_pid);
        if(count($src_files)==0)
            return;

        global $application;
        $i = 0;
        foreach($src_files as $k => $file_info)
        {
            if($file_info['is_uploaded'] == 'N')
            {
                $this->addFileToProduct($to_pid,$file_info['file_name'],$file_info['file_path'],$file_info['file_descr'],false);
            }
            else
            {
                do
                {
                    $new_file_path = $application->getAppIni('PRODUCT_FILES_DIR').'pfile_'.$to_pid.'_'.sprintf("%04d",$i);
                    $i++;
                }while(file_exists($new_file_path));

                if(copy($file_info['file_path'],$new_file_path))
                {
                    $this->addFileToProduct($to_pid,$file_info['file_name'],$new_file_path,$file_info['file_descr']);
                };
            };
        };

        return;
    }

    function getHotlinksListForOrder($order_id)
    {
        $opids = modApiFunc('Checkout','getOrderProductsIDs',$order_id);
        $hotlinks = array();
        for($i=0;$i<count($opids);$i++)
            $hotlinks = array_merge($hotlinks,$this->getHotlinksList($opids[$i]));

        return $hotlinks;
    }

    function updatePFileDescr($file_id,$file_descr)
    {
        global $application;
        $tables = $this->getTables();
        $file_table = $tables['pf_files']['columns'];

        $query = new DB_Update('pf_files');
        $query->addUpdateValue($file_table['file_descr'],$file_descr);
        $query->WhereValue($file_table['file_id'], DB_EQ, $file_id);
        $application->db->PrepareSQL($query);
        return $application->db->DB_Exec();
    }

    function __setStatusForAllHotlinksOfOrder($order_id,$status)
    {
        global $application;
        $tables = $this->getTables();
        $hl_table = $tables['pf_hotlinks']['columns'];

        $opids = modApiFunc('Checkout','getOrderProductsIDs',$order_id);

        if(!empty($opids))
        {
            $query = new DB_Update('pf_hotlinks');
            $query->addUpdateValue($hl_table['status'],$status);
            $query->Where($hl_table['order_product_id'], DB_IN, "('".implode("','",$opids)."')");

            $application->db->PrepareSQL($query);
            $application->db->DB_Exec();
        };
    }

    function __increaseHotlinkTries($hl_key)
    {
        global $application;
        $tables = $this->getTables();
        $hl_table = $tables['pf_hotlinks']['columns'];

        $query = new DB_Update('pf_hotlinks');
        $query->addUpdateExpression($hl_table['was_try'],'was_try+1');
        $query->WhereValue($hl_table['hotlink_key'],DB_EQ,$hl_key);

        $application->db->PrepareSQL($query);
        return $application->db->DB_Exec();
    }

    function isDownloadsDirNotWritable()
    {
        global $application;
        $dir = $application->getAppIni('PRODUCT_FILES_DIR');
        return (!is_dir($dir) or !is_writable($dir));
    }

};

?>