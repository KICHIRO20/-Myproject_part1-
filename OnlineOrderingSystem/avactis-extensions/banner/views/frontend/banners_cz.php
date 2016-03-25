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
 *
 * @package Banner
 * @access  public
 * @author  Ninad
 */
class Banners
{
	//------------------------------------------------
	//               PUBLIC DECLARATION
	//------------------------------------------------

	/**#@+
	 * @access public
	 */

	/**
	 * The view constructor.
	 */
	function Banners()
	{
		global $application;

		$this->templateFiller = new TemplateFiller();
		$this->template = $application->getBlockTemplate('Banners');
		$this->templateFiller->setTemplate($this->template);
	}

	function getTemplateFormat()
	{
		$format = array(
				'layout-file'        => 'avactis-extensions/banner/views/frontend/blocks_ini/banners-config.ini'
				,'files' => array(
						'Container' => TEMPLATE_FILE_SIMPLE
						,'BannerContentContainer' => TEMPLATE_FILE_SIMPLE
						,'BannerSliderCode' => TEMPLATE_FILE_SIMPLE
						,'BannerHtmlContainer' => TEMPLATE_FILE_SIMPLE
						,'BannerImageContainer' => TEMPLATE_FILE_SIMPLE
				)
				,'options' => array(
				)
		);
		return $format;
	}

	function bannerContent($banner){
		$defaultSettings = modApiFunc('Banner', 'getBannerImageSettings');
		$html =  '' ;
		global $application ;
		foreach ($banner['content'] as $content){
			if($content['type'] == 'H'){
				$template_contents =array (
						"Type"=>$content['type'],
						"ContentID"=>$content['id'],
						"HtmlContent"=>$content['code'],
						"Height"=>$banner['height'],
						"Navigation"=>$banner['nav'],
						"Width"=>$banner['width'],
						"DefaultHeight"=>$defaultSettings['TOP_BANNER_DEFAULT_HEIGHT'],
						"DefaultWidth"=>$defaultSettings['TOP_BANNER_DEFAULT_WIDTH'],

				);
				$this->_Template_Contents=$template_contents;
				$application->registerAttributes($this->_Template_Contents);
				$html .= $this->templateFiller->fill('BannerHtmlContainer',null,dirname(__FILE__));
			}
			if($content['type'] == 'I'){
				$template_contents =array (
						"Type"=>$content['type'],
						"ImageID"=>$content['imageid'],
						"Imagepath"=>$content['image_path'],
						"Image_x"=>$content['image_x'],
						"Image_y"=>$content['image_y'],
						"Url"=>$content['url'],
						"Alt"=>$content['alt'],
						"Height"=>$height,
						"Width"=>$width,
						"DefaultHeight"=>$defaultSettings['TOP_BANNER_DEFAULT_HEIGHT'],
						"DefaultWidth"=>$defaultSettings['TOP_BANNER_DEFAULT_WIDTH'],


				);
				$this->_Template_Contents=$template_contents;
				$application->registerAttributes($this->_Template_Contents);
				$html .= $this->templateFiller->fill('BannerImageContainer',null,dirname(__FILE__));

			}

		}

		return $html ;
	}
	/* function getSliderCode($banner){
		$html = '';
		global $application;
		$template_contents =array (
				"ID"=>$banner['bannerid'],
				"Navigation"=>$banner['nav'],
				"Effect"=>$banner['effect']

		);
		$this->_Template_Contents=$template_contents;
		$application->registerAttributes($this->_Template_Contents);
		$html .= $this->templateFiller->fill('BannerSliderCode');
		return $html;
	} */
	function getBannerContents(){
		$html_code = '';
		global $application;
		$defaultSettings = modApiFunc('Banner', 'getBannerImageSettings');
		$current_time = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
		$banners = modApiFunc('Banner', 'getBannersContentbasedOnTime' , $this->bLocation, $current_time );
		$banners_loc =array(
				'T'=>'top_banners',
				'L'=>'left_banners',
				'R'=>'right_banners',
				'B'=>'bottom_banners'
				);
		foreach($banners[$banners_loc[$this->bLocation]] as $banner)
		{
			$template_contents = array(
					"BannerID"=>$banner['bannerid'],
					"BannerWidth"=>$banner['width'],
					"BannerHeight"=>$banner['height'],
					"Content"=>$this->bannerContent($banner),
					"DefaultWidth"=>$defaultSettings['TOP_BANNER_DEFAULT_WIDTH'],
					"DefaultHeight"=>$defaultSettings['TOP_BANNER_DEFAULT_HEIGHT'],
					//"SliderCode"=>$this->getSliderCode($banner),
					"Navigation"=>$banner['nav'],
					"Effect"=>$banner['effect'],
					"Loc"=>$this->bLocation
			);
			$this->_Template_Contents=$template_contents;
			$application->registerAttributes($this->_Template_Contents);
			$html_code .= $this->templateFiller->fill('BannerContentContainer',null,dirname(__FILE__));
		};
	 return $html_code;
	}
	function output($params=array())
	{

		global $application;
		if (func_num_args() > 0){
			$this->bLocation = func_get_arg(0);
		}else{
			$this->bLocation = 'T';
		}
		$this->templateFiller = new TemplateFiller();
		$this->template = $application->getBlockTemplate('Banners');
		$this->templateFiller->setTemplate($this->template);
		$template_contents = array(
        	"BannerContent"=> $this->getBannerContents(),
			"Location"=>$this->bLocation
        );
        $this->_Template_Contents=$template_contents;
        $application->registerAttributes($this->_Template_Contents);

        return $this->templateFiller->fill('Container',null,dirname(__FILE__));

	}
	function getTag($tag)
	{
		if (getKeyIgnoreCase($tag, $this->_Template_Contents) == null)
		{
			if(preg_match("/Product(.+)/",$tag,$matches))
			{
				//return $this->currentSL_obj->getProductTagValue($matches[1]);
			};
		}
		else return getKeyIgnoreCase($tag, $this->_Template_Contents);
	}
	var $templateFiller;
	var $bLocation;
	var $params;

}

?>