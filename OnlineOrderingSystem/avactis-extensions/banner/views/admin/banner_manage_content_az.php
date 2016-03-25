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


class BannerContentManagement
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
	function BannerContentManagement()
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
			//$this->initFormData();
		}
		$this->setBannerID($_GET['bannerid']);
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
	function setBannerID($id){
		$this->_bId=$id;
	}
	function getBannerID(){
		return $this->_bId;
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
				$this->bType = "B";
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
	function getFormAction(){
		$request = new Request();
		$request->setView('banner_manage_content_az');
		$request->setAction("add_banner_content_info");
		$request->setKey('type', $this->bType);
		$request->setKey('bannerid', $_GET['bannerid']);
		$form_action = $request->getURL();
		loadCoreFile('html_form.php');
		$HtmlForm1 = new HtmlForm();
		$action=$HtmlForm1->genForm($form_action, "POST", "BannerContentManagementForm");
		return $action;
	}
	function status(){
		$status = array(
				"Y"=>"Enabled",
				"N"=>"Disabled"
		);
		return $status;
	}
	function getAvilStatus($default = 'Y'){
		$value = '';
		if(!isset($this->_Status))
		{
			$this->_Status = $this->status();
		}
		foreach ($this->_Status as $key => $status)
		{
			$value .= '<option value="'. $key .'" '. ($key  == $default ? " selected" : "").'>'. $status. '</option>';
		}
		return $value;
	}
	function getBannerImages($res){
		global $application;
		$BannerN = 1;
		$retval = '';
		if(!empty($res)){
			foreach ($res as $images){
				$template_contents = array
				(
						"BannerN"  => $BannerN++,
						"ImageID"  => $images['imageid'],
						"ImagePath"=> $images['image_path'],
						"ImageUrl"=> $images['url'],
						"ImageAlt"=> $images['alt'],
						"ImageAvail"=>$this->getAvilStatus($images['avail']),
						"DelCheckboxCssClass" => ($images["imageid"] === NULL) ? "display_none" : ""

				);
				$this->_Template_Contents = $template_contents;
				$application->registerAttributes($this->_Template_Contents);
				$retval .= $this->mTmplFiller->fill("banner/","banner_content_management/banner_image_list_item.tpl.html", array());

			}
		}

		return $retval;
	}
	function getBannerHtml($resHtml){
		global $application;
		$HtmlN = 1;
		$retval = '';
		if(!empty($resHtml)){
			foreach ($resHtml as $htmlContent){
				$template_contents = array
				(
						"HtmlN"  => $HtmlN++,
						"HtmlID"  => $htmlContent['id'],
						"Code"=> $htmlContent['code'],
						"HtmlAvail"=>$this->getAvilStatus($htmlContent['avail']),
						"DelCheckboxCssClass" => ($htmlContent["id"] === NULL) ? "display_none" : ""

				);
				$this->_Template_Contents = $template_contents;
				$application->registerAttributes($this->_Template_Contents);
				$retval .= $this->mTmplFiller->fill("banner/","banner_content_management/banner_html_content_list_item.tpl.html", array());

			}
		}

		return $retval;
	}
	function getBannerImagesFooter(){
		global $application;
		$template_contents = array();
		$this->_Template_Contents = $template_contents;
		$application->registerAttributes($this->_Template_Contents);
		return $this->mTmplFiller->fill("banner/", "banner_content_management/banner_image_footer.tpl.html", array());

	}
	function getBannerImagesContainer(){
		global $application;
		$retval = "";
		$res = modApiFunc('Banner', 'getImageContentAll' , $this->_bId);
		if(empty($res)){
			return $retval;
		}else{
			$template_contents = array(
					"Imageitems"=>$this->getBannerImages($res),
					"BannerImagesFooter"=>$this->getBannerImagesFooter(),
					"Action"=>$this->getFormAction(),
					"BannerID"=>$this->_bId,
					"Type"=>$this->bType
			);
			$this->_Template_Contents = $template_contents;
			$application->registerAttributes($this->_Template_Contents);
			$retval .= $this->mTmplFiller->fill("banner/", "banner_content_management/banner_images_container.tpl.html", array());
		}
		return $retval;
	}
	function getBannerHtmlContainer(){
		global $application;
		$retval = "";
		$resHtml = modApiFunc('Banner', 'getHtmlContentAll' , $this->_bId);
		if(empty($resHtml)){
			return $retval;
		}else{
			$template_contents = array(
					"HtmlItems"=>$this->getBannerHtml($resHtml),
					"ActionHtml"=>$this->getFormAction(),
					"BannerID"=>$this->_bId,
					"Type"=>$this->bType
					//"DeleteFormAction"=>$this->getFormAction(),
			);
			$this->_Template_Contents = $template_contents;
			$application->registerAttributes($this->_Template_Contents);
			$retval .= $this->mTmplFiller->fill("banner/", "banner_content_management/banner_html_container.tpl.html", array());
		}
		return $retval;
	}
	function output()
	{

		global $application;
		$template_contents = array();
		$template_contents= array(
				"Type"=>$this->getBannerType(),
				"Location"=>$this->getLocation(),
				"FormAction"=>$this->getFormAction(),
				"BannerID"=>$this->getBannerID(),
				"BannerImages"=>$this->getBannerImagesContainer(),
				"BannerHtml"=>$this->getBannerHtmlContainer()

		);
		$this->_Template_Contents = $template_contents;
		$application->registerAttributes($this->_Template_Contents);
		return $this->mTmplFiller->fill("banner/", "banner_content_management/banner_content.tpl.html", array());
	}

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
	var $_bId;
	var $_Banner;
	var $_bEffects;
	var $_Status;
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