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
 * @package Images
 * @author Vadim Lyalikov
 *
 */

class Images
{
    function Images()
    {}

    function install()
    {
        $query = new DB_Table_Create(Images::getTables());
        modApiFunc('EventsManager',
               'addEventHandler',
               'ImageFKRemovedEvent',
               'Images',
               'OnImageFKRemoved');
    }

    function uninstall()
    {
        $query = new DB_Table_Delete(Images::getTables());
    }

    function getTables()
    {
        global $application;
        static $tables;

        if (is_array($tables))
        {
            return $tables;
        };

        $table = 'images';
        $tables[$table] = array(
            'columns'   => array(
                'image_id'        => $table.'.image_id'
               ,'image_media'     => $table.'.image_media'
               ,'image_path'      => $table.'.image_path'
               ,'image_url'       => $table.'.image_url'
               ,'image_mime_type' => $table.'.image_mime_type'
               ,'image_width'     => $table.'.image_width'
               ,'image_height'    => $table.'.image_height'
               ,'image_filesize'  => $table.'.image_filesize'
               ,'image_alt_text'  => $table.'.image_alt_text'
             )
           ,'types'     => array(
                'image_id'        => DBQUERY_FIELD_TYPE_INT.' not null auto_increment'
               ,'image_media'     => DBQUERY_FIELD_TYPE_IMAGE_MEDIA
               ,'image_path'      => DBQUERY_FIELD_TYPE_TEXT
               ,'image_url'       => DBQUERY_FIELD_TYPE_TEXT
               ,'image_mime_type' => DBQUERY_FIELD_TYPE_CHAR255
               ,'image_width'     => DBQUERY_FIELD_TYPE_INT
               ,'image_height'    => DBQUERY_FIELD_TYPE_INT
               ,'image_filesize'  => DBQUERY_FIELD_TYPE_INT
               ,'image_alt_text'  => DBQUERY_FIELD_TYPE_CHAR255
             )
           ,'primary'   => array(
                'image_id'
             )
        );

        //                                                         .
        $table = 'image_thumbnails';
        $tables[$table] = array(
            'columns'   => array(
                'image_id'           => $table.'.image_id'
               ,'thumbnail_image_id' => $table.'.thumbnail_image_id'
             )
           ,'types'     => array(
                'image_id'           => DBQUERY_FIELD_TYPE_INT.' not null DEFAULT 0'
               ,'thumbnail_image_id' => DBQUERY_FIELD_TYPE_INT.' not null DEFAULT 0'
             )
        );
        return $application->addTablePrefix($tables);
    }

	//========================================================================================================//
	//=== PUBLIC functions  ==================================================================================//
	//========================================================================================================//

    /**
     *                                        .
     * :                                            .
     *
     * @param unknown_type $image_id
     * @return unknown
     */
    function getImageData($image_id)
    {
        if($image_id === null)
        {
            $image_data = null;
            $image_thumbnail_data = null;
        }
        else
        {
            $image_data = modApiFunc("Images", "selectImage", $image_id);
            $image_thumbnail_data = modApiFunc("Images", "selectImageThumbnail", $image_id, IMAGE_THUMB_SIZE);
        }

        $image_obj = NULL;
        if($image_data === NULL)
        {
            $image_obj = new image_obj();
            $image_obj->set_error("ERR_IMAGE_ID_NOT_FOUND_IN_DB", $image_id);
        }
        else
        {
        	$image_data['image_src'] = modApiFunc("Images", "getImageSRC", $image_data);
            $image_thumbnail_data['image_src'] = modApiFunc("Images", "getImageSRC", $image_thumbnail_data);
            $image_obj = new image_obj($image_id);
        }

        $res = array
        (
            'image_obj' => $image_obj
           ,'image_data' => $image_data
           ,'image_thumbnail_data' => $image_thumbnail_data
        );
        return $res;
    }


