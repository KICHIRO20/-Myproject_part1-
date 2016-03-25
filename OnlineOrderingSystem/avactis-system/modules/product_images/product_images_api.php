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
 * @package ProductImages
 * @author Egor V. Derevyankin
 *
 */

class Product_Images
{
    function Product_Images()
    {
    }

    function install()
    {
        global $application;
        loadCoreFile('csv_parser.php');
        $csv_parser = new CSV_Parser();

        $tables = Product_Images::getTables();
        $query = new DB_Table_Create($tables);

        $table = 'pi_settings';
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
    }

    function uninstall()
    {
        $query = new DB_Table_Delete(Product_Images::getTables());
    }

    function getTables()
    {
        static $tables;

        if (is_array($tables))
        {
            return $tables;
        }

        $tables=array();

        $table='pi_settings';
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

        $table='pi_images';
        $tables[$table]=array();
        $tables[$table]['columns']=array(
            'image_id'      => $table.'.image_id'
           ,'product_id'    => $table.'.product_id'
           ,'image_path'    => $table.'.image_path'
           ,'image_mime_type' => $table.'.image_mime_type'
           ,'image_sizes'     => $table.'.image_sizes'
           ,'thumb_path'    => $table.'.thumb_path'
           ,'thumb_sizes'   => $table.'.thumb_sizes'
           ,'alt_text'      => $table.'.alt_text'
           ,'sort_order'    => $table.'.sort_order'
        );
        $tables[$table]['types']=array(
            'image_id'      => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
           ,'product_id'    => DBQUERY_FIELD_TYPE_INT .' NOT NULL DEFAULT 0'
           ,'image_path'    => DBQUERY_FIELD_TYPE_TEXT
           ,'image_mime_type'     => DBQUERY_FIELD_TYPE_CHAR255
           ,'image_sizes'   => DBQUERY_FIELD_TYPE_TEXT .' NOT NULL '
           ,'thumb_path'    => DBQUERY_FIELD_TYPE_TEXT
           ,'thumb_sizes'   => DBQUERY_FIELD_TYPE_TEXT
           ,'alt_text'      => DBQUERY_FIELD_TYPE_CHAR255 .' NOT NULL DEFAULT \'\''
           ,'sort_order'    => DBQUERY_FIELD_TYPE_INT .' NOT NULL DEFAULT 0'
        );
        $tables[$table]['primary']=array(
            'image_id'
        );
        $tables[$table]['indexes']=array(
            'IDX_pid' => 'product_id'
        );

        global $application;
        return $application->addTablePrefix($tables);
    }

    function getSettings()
    {
        $res=execQuery("SELECT_PRODUCT_IMAGES_SETTINGS",array());

        $settings=array();

        foreach($res as $k => $sval)
            $settings[$sval['setting_key']]=$sval['setting_value'];

        return $settings;
    }

    function updateSettings($settings)
    {
        global $application;
        $tables=$this->getTables();
        $stable=$tables['pi_settings']['columns'];

        foreach($settings as $skey => $sval)
        {
            $query = new DB_Update('pi_settings');
            $query->addUpdateValue($stable['setting_value'],$sval);
            $query->WhereValue($stable['setting_key'], DB_EQ, $skey);
            $application->db->PrepareSQL($query);
            $application->db->DB_Exec();
        };

        return;
    }

    function checkImageUploading()
    {
        if(!ini_get('file_uploads'))
            return 1;

        global $application;

        if(!is_dir($application->getAppIni('PATH_IMAGES_DIR')))
            return 2;

        if(!is_writable($application->getAppIni('PATH_IMAGES_DIR')))
            return 3;

        return 0;
    }

