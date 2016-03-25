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

class add_banner_info extends AjaxAction
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
	function add_banner_info()
	{
	}

	function onAction()
	{
		global $application;
		$admin_path = $application->getAppIni('PATH_ADMIN_DIR');
		$request = $application->getInstance('Request');
		if(modApiFunc('Session', 'is_Set', 'SessionPost'))
		{
			_fatal(array( "CODE" => "CORE_050"), __CLASS__, __FUNCTION__);
		}
		$SessionPost = $_POST;
		$SessionPost["ViewState"]["ErrorsArray"] = array();
		$url = $admin_path.'banner_content.php?type=' . $SessionPost["banner_location"];
		switch($request->getValueByKey('FormSubmitValue')){
			case "AddRow" :
				{
					$nErrors = 0;
					loadCoreFile('html_form.php');
					$HtmlForm1 = new HtmlForm();

					$error_message_text = "";
					if ($nErrors == 0)
					{
						unset($SessionPost["ViewState"]["ErrorsArray"]);
						$bid = $this->saveDataToDB($SessionPost);
						$SessionPost["ViewState"]["hasCloseScript"] = "true";
						header('Location: admin.php?page_view=BannerManagement&type=' . $SessionPost["banner_location"]);
					}
					break;

				}
			case "DelRows" :
				{
					if(isset($SessionPost["selected_banners"]))
					{
						$selected_banners_array = $SessionPost["selected_banners"];

						$nErrors = sizeof($SessionPost["ViewState"]["ErrorsArray"]);
						if($nErrors == 0)
						{
							unset($SessionPost["ViewState"]["ErrorsArray"]);
							modApiFunc("Banner", "deleteBanners", $selected_banners_array);
							modApiFunc('Banner', 'deleteBannersHtml' , $selected_banners_array);
							modApiFunc('Banner', 'deleteBannersImages' , $selected_banners_array);
							header('Location: admin.php?page_view=BannerManagement&type=' . $SessionPost["banner_location"]);
						}
					}
					break;

				}
			case "UpdateRows" :
				{
					if(isset($SessionPost['banner_data'])){
						foreach($SessionPost['banner_data'] as $key => $value){
							list($month, $day, $year) = explode('-', $value['start_date']);
							$start_date= mktime(0, 0, 0, $month, $day, $year);
							list($month, $day, $year) = explode('-', $value['end_date']);
							$end_date= mktime(0, 0, 0, $month, $day, $year);
							$end_date += 86399;
							if(isset($value['nav'])){
								$nav = $value['nav'];
							}else{
								$nav = "N";
							}
							$data_to_update = array (
									'banner_id'=>$key,
									'banner_width'=>$value['banner_width'],
									'banner_height'=>$value['banner_height'],
									'start_date'=>$start_date,
									'end_date'=>$end_date,
									'banner_effect'=>$value['banner_effect'],
									'nav'=>$nav

							);
							 $nErrors = sizeof($SessionPost["ViewState"]["ErrorsArray"]);
							if($nErrors == 0)
							{
								unset($SessionPost["ViewState"]["ErrorsArray"]);
								modApiFunc("Banner", "updateBannerInfo", $data_to_update);
								header('Location: admin.php?page_view=BannerManagement&type=' . $SessionPost["banner_location"]);
							}
						}

					}

					break;
				}
			default : _fatal(array( "CODE" => "CORE_051"), __CLASS__, __FUNCTION__, $request->getValueByKey('FormSubmitValue'));
		}

	}

	function saveDataToDB($data)
	{
		#
		# Insert new banner into the database and get its bannerid
		#
		list($month, $day, $year) = explode('-', $data['start_date']);
		$start_date= mktime(0, 0, 0, $month, $day, $year);
		list($month, $day, $year) = explode('-', $data['end_date']);
		$end_date= mktime(0, 0, 0, $month, $day, $year);
		$end_date += 86399;
		$query_banner_data = array(
		'location'   => $data['banner_location'],
		'width'      => intval($data['banner_width']),
		'height'     => intval($data['banner_height']),
		'order_by'   => intval($data['banner_orderby']),
		'start_date' => $start_date,
		'end_date'   => $end_date,
		'effect'     => $data['banner_effect'],
		'nav'        => (!empty($data['nav'])) ? $data['nav'] : '',
		);
		return modApiFunc('Banner','insertBannerInfo',$query_banner_data );
	}


}
?>