    /**
     *                 -Image Action' .
     *           Manufacturers :: AddManufacturer:
     *
     *           $image_obj = modApiFunc("Images", "processImageInput", "mnf_image");
     *           $error = $image_obj->get_error();
     *           if($error != NULL)
     *           {
     *              ...
     *           }
     *
     *
     *
     *                                        (                             ,
     *         ,                  . .)                           image_obj                     .
     *
     *                            (local_file, server_file, url),                                   .
     *                  ,                                              ,    image_id                ,
     *                                     .
     *
     *                                       ,               alt_text,
     *          .
     *
     *                                       ,                                        ,
     *        image_obj.
     */
    function processImageInput($image_input_id)
    {
        global $application;
        $request = &$application->getInstance('Request');

        //js2php:               ,
        //          .
        $image_id        = $request->getValueByKey('ii_image_id_'.$image_input_id);
        if(empty($image_id))
        {
            $image_id = NULL;
        }

        $image_error_code = $request->getValueByKey('ii_error_code_'.$image_input_id);
        $image_error_msg = $request->getValueByKey('ii_error_msg_'.$image_input_id);
        if(empty($image_error_code))
        {
            $image_error_code = NULL;
            $image_error_msg = ''; //
        }

        $image_obj = new image_obj($image_id, $image_error_code, $image_error_msg);
        //             .

        $source = $request->getValueByKey('ii_source_'.$image_input_id);
        $b_source_empty = false;
        switch($source)
        {
            case NULL:
            case "":
            {
                break;
            }
            case 'local_file':
            {
                if(isset($_FILES["ii_input_file_".$image_input_id]) &&
                    !empty($_FILES["ii_input_file_".$image_input_id]['name']))
                {
                    $new_image_obj = modApiFunc("Images", "process_images_upload_local_file" , "ii_alt_text_".$image_input_id, "ii_input_file_".$image_input_id, 'ii_image_id_'.$image_input_id);
                    //                             ,                  ,    image_id                .
                    if(!$image_obj->is_empty() && $new_image_obj->is_empty())
                    {
                        $new_image_obj->set_id($image_obj->get_id());
                    }
                    $image_obj = $new_image_obj;
                }
                else
                {
                    //               ,                                    .
                    $b_source_empty = true;
                }
                break;
            }
            case 'server_file':
            {

                $path = $request->getValueByKey("server_only_server_file_ii_server_file_".$image_input_id);
                if(!empty($path))
                {
                    $new_image_obj = modApiFunc("Images", "process_images_upload_server_file" , "ii_alt_text_".$image_input_id, "server_only_server_file_ii_server_file_".$image_input_id, 'ii_image_id_'.$image_input_id);
                    //                             ,                  ,    image_id                .
                    if(!$image_obj->is_empty() && $new_image_obj->is_empty())
                    {
                        $new_image_obj->set_id($image_obj->get_id());
                    }
                    $image_obj = $new_image_obj;
                }
                else
                {
                    //               ,                                    .
                    $b_source_empty = true;
                }
                break;
            }
            case 'url':
            {
                $url = $request->getValueByKey("ii_url_".$image_input_id);
                if(!empty($url) &&
                   $url != getMsg("IMG", "EMPTY_URL"))
                {
                    $new_image_obj = modApiFunc("Images", "process_images_upload_url" , "ii_alt_text_".$image_input_id, "ii_url_".$image_input_id, 'ii_image_id_'.$image_input_id);
                    //                             ,                  ,    image_id                .
                    if(!$image_obj->is_empty() && $new_image_obj->is_empty())
                    {
                        $new_image_obj->set_id($image_obj->get_id());
                    }
                    $image_obj = $new_image_obj;
                }
                else
                {
                    //          .
                    $b_source_empty = true;
                }
                break;
            }
            default:
            {
                //                                    ,  . .              -       (local_file     url)
                //                              .
                break;
            }
        }

        //                source,             alt_text.
        if($b_source_empty === true &&
           $image_obj->get_id() !== null)
        {
            //                 alt_text
            $image_obj = modApiFunc("Images", "process_images_update_alt_text" , "ii_alt_text_".$image_input_id, $image_obj->get_id());
        }
        return $image_obj;
    }

