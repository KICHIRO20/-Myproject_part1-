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

class PageView
{
    function getTemplateFormat()
    {
        $format = array(
            'layout-file' => 'cms-pageview-block.ini',
            'files'       => array(
                'Container' => TEMPLATE_FILE_SIMPLE,
            ),
            'options'     => array(
            )
        );
        return $format;
    }

    function getViewCacheKey()
    {
        return null;
        global $application;

        //                          Application::init().
        //                    POST       ,
        if (modApiFunc('Session', 'is_Set', '__POST_DATA_SENT__') == true)
        {
        	modApiFunc('Session', 'un_Set', '__POST_DATA_SENT__');
        	return null;
        }

        $sections = $application->getSectionByCurrentPagename();
        if(
            (in_array('ProductList',$sections) || in_array('ProductInfo',$sections)) &&
            modApiFunc('Cart', 'getCartProductsQuantity') == 0 &&
            modApiFunc('Customer_Account','getCurrentSignedCustomer') == null
          )
        {
            $cache_key = array();

            // depends on current URL
            $request = &$application->getInstance('Request');
            $cache_key[] = $request->selfURL();

            // depends on current language
            $cache_key[] = modApiFunc('MultiLang', 'getLanguage');

            // depends on current currency
            $cache_key[] = modApiFunc("Localization", "getSessionDisplayCurrency");

            // depends on current catalog state
            $cache_key[] = modApiFunc('CProductListFilter','getCurrentCategoryId');
            $cache_key[] = modApiFunc('CProductListFilter','getCurrentManufactureId');
            $cache_key[] = modApiFunc('CProductListFilter','getCurrentMinSalePrice');
            $cache_key[] = modApiFunc('CProductListFilter','getCurrentMaxSalePrice');
            $cache_key[] = modApiFunc('CProductListFilter','getCurrentSortField');
            $cache_key[] = modApiFunc('CProductListFilter','getCurrentMinSalePrice');
            $cache_key[] = modApiFunc('Catalog','getCurrentProductID');

            return md5(serialize($cache_key));
        }
        else
        {
            return null;
        }
    }

    function output()
    {
        do_action('after_theme_loaded');
        $page = 'default';
        if (func_num_args() > 0) $page = func_get_arg(0);
        return $this->outputPageByMap($page);
    }

    function outputPageByMap($page)
    {
        global $__TPL_DIR__, $__TPL_URL__, $application;
        $page = basename($page);
        $map_file = modApiFunc('Layout_CMS', 'getThemePath').'map.ini';
        $use_cached = false;

        if (file_exists($map_file))
        {
            $ini_cache = $application->getIniCache();
            $map_mtime = filemtime($map_file);
            if ($map_mtime == $ini_cache->read($map_file.'-mtime')) {
                $map = $ini_cache->read($map_file);
                $use_cached = true;
            }
            else {
                CProfiler::ioStart($map_file, 'parse');
                $map = parse_ini_file($map_file, true);
                CProfiler::ioStop();
                $ini_cache->write($map_file.'-mtime', $map_mtime);
                $ini_cache->write($map_file, $map);
            }
        }
        else
        {
            $map = modApiFunc('Layout_CMS','generateMap',$page);
        }

        if (isset($map['default']))
        {
            $map_default = $map['default'];
        }

        if (isset($map[$page]))
        {
            $map = array_merge($map_default, $map[$page]);
        }
        else
        {
            _fatal("The page [$page] not found in the map file [$map_file]");
        }

        $template_path = getTemplateFileAbsolutePath('pages/templates/'.$map['template']);
        $tpl_cache = $application->getTplCache();
        $template_mtime = filemtime($template_path);
        if ($template_mtime == $tpl_cache->read($template_path.'-mtime')) {
            $template_content = $tpl_cache->read($template_path);
        }
        else {
            $template_file = new CFile($template_path);
            $template_content = $template_file->getContent();
            $tpl_cache->write($template_path.'-mtime', $template_mtime);
            $tpl_cache->write($template_path, $template_content);
            $use_cached = false;
        }

        if ($use_cached) {
            $contents = $tpl_cache->read($template_path.'-'.$page);
        }
        if (! isset($contents)) {
            $replace = array();
            foreach ($map as $k=>$v)
            {
                $replace['#'.$k.'#'] = $v;
                $replace['['.$k.']'] = htmlentities($v, ENT_QUOTES);
            }
            $contents = '?>'.strtr($template_content, $replace);
            $tpl_cache->write($template_path.'-'.$page, $contents);
        }

        ob_start();
        eval($contents);
        $contents = ob_get_contents();
        ob_end_clean();

        $contents = str_replace('<br>','<br/>', $contents);

        return $contents;
    }

/*    function getTag()
    {
        return null;
    }*/
}
?>