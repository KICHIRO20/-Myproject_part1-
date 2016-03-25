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
 * Banner module.
 * This action is responsible for Adding Banners.
 *
 * @package Banners
 * @access  public
 * @author Ninad
 */
class add_banner_content_info extends AjaxAction
{
	//------------------------------------------------
	//               PUBLIC DECLARATION
	//------------------------------------------------

	/**#@+
	 * @access public
	 */

	/**
	 * Action constructor
	 */
	function add_banner_content_info()
	{
	}
	function is_url($url)
	{
		if (empty($url) || !is_string($url))
			return false;

		return preg_match('/^(http|https|ftp):\/\//isS', $url);
	}
	function func_get_image_size($filename, $is_image = false)
	{
		static $img_types = array (
				'1' => 'image/gif',
				'2' => 'image/jpeg',
				'3' => 'image/png',
				'4' => 'application/x-shockwave-flash',
				'5' => 'image/psd',
				'6' => 'image/bmp',
				'13' => 'application/x-shockwave-flash',
		);

		if (empty($type))
			list($width, $height, $type) = @getimagesize($filename);
		return array(
				$width,
				$height,
				$type,
		);
	}
	function onAction()
	{
		global $application;
		$request = $application->getInstance('Request');
		if(modApiFunc('Session', 'is_Set', 'SessionPost'))
		{
			_fatal(array( "CODE" => "CORE_050"), __CLASS__, __FUNCTION__);
		}
		$SessionPost = $_POST;
		unset($_POST);

		switch($SessionPost['mode']){
			case "upload_image" :
				{
					$nErrors = 0;
					loadCoreFile('html_form.php');
					$HtmlForm1 = new HtmlForm();
					$error_message_text = "";
					if ($nErrors == 0 && ($SessionPost['banner_type'] == 'image'))
					{
						$uploadfile = $application->getUploadImageName($_FILES['filePhoto']['name']);

						if (move_uploaded_file($_FILES['filePhoto']['tmp_name'], $uploadfile)){
							if ($application->getCurrentProtocol() == "https" && $application->getAppIni('HTTPS_URL_IMAGES_DIR'))
							{
								$imagesUrl = $application->getAppIni('HTTPS_URL_IMAGES_DIR');
							}else{
								$imagesUrl = $application->getAppIni('HTTP_URL_IMAGES_DIR');
							}

							$file_name = basename($uploadfile);
							$image_info = modApiFunc('Banner', 'getImageInfo' , "" , $file_name);
							$imagesUrl .= $file_name;
							$query_banner_image=array(
									'bannerid'=>$SessionPost['bannerid'],
									'image_path'=>$imagesUrl,
									'image_type'=>$image_info['type'],
									'image_x'=>$image_info['width'],
									'image_y'=>$image_info['height'],
									'image_size'=>$_FILES['filePhoto']['size'],
									'filename'=>$file_name,
									'date'=>date("Y/m/d"),
									'alt'=>$SessionPost['alt'],
									'url'=>$SessionPost['url'],
									'type'=>'I'

							);
							$bimageid = $this->saveBannerImageInfoToDB($query_banner_image);
						}
						header('Location: admin.php?page_view=BannerContentManagement&bannerid=' . $SessionPost["bannerid"] .  '&type=' . $SessionPost["type"]);

					}
					else if($nErrors == 0 && ($SessionPost['banner_type'] == 'html')){
						$imagesUrl = $application->getAppIni('URL_IMAGES_DIR');

						$query_banner_html=array(
								'bannerid'=>$SessionPost['bannerid'],
								'code'=>$SessionPost['html_banner'],
								'type'=>'H'
						);
						$bhid = $this->saveHtmlDataToDB($query_banner_html);
						header('Location: admin.php?page_view=BannerContentManagement&bannerid=' . $SessionPost["bannerid"] .  '&type=' . $SessionPost["type"]);
					}
					break;

				}
			case "update_image_content" :
				{
					if ($SessionPost['FormSubmitValue'] == "UpdateRows"){

						if(isset($SessionPost['image'])){
							foreach($SessionPost['image'] as $k => $v){
								$image_data_to_update =array(
										   'imageid' => $k,
										   'url' => $v['url'],
										   'alt'=> $v['alt'],
										   'avail'=>$v['avail']
										);
								$nErrors = sizeof($SessionPost["ViewState"]["ErrorsArray"]);
								if($nErrors == 0)
								{
									unset($SessionPost["ViewState"]["ErrorsArray"]);
									modApiFunc("Banner", "updateImageInfo", $image_data_to_update);
									header('Location: admin.php?page_view=BannerContentManagement&bannerid=' . $SessionPost["bannerid"] .  '&type=' . $SessionPost["type"]);
								}
							}

						}
					}
					if($SessionPost['FormSubmitValue'] == "DelRows"){

					 if(isset($SessionPost['selected_images'])){
							$images_to_delete = $SessionPost['selected_images'];
							$nErrors = sizeof($SessionPost["ViewState"]["ErrorsArray"]);
							if($nErrors == 0)
							{
								unset($SessionPost["ViewState"]["ErrorsArray"]);
								modApiFunc("Banner", "deleteBannersImageContent", $images_to_delete);
								header('Location: admin.php?page_view=BannerContentManagement&bannerid=' . $SessionPost["bannerid"] .  '&type=' . $SessionPost["type"]);
							}
						}
					}

					break;
				}
			case "update_html_content":
				{
					if ($SessionPost['FormSubmitValue'] == "UpdateHtmlContent"){
						if(isset($SessionPost['code_data'])){
							foreach($SessionPost['code_data'] as $k => $v){
								$html_data_to_update =array(
										'contentid' => $k,
										'code' => $v['html_code'],
										'avail'=>$v['avail']
								);
								$nErrors = sizeof($SessionPost["ViewState"]["ErrorsArray"]);
								if($nErrors == 0)
								{
									unset($SessionPost["ViewState"]["ErrorsArray"]);
									modApiFunc("Banner", "updateHtmlInfo", $html_data_to_update);
									header('Location: admin.php?page_view=BannerContentManagement&bannerid=' . $SessionPost["bannerid"] .  '&type=' . $SessionPost["type"]);
								}
							}

						}
					}
					if($SessionPost['FormSubmitValue'] == "DelHtmlContent"){
						if(isset($SessionPost['selected_html_content'])){
							$html_content_to_delete = $SessionPost['selected_html_content'];
							$nErrors = sizeof($SessionPost["ViewState"]["ErrorsArray"]);
							if($nErrors == 0)
							{
								unset($SessionPost["ViewState"]["ErrorsArray"]);
								modApiFunc("Banner", "deleteBannersHtmlContent", $html_content_to_delete);
								header('Location: admin.php?page_view=BannerContentManagement&bannerid=' . $SessionPost["bannerid"] .  '&type=' . $SessionPost["type"]);
							}
						}
					}
				}
				//	default : _fatal(array( "CODE" => "CORE_051"), __CLASS__, __FUNCTION__, $request->getValueByKey('FormSubmitValue'));
		}

	}
	function saveHtmlDataToDB($query_data){
         return modApiFunc('Banner','insertBannerHtmlData',$query_data );
     }
     function saveBannerImageInfoToDB($image_data){
         return modApiFunc('Banner','insertBannerImageData',$image_data );
    }

}
?>