    function addImageToProduct($product_id,$image_path,$alt_text,$thumb_path=null)
    {
        global $application;
        $tables = $this->getTables();
        $imgs_table = $tables['pi_images']['columns'];

        // detailed image resizing
		$piSettings = modApiFunc('Product_Images','getSettings');
		if($piSettings['RESIZE_DETAILED_LARGE_IMAGE'] == 'Y' && function_exists('gd_info'))
		{
			$image_path = modApiFunc('Product_Images', 'resizeImage', $image_path, $piSettings['DETAILED_LARGE_IMAGE_SIZE']);
		}

        $_is = getimagesize($image_path);

        $img_sizes = array(
            'width' => $_is[0]
           ,'height' => $_is[1]
           ,'filesize' => filesize($image_path)
        );
		$image_path = str_replace($application->getAppIni('PATH_IMAGES_DIR'),'',$image_path);
        $query = new DB_Insert('pi_images');
        $query->addInsertValue($product_id,$imgs_table['product_id']);
        $query->addInsertValue($image_path,$imgs_table['image_path']);
        $query->addInsertValue(serialize($img_sizes),$imgs_table['image_sizes']);
        $query->addInsertValue($_is['mime'],$imgs_table['image_mime_type']);
        $query->addMultiLangInsertValue($alt_text, $imgs_table['alt_text'],
                                        $imgs_table['image_id'],
                                        'Product_Images');
        $query->addInsertValue($this->__getMaxSortOrderOfProductImages($product_id)+1,$imgs_table['sort_order']);

        if($thumb_path != null)
        {
            $_ts = getimagesize($thumb_path);
            $thumb_sizes = array(
                'width' => $_ts[0]
               ,'height' => $_ts[1]
               ,'filesize' => filesize($thumb_path)
            );
			$thumb_path = str_replace($application->getAppIni('PATH_IMAGES_DIR'),'',$thumb_path);
            $query->addInsertValue($thumb_path,$imgs_table['thumb_path']);
            $query->addInsertValue(serialize($thumb_sizes),$imgs_table['thumb_sizes']);
        }

        $application->db->getDB_Result($query);

        $file_id = $application->db->DB_Insert_Id();

        return $file_id;
    }

    function delImagesFromProduct($product_id,$images_ids,$del_from_server)
    {
        global $application;
        $tables = $this->getTables();
        $imgs_table = $tables['pi_images']['columns'];

        $query = new DB_Select();
        $query->addSelectField($imgs_table['image_path']);
        $query->addSelectField($imgs_table['thumb_path']);
        $query->WhereValue($imgs_table['product_id'], DB_EQ, $product_id);
        $query->WhereAND();
        $query->Where($imgs_table['image_id'],DB_IN,'(\''.implode('\',\'',$images_ids).'\')');
        $res = $application->db->getDB_Result($query);

		if(strcmp($del_from_server,'yes') == 0)
        	$this->unlinkFiles($res);

        $query = new DB_Delete('pi_images');
        $query -> deleteMultiLangField($imgs_table['alt_text'],
                                       $imgs_table['image_id'],
                                       'Product_Images');
        $query->WhereValue($imgs_table['product_id'],DB_EQ,$product_id);
        $query->WhereAND();
        $query->Where($imgs_table['image_id'],DB_IN,'(\''.implode('\',\'',$images_ids).'\')');

        $application->db->getDB_Result($query);

        return;
    }

    function unlinkFiles($paths)
    {
		global $application;
        for($i=0;$i<count($paths);$i++)
        {
			$mainImg = $paths[$i]['image_path'];
			$thumbImg = $paths[$i]['thumb_path'];

			if(!file_exists($mainImg))
				$mainImg = $application->getAppIni('PATH_IMAGES_DIR').$mainImg;
			if(!file_exists($thumbImg))
				$thumbImg = $application->getAppIni('PATH_IMAGES_DIR').$thumbImg;

            @unlink($mainImg);
            @unlink($thumbImg);
        };
        return;
    }

    function updateImagesOfProduct($images_data)
    {
        if(empty($images_data))
            return;

        global $application;
        $tables = $this->getTables();
        $images_table = $tables['pi_images']['columns'];

        foreach($images_data as $image_id => $image_info)
        {
            $query = new DB_Update('pi_images');
            $query -> addMultiLangUpdateValue($images_table['alt_text'],
                                              $image_info['alt_text'],
                                              $images_table['image_id'],
                                              $image_id, 'Product_Images');
            $query->WhereValue($images_table['image_id'], DB_EQ, $image_id);
            $application->db->getDB_Result($query);
        };

        return;
    }

