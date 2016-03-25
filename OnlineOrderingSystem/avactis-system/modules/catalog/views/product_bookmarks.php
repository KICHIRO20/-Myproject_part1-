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
 * @package Catalog
 * @author Egor V. Derevyankin
 *
 */

class ProductBookmarks
{
    function ProductBookmarks()
    {
        global $application;
        $this->MR = &$application->getInstance('MessageResources','bookmarks-messages','AdminZone');
        $this->terminator_outed = false;
    }

    function gen_bmOnClick($bm_location)
    {
        $code = "window.location='";
        $request = new Request();
        $request->setView($bm_location['view']);
        if(isset($bm_location['action']))
            $request->setAction($bm_location['action']);
        if(isset($bm_location['keys']))
            foreach($bm_location['keys'] as $key => $value)
                $request->setKey($key,$value);
        $code .= $request->getURL();
        $code .= "';";
        return $code;
    }

    function outputBookmarks()
    {
        global $application;

        $html_code = "";

        foreach($this->bms as $page => $bm)
        {
            if($page == 'terminator')
            {
                $tpl_content = array(
                    "bmClass" => ($page == $this->page) ? 'active' : ((@strstr($this->status_depends[$this->page.'_'.$this->page_status],$page.'_notavail')) ? 'notavail disabled' : 'inactive')
                   ,"bmIcon" => $bm['icon'] . ((@strstr($this->status_depends[$this->page.'_'.$this->page_status],$page.'_notavail')) ? '-na' : '')
                   ,"bmOnClick" => ($page == $this->page or @strstr($this->status_depends[$this->page.'_'.$this->page_status],$page.'_notavail')) ? '' : 'OnTerminatorClick()'
                   ,"bmName" => $page
                );
                $tpl_file = 'terminator';
                $this->terminator_outed = true;
            }
            else
            {
                $tpl_content = array(
                    "bmClass" => ($page == $this->page) ? 'active' : ((@strstr($this->status_depends[$this->page.'_'.$this->page_status],$page.'_notavail')) ? 'notavail disabled' : 'inactive')
                   ,"bmIcon" => $bm['icon'] . ((@strstr($this->status_depends[$this->page.'_'.$this->page_status],$page.'_notavail')) ? '-na' : '')
                   ,"bmText" => $this->MR->getMessage($bm['title'])
                   ,"bmOnClick" => ($page == $this->page or @strstr($this->status_depends[$this->page.'_'.$this->page_status],$page.'_notavail')) ? '' : $this->gen_bmOnClick($bm['location'])
                   ,"bmName" => $page
                );
                $tpl_file = 'bookmark';
            };

            $this->_Template_Contents=$tpl_content;
            $application->registerAttributes($this->_Template_Contents);
            $this->mTmplFiller = &$application->getInstance('TmplFiller');
            $html_code .= $this->mTmplFiller->fill("catalog/bookmarks/", "{$tpl_file}.tpl.html",array());
        };

        return $html_code;
    }

    function output()
    {
        global $application;

        $this->page=func_get_arg(0);
        $this->entity_id=func_get_arg(1);
        if(func_num_args()==3)
            $this->page_status=func_get_arg(2);
        else
            $this->page_status='read';

        $this->_initBookmarks();

        $tpl_content = array(
            "bmBGColor" => 'transparent'
           ,"Bookmarks" => $this->outputBookmarks()
           ,"RightSpace" => ($this->_need_right_space) ? '<td width="100%" class="bookmarks_space">&nbsp;</td>' : ''
        );

        $this->_Template_Contents=$tpl_content;
        $application->registerAttributes($this->_Template_Contents);
        $this->mTmplFiller = &$application->getInstance('TmplFiller');
        return $this->mTmplFiller->fill("catalog/bookmarks/", "container.tpl.html",array());
    }

    function getTag($tag)
    {
        return getKeyIgnoreCase($tag, $this->_Template_Contents);
    }

    function _initBookmarks()
    {
        $this->bms = array('details' => array(
                                'title' => 'PRD_DETAILS'
                               ,'icon' => 'fa-file-text-o'
                               ,'location' => array(
                                    'view' => 'Catalog_EditProduct'
                                   ,'action' => 'SetCurrentProduct'
                                   ,'keys' => array(
                                                'prod_id' => $this->entity_id
                                              )
                                )
                           ),
                           'options' => array(
                                 'title' => 'PRD_OPTIONS'
                                ,'icon' => 'fa-list'
                                ,'location' => array(
                                    'view' => 'PO_OptionsList'
                                   ,'keys' => array(
                                                'parent_entity' => 'product'
                                               ,'entity_id' => $this->entity_id
                                              )
                                )
                           ),
                           'files' => array(
                                 'title' => 'PRD_FILES'
                                ,'icon' => 'fa-file-pdf-o'
                                ,'location' => array(
                                    'view' => 'PF_FilesList'
                                   ,'keys' => array(
                                                'product_id' => $this->entity_id
                                              )
                                )
                           ),
                           'images' => array(
                                 'title' => 'PRD_IMAGES'
                                ,'icon' => 'fa-file-image-o '
                                ,'location' => array(
                                    'view' => 'PI_ImagesList'
                                   ,'keys' => array(
                                                'product_id' => $this->entity_id
                                              )
                                )
                           ),
/* 'color_swatch' => array(
			        				'title' => 'PRD_COLOR_SWATCH'
			        				,'icon' => 'p-images'
			        				,'location' => array(
			        						'view' => 'PI_ColorSwatch'
			        						,'keys' => array(
			        								'product_id' => $this->entity_id
			        						)
			        				)
			        		), */
                           'categories' => array(
                                 'title' => 'PRD_CATEGORIES'
                                ,'icon' => 'fa-sitemap'
                                ,'location' => array(
                                    'view' => 'MngProductCats'
                                   ,'keys' => array(
                                                'product_id' => $this->entity_id
                                              )
                                )
                           ),
                           'quantity_discounts' => array(
                                 'title' => 'PRD_QUANTITY_DISCOUNTS'
                                ,'icon' => 'fa-money'
                                ,'location' => array(
                                    'view' => 'manage_quantity_discounts_az'
                                   ,'keys' => array(
                                                'product_id' => $this->entity_id
                                              )
                                )
                           ),
                           'related' => array(
                                 'title' => 'PRD_RELATED'
                                ,'icon' => 'fa-indent '
                                ,'location' => array(
                                    'view' => 'related_products'
                                   ,'keys' => array(
                                               'product_id' => $this->entity_id
                                              )
                                )
                           )
                     );

        $this->status_depends = array(
            'details_edit' => ''
           ,'details_add' => 'options_notavail files_notavail images_notavail categories_notavail quantity_discounts_notavail related_notavail'
        );

        $this->_need_right_space = false;
    }

    var $_Template_Contents;
    var $MR;
    var $bms;
    var $m_bms;
    var $page;
    var $entity_id;
    var $page_status;
    var $status_depends;
    var $terminator_outed;
    var $_need_right_space;
};

?>