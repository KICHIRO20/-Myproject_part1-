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
/***********************************************************************
 | Avactis (TM) Shopping Cart software developed by HBWSL.
| http://www.avactis.com
| -----------------------------------------------------------------------
| All source codes & content (c) Copyright 2004-2013, HBWSL.
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
 * "Banner -> Banner Management" View.
 *
 * @package Banner
 * @access  public
 * @author  Ninad
 *
 */


class BannerManagement
{
	//------------------------------------------------
	//               PUBLIC DECLARATION
	//------------------------------------------------

	/**#@+
	 * @access public
	 */

	/**
	 * The view constructor.
	 * About data flow. All data is transferred.
	 * <p> Action -> View :
	 * <p> Through session variable @var $SessionPost (created from POST data),
	 * especially it's $SessionPost["ViewState"] array, containing current View
	 * state information. The state does not include such information like already
	 * inputted name, description values. It includes variables, determining the
	 * view structure: table or list, image or input field etc. @see @var SessionPost.
	 * <p> View -> Action :
	 * <p> Through POST data. All form'related session data is removed while
	 * processing view output.
	 */
	function BannerManagement()
	{
		global $application;
		$this->mTmplFiller = &$application->getInstance('TmplFiller');
		$this->mTmplFiller->setTemplatePath("avactis-extensions/");
		if(modApiFunc("Session", "is_Set", "SessionPost"))
		{
			//$this->copyFormData();
			modApiFunc('Session', 'un_Set', 'SessionPost');
		}
		else
		{
			$this->initFormData();
		}

		$this->setLocation($_GET['type']);
	}

	function initFormData()
	{
		$this->_Banner = array();
		$this->ViewState =
		array(
				"hasCloseScript" => "false",
		);
		$this->POST  =
		array(
				/* "BannerStartDateFYearValue" => date("Y"), //current date
				 "BannereStartDateMonthValue" => date("m"),
		"BannerStartDateDayValue" => date("d"),

		"BannerEndDateFYearValue" => date("Y"),
		"BannerEndDateMonthValue" => date("m"),
		"BannerEndDateDayValue" => date("d"),
		*/

		);
	}

	function setLocation($location){
		switch($location){
			case 'L':
				$this->bLocation = "<span>Left banner</span>";
				$this->bType = "L";
				break;
			case 'T':
				$this->bLocation = "<span>Top banner</span>";
				$this->bType = "T";
				break;
			case 'B':
				$this->bLocation = "<span>Bottom banner</span>";
				$this->bType = B;
				break;
			case 'R':
				$this->bLocation = "<span>Right banner</span>";
				$this->bType = "R";
				break;
		}

	}
	function getLocation(){
		return $this->bLocation;
	}
	function getBannerType(){
		return $this->bType;
	}
	function getCurrentBanners($res){
		global $application;
		$pagesList = modApiFunc('Layout_CMS', 'getPagesList');

		$BannerN = 1;
		if(!empty($res)){
			foreach($res as $banners){
				$start_date=date('m-d-Y',$banners["start_date"]);
				$end_date=date('m-d-Y',$banners["end_date"]);
				$template_contents = array
				(
						"BannerID" => ($banners["bannerid"] === NULL ? "" : $banners["bannerid"]),
						"BannerN"  => $BannerN++,
						"Location" => $banners["location"],
						"BannerWidth" => $banners["width"],
						"BannerHeight"=>$banners["height"],
						"BannerOrder"=>$banners["order_by"],
						"BannerStartDate"=>$start_date,
						"BannerEndDate"=>$end_date,
						"BannerStatus"=> isset($banners['status']) ? $banners['status'] : "",
						"BannerNavigation"=>($banners["nav"]=='Y' ? "checked":''),
						"BannerEffect"=>$this->getBannerEffect($banners["effect"],$banners["bannerid"]),
						"PagesList"=>$this->outPagesList($pagesList),
						"DelCheckboxCssClass" => ($banners["bannerid"] === NULL) ? "display_none" : ""

				);
				$this->_Template_Contents = $template_contents;
				$application->registerAttributes($this->_Template_Contents);
				$retval .= $this->mTmplFiller->fill("banner/","banner_management/banner_list_item.tpl.html", array());
			}
		}
		return $retval;

	}