    function updateImagesSortOrder($product_id,$sort_order)
    {
        global $application;
        $tables = $this->getTables();
        $images_table = $tables['pi_images']['columns'];

        for($i=0;$i<count($sort_order);$i++)
        {
            $query = new DB_Update('pi_images');
            $query->addUpdateValue($images_table['sort_order'],$i);
            $query->WhereValue($images_table['product_id'], DB_EQ, $product_id);
            $query->WhereAND();
            $query->WhereValue($images_table['image_id'], DB_EQ, $sort_order[$i]);
            $application->db->PrepareSQL($query);
            $application->db->DB_Exec();
        }

        return;
    }

    function getImagesListForProduct($product_id)
    {
        $params = array("product_id" => $product_id);

        return execQuery("SELECT_PRODUCT_IMAGES_LIST", $params);
    }

    /**
     * @return array('error','full_path','base_name','file_size')
     */
    function moveUploadedFileToImagesDir($product_id,$src_fname,$img_prefix="")
    {
        global $application;

        $result = array(
            'error' => ''
           ,'full_path' => ''
           ,'base_name' => ''
           ,'http_link' => ''
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

        $_tmp = getimagesize($src_file['tmp_name']);

        if($_tmp === false)
        {
            $result['error'] = UPLOAD_FILE_IS_NOT_IMAGE;
            return $result;
        }

        $img_ext = str_replace("image/",".",$_tmp['mime']);

        $dest_dir = $application->getAppIni('PATH_IMAGES_DIR');

        do
        {
            $new_image_name = $application->getUploadImageName($src_file['name']);
        }while(file_exists($new_image_name));


        if(!move_uploaded_file($src_file['tmp_name'],$new_image_name))
        {
            $result['error'] = UPLOAD_ERR_CANT_MOVE_FILE;
            return $result;
        };

        $result['full_path'] = $new_image_name;
        $result['base_name'] = basename($new_image_name);
        $result['http_link'] = $application->getAppIni('URL_IMAGES_DIR').$result['base_name'];

        return $result;
    }

    function moveImageToImagesDir($product_id,$image_path)
    {
        global $application;

        $_tmp = getimagesize($image_path);
        $img_ext = str_replace("image/",".",$_tmp['mime']);

        $dest_dir = $application->getAppIni('PATH_IMAGES_DIR');

        do{
            $dest_path = $application->getUploadImageName(basename($image_path));
        }while(file_exists($dest_path));

        if(!rename($image_path,$dest_path))
            return null;
        else
            return $dest_path;
    }

    function copyImageToImagesDir($product_id,$image_path)
    {
        global $application;

        $_tmp = getimagesize($image_path);
        $img_ext = str_replace("image/",".",$_tmp['mime']);

        $dest_dir = $application->getAppIni('PATH_IMAGES_DIR');

        do{
            $dest_path = $application->getUploadImageName(basename($image_path));
        }while(file_exists($dest_path));

        if(!copy($image_path,$dest_path))
            return null;
        else
            return $dest_path;
    }

    function convertSizes($width, $height, $need)
    {
        if ($width <= $need && $height <= $need)
            return array ($width,$height);

        if($width >= $height)
        {
            $height = $height * $need / $width;
            $width = $need;
        }
        else
        {
            $width = $width * $need / $height;
            $height = $need;
        }
        return array (round($width), round($height));
    }

    /*
     * this function returns an image resource given it's path,
     * or NULL if unsuccessful
     */
    function getImageRes($imagePath)
    {
        if (!function_exists('gd_info'))
            return null;

        $imgInfo = getimagesize($imagePath);

        $imageRes = null;
        switch ($imgInfo[2])
        {
            case IMAGETYPE_GIF:
                $imageRes = imagecreatefromgif($imagePath);
                break;

            case IMAGETYPE_JPEG:
                $imageRes = imagecreatefromjpeg($imagePath);
                break;

            case IMAGETYPE_PNG:
                $imageRes = imagecreatefrompng($imagePath);
                break;

            case IMAGETYPE_BMP:
            case IMAGETYPE_WBMP:
                $imageRes = imagecreatefromwbmp($imagePath);
                break;

            case IMAGETYPE_XBM:
                $imageRes = imagecreatefromxbm($imagePath);
                break;
        };

        return $imageRes;
    }

    function genThumbnail($product_id, $original_image_path, $thumb_side=null, $subfolder = '')
    {
        if(!function_exists('gd_info'))
            return null;

        $img_info = getimagesize($original_image_path);
        if(!(imagetypes() & $this->__imageTypeToBit($img_info[2])))
            return null;

        $oimage = $this->getImageRes($original_image_path);
        if ($oimage == null)
            return null;

        $sets = $this->getSettings();
        if ($thumb_side == null || intval($thumb_side) <= 0)
        {
            $thumb_side = $sets['THUMB_SIDE'];
        }
        else
        {
            $thum_side = intval($thumb_side);
        };

        list ($dst_x,$dst_y) = $this->convertSizes($img_info[0], $img_info[1], $thumb_side);
        $dst_image = imagecreatetruecolor($dst_x, $dst_y);
	imagecolortransparent($dst_image, imagecolorallocatealpha($dst_image, 0, 0, 0, 127));
	imagealphablending($dst_image, false);
	imagesavealpha($dst_image, true);
        imagecopyresampled($dst_image, $oimage, 0, 0, 0, 0, $dst_x, $dst_y, $img_info[0], $img_info[1]);

        global $application;

        $large_image_path_parts = pathinfo($original_image_path);
        $large_image_basename = $large_image_path_parts['basename'];
        $large_image_ext = $large_image_path_parts['extension'];
        $large_image_basename_without_ext = _ml_substr($large_image_basename, 0, -1 * (_ml_strlen("." . $large_image_ext)));

        $thumb_basename = "thumb_" . $large_image_basename_without_ext . "." . $large_image_ext;
        $new_image_path = $application->getUploadImageName($thumb_basename, $subfolder);

		if ($large_image_ext == 'png' && imagepng($dst_image, $new_image_path))
            return $new_image_path;
        if (imagejpeg($dst_image, $new_image_path, $application->getAppIni('JPEG_THUMBNAIL_QUALITY')))
            return $new_image_path;
        else
            return null;
    }

    /*
     * this function gets an image's path, a desired side size
     * and returns a resized image's path if we need to shrink it
     */
    function resizeImage($path, $side)
    {
        // first check if we need to do any resizing at all
        $imageInfo = getimagesize($path);
        if ($imageInfo[0] <= $side && $imageInfo[1] <= $side)
        {
            // image is too small already, return
            return $path;
        }

        list ($destW, $destH) = $this->convertSizes($imageInfo[0], $imageInfo[1], $side);
        $src = $this->getImageRes($path);
        $dest = imagecreatetruecolor($destW, $destH);
	imagecolortransparent($dest, imagecolorallocatealpha($dest, 0, 0, 0, 127));
	imagealphablending($dest, false);
	imagesavealpha($dest, true);
        imagecopyresampled($dest, $src, 0, 0, 0, 0, $destW, $destH, $imageInfo[0], $imageInfo[1]);
        imagepng($dest, $path);

        return $path;
    }

    function genPngThumbnailInMemory($original_image_path,$thumb_side=null)
    {
        if(!function_exists('gd_info'))
            return null;

        $img_info = getimagesize($original_image_path);

        if(!(imagetypes() & $this->__imageTypeToBit($img_info[2])))
            return null;

        $oimage = null;
        switch($img_info[2])
        {
            case IMAGETYPE_GIF:
                    $oimage = imagecreatefromgif($original_image_path);
                    break;
            case IMAGETYPE_JPEG:
                    $oimage = imagecreatefromjpeg($original_image_path);
                    break;
            case IMAGETYPE_PNG:
                    $oimage = imagecreatefrompng($original_image_path);
                    break;
            case IMAGETYPE_BMP:
            case IMAGETYPE_WBMP:
                    $oimage = imagecreatefromwbmp($original_image_path);
                    break;
            case IMAGETYPE_XBM:
                    $oimage = imagecreatefromxbm($original_image_path);
                    break;
        };

        if($oimage == null)
            return null;

        $sets = $this->getSettings();
        if($thumb_side == null or intval($thumb_side) <= 0)
        {
            $thumb_side = $sets['THUMB_SIDE'];
        }
        else
        {
            $thum_side = intval($thumb_side);
        };

        list($dst_x,$dst_y) = $this->convertSizes($img_info[0],$img_info[1],$thumb_side);
        $dst_image = imagecreatetruecolor($dst_x,$dst_y);
        imagecopyresampled($dst_image, $oimage, 0, 0, 0, 0, $dst_x, $dst_y, $img_info[0], $img_info[1]);

        $ret = imagepng($dst_image);
        if($ret === TRUE)
            return $ret;
        else
            return null;
    }

    function delAllImagesFromProducts($products_ids)
    {
        global $application;
        $tables = $this->getTables();
        $images_table = $tables['pi_images']['columns'];

        $query = new DB_Select();
        $query->addSelectField($images_table['image_path']);
        $query->addSelectField($images_table['thumb_path']);
        $query->Where($images_table['product_id'], DB_IN, "('".implode("','",$products_ids)."')");
        $res = $application->db->getDB_Result($query);
        if(count($res) > 0)
        {
            //$this->unlinkFiles($res);

            $query = new DB_Delete('pi_images');
            $query -> deleteMultiLangField($images_table['alt_text'],
                                           $images_table['image_id'],
                                           'Product_Images');
            $query->Where($images_table['product_id'], DB_IN, "('".implode("','",$products_ids)."')");
            $application->db->getDB_Result($query);
        };

        return;
    }

    function copyAllImagesFromProductToProduct($from_pid, $to_pid)
    {
        $src_images = $this->getImagesListForProduct($from_pid);
        if(count($src_images)==0)
            return;

        foreach($src_images as $k => $image_info)
        {
            $new_image_path = $this->copyImageToImagesDir($to_pid,$image_info['image_path']);

            if($new_image_path != null)
            {
                if($image_info['thumb_path']!='')
                    $new_thumb_path = $this->copyImageToImagesDir($to_pid,$image_info['thumb_path']);
                else
                    $new_thumb_path = null;

                $this->addImageToProduct($to_pid,$new_image_path,$image_info['alt_text'],$new_thumb_path);
            };
        };
    }

    function getImageURL($path)
    {
        global $application;
		if(file_exists($application->getAppIni('PATH_IMAGES_DIR').$path))
			return $application->getAppIni('URL_IMAGES_DIR').$path;
		else
			return $application->getAppIni('URL_IMAGES_DIR').basename($path);
    }

    function __imageTypeToBit($type)
    {
        switch($type)
        {
            case IMAGETYPE_GIF:
                return IMG_GIF;
            case IMAGETYPE_JPEG:
                return IMG_JPEG;
            case IMAGETYPE_PNG:
                return IMG_PNG;
            case IMAGETYPE_BMP:
            case IMAGETYPE_WBMP:
                return IMG_WBMP;
            case IMAGETYPE_XBM:
                return IMG_XPM;
        };

        return 0;
    }

    function __getMaxSortOrderOfProductImages($product_id)
    {
        global $application;
        $tables = $this->getTables();
        $imgs_table = $tables['pi_images']['columns'];

        $query = new DB_Select();
        $query->addSelectField($query->fMax($imgs_table['sort_order']), 'max_sort_order');
        $query->WhereValue($imgs_table['product_id'], DB_EQ, $product_id);
        $res = $application->db->getDB_Result($query);
        return $res[0]['max_sort_order'];
    }
};

?>