    //========================================================================================================//
    //=== PRIVATE functions  =================================================================================//
    //========================================================================================================//

    function OnImageFKRemoved($image_id)
    {
    	//    -                           .
    	//       .
    	//:          ,                                   .
    	modApiFunc("Images", "deleteImages", array($image_id));
    }

    function getAZImageSRC($short_name)
    {
    	global $application;
        return 'images/' .  basename($short_name);
    }

    function getImageSRC($db_image)
    {
    	global $application;
    	switch($db_image['image_media'])
    	{
    		case IMAGE_MEDIA_SERVER_PATH:
    	    {
    	    	$value = $application->getAppIni('URL_IMAGES_DIR').basename($db_image['image_path']);
    	    	break;
    	    }
    		case IMAGE_MEDIA_URL:
    	    {
    	    	//:                           http/https.
    	    	$value = $db_image['image_url'];
    		    break;
    	    }
    		case IMAGE_MEDIA_THEME_PATH:
    			$value = $db_image['image_path'];
    			break;
    		default:
    	    {
    	    	$value = NULL;
    	    	break;
    	    }
    	}
    	return $value;
    }

    function insertImage($data)
    {
    	global $application;
        execQuery('INSERT_IMAGE',$data);

        $image_id = $application->db->DB_Insert_Id();
        return $image_id;
    }

    function insertThumbnail($image_id, $thumbnail_image_id)
    {
        global $application;
        execQuery('INSERT_IMAGE_THUMBNAIL', array('image_id' => $image_id, 'thumbnail_image_id' => $thumbnail_image_id));
    }

    function updateImage($data)
    {
        global $application;
        execQuery('UPDATE_IMAGE',$data);
    }

    function unlinkFile($path)
    {
        @unlink($path);
        return;
    }

    //       id-       thumbnail-  ,                      .
    function getThumbnailsList($image_id)
    {
        $result = execQuery('SELECT_IMAGE_THUMBNAILS_LIST', array('image_id' => $image_id));
        if(empty($result))
        {
            return NULL;
        }
        else
        {
        	$res = array();
        	foreach($result as $item)
        	{
                $res[] = $item['thumbnail_image_id'];
        	}
        	return $res;
        }
    }

    //
    function deleteImageThumbnails($image_id)
    {
    	//                          ,                         .
    	$thumbs_list = modApiFunc("Images", "getThumbnailsList", $image_id);
    	if(!empty($thumbs_list))
    	{
   			//
   			modApiFunc("Images", "deleteImages", $thumbs_list);
    	}
        execQuery('DELETE_IMAGE_THUMBNAILS', array('image_id' => $image_id));
    }

    function deleteImages($image_id_list)
    {
    	if(!empty($image_id_list))
    	{
    	    foreach($image_id_list as $image_id)
	        {
	            $image_data = modApiFunc("Images", "selectImage", $image_id);
	            if($image_data['image_media'] == IMAGE_MEDIA_SERVER_PATH)
	            {
	                modApiFunc("Images", "unlinkFile", $image_data['image_path']);
	            }
	            //                 .
	            //                      ,  . .
	            //                                          .
	            //:               .
	            modApiFunc("Images", "deleteImageThumbnails", $image_id);
	        }

    	    execQuery('DELETE_IMAGES', array('image_id_list' => $image_id_list));
    	}
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
                $imageRes = $this->imagecreatefrombmp($imagePath);
                break;

            case IMAGETYPE_WBMP:
                $imageRes = imagecreatefromwbmp($imagePath);
                break;

            case IMAGETYPE_XBM:
                $imageRes = imagecreatefromxbm($imagePath);
                break;
        };