	function outPagesList($pagesList)
	{
		global $application;
		$html = '';
		foreach($pagesList as $page)
		{
			$template_contents = array(
					'PageName' => $page
			);
			$this->_Template_Contents = $template_contents;
			$application->registerAttributes($this->_Template_Contents);
			$html.= $this->mTmplFiller->fill("banner/", "banner_management/page-item.tpl.html", array());
		}
		return $html;
	}
	function Effects(){
		$effect=array("fade","wipe","scrollDown","scrollUp","fadeZoom",
				"cover","blindX","blindY","curtainX","curtainY","growX"
				,"growY","none","scrollLeft","scrollRight","scrollHorz"
				,"scrollVert","shuffle","slideX","slideY","toss","turnUp"
				,"turnDown","turnRight","uncover","zoom","all"
		);

		return $effect ;
	}
	function getBannerEffect($slEffect="fade",$bid){
		$value = '';
		if(!isset($this->_bEffects))
		{
			$this->_bEffects = $this->Effects();
		}
		foreach ($this->_bEffects as $effect)
		{
			$value .= '<option value="'. $effect .'" '. ($effect == $slEffect ? " selected" : "").'>'. ucfirst($effect). '</option>';
		}
		return $value;
	}
	function getFormAction()
	{
		$request = new Request();
		$request->setView('banner_manage_az');
		$request->setAction("add_banner_info");
		$request->setKey('type', $this->bType);
		$form_action = $request->getURL();
		loadCoreFile('html_form.php');
		$HtmlForm1 = new HtmlForm();
		$action=$HtmlForm1->genForm($form_action, "POST", "BannerManagementForm");
		return $action;
	}

	function getBannerContainer(){
		global $application;
		$current_time = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
		$banners = modApiFunc('Banner', 'getBanners', $this->getBannerType());
		foreach ($banners as $key => $item){
			if ($banners[$key]['start_date'] > $current_time) {
				$banners[$key]['status'] = 'future';
			}
			if ($banners[$key]['end_date'] < $current_time) {
				$banners[$key]['status'] = 'expired';
			}

		}
		$retval = "";
		if(sizeof($banners) == 0){
			$retval .= $this->mTmplFiller->fill("banner/", "banner_management/banner_list_item_no_items.tpl.html", array());
		}else{
			$template_contents = array(
					"Items"=>$this->getCurrentBanners($banners),
					"BannerFooter"=>$this->getBannerFooter(),
					"DeleteFormAction"=>$this->getFormAction(),
			);
			$this->_Template_Contents = $template_contents;
			$application->registerAttributes($this->_Template_Contents);
			$retval .= $this->mTmplFiller->fill("banner/", "banner_management/banner_container_list.tpl.html", array());
		}
		return $retval;
	}
	function getBannerFooter(){
		global $application;
		$template_contents = array();
		$this->_Template_Contents = $template_contents;
		$application->registerAttributes($this->_Template_Contents);
		return $this->mTmplFiller->fill("banner/", "banner_management/banner_footer.tpl.html", array());

	}
	function output()
	{
		global $application;
		$pagesList = modApiFunc('Layout_CMS', 'getPagesList');
		$template_contents = array();
		$template_contents= array(
				"Type"=>$this->getBannerType(),
				"Location"=>$this->getLocation(),
				"PagesList"=>$this->outPagesList($pagesList),
				"FromAction"=>$this->getFormAction(),
				"Current_Banners"=>$this->getBannerContainer(),
				"BannerEffect"=>$this->getBannerEffect(),
		);
		$this->_Template_Contents = $template_contents;
		$application->registerAttributes($this->_Template_Contents);
		return $this->mTmplFiller->fill("banner/", "banner_management/banner_management.tpl.html", array());
	}


	/**
	 * @ describe the function AddCategory->getTag.
	 */
	function getTag($tag)
	{
		global $application;
		$value = getKeyIgnoreCase($tag, $this->_Template_Contents);
		if ($value == null)
		{
			switch ($tag)
			{
				case 'Breadcrumb':
					$obj = &$application->getInstance('Breadcrumb');
					$value = $obj->output(false);
					break;

				case 'ErrorIndex':
					$value = $this->_error_index;
					break;

				case 'Error':
					$value = $this->_error;
					break;
			};
		}

		return $value;
	}


	/**#@-*/

	//------------------------------------------------
	//              PRIVATE DECLARATION
	//------------------------------------------------

	/**#@+
	 * @access private
	 */
	var $bLocation;
	var $bType;
	var $_Banner;
	var $_bEffects;
	/**
	 * Pointer to the module object.
	 */

	/**
	 * Pointer to the template filler object.
	 * It needs to track sequences of identical templates, like lists.
	 */
	var $mTmplFiller;
	/**#@-*/

	/**
	 * Pointer to the received from action or prepared FORM data.
	 */
	var $POST;

	/**
	 * View state structure. It comes from action.
	 * $SessionPost["ViewState"] structure example:
	 * <br>array
	 * <br>(
	 * <br>    "hasCloseScript"  = "false"           //true/false
	 * <br>    "ErrorsArray"     =  array()          //true/false
	 * <br>    "LargeImage"      = "image.jpg"       //
	 * <br>    "SmallImage"      = "image_small.jpg" //
	 * <br>)
	 */
	var $ViewState;

	/**
	 * List of error ids. It comes from action.
	 */
	var $ErrorsArray;
	var $ErrorMessages;

	var $_Template_Contents;

	var $MessageResources;
	var $_error_index;
	var $_error;
}
?>