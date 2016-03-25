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
 * Menu Manager module.
 * @author Prasada
 * @package Menu Manager
 * @access  public
 */
class AdminMenuManager
{
	//------------------------------------------------
	//               PUBLIC DECLARATION
	//------------------------------------------------

	/**#@+
	 * @access public
	 */

	/**
	 * The view constructor.
	 *
	 * @ finish the functions on this page
	 */


	function AdminMenuManager()
	{
		$this->mTmplFiller = new TmplFiller();

	}

	function generateMenus($groupname,$parent)
	{
		global $application;
		global $parentGBL;
		$obj = &$application->getInstance('MessageResources');

		$retval = "";
		$menuitems = "";
		$href = "javascript:void(0);";
		$menulist = modApiFunc('MenuManager','getMenus',$groupname,$parent);
		$count = count($menulist);
		foreach($menulist as $item)
		{
			$name = $item['menu_name'];
			$arr = explode(",",$name);
			$menuitems = getValxMsg($arr[0],$arr[1]);
			$menulink  = $item['menu_url'];
			$menudesc = $item['menu_desc'];
			$new_window = $item['new_window'];
			$class = (strtolower($parentGBL)==strtolower($menuitems)) ? "active" : "";

			if($item['icon_image'] != "")
			{
				$icon = $item['icon_image'];
				$iconimg = "<i class=$icon></i>";
			}
			else
			{
				$iconimg = "";
			}

			$parent = $item['parent'];
			$has_subcategory = $item['has_subcategory'];
			$menuclass = explode(".",$menulink);

			if(strpos($menuclass[0],'catalog') !== FALSE)
			{
				$menuli = "";
			}else{
				$menuli = $menuclass[0];
			}

			if($new_window == 1)
			{
				$onclick = "javascript:openURLinNewWindow('".$menulink."','".$menuitems."')";
				$retval  .= "<li><a href=\"".$href."\" onclick=\"".$onclick."\">$iconimg<span>$menuitems</span></a>";
			}
			else
			{
				$retval .= "<li class='$class'><a href=$menulink>$iconimg<span class='title'>$menuitems</span>";
				$class1 = "active";
				if (strcmp($class, $class1) == 0) {
	    				$retval .= '<span class="selected"></span></a>';
				}
				else {
					$retval .= '<span class="arrow "></span></a>';
				}
			}
			if($has_subcategory){
				$retval .= "<ul class='sub-menu'>";
				$retval .= $this->generateMenus($groupname,$name);
				$retval .= "</ul>";
			}
			$retval .= "</li>";

		}

		return $retval;
	}


	function outputStoreSettingsMenu($groupname,$parent)
	{
		global $application;
		$result= "";
		$menulist = modApiFunc('MenuManager','getMenus',$groupname,$parent);
		foreach($menulist as $item)
		{
			$menuname = $item['menu_name'];
			$arr = explode(",",$menuname);
			$menuname = getValxMsg($arr[0],$arr[1]);
			$winname = escapeJSScript($menuname);

			$menudescription = $item['menu_desc'];
			$arr = explode(",",$menudescription);
			$menudesc = getValxMsg($arr[0],$arr[1]);
			$menuurl = $item['menu_url'];
			$new_window = $item['new_window'];
			$template_contents = array(
					'MenuName' 			=> $menuname,
					'WindowName'		=> $winname,
					'MenuURL'			=> $menuurl,
					'MenuDescription' 	=> $menudesc,
					'OpenInNewWindow'   => $new_window
					);

			$this->_Template_Contents = $template_contents;

			$application->registerAttributes($this->_Template_Contents);
			$result .= $this->mTmplFiller->fill('menu_manager/','store_settings_item.tpl.html', array());
		}

		return $result;
	}


	function output()
	{
		global $application;

		$groupname = "";

		if (func_num_args() > 0)
		{
			$groupname = func_get_arg(0);
			$parent = func_get_arg(1);
			if($groupname=='home')
			{

				$template_contents = array(
						'Menu_Items' => $this->generateMenus($groupname,0),
						);

				$this->_Template_Contents = $template_contents;

				$application->registerAttributes($this->_Template_Contents);

				return $this->mTmplFiller->fill('menu_manager/','container.tpl.html', array());
			}

			else
			{

				$grpdisplayname = explode(",",$groupname);
				$template_contents = array(
						'Menu_Items' 		=> $this->outputStoreSettingsMenu($groupname,$parent),
						'MenuGroupName' 	=> getValxmsg($grpdisplayname[0],$grpdisplayname[1]),
						);

				$this->_Template_Contents = $template_contents;

				$application->registerAttributes($this->_Template_Contents);

				return $this->mTmplFiller->fill('menu_manager/','store_settings_container.tpl.html', array());
			}

		}
	}


	/**
	 * @ describe the function ProductList->getTag.
	 */
	function getTag($tag)
	{
		return getKeyIgnoreCase($tag, $this -> _Template_Contents);
	}


	//------------------------------------------------
	//              PRIVATE DECLARATION
	//------------------------------------------------

	/**#@+
	 * @access private
	 */
	var $mTmplFiller;
	var $_Template_Contents;

}
?>