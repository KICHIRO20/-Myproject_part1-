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
loadModuleFile('cms/cms_api.php');

/**
 * @package CMS
 * @author Sergey Kulitsky
 */

class CCMSPageInfo
{
    function CCMSPageInfo($page_id = 0)
    {
        // this class processes all tags for pages
        // so page_id = 0 means the info should be gathered
        // for all pages which is actual for some tags

        $this -> _page_id = 0;

        // first of all trying to get data for provided page_id
        $this -> _page_data = modApiFunc('CMS', 'getPageInfo', $page_id);
        if ($this -> _page_data)
            $this -> _page_id = $this -> _page_data['page_id'];
    }

    /**
     * Returns Page info tag value
     */
    function getCMSPageTagValue($tag, $params)
    {
        $output = '';

        switch($tag)
        {
            case 'index':
                $output = @$this -> _page_data['page_index'];
                break;

            case 'name':
                $output = @$this -> _page_data['name'];
                break;

            case 'content':
                $output = @$this -> _page_data['descr'];
                break;

            case 'parentpage':
                $output = @$this -> _page_data['parent_id'];
                break;
            case 'prefix':
                $output = @$this -> _page_data['seo_prefix'];
                if (!$output)
                    $output = @$this -> _page_data['page_index'];
                break;
            case 'title':
                $output = @$this -> _page_data['seo_title'];
                if (!$output)
                    $output = @$this -> _page_data['name'];
                break;

            case 'metadescription':
                $output = @$this -> _page_data['seo_descr'];
                break;

            case 'metakeywords':
                $output = @$this -> _page_data['seo_keywords'];
                break;

            case 'link':
                $r = new Request();
                $r -> setView('CMSPage');
                $r -> setKey('page_id', $this -> _page_id);
                $output = $r -> getURL();
        }

        return $output;
    }

    var $_page_id;
    var $_page_data;
}
?>