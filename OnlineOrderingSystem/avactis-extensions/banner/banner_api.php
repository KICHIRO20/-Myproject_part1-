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
 * Module "Banner"
 *
 * @package Banner
 * @author Ninad
 */
class Banner
{
	//------------------------------------------------
	//               PUBLIC DECLARATION
	//------------------------------------------------

	/**#@+
	 * @access public
	 */

	/**
	 * Banner constructor
	 */
	function Banner()
	{
	}
	function install()
	{
		global $application;

        loadCoreFile('csv_parser.php');
		$csv_parser = new CSV_Parser();

		$tables = Banner::getTables();
		$query = new DB_Table_Create($tables);

		$table_banners_settings = 'banners_settings';
		$columns = $tables[$table_banners_settings]['columns'];

		list($flt,$Default_Settings) = $csv_parser->parse_file(dirname(__FILE__)."/includes/default_settings.csv");
		{
			foreach($Default_Settings as $key => $setting)
			{
				$query = new DB_Insert($table_banners_settings);
				$query->addInsertValue($setting["key"], $columns['setting_key']);
				$query->addInsertValue($setting["value"], $columns['setting_value']);
				$application->db->getDB_Result($query);
			};
		};
	}

	function uninstall()
	{
        global $application;
	}

	function getTables()
	{
		static $tables;

		if (is_array($tables))
		{
			return $tables;
		}
		$tables = array ();

		$table_banners_images = 'banners_images' ;
		$tables[$table_banners_images] = array();
		$tables[$table_banners_images]['columns'] = array
		(
				'imageid'      => $table_banners_images.'.imageid'
				,'bannerid'    => $table_banners_images.'.bannerid'
				,'image_path'  => $table_banners_images.'.image_path'
				,'image_type'  => $table_banners_images.'.image_type'
				,'image_x'     => $table_banners_images.'.image_x'
				,'image_y'     => $table_banners_images.'.image_y'
				,'image_size'  => $table_banners_images.'.image_size'
				,'filename'    => $table_banners_images.'.filename'
				,'date'        => $table_banners_images.'.date'
				,'alt'         => $table_banners_images.'.alt'
				,'avail'       => $table_banners_images.'.avail'
				,'order_by'    => $table_banners_images.'.order_by'
				,'url'         => $table_banners_images.'.url'
				,'type'        => $table_banners_images.'.type'

		);
		$tables[$table_banners_images]['types'] = array
		(
				'imageid'      => DBQUERY_FIELD_TYPE_INT . " NOT NULL auto_increment"
				,'bannerid'    => DBQUERY_FIELD_TYPE_INT . " NOT NULL DEFAULT 0"
				,'image_path'  => DBQUERY_FIELD_TYPE_CHAR255 ." NOT NULL DEFAULT ''"
				,'image_type'  => DBQUERY_FIELD_TYPE_CHAR255 ." NOT NULL DEFAULT 'image/jpeg'"
				,'image_x'     => DBQUERY_FIELD_TYPE_INT . " NOT NULL DEFAULT 0"
				,'image_y'     => DBQUERY_FIELD_TYPE_INT . " NOT NULL DEFAULT 0"
				,'image_size'  => DBQUERY_FIELD_TYPE_INT . " NOT NULL DEFAULT 0"
				,'filename'    => DBQUERY_FIELD_TYPE_CHAR255 ." NOT NULL DEFAULT ''"
				,'date'        => DBQUERY_FIELD_TYPE_INT . " NOT NULL DEFAULT 0"
				,'alt'         => DBQUERY_FIELD_TYPE_CHAR255 ." NOT NULL DEFAULT ''"
				,'avail'       => DBQUERY_FIELD_TYPE_CHAR1 ." NOT NULL DEFAULT 'Y'"
				,'order_by'    => DBQUERY_FIELD_TYPE_INT . " NOT NULL DEFAULT 0"
				,'url'         => DBQUERY_FIELD_TYPE_CHAR255 . " NOT NULL DEFAULT ''"
				,'type'         => DBQUERY_FIELD_TYPE_CHAR1 . " NOT NULL DEFAULT ''"
		);
		$tables[$table_banners_images]['primary'] = array
		(
				'imageid'
		);
		$tables[$table_banners_images]['indexes'] = array(
				'IDX_bi' => 'bannerid'
		);

		$table_banners_settings = 'banners_settings';
		$tables[$table_banners_settings]=array();
		$tables[$table_banners_settings]['columns'] = array
		(
				'setting_id'    => $table_banners_settings.'.setting_id'
				,'setting_key'   => $table_banners_settings.'.setting_key'
				,'setting_value' => $table_banners_settings.'.setting_value'
		);
		$tables[$table_banners_settings]['types'] = array(
				'setting_id'    => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
				,'setting_key'   => DBQUERY_FIELD_TYPE_CHAR255.' NOT NULL DEFAULT \'\''
				,'setting_value' => DBQUERY_FIELD_TYPE_CHAR255.' NOT NULL DEFAULT \'\''

		);
		$tables[$table_banners_settings]['primary']=array(
				'setting_id'
		);

		$table_banners = 'banners';
		$tables[$table_banners] = array();
		$tables[$table_banners]['columns'] = array
		(
				'bannerid'               => $table_banners.'.bannerid'
				,'location'               => $table_banners.'.location'
				,'width'            => $table_banners.'.width'
				,'height'            => $table_banners.'.height'
				,'order_by'            => $table_banners.'.order_by'
				,'start_date'            => $table_banners.'.start_date'
				,'end_date'            => $table_banners.'.end_date'
				,'effect'            => $table_banners.'.effect'
				,'pages'            => $table_banners.'.pages'
				,'nav'            => $table_banners.'.nav'

		);
		$tables[$table_banners]['types'] = array
		(
				'bannerid'                => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
				,'location'     => DBQUERY_FIELD_TYPE_CHAR32 ." NOT NULL DEFAULT 'T'"
				,'width'       => DBQUERY_FIELD_TYPE_INT . " NOT NULL DEFAULT 0"
				,'height'       => DBQUERY_FIELD_TYPE_INT ." NOT NULL DEFAULT 0"
				,'order_by'       => DBQUERY_FIELD_TYPE_INT ." NOT NULL DEFAULT 0"
				,'start_date'       => DBQUERY_FIELD_TYPE_CHAR255 ." NOT NULL DEFAULT 0"
				,'end_date'       => DBQUERY_FIELD_TYPE_CHAR255 ." NOT NULL DEFAULT 0"
				,'effect'       => DBQUERY_FIELD_TYPE_CHAR255 ." NOT NULL DEFAULT ''"
				,'pages'       => DBQUERY_FIELD_TYPE_CHAR32 ." NOT NULL DEFAULT ''"
				,'nav'       => DBQUERY_FIELD_TYPE_CHAR32 ." NOT NULL DEFAULT 'N'"


		);
		$tables[$table_banners]['primary'] = array
		(
				'bannerid'
		);

		$table_banners_html = 'banners_html';
		$tables[$table_banners_html] = array();
		$tables[$table_banners_html]['columns'] = array
		(
				'id'               => $table_banners_html.'.id'
				,'bannerid'        => $table_banners_html.'.bannerid'
				,'code'           => $table_banners_html.'.code'
				,'avail'          => $table_banners_html.'.avail'
				,'order_by'       => $table_banners_html.'.order_by'
				,'type'           => $table_banners_html.'.type'

		);
		$tables[$table_banners_html]['types'] = array
		(
				'id'	       => DBQUERY_FIELD_TYPE_INT . ' NOT NULL auto_increment'
				,'bannerid'    => DBQUERY_FIELD_TYPE_INT . " NOT NULL DEFAULT 0"
				,'code'        => DBQUERY_FIELD_TYPE_LONGTEXT . " NOT NULL DEFAULT ''"
				,'avail'       => DBQUERY_FIELD_TYPE_CHAR1 ." NOT NULL DEFAULT 'Y'"
				,'order_by'    => DBQUERY_FIELD_TYPE_INT . " NOT NULL DEFAULT 0"
				,'type'         => DBQUERY_FIELD_TYPE_CHAR1 . " NOT NULL DEFAULT ''"
		);

		$tables[$table_banners_html]['primary'] = array
		(
				'id'
		);
		$tables[$table_banners_html]['indexes'] = array(
				'IDX_bh' => 'bannerid'
		);


		global $application;
		return $application->addTablePrefix($tables);
	}
	function getImageInfo($prefix, $tmp_file){
		global $application;
		static $img_types = array (
				'1' => 'image/gif',
				'2' => 'image/jpeg',
				'3' => 'image/png',
				'4' => 'application/x-shockwave-flash',
				'5' => 'image/psd',
				'6' => 'image/bmp',
				'13' => 'application/x-shockwave-flash',
		);
		$dir = $application->getAppIni('PATH_IMAGES_DIR');
		if(is_file($dir . $tmp_file))
		{
			$image_info = getimagesize($dir . $tmp_file);
			if (!empty($img_types[$image_info[2]])) {
				$type = $img_types[$image_info[2]];
			}
			return array(
					'name' => $tmp_file,
					'width' => $image_info[0],
					'height' => $image_info[1],
					'type'=>$type,
			);
		}
		else
		{
			return array(
					'name' => '',
					'width' => 0,
					'height' => 0,
					'type'=>''
			);
		};
	}
	function deleteBanners($banners_id_array){
		global $application;
		$tables = $this->getTables();
		$tr = $tables['banners']['columns'];
		$query = new DB_Delete('banners');
		$query->WhereField( $tr['bannerid'], DB_IN, "('".implode("', '", $banners_id_array)."') ");
		$application->db->getDB_Result($query);
	}
	function deleteBannersHtml($id_array){
		global $application;
		$tables = $this->getTables();
		$tr = $tables['banners_html']['columns'];
		$query = new DB_Delete('banners_html');
		$query->WhereField( $tr['bannerid'], DB_IN, "('".implode("', '", $id_array)."') ");
		$application->db->getDB_Result($query);
	}
	function deleteBannersImages($id_array){
		global $application;
		$tables = $this->getTables();
		$tr = $tables['banners_images']['columns'];
		$query = new DB_Delete('banners_images');
		$query->WhereField( $tr['bannerid'], DB_IN, "('".implode("', '", $id_array)."') ");
		$application->db->getDB_Result($query);
	}
	function deleteBannersHtmlContent($html_content_ids){
		global $application;
		$tables = $this->getTables();
		$tr = $tables['banners_html']['columns'];
		$query = new DB_Delete('banners_html');
		$query->WhereField( $tr['id'], DB_IN, "('".implode("', '", $html_content_ids)."') ");
		$application->db->getDB_Result($query);
	}
	function deleteBannersImageContent($image_ids){
		global $application;
		$tables = $this->getTables();
		$tr = $tables['banners_images']['columns'];
		$query = new DB_Delete('banners_images');
		$query->WhereField( $tr['imageid'], DB_IN, "('".implode("', '", $image_ids)."') ");
		$application->db->getDB_Result($query);
	}
	function updateBannerInfo($data_to_update){
		return execQuery('UPDATE_BANNER_INFO' ,array($data_to_update));
	}
	function updateImageInfo($Img_data){
		return execQuery('UPDATE_IMAGE_INFO' ,array($Img_data));
	}

