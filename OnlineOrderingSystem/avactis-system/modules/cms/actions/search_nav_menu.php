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
    * Action is to search by category , static pages and system pages
    * This action is waiting for getting a query string from _GET
    * or _POST array by the key 'search_pattern'.
    *
    * If the query string is not empty, then will be done a search
    * and the search id (Search ID) will be saved to the session
    * under the key 'search_result_id'.
    *
    * @package Cms
    * @author Mugdha Wadhokar
    */
   class SearchNavMenu extends AjaxAction
   {
   	/** Processes action. */
   	function onAction()
   	{
   		global $application;
   		$request = $application->getInstance('Request');
   		$search_string = trim($request->getValueByKey('search_string'));
   		$search_type = trim($request->getValueByKey('search_type'));
   		$Result =[];
   		$search_result = $search_string;
   		if (_ml_strlen($search_string) < 1)
   		{
   			modApiFunc('Session', 'un_Set', 'search_result_menu');
   			$search_result = '';
   			return $search_result;
   		}
   		else
   		{
   			$Result = '';
   			if($search_type == "systemPages")
   			{
   				$SystemPages = modApiFunc('CMS', 'getSystemPageList');
   				$i = 0;
   				foreach( $SystemPages as $key => $value)
   				{
   					$occurerence = stripos($value , $search_string);
   					if($occurerence === false) continue; else { $Result[$i] = $value; $i++; }
   				}
   			} //systemPages
   			if($search_type == "staticArticle")
   			{
   				$Static_Pages =  modApiFunc('CMS', 'getPageTree', 0);
   				foreach( $Static_Pages as $key => $value)
   				{
   					$occurerence = stripos($value['name'] , $search_string);
   					if($occurerence === false) continue;
   					else { $Result[$value['page_id']] = $value['name']; }
   				}
   			}//static article
   			if($search_type == "categoryList")
   			{
   				$catalog_tree = modApiFunc("Catalog", "getSubcategoriesFullListWithParent", 1);
   				foreach( $catalog_tree as $key => $cat)
   				{
   					$occurerence = stripos($cat['name'] , $search_string);
   					if($occurerence === false) continue;
   					else { $Result[$cat['id']] = $cat['name']; }
   				}
   			}// category list
   			return json_encode($Result);} } }
   ?>