        return $imageRes;
    }

    /**
     * creates an image resource from given .BMP file
     *
     * @param unknown_type $filename
     * @return unknown
     */
    function ImageCreateFromBMP($filename)
    {
        if (! $f1 = fopen($filename,"rb")) return FALSE;

        $FILE = unpack("vfile_type/Vfile_size/Vreserved/Vbitmap_offset", fread($f1,14));
        if ($FILE['file_type'] != 19778)
        {
            return FALSE;
        }

        $BMP = unpack('Vheader_size/Vwidth/Vheight/vplanes/vbits_per_pixel'.
                     '/Vcompression/Vsize_bitmap/Vhoriz_resolution'.
                     '/Vvert_resolution/Vcolors_used/Vcolors_important', fread($f1,40));

        $BMP['colors'] = pow(2,$BMP['bits_per_pixel']);
        if ($BMP['size_bitmap'] == 0)
        {
            $BMP['size_bitmap'] = $FILE['file_size'] - $FILE['bitmap_offset'];
        }

        $BMP['bytes_per_pixel'] = $BMP['bits_per_pixel']/8;
        $BMP['bytes_per_pixel2'] = ceil($BMP['bytes_per_pixel']);
        $BMP['decal'] = ($BMP['width']*$BMP['bytes_per_pixel']/4);
        $BMP['decal'] -= floor($BMP['width']*$BMP['bytes_per_pixel']/4);
        $BMP['decal'] = 4-(4*$BMP['decal']);
        if ($BMP['decal'] == 4)
        {
            $BMP['decal'] = 0;
        }

        $PALETTE = array();
        if ($BMP['colors'] < 16777216)
        {
            $PALETTE = unpack('V'.$BMP['colors'], fread($f1,$BMP['colors']*4));
        }

        $IMG = fread($f1,$BMP['size_bitmap']);
        $VIDE = _byte_chr(0);

        $res = imagecreatetruecolor($BMP['width'],$BMP['height']);
        $P = 0;
        $Y = $BMP['height']-1;
        while ($Y >= 0)
        {
            $X=0;
            while ($X < $BMP['width'])
            {
                if ($BMP['bits_per_pixel'] == 24)
                {
                    $COLOR = unpack("V",_byte_substr($IMG,$P,3).$VIDE);
                }
                elseif ($BMP['bits_per_pixel'] == 16)
                {
                    $COLOR = unpack("n",_byte_substr($IMG,$P,2));
                    $COLOR[1] = $PALETTE[$COLOR[1]+1];
                }
                elseif ($BMP['bits_per_pixel'] == 8)
                {
                    $COLOR = unpack("n",$VIDE._byte_substr($IMG,$P,1));
                    $COLOR[1] = $PALETTE[$COLOR[1]+1];
                }
                elseif ($BMP['bits_per_pixel'] == 4)
                {
                    $COLOR = unpack("n",$VIDE._byte_substr($IMG,floor($P),1));
                    if (($P*2)%2 == 0)
                    {
                        $COLOR[1] = ($COLOR[1] >> 4);
                    }
                    else
                    {
                        $COLOR[1] = ($COLOR[1] & 0x0F);
                    }
                    $COLOR[1] = $PALETTE[$COLOR[1]+1];
                }
                elseif ($BMP['bits_per_pixel'] == 1)
                {
                    $COLOR = unpack("n",$VIDE._byte_substr($IMG,floor($P),1));
                    if     (($P*8)%8 == 0) $COLOR[1] =  $COLOR[1]        >>7;
                    elseif (($P*8)%8 == 1) $COLOR[1] = ($COLOR[1] & 0x40)>>6;
                    elseif (($P*8)%8 == 2) $COLOR[1] = ($COLOR[1] & 0x20)>>5;
                    elseif (($P*8)%8 == 3) $COLOR[1] = ($COLOR[1] & 0x10)>>4;
                    elseif (($P*8)%8 == 4) $COLOR[1] = ($COLOR[1] & 0x8)>>3;
                    elseif (($P*8)%8 == 5) $COLOR[1] = ($COLOR[1] & 0x4)>>2;
                    elseif (($P*8)%8 == 6) $COLOR[1] = ($COLOR[1] & 0x2)>>1;
                    elseif (($P*8)%8 == 7) $COLOR[1] = ($COLOR[1] & 0x1);
                    $COLOR[1] = $PALETTE[$COLOR[1]+1];
                }
                else
                {
                    return FALSE;
                }
                imagesetpixel($res,$X,$Y,$COLOR[1]);
                $X++;
                $P += $BMP['bytes_per_pixel'];
            }
            $Y--;
            $P+=$BMP['decal'];
        }

        fclose($f1);

        return $res;
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

    function createImageThumbnail($image_id, $thumbnail_side)
    {
        global $application;
    	//                                           ,
    	//  thumbnail.               null.                                ,
    	//               null.
    	$image_data = modApiFunc("Images", "selectImage", $image_id);

    	if($image_data !== NULL &&
    	   $image_data['image_media'] == IMAGE_MEDIA_SERVER_PATH)
    	{
    		//                      gd.
            $original_image_path = $image_data['image_path'];
	        if(!function_exists('gd_info'))
	            return NULL;

	        $img_info = getimagesize($original_image_path);
	        if(!(imagetypes() & $this->__imageTypeToBit($img_info[2])))
	            return null;

	        $oimage = $this->getImageRes($original_image_path);
	        if ($oimage == null)
	            return null;

	        list ($dst_x,$dst_y) = $this->convertSizes($img_info[0], $img_info[1], $thumbnail_side);
	        $dst_image = imagecreatetruecolor($dst_x, $dst_y);
	        imagecopyresampled($dst_image, $oimage, 0, 0, 0, 0, $dst_x, $dst_y, $img_info[0], $img_info[1]);

	        $large_image_path_parts = pathinfo($original_image_path);
	        $large_image_basename = $large_image_path_parts['basename'];
	        $large_image_ext = $large_image_path_parts['extension'];
	        $large_image_basename_without_ext = _ml_substr($large_image_basename, 0, -1 * (_ml_strlen("." . $large_image_ext)));

	        $thumb_basename = "thumb_" . $large_image_basename_without_ext . '.jpeg';
            $new_image_path = $application->getUploadImageName($thumb_basename);

	        if (imagejpeg($dst_image, $new_image_path, $application->getAppIni('JPEG_THUMBNAIL_QUALITY')))
	        {
		        $_is = getimagesize($new_image_path);
	            $data = array
	            (
	                'image_media' => IMAGE_MEDIA_SERVER_PATH
	               ,'image_path' => $new_image_path
	               ,'image_url' => ""
	               ,'image_mime_type'  => $_is['mime']
	               ,'image_width' => $_is[0]
	               ,'image_height' => $_is[1]
	               ,'image_filesize' => filesize($new_image_path)
	               ,'image_alt_text' => $image_data['image_alt_text']
	            );
	        	$thumbnail_image_id = modApiFunc("Images", "insertImage", $data);

	        	modApiFunc("Images", "insertThumbnail", $image_id, $thumbnail_image_id);
	            return $thumbnail_image_id;
	        }
	        else
	        {
	            return null;
	        }
    	}
    	else
    	{
    	}
    }

    function selectImageThumbnail($image_id, $thumbnail_side)
    {
    	//                                   $thumbnail_side,                .
    	$image_data = modApiFunc("Images", "selectImage", $image_id);
        list ($dst_x,$dst_y) = $this->convertSizes($image_data['image_width'], $image_data['image_height'], $thumbnail_side);
        if($dst_x == $image_data['image_width'] &&
           $dst_y == $image_data['image_height'])
        {
        	return $image_data;
        }
        else
        {
	        //                     image_thumbnails.                   thumbnail
	        //                       ,                      .               null.
	        //                                         ,             .
	        $result = execQuery('SELECT_IMAGE_THUMBNAIL', array('image_id' => $image_id
	                                                           ,'thumbnail_side' => $thumbnail_side));
	        $data = null;
	        if(empty($result))
	        {
	            //                                               .
	            //                                                               .
	            $thumbnail_image_id = modApiFunc("Images", "createImageThumbnail", $image_id, $thumbnail_side);
	            if($thumbnail_image_id !== null)
	            {
	                $result = execQuery('SELECT_IMAGE_THUMBNAIL', array('image_id' => $image_id
	                                                                   ,'thumbnail_side' => $thumbnail_side));
	                if(!empty($result))
	                {
	                    $data = $result[0];
	                }
	            }

	            if($data === null)
	            {
	                //                                        .
	                //             image   image_media = URL,          gd                . .
	                $data = modApiFunc("Images", "selectImage", $image_id);
	                //
	                if($data['image_media'] == IMAGE_MEDIA_SERVER_PATH)
	                {
	                    list ($dst_x,$dst_y) = $this->convertSizes($data['image_width'], $data['image_height'], $thumbnail_side);
	                    $data['image_width'] = $dst_x;
	                    $data['image_height'] = $dst_y;
	                }
	                else if($data['image_media'] == IMAGE_MEDIA_URL)
	                {
	                    $data['image_width'] = $thumbnail_side;
	                    $data['image_height'] = "";
	                }
	            }
	        }
	        else
	        {
	            $data = $result[0];
	        }
	        return $data;
        }
    }

    function selectImage($image_id)
    {
    	if($image_id === NULL || empty($image_id) || is_numeric($image_id) !== TRUE)
    	{
    		return NULL;
    	}
    	else
    	{
            $result = execQuery('SELECT_IMAGE', array('image_id' => $image_id));
            if(empty($result))
            {
            	return NULL;
            }
            else
            {
            	return $result[0];
            }
    	}
    }

    /**
     * @return array('error','full_path','base_name','file_size')
     */
    function moveUploadedFileToImagesDir($src_fname,$img_prefix="")
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

    function uploadServerFile($path)
    {
        global $application;

        $result = array(
            'error' => ''
           ,'full_path' => ''
           ,'base_name' => ''
           ,'http_link' => ''
        );
        $result['error'] = UPLOAD_ERR_OK;

        $_tmp = getimagesize($path);
        if($_tmp === false)
        {
            $result['error'] = UPLOAD_FILE_IS_NOT_IMAGE;
            return $result;
        }

        $new_image_name = $application->getUploadImageName($path);

        if(!copy($path,$new_image_name))
        {
            $result['error'] = UPLOAD_ERR_CANT_CP_FILE;
            return $result;
        };

        $result['full_path'] = $new_image_name;
        $result['base_name'] = basename($new_image_name);
        $result['http_link'] = $application->getAppIni('URL_IMAGES_DIR').$result['base_name'];

        return $result;
    }

    function process_images_upload_local_file($alt_text_input_name, $input_name, $image_id_input_name)
    {
    	global $application;
        $res = modApiFunc('Images', 'moveUploadedFileToImagesDir', $input_name, $image_id_input_name);
        $request = &$application->getInstance('Request');
        $alt_text = $request->getValueByKey($alt_text_input_name);
        $old_image_id = $request->getValueByKey($image_id_input_name);

        $image_obj = NULL;

        if($res['error'] != UPLOAD_ERR_OK)
        {
            $res['error_msg'] = modApiFunc('Shell', 'getMsgByErrorCode', $res['error']);
            $image_obj = new image_obj(NULL, $res['error'], $res['error_msg']);
        }
        else
        {
            $new_path = $res['full_path'];

            $_is = getimagesize($new_path);
            $data = array
            (
                'image_media' => IMAGE_MEDIA_SERVER_PATH
               ,'image_path' => $new_path
               ,'image_url' => ""
               ,'image_mime_type'  => $_is['mime']
               ,'image_width' => $_is[0]
               ,'image_height' => $_is[1]
               ,'image_filesize' => filesize($new_path)
               ,'image_alt_text' => $alt_text
            );
            $image_id = modApiFunc('Images', 'insertImage', $data);
            $thumb_image_id = modApiFunc("Images", "createImageThumbnail", $image_id, IMAGE_THUMB_SIZE);
            $thumb_image_data = modApiFunc("Images", "selectImageThumbnail", $image_id, IMAGE_THUMB_SIZE);
            $image_obj = new image_obj($image_id);

            //
            if(!empty($old_image_id) && is_numeric($old_image_id))
            {
                modApiFunc("Images", "deleteImages", array($old_image_id));
            }
        }
        return $image_obj;
    }

    function process_images_upload_server_file($alt_text_input_name, $input_name, $image_id_input_name)
    {
        global $application;
        $request = &$application->getInstance('Request');
        $server_file_path = $request->getValueByKey($input_name);

        $alt_text = $request->getValueByKey($alt_text_input_name);
        $res = modApiFunc('Images', 'uploadServerFile', $server_file_path);

        $alt_text = $request->getValueByKey($alt_text_input_name);
        $old_image_id = $request->getValueByKey($image_id_input_name);

        $image_obj = NULL;

        if($res['error'] != UPLOAD_ERR_OK)
        {
            $res['error_msg'] = modApiFunc('Shell', 'getMsgByErrorCode', $res['error']);
            $image_obj = new image_obj(NULL, $res['error'], $res['error_msg']);
        }
        else
        {
            $new_path = $res['full_path'];

            $_is = getimagesize($new_path);
            $data = array
            (
                'image_media' => IMAGE_MEDIA_SERVER_PATH
               ,'image_path' => $new_path
               ,'image_url' => ""
               ,'image_mime_type'  => $_is['mime']
               ,'image_width' => $_is[0]
               ,'image_height' => $_is[1]
               ,'image_filesize' => filesize($new_path)
               ,'image_alt_text' => $alt_text
            );
            $image_id = modApiFunc('Images', 'insertImage', $data);
            $thumb_image_id = modApiFunc("Images", "createImageThumbnail", $image_id, IMAGE_THUMB_SIZE);
            $thumb_image_data = modApiFunc("Images", "selectImageThumbnail", $image_id, IMAGE_THUMB_SIZE);
            $image_obj = new image_obj($image_id);

            //
            if(!empty($old_image_id) && is_numeric($old_image_id))
            {
                modApiFunc("Images", "deleteImages", array($old_image_id));
            }
        }
        return $image_obj;
    }

    function process_images_upload_url($alt_text_input_name, $input_name, $image_id_input_name)
    {
        global $application;
        $request = &$application->getInstance('Request');
        $url = $request->getValueByKey($input_name);
        $alt_text = $request->getValueByKey($alt_text_input_name);
        $old_image_id = $request->getValueByKey($image_id_input_name);

        loadCoreFile("URI.class.php");
        $uri = new URI($url);
        if($uri === false || $url == '' || empty($uri->full))
        {
        	$url = '';
        }
        $image_obj = NULL;
        if(empty($url))
        {
            $image_obj = new image_obj();
            $image_obj->set_error("ERR_INVALID_URL");
        }
        else
        {
            $data = array
            (
                'image_media' => IMAGE_MEDIA_URL
               ,'image_path' => ""
               ,'image_url' => $url
               ,'image_mime_type'  => ""
               ,'image_width' => ""
               ,'image_height' => ""
               ,'image_filesize' => ""
               ,'image_alt_text' => $alt_text
            );
            $image_id = modApiFunc('Images', 'insertImage', $data);
            $image_obj = new image_obj($image_id);

            //
            if(!empty($old_image_id) && is_numeric($old_image_id))
            {
                modApiFunc("Images", "deleteImages", array($old_image_id));
            }
        }

        return $image_obj;
    }

    function process_images_update_alt_text($alt_text_input_name, $image_id)
    {
        global $application;
        $request = &$application->getInstance('Request');
        $new_alt_text = $request->getValueByKey($alt_text_input_name);

        $image_obj = NULL;
        $data = modApiFunc('Images', 'selectImage', $image_id);
        if($data !== NULL)
        {
            $data['image_alt_text'] = $new_alt_text;
            modApiFunc('Images', 'updateImage', $data);
            $image_obj = new image_obj($image_id);
        }
        else
        {
            $image_obj = new image_obj();
            $image_obj->set_error("ERR_IMAGE_ID_NOT_FOUND_IN_DB", $image_id);
        }
        return $image_obj;
    }
};

?>