	function updateHtmlInfo($Html_data){
		return execQuery('UPDATE_HTML_INFO' ,array($Html_data));
	}
	function insertBannerInfo($banner_data){
		global $application;
		$tables = $this->getTables();
		$tr = $tables['banners']['columns'];
		$query = new DB_Insert('banners');

		$query->addInsertValue($banner_data['location'], $tr['location']);
		$query->addInsertValue($banner_data['width'], $tr['width']);
		$query->addInsertValue($banner_data['height'], $tr['height']);
		$query->addInsertValue($banner_data['order_by'], $tr['order_by']);
		$query->addInsertValue($banner_data['start_date'], $tr['start_date']);
		$query->addInsertValue($banner_data['end_date'], $tr['end_date']);
		$query->addInsertValue($banner_data['effect'], $tr['effect']);
		$query->addInsertValue($banner_data['nav'], $tr['nav']);

		$result = $application->db->getDB_Result($query);
		return $application->db->DB_Insert_Id();

	}
	function insertBannerHtmlData($query_data){
		return execQuery('INSERT_BANNER_HTML_CONTENT', array($query_data));

	}
	function insertBannerImageData($image_data){
		return execQuery('INSERT_BANNER_IMAGE_CONTENT', array($image_data));
	}
	function getBanners($location){
		$result = execQuery('SELECT_BANNERS', array('banner_location'=>$location));
		$result_array = array();
		foreach($result as $row)
		{
			$result_array[] = $row;
		}
		return $result_array;
	}
	function getBannersOnTime($location,$time){
		$result = execQuery('SELECT_BANNERS_BASED_ON_TIME', array('banner_location'=>$location,'banner_period'=>$time));
		$result_array = array();
		foreach($result as $row)
		{
			$result_array[] = $row;
		}
		return $result_array;
	}
	function getBannerImageSettings(){
		$res=execQuery("SELECT_BANNER_IMAGES_SETTINGS",array());

		$settings=array();

		foreach($res as $k => $sval)
			$settings[$sval['setting_key']]=$sval['setting_value'];

		return $settings;
	}
	function func_banner_system_resize_banner_image($img_y, $img_x, $height = 0, $width = 0,$location) {


		$banner_image_settings = $this -> getBannerImageSettings();

		if ($height == 0 ) {
			$height = $banner_image_settings['TOP_BANNER_DEFAULT_HEIGHT'];
		}

		if ($width == 0 ) {
			$width = $banner_image_settings['TOP_BANNER_DEFAULT_WIDTH'];
		}

		if ($height == 0 || $width == 0) {
			return false;
		}

		$s = min($width / $img_x, $height / $img_y);
		$_img_x = round($s * $img_x);
		$_img_y = round($s * $img_y);

		return array('image_x' => $_img_x, 'image_y' => $_img_y);
	}
	function getBannersContentbasedOnTime($location,$time){
		$banners[$location]=$this->getBannersOnTime($location,$time);
		if (!empty($banners)){
			$banner_types = array(
					'T' => 'top',
					'B' => 'bottom',
					'L' => 'left',
					'R' => 'right'
			);
			foreach ($banner_types as $k => $v) {
				$banners_final[$v .'_banners'] = (!empty($banners[$k])) ? $this->collect_banner_content($banners[$k],$location) : '';
			}
		}
		return $banners_final;
	}
	function collect_banner_content($banners,$location){
		foreach($banners as $k => $v){
			$html_content = $this->collect_html_content($v['bannerid']);
			$image_content = $this->collect_image_content($v['bannerid']);
			$banner_content=array('html'=>$html_content,'image'=>$image_content);
			foreach($banner_content['image'] as $key => $image){
				$image_size = array();
				if (
						$image['image_y'] > $banners[$k]['height']
						|| (
								$image['image_x'] > $banners[$k]['width']
								&& $image['image_y'] != 0
								&& $image['image_x'] != 0
						)
				) {
                    	$image_size = $this->func_banner_system_resize_banner_image($image['image_y'], $image['image_x'], $banners[$k]['height'], $banners[$k]['width'],$location);
                  }
                  if (!empty($image_size)) {
                  	$banner_content['image'][$key]['image_x'] = $image_size['image_x'];
                  	$banner_content['image'][$key]['image_y'] = $image_size['image_y'];

                  }

			}
			$final_banner_content = array();
			 foreach($banner_content as $value){
                 foreach($value as $item){
                   $final_banner_content[]=$item;
               }

			}
			$banners[$k]['content']=$final_banner_content;
		}
		return $banners;

	}
	function collect_html_content($bid){
         $result = execQuery('SELECT_HTML_CONTENT', array('banner_id' => $bid));
         return $result ;
   }
   function collect_image_content($bid){
          $result = execQuery('SELECT_IMAGE_CONTENT', array('banner_id' => $bid));
          return $result ;
   }
   function getImageContentAll($bid){
         $result = execQuery('SELECT_IMAGES', array('banner_id' => $bid));

         return $result ;
   }
   function getHtmlContentAll($bid){
        $result = execQuery('SELECT_HTML_ALL', array('banner_id' => $bid));
       return $result ;
   }

}
?>