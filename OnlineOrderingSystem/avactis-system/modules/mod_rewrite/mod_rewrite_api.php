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
 * @package ModRewrite
 * @author Egor V. Derevyankin
 *
 */

class Mod_Rewrite
{
    function Mod_Rewrite()
    {
        $this->_loadSettings();
        $this->_loadRewriteScheme();
        $this->_prepareQueriesSuffixesAndPrefixes();
    }

    function install()
    {
        global $application;
        loadCoreFile('csv_parser.php');
        $csv_parser = new CSV_Parser();

        $tables = Mod_Rewrite::getTables();
        $query = new DB_Table_Create($tables);

        $def_scheme_id = 0;
        $table = 'mr_schemes';
        $columns = $tables[$table]['columns'];

        list($flt,$Default_Schemes) = $csv_parser->parse_file(dirname(__FILE__)."/includes/default_rewrite_schemes.csv");
        if(count($Default_Schemes) > 0)
        {
            foreach($Default_Schemes as $key => $scheme)
            {
                $query = new DB_Insert($table);
                $query->addInsertValue($scheme["scheme_name"], $columns['scheme_name']);
                $query->addInsertValue($scheme["cat_rule_tpl"], $columns['cat_rule_tpl']);
                $query->addInsertValue($scheme["prod_rule_tpl"], $columns['prod_rule_tpl']);
                $query->addInsertValue($scheme["cms_rule_tpl"], $columns['cms_rule_tpl']);
                $application->db->getDB_Result($query);

                if($key == 0)
                {
                    $def_scheme_id = $application->db->DB_Insert_Id();
                };
            };
        };

        $table = 'mr_settings';
        $columns = $tables[$table]['columns'];

        list($flt,$Default_Settings) = $csv_parser->parse_file(dirname(__FILE__)."/includes/default_settings.csv");
        if(count($Default_Settings) > 0)
        {
            foreach($Default_Settings as $key => $setting)
            {
                if($setting['key'] == 'REWRITE_SCHEME')
                {
                    $setting['value'] = $def_scheme_id;
                };

                $query = new DB_Insert($table);
                $query->addInsertValue($setting["key"], $columns['setting_key']);
                $query->addInsertValue($setting["value"], $columns['setting_value']);
                $application->db->getDB_Result($query);
            };
        };
    }

    function uninstall()
    {
        $query = new DB_Table_Delete(Mod_Rewrite::getTables());
    }

    function getTables()
    {
        static $tables;

        if (is_array($tables))
        {
            return $tables;
        }

        $tables=array();

        $table = 'mr_settings';
        $tables[$table] = array();
        $tables[$table]['columns'] = array(
            'setting_id'    => $table.'.setting_id'
           ,'setting_key'   => $table.'.setting_key'
           ,'setting_value' => $table.'.setting_value'
        );
        $tables[$table]['types'] = array(
            'setting_id'    => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
           ,'setting_key'   => DBQUERY_FIELD_TYPE_CHAR255.' NOT NULL DEFAULT \'\''
           ,'setting_value' => DBQUERY_FIELD_TYPE_CHAR255.' NOT NULL DEFAULT \'\''
        );
        $tables[$table]['primary'] = array(
            'setting_id'
        );

        $table = 'mr_schemes';
        $tables[$table]['columns'] = array(
            'scheme_id'     => $table.'.scheme_id'
           ,'scheme_name'   => $table.'.scheme_name'
           ,'cat_rule_tpl'  => $table.'.cat_rule_tpl'
           ,'prod_rule_tpl' => $table.'.prod_rule_tpl'
           ,'cms_rule_tpl' => $table.'.cms_rule_tpl'
        );
        $tables[$table]['types'] = array(
            'scheme_id'     => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
           ,'scheme_name'   => DBQUERY_FIELD_TYPE_CHAR255.' NOT NULL DEFAULT \'\''
           ,'cat_rule_tpl'  => DBQUERY_FIELD_TYPE_CHAR255.' NOT NULL DEFAULT \'\''
           ,'prod_rule_tpl' => DBQUERY_FIELD_TYPE_CHAR255.' NOT NULL DEFAULT \'\''
           ,'cms_rule_tpl' => DBQUERY_FIELD_TYPE_CHAR255.' NOT NULL DEFAULT \'\''
        );
        $tables[$table]['primary'] = array(
            'scheme_id'
        );
        $tables[$table]['indexes'] = array(
            'UNIQUE KEY sn' => 'scheme_name'
        );

        $table = 'mr_integrity';
        $tables[$table] = array();
        $tables[$table]['columns'] = array(
            'record_id'     => $table.'.record_id'
           ,'layout_path'   => $table.'.layout_path'
           ,'layout_md5'    => $table.'.layout_md5'
           ,'htaccess_path' => $table.'.htaccess_path'
           ,'htaccess_md5'  => $table.'.htaccess_md5'
           ,'sefu_md5'      => $table.'.sefu_md5'
           ,'sets_md5'      => $table.'.sets_md5'
           ,'mr_active'     => $table.'.mr_active'
        );
        $tables[$table]['types'] = array(
            'record_id'     => DBQUERY_FIELD_TYPE_INT .' NOT NULL auto_increment'
           ,'layout_path'   => DBQUERY_FIELD_TYPE_CHAR255.' NOT NULL DEFAULT \'\''
           ,'layout_md5'    => DBQUERY_FIELD_TYPE_CHAR50.' NOT NULL DEFAULT \'\''
           ,'htaccess_path' => DBQUERY_FIELD_TYPE_CHAR255.' NOT NULL DEFAULT \'\''
           ,'htaccess_md5'  => DBQUERY_FIELD_TYPE_CHAR50.' NOT NULL DEFAULT \'\''
           ,'sefu_md5'      => DBQUERY_FIELD_TYPE_CHAR50.' NOT NULL DEFAULT \'\''
           ,'sets_md5'      => DBQUERY_FIELD_TYPE_CHAR50.' NOT NULL DEFAULT \'\''
           ,'mr_active'     => "ENUM ('N','Y') NOT NULL DEFAULT 'N'"
        );
        $tables[$table]['primary'] = array(
            'record_id'
        );
        $tables[$table]['indexes'] = array(
            'UNIQUE KEY lp' => 'layout_path'
        );

        global $application;
        return $application->addTablePrefix($tables);
    }

    function getSettings()
    {

        $res = execQuery("SELECT_MR_SETTINGS", array());

        $settings=array();

        foreach($res as $k => $sval)
            $settings[$sval['setting_key']]=$sval['setting_value'];

        return $settings;
    }

    function updateSettings($settings)
    {
        global $application;
        $tables=$this->getTables();
        $stable=$tables['mr_settings']['columns'];

        foreach($settings as $skey => $sval)
        {
            $query = new DB_Update('mr_settings');
            $query->addUpdateValue($stable['setting_value'],$sval);
            $query->WhereValue($stable['setting_key'], DB_EQ, $skey);
            $application->db->PrepareSQL($query);
            $application->db->DB_Exec();
        };

        $this->_loadSettings();
        $this->_loadRewriteScheme();
        $this->_prepareQueriesSuffixesAndPrefixes();

        modApiFunc('EventsManager','throwEvent','ModRewriteChanged');

        return;
    }

    /**
     *
     */
    function getRewriteSchemes()
    {
        /*global $application;
        $tables = $this->getTables();
        $rs_table = 'mr_schemes';

        $query = new DB_Select();
        $query->addSelectTable('mr_schemes');
        $query->addSelectField('*');*/
        return execQuery("SELECT_MR_SCHEMES",array()); //$application->db->getDB_Result($query);
    }

    /**
     *
     * @return array(
     *   'server_soft'   => string
     *  ,'server_api'    => string
     *  ,'mod_rewrite_loaded'      => enum('Y','N','NA')
     *  )
     */
    function detectModRewrite()
    {
        $server_soft = $_SERVER["SERVER_SOFTWARE"];
        $server_api = php_sapi_name();
        if($server_api == 'apache' || $server_api=='apache2handler')
        {
            if(function_exists('apache_get_modules'))
            {
                $apache_modules = apache_get_modules();
                if(in_array('mod_rewrite',$apache_modules))
                    $mod_rewrite_loaded = 'Y';
                else
                    $mod_rewrite_loaded = 'N';
            }
            else
            {
                $mod_rewrite_loaded = 'NA';
            };
        }
        elseif ($server_api == 'litespeed')
        {
            $mod_rewrite_loaded = 'Y';
        }
        else
        {
            if(preg_match("/^apache/i",$server_soft))
            {
                $mod_rewrite_loaded = 'NA';
            }
            else
            {
                $mod_rewrite_loaded = 'N';
            };
        };

        return compact("server_soft","server_api","mod_rewrite_loaded");
    }

    /**
 	* build apache mod_rewrite block for .htaccess file
 	*/
    function genRewriteBlock($cz_layout_file_path)
    {
        global $application;

        $cz_layouts = array_keys(LayoutConfigurationManager::static_get_cz_layouts_list());

        for($i=0;$i<count($cz_layouts);$i++)
        {
            $cz_layouts[$i] = $this->_prepareAbsFilePath($cz_layouts[$i]);
        }

        if(!in_array($cz_layout_file_path,$cz_layouts))
        {
            return null;
        };

        $parsed_info = _parse_cz_layout_ini_file($cz_layout_file_path,true);

        # prepare info for categories pages
        $cats_info = array(
            'by_id' => array()
           ,'as_sub' => array()
           ,'default' => ''
        );

        foreach($parsed_info['ProductList'] as $layout_key => $rel_script_path)
        {
            //
			$layout_key = str_replace(' ', '', $layout_key);

			if(_ml_strtolower($layout_key) == 'default')
            {
                $cats_info['default'] = trim(_ml_strtolower($rel_script_path));
                continue;
            };
            if(preg_match("/^categories\s*\{([0-9\,\+]+)\}$/i",trim(_ml_strtolower($layout_key)),$matches))
            {
                $row_ids = array_map("trim",explode(",",$matches[1]));
                foreach($row_ids as $row_id)
                {
                    if(_ml_strpos($row_id,'+')===false)
                    {
                        $fine_id = intval($row_id);
                        $cats_info['by_id'][trim(_ml_strtolower($rel_script_path))][] = $fine_id;
                    }
                    else
                    {
                        $fine_id = intval($row_id);
                        $cats_info['as_sub'][trim(_ml_strtolower($rel_script_path))][] = $fine_id;
                    };
                };
            };
        };

        $rw_strings = array();

        # 1.                      ,                             ID
        if(!empty($cats_info['by_id']))
        {
            foreach($cats_info['by_id'] as $rel_script_path => $ids_array)
            {
                $str = str_replace(
                    array('%category_id%','%page_number%','%parent_cid%','%query_cat_prefix%','%seo_cat_prefix%')
                   ,array(implode('|',$ids_array),'[0-9]+','',$this->queries_prefixes['category'],'.*')
                   ,$this->rewrite_scheme['category']
                );

                $rw_strings[] = 'RewriteRule '.$str.' '.$rel_script_path.'?'.$this->queries_suffixes['category'].' [L]';

                #                                  .
                //$rw_strings[]='RewriteRule ^'.$this->queries_prefixes['category'].'/('.implode('|',$ids_array).')/([0-9])+/.*\.html '.$rel_script_path.'?'.$this->queries_suffixes['category'].' [L]';
            };
        };

        # 2.                                 ID+
        if(!empty($cats_info['as_sub']))
        {
            foreach($cats_info['as_sub'] as $rel_script_path => $ids_array)
            {
                $str = str_replace(
                    array('%category_id%','%page_number%','%parent_cid%','%query_cat_prefix%','%seo_cat_prefix%')
                   ,array('[0-9]+','[0-9]+','-'. ((count($ids_array) > 1) ? '(' : '') . (implode('|',$ids_array)) . ((count($ids_array) > 1) ? ')' : '') . '\+'
                   ,$this->queries_prefixes['category'],'.*')
                   ,$this->rewrite_scheme['category']
                );

                $rw_strings[] = 'RewriteRule '.$str.' '.$rel_script_path.'?'.$this->queries_suffixes['category'].' [L]';

                #                                  .
                //$rw_strings[]='RewriteRule ^'.$this->queries_prefixes['category'].'/([0-9])+/([0-9])+/('.(implode('|',$ids_array)).'\+)/.*\.html '.$rel_script_path.'?'.$this->queries_suffixes['category'].' [L]';
            };
        };

        # 3.

        $str = str_replace(
            array('%category_id%','%page_number%','%parent_cid%','%query_cat_prefix%','%seo_cat_prefix%')
           ,array('[0-9]+','[0-9]+','',$this->queries_prefixes['category'],'.*')
           ,$this->rewrite_scheme['category']
        );

        $rw_strings[] = 'RewriteRule '.$str.' '.$cats_info['default'].'?'.$this->queries_suffixes['category'].' [L]';

        #                                  .
        //$rw_strings[] = 'RewriteRule ^'.$this->queries_prefixes['category'].'/([0-9])+/([0-9])+/.*\.html '.$cats_info['default'].'?'.$this->queries_suffixes['category'].' [L]';

        $prods_info = array(
            'by_id' => array()
           ,'as_cat_child' => array(
                                    'by_id' => array()
                                   ,'as_sub' => array()
                                )
           ,'default' => ''
        );

        foreach($parsed_info['ProductInfo'] as $layout_key => $rel_script_path)
        {
            //
			$layout_key = str_replace(' ', '', $layout_key);

			if(_ml_strtolower($layout_key) == 'default')
            {
                $prods_info['default'] = trim(_ml_strtolower($rel_script_path));
                continue;
            };
            if(preg_match("/products\s*\{([0-9\,]+)\}/i",$layout_key,$matches))
            {
                $row_ids = array_map("trim",explode(",",$matches[1]));
                foreach($row_ids as $row_id)
                {
                    $fine_id = intval($row_id);
                    $prods_info['by_id'][trim(_ml_strtolower($rel_script_path))][] = $fine_id;
                };
                continue;
            };
            if(preg_match("/categories\s*\{([0-9\,\+]+)\}/i",$layout_key,$matches))
            {
                $row_ids = array_map("trim",explode(",",$matches[1]));
                foreach($row_ids as $row_id)
                {
                    if(_ml_strpos($row_id,"+")===false)
                    {
                        $fine_id = intval($row_id);
                        $prods_info['as_cat_child']['by_id'][trim(_ml_strtolower($rel_script_path))][] = $fine_id;
                    }
                    else
                    {
                        $fine_id = intval($row_id);
                        $prods_info['as_cat_child']['as_sub'][trim(_ml_strtolower($rel_script_path))][] = $fine_id;
                    };
                };
            };
        };

        # 1.                                               ID
        if(!empty($prods_info['by_id']))
        {
            foreach($prods_info['by_id'] as $rel_script_path => $ids_array)
            {
                $str = str_replace(
                    array('%product_id%','%parent_cid%','%query_prod_prefix%','%seo_prod_prefix%')
                   ,array(implode('|',$ids_array),'',$this->queries_prefixes['product'],'.*')
                   ,$this->rewrite_scheme['product']
                );

                $rw_strings[] = 'RewriteRule '.$str.' '.$rel_script_path.'?'.$this->queries_suffixes['product'].' [L]';

                #                                  .
                //$rw_strings[] = 'RewriteRule ^'.$this->queries_prefixes['product'].'/('.implode('|',$ids_array).')/.*\.html '.$rel_script_path.'?'.$this->queries_suffixes['product'].' [L]';
            };
        };

        # 2.                                                  ID
        if(!empty($prods_info['as_cat_child']['by_id']))
        {
            foreach($prods_info['as_cat_child']['by_id'] as $rel_script_path => $ids_array)
            {
                $str = str_replace(
                    array('%product_id%','%parent_cid%','%query_prod_prefix%','%seo_prod_prefix%')
                   ,array('[0-9]+','-'.((count($ids_array) > 1) ? '(' : '').implode('|',$ids_array).((count($ids_array) > 1) ? ')' : '')
                   ,$this->queries_prefixes['product'],'.*')
                   ,$this->rewrite_scheme['product']
                );

                $rw_strings[] = 'RewriteRule '.$str.' '.$rel_script_path.'?'.$this->queries_suffixes['product'].' [L]';

                #                                  .
                //$rw_strings[] = 'RewriteRule ^'.$this->queries_prefixes['product'].'/([0-9]+)/('.implode('|',$ids_array).')/.*\.html '.$rel_script_path.'?'.$this->queries_suffixes['product'].' [L]';
            };
        };

        # 3.                                                  ID+
        if(!empty($prods_info['as_cat_child']['as_sub']))
        {
            foreach($prods_info['as_cat_child']['as_sub'] as $rel_script_path => $ids_array)
            {
                $str = str_replace(
                    array('%product_id%','%parent_cid%','%query_prod_prefix%','%seo_prod_prefix%')
                   ,array('[0-9]+','-'.((count($ids_array) > 1) ? '(' : '').implode('|',$ids_array).((count($ids_array) > 1) ? ')' : '').'\+'
                   ,$this->queries_prefixes['product'],'.*')
                   ,$this->rewrite_scheme['product']
                );

                $rw_strings[] = 'RewriteRule '.$str.' '.$rel_script_path.'?'.$this->queries_suffixes['product'].' [L]';

                #                                  .
                //$rw_strings[] = 'RewriteRule ^'.$this->queries_prefixes['product'].'/([0-9]+)/('.implode('|',$ids_array).'\+)/.*\.html '.$rel_script_path.'?'.$this->queries_suffixes['product'].' [L]';
            };
        };

        # 4.
        $str = str_replace(
            array('%product_id%','%parent_cid%','%query_prod_prefix%','%seo_prod_prefix%')
           ,array('[0-9]+','',$this->queries_prefixes['product'],'.*')
           ,$this->rewrite_scheme['product']
        );

        $rw_strings[] = 'RewriteRule '.$str.' '.$prods_info['default'].'?'.$this->queries_suffixes['product'].' [L]';

        #                                  .
        //$rw_strings[] = 'RewriteRule ^'.$this->queries_prefixes['product'].'/([0-9])+/.*\.html '.$prods_info['default'].'?'.$this->queries_suffixes['product'].' [L]';

        $cms_info = array(
            'by_id' => array()
           ,'as_sub' => array()
           ,'default' => ''
        );

        foreach($parsed_info['CMSPage'] as $layout_key => $rel_script_path)
        {
            //
			$layout_key = str_replace(' ', '', $layout_key);

			if(_ml_strtolower($layout_key) == 'default')
            {
                $cms_info['default'] = trim(_ml_strtolower($rel_script_path));
                continue;
            };
            if(preg_match("/cmspage\s*\{([0-9\,]+)\}/i",$layout_key,$matches))
            {
                $row_ids = array_map("trim",explode(",",$matches[1]));
                foreach($row_ids as $row_id)
                {
                    $fine_id = intval($row_id);
                    $cms_info['by_id'][trim(_ml_strtolower($rel_script_path))][] = $fine_id;
                };
                continue;
            };
        };

        # .                                               ID
        if(!empty($cms_info['by_id']))
        {
            foreach($cms_info['by_id'] as $rel_script_path => $ids_array)
            {
                $str = str_replace(
                    array('%page_id%','%query_cms_prefix%','%seo_cms_prefix%')
                   ,array(implode('|',$ids_array),$this->queries_prefixes['cmspage'],'.*')
                   ,$this->rewrite_scheme['cmspage']
                );

                $rw_strings[] = 'RewriteRule '.$str.' '.$rel_script_path.'?'.$this->queries_suffixes['cmspage'].' [L]';

                #                                  .
                //$rw_strings[] = 'RewriteRule ^'.$this->queries_prefixes['product'].'/('.implode('|',$ids_array).')/.*\.html '.$rel_script_path.'?'.$this->queries_suffixes['product'].' [L]';
            };
        };

        # 2.                                                  ID
        if(!empty($cms_info['as_cat_child']['by_id']))
        {
            foreach($cms_info['as_cat_child']['by_id'] as $rel_script_path => $ids_array)
            {
                $str = str_replace(
                    array('%page_id%','%query_cms_prefix%','%seo_cms_prefix%')
                   ,array('[0-9]+','-'.((count($ids_array) > 1) ? '(' : '').implode('|',$ids_array).((count($ids_array) > 1) ? ')' : '')
                   ,$this->queries_prefixes['cmspage'],'.*')
                   ,$this->rewrite_scheme['cmspage']
                );

                $rw_strings[] = 'RewriteRule '.$str.' '.$rel_script_path.'?'.$this->queries_suffixes['cmspage'].' [L]';

                #                                  .
                //$rw_strings[] = 'RewriteRule ^'.$this->queries_prefixes['product'].'/([0-9]+)/('.implode('|',$ids_array).')/.*\.html '.$rel_script_path.'?'.$this->queries_suffixes['product'].' [L]';
            };
        };

        # 3.                                                  ID+
        if(!empty($cms_info['as_cat_child']['as_sub']))
        {
            foreach($cms_info['as_cat_child']['as_sub'] as $rel_script_path => $ids_array)
            {
                $str = str_replace(
                    array('%page_id%','%query_cms_prefix%','%seo_cms_prefix%')
                   ,array('[0-9]+','-'.((count($ids_array) > 1) ? '(' : '').implode('|',$ids_array).((count($ids_array) > 1) ? ')' : '').'\+'
                   ,$this->queries_prefixes['cmspage'],'.*')
                   ,$this->rewrite_scheme['cmspage']
                );

                $rw_strings[] = 'RewriteRule '.$str.' '.$rel_script_path.'?'.$this->queries_suffixes['cmspage'].' [L]';

                #                                  .
                //$rw_strings[] = 'RewriteRule ^'.$this->queries_prefixes['product'].'/([0-9]+)/('.implode('|',$ids_array).'\+)/.*\.html '.$rel_script_path.'?'.$this->queries_suffixes['product'].' [L]';
            };
        };

        # 4.
        $str = str_replace(
            array('%page_id%','%query_cms_prefix%','%seo_cms_prefix%')
           ,array('[0-9]+',$this->queries_prefixes['cmspage'],'.*')
           ,$this->rewrite_scheme['cmspage']
        );

        $rw_strings[] = 'RewriteRule '.$str.' '.$cms_info['default'].'?'.$this->queries_suffixes['cmspage'].' [L]';

        #                                  .
        //$rw_strings[] = 'RewriteRule ^'.$this->queries_prefixes['product'].'/([0-9])+/.*\.html '.$prods_info['default'].'?'.$this->queries_suffixes['product'].' [L]';

        loadCoreFile('URI.class.php');
        $uriObj = new URI($parsed_info['Site']['SiteURL']);

        #        Options +FollowSymLinks
        //if($application->getAppIni('MR_ADD_FSL_STRING') == 'YES')
        if($this->settings['ADD_FSL_STRING'] == 'Y')
        {
            $fsl_string = 'Options +FollowSymLinks';
        }
        else
        {
            $fsl_string = '';
        };

        #
        $rw_first = str_replace(
            array('%http_site_url%','%url_dir%','%mr_fsl_string%')
           ,array($parsed_info['Site']['SiteURL'],$uriObj->getPart('dir') . ((_ml_substr($uriObj->getPart('dir'), -1) != '/') ? '/' : ''),$fsl_string)
           ,file_get_contents(dirname(__FILE__).'/includes/rewrite_block_first_strings')
        );

        #
        $rw_scheme_block = str_replace(
         array('%category_prefix%','%product_prefix%','%cms_prefix%')
        ,array($this->queries_prefixes['category'], $this->queries_prefixes['product'],$this->queries_prefixes['cmspage'])
	,file_get_contents(dirname(__FILE__).'/includes/rewrite_block_for_'.$this->settings['rewrite_scheme_name'])
        );

        #
        $rw_last = file_get_contents(dirname(__FILE__).'/includes/rewrite_block_last_strings');

        $rw_block = REWRITE_BLOCK_IDENT_BEGIN ."\n". $rw_first ."\n". $rw_scheme_block ."\n". implode("\n",$rw_strings) ."\n". $rw_last ."\n" .REWRITE_BLOCK_IDENT_END ."\n";

        return $rw_block;
    }

    function saveHTAcontent($hta_content, $layout_path)
    {
        $return = array();

        global $application;

        $row_layout = _parse_cz_layout_ini_file($layout_path,true);
	if (isset($row_layout['Site']['SitePath'])){
        	$dest_file_path = $row_layout['Site']['SitePath'].'.htaccess';
	}
	else {
		$dest_file_path = $application->appIni["PATH_ASC_ROOT"].'.htaccess';
	}

        if(file_exists($dest_file_path) and !is_writable($dest_file_path))
        {
            $return[] = 'ERR_CANT_WRITE_FILE';
            return $return;
        };

        if(!is_writable(dirname($dest_file_path)))
        {
            $return[] = 'ERR_CANT_WRITE_FILE';
            return $return;
        };

        #                     ,
        if(file_exists($dest_file_path))
        {
            $file = new CFile($dest_file_path);
            $lines = array_map("rtrim", $file->getLines());
            $out_content = '';
            $i=0;
            while($i < count($lines) and $lines[$i] != REWRITE_BLOCK_IDENT_BEGIN)
            {
                $out_content .= $lines[$i]."\n";
                $i++;
            };
            $out_content .= $hta_content;
            $i++;
            while($i < count($lines) and $lines[$i] != REWRITE_BLOCK_IDENT_END)
            {
                $i++;
            };
            $i++;
            while($i < count($lines))
            {
                $out_content .= $lines[$i]."\n";
                $i++;
            };
        }
        else
        {
            $out_content = $hta_content;
        };

        asc_file_put_contents($dest_file_path, $out_content);

        $layout_integrity = array(
            'product_list'  => $row_layout['ProductList']
           ,'product_info'  => $row_layout['ProductInfo']
           ,'cms_page'  => $row_layout['CMSPage']
	);

        $htaccess_md5 = md5($hta_content);

        $sefu_integrity = array(
            'cats_template' => $application->getAppIni('SEFU_CATEGORY_QUERY_STRING_SUFFIX')
           ,'prods_template' => $application->getAppIni('SEFU_PRODUCT_QUERY_STRING_SUFFIX')
           ,'cms_template' => $application->getAppIni('SEFU_CMSPAGE_QUERY_STRING_SUFFIX')
        );

        $sets_integrity = array(
            'cats_prefix'   => $this->settings['CATS_PREFIX']
           ,'prods_prefix'  => $this->settings['PRODS_PREFIX']
           ,'cms_prefix'  => $this->settings['CMS_PREFIX']
           ,'rewrite_scheme' => $this->settings['REWRITE_SCHEME']
        );

        $tables = $this->getTables();
        $integrity_table = $tables['mr_integrity']['columns'];

        $query = new DB_Replace('mr_integrity');
        $query->addReplaceValue($layout_path, $integrity_table['layout_path']);
        $query->addReplaceValue(md5(serialize($layout_integrity)), $integrity_table['layout_md5']);
        $query->addReplaceValue($dest_file_path, $integrity_table['htaccess_path']);
        $query->addReplaceValue($htaccess_md5, $integrity_table['htaccess_md5']);
        $query->addReplaceValue(md5(serialize($sefu_integrity)), $integrity_table['sefu_md5']);
        $query->addReplaceValue(md5(serialize($sets_integrity)), $integrity_table['sets_md5']);

        $application->db->PrepareSQL($query);
        $application->db->DB_Exec();

        //                    .      ,        ,                     .
        $this->enableMRforLayout($layout_path);

        return $return;
    }

    /**
     * 	                            URL                            URL
     *  mod_rewrite
     * @param string $url - URL
     * @return string -                 URL
     */
    function encodeURL($url)
    {
        global $application;
        if($application->row_layout_ini_array === null)
            return $url;

        $row_layout_ini_array = $application->row_layout_ini_array;

        loadCoreFile('URI.class.php');
        $uriObj = new URI($url);

        if($uriObj->getPart('scheme')=='http://')
        {
            $r_string = str_replace($row_layout_ini_array['Site']['SiteURL'],'',$url);
        }
        elseif($uriObj->getPart('scheme')=='https://')
        {
            $r_string = str_replace($row_layout_ini_array['Site']['SiteHTTPSURL'],'',$url);
        }
        else
        {
            return $url;
        };

        $rel_script_path = str_replace($uriObj->getPart('query'),'',$r_string);
        $query_string = str_replace('?','',$uriObj->getPart('query'));

        if(strstr($query_string,'asc_action=SetCurrentProduct')!==false)
        {
            #           URL             .
            preg_match("/prod_id=(\d+)/i",$query_string,$matches);
            $product_id = $matches[1];

            #        ProductInfo
            $founded_keys = array();
            foreach($row_layout_ini_array['ProductInfo'] as $layout_key => $layout_script)
            {
                if($layout_script == $rel_script_path)
                {
                    $founded_keys[] = $layout_key;
                };
            };

            $prodObj = &$application->getInstance('CProductInfo',$product_id);
            $seo_html_name = $prodObj->getProductTagValue('SEOPrefix');
            if($seo_html_name == '' or $seo_html_name == null)
            {
                $seo_html_name = 'info';    //: move to config
            };

            $encode_rule = array('type'=>'default','prod_id'=>$product_id,'cat_id'=>null);

            # 1.                          Products {x}
            foreach($founded_keys as $lkey)
            {
                if(preg_match("/^products/i",$lkey))
                {
                    $encode_rule['type'] = 'product';
                    break;
                };
            };

            # 2.                          Categories {x}
            if($encode_rule['type']=='default')
            {
                $choosen_cat = $prodObj->chooseCategoryID();
                reset($founded_keys);
                $parent_cat = null;
                foreach($founded_keys as $lkey)
                {
                    if(preg_match("/^categories\s*\{([0-9\,\+]+)\}/i",$lkey,$matches))
                    {
                        $row_cats_ids = array_map("trim",explode(",",$matches[1]));
                        if(in_array($choosen_cat,$row_cats_ids))
                        {
                            $encode_rule['type'] = 'category';
                            $encode_rule['cat_id'] = $choosen_cat;
                            break;
                        }
                        else
                        {
                            foreach($row_cats_ids as $cat_id_str)
                            {
                                if(strstr($cat_id_str,'+')!==false)
                                {
                                    $parent_cat = str_replace('+','',$cat_id_str);
                                    if($parent_cat == $choosen_cat)
                                    {
                                        $encode_rule['type'] = 'category';
                                        $encode_rule['cate_id'] = $cat_id_str;
                                        break;
                                    };
                                    $parent_cat = $cat_id_str;
                                };
                            };
                        };
                    };
                };

                if($encode_rule['type']=='default' and $parent_cat!==null)
                {
                    $encode_rule['type'] = 'category';
                    $encode_rule['cat_id'] = $parent_cat;
                };
            };

            #                                               URL

            $encoded_url = '';
            if($uriObj->getPart('scheme')=='https://')
            {
                $encoded_url .= $row_layout_ini_array['Site']['SiteHTTPSURL'];
            }
            else
            {
                $encoded_url .= $row_layout_ini_array['Site']['SiteURL'];
            };

            switch($encode_rule['type'])
            {
                case 'category':
                    $encoded_url .= str_replace(
                        array('%product_id%','%parent_cid%','%query_prod_prefix%','%seo_prod_prefix%')
                       ,array($encode_rule['prod_id'],'-'.$encode_rule['cat_id'],$this->queries_prefixes['product'],$seo_html_name)
                       ,$this->encode_scheme['product']
                    );
                    #                                  .
                    //$encoded_url .= $this->queries_prefixes['product'].'/'.$encode_rule['prod_id'].'/'.$encode_rule['cat_id'].'/'.$seo_html_name.'.html';
                    break;
                case 'product':
                case 'default':
                    $encoded_url .= str_replace(
                        array('%product_id%','%parent_cid%','%query_prod_prefix%','%seo_prod_prefix%')
                       ,array($encode_rule['prod_id'],'',$this->queries_prefixes['product'],$seo_html_name)
                       ,$this->encode_scheme['product']
                    );
                    #                                  .
                    //$encoded_url .= $this->queries_prefixes['product'].'/'.$encode_rule['prod_id'].'/'.$seo_html_name.'.html';
                    break;
            };

            return $encoded_url;
        };

        if(strstr($query_string,'asc_action=SetCurrCat')!==false
            or strstr($query_string,'asc_action=Paginator_SetPage&pgname=Catalog_ProdsList_')!==false)
        {
            #           URL
            if(preg_match("/category_id=(\d+)/i",$query_string,$matches))
            {
                $category_id = $matches[1];
                $page_number = 1;
            }
            else
            {
                preg_match("/pgname=Catalog_ProdsList_(\d+)&pgnum=(\d+)/i",$query_string,$matches);
                $category_id = $matches[1];
                $page_number = $matches[2];
            };

            $catObj = &$application->getInstance('CCategoryInfo',$category_id);
            $seo_html_name = $catObj->getCategoryTagValue('seo_url_prefix');
            if($seo_html_name===null or $seo_html_name=='')
            {
                $seo_html_name = 'info';
            };

            #        ProductList
            $founded_keys = array();
            foreach($row_layout_ini_array['ProductList'] as $layout_key => $layout_script)
            {
                if($layout_script == $rel_script_path)
                {
                    $founded_keys[] = $layout_key;
                };
            };

            $encode_rule = array('type'=>'default','cat_id'=>$category_id,'parent_cat_id'=>null);
            $category_parents = modApiFunc('Catalog','getCategoryFullPathAsCategoriesIDs',$category_id,false);

            #                          Categories {x}
            foreach($founded_keys as $lkey)
            {
                if(preg_match("/^categories\s*\{([0-9\,\+]+)\}/i",$lkey,$matches))
                {
                    $row_cats_ids = array_map("trim",explode(",",$matches[1]));
                    if(in_array($category_id,$row_cats_ids) or in_array($category_id.'+',$row_cats_ids))
                    {
                        $encode_rule['type'] = 'category';
                        break;
                    };

                    $parent_ids = array();
                    foreach($row_cats_ids as $parent_id)
                    {
                        if(strstr($parent_id,'+'))
                        {
                            $parent_ids[] = str_replace('+','',$parent_id);
                        };
                    };

                    #                            ID+ ID
                    if(!empty($parent_ids) and !empty($category_parents))
                    {
                        foreach($category_parents as $cpid)
                        {
                            if(in_array($cpid, $parent_ids))
                            {
                                $encode_rule['type'] = 'parent';
                                $encode_rule['parent_cat_id'] = $cpid.'+';
                                break;
                            };
                        };
                    };

                    #           ,
                    if($encode_rule['parent_cat_id']!==null)
                    {
                        break;
                    };
                };
            };

            #                                               URL

            $encoded_url = '';
            if($uriObj->getPart('scheme')=='https://')
            {
                $encoded_url .= $row_layout_ini_array['Site']['SiteHTTPSURL'];
            }
            else
            {
                $encoded_url .= $row_layout_ini_array['Site']['SiteURL'];
            };

            switch($encode_rule['type'])
            {
                case 'parent':
                    $encoded_url .= str_replace(
                        array('%category_id%','%page_number%','%parent_cid%','%query_cat_prefix%','%seo_cat_prefix%')
                       ,array($encode_rule['cat_id'],$page_number,'-'.$encode_rule['parent_cat_id'],$this->queries_prefixes['category'],$seo_html_name)
                       ,$this->encode_scheme['category']
                    );
                    #                                  .
                    //$encoded_url .= $this->queries_prefixes['category'].'/'.$encode_rule['cat_id'].'/'.$page_number.'/'.$encode_rule['parent_cat_id'].'/'.$seo_html_name.'.html';
                    break;
                case 'category':
                case 'default':
                    $encoded_url .= str_replace(
                        array('%category_id%','%page_number%','%parent_cid%','%query_cat_prefix%','%seo_cat_prefix%')
                       ,array($encode_rule['cat_id'],$page_number,'',$this->queries_prefixes['category'],$seo_html_name)
                       ,$this->encode_scheme['category']
                    );
                    #                                  .
                    //$encoded_url .= $this->queries_prefixes['category'].'/'.$encode_rule['cat_id'].'/'.$page_number.'/'.$seo_html_name.'.html';
                    break;
            };

            return $encoded_url;
        };
        if(strstr($query_string,'page_id=')!==false)
        {
            #           URL             .
            preg_match("/page_id=(\d+)/i",$query_string,$matches);
            $page_id = $matches[1];

            #        CMSPage
            $founded_keys = array();
            foreach($row_layout_ini_array['CMSPage'] as $layout_key => $layout_script)
            {
                if($layout_script == $rel_script_path)
                {
                    $founded_keys[] = $layout_key;
                };
            };

            $cmsObj = &$application->getInstance('CCMSPageInfo',$page_id);
            $seo_html_name = $cmsObj->getCMSPageTagValue('prefix',array());
	    if($seo_html_name == '' or $seo_html_name == null)
            {
                $seo_html_name = 'info';    //: move to config
            };

            $encode_rule = array('type'=>'default','page_id'=>$page_id);

            #                                               URL

            $encoded_url = '';
            if($uriObj->getPart('scheme')=='https://')
            {
                $encoded_url .= $row_layout_ini_array['Site']['SiteHTTPSURL'];
            }
            else
            {
                $encoded_url .= $row_layout_ini_array['Site']['SiteURL'];
            };

            switch($encode_rule['type'])
            {
                case 'default':
                    $encoded_url .= str_replace(
                        array('%page_id%','%query_cms_prefix%','%seo_cms_prefix%')
                       ,array($encode_rule['page_id'],$this->queries_prefixes['cmspage'],$seo_html_name)
                       ,$this->encode_scheme['cmspage']
                    );
                    #                                  .
                    //$encoded_url .= $this->queries_prefixes['product'].'/'.$encode_rule['prod_id'].'/'.$seo_html_name.'.html';
                    break;
            };

            return $encoded_url;
        };

        #                                            ,    URL                  .
        return $url;
    }

    function enableMRforLayout($layout_path)
    {
        global $application;
        $tables = $this->getTables();
        $int_table = $tables['mr_integrity']['columns'];

        $query = new DB_Update('mr_integrity');
        $query->addUpdateValue($int_table['mr_active'], 'Y');
        $query->WhereValue($int_table['layout_path'], DB_EQ, $layout_path);
        $result = $application->db->getDB_Result($query);
        modApiFunc('EventsManager','throwEvent','ModRewriteChanged');
        return;
    }

    function disableMRforLayout($layout_path)
    {
        global $application;
        $tables = $this->getTables();
        $int_table = $tables['mr_integrity']['columns'];

        $query = new DB_Update('mr_integrity');
        $query->addUpdateValue($int_table['mr_active'], 'N');
        $query->WhereValue($int_table['layout_path'], DB_EQ, $layout_path);
        $result = $application->db->getDB_Result($query);
        modApiFunc('EventsManager','throwEvent','ModRewriteChanged');
        return;
    }

    function isModRewriteAvailable()
    {
        return (isset($this->settings['ACTIVE']) and $this->settings['ACTIVE']=='Y');
    }

    function setCurrentLayout($cz_layout_path)
    {
        if(!$this->_isIntegrityCorrect($cz_layout_path,true))
        {
            $this->settings['ACTIVE'] = 'N';
            return;
        };

        $mr_info = $this->detectModRewrite();
        if($mr_info['mod_rewrite_loaded']=='N')
        {
            $this->settings['ACTIVE'] = 'N';
            return;
        };
    }

    function _isIntegrityCorrect($cz_layout_path,$auto_set_active=false)
    {
        $cz_layout_path = $this->_prepareAbsFilePath($cz_layout_path);
        $integrity_info = $this->_getIntegrityInfoForCZLayout($cz_layout_path);

        if($integrity_info === null)
        {
            return false;
        };

        global $application;

        $row_layout = _parse_cz_layout_ini_file($cz_layout_path,true);
        $layout_integrity = array(
            'product_list' => $row_layout['ProductList']
           ,'product_info' => $row_layout['ProductInfo']
           ,'cms_page' => $row_layout['CMSPage']
	);

        if($integrity_info['layout_md5'] != md5(serialize($layout_integrity)))
        {
            return false;
        };

        if(!file_exists($integrity_info['htaccess_path']))
        {
            return false;
        };

        $lines = file($integrity_info['htaccess_path']);
        $hta_content = '';
        $i = 0;
        while($i < count($lines) and $lines[$i] != REWRITE_BLOCK_IDENT_BEGIN."\n")
        {
            $i++;
        };
        while($i < count($lines) and $lines[$i] != REWRITE_BLOCK_IDENT_END."\n")
        {
            $hta_content .= $lines[$i];
            $i++;
        };
        if($i < count($lines))
            $hta_content .= $lines[$i];

        if($integrity_info['htaccess_md5'] != md5($hta_content))
        {
            return false;
        };

        $sefu_integrity = array(
            'cats_template' => $application->getAppIni('SEFU_CATEGORY_QUERY_STRING_SUFFIX')
           ,'prods_template' => $application->getAppIni('SEFU_PRODUCT_QUERY_STRING_SUFFIX')
           ,'cms_template' => $application->getAppIni('SEFU_CMSPAGE_QUERY_STRING_SUFFIX')
	);

        if($integrity_info['sefu_md5'] != md5(serialize($sefu_integrity)))
        {
            return false;
        };

        $sets_integrity = array(
            'cats_prefix'   => $this->settings['CATS_PREFIX']
           ,'prods_prefix'  => $this->settings['PRODS_PREFIX']
           ,'cms_prefix'  => $this->settings['CMS_PREFIX']
	   ,'rewrite_scheme' => $this->settings['REWRITE_SCHEME']
        );

        if($integrity_info['sets_md5'] != md5(serialize($sets_integrity)))
        {
            return false;
        };

        if($integrity_info['mr_active']=='Y' and $auto_set_active)
        {
            $this->settings['ACTIVE'] = 'Y';
        };
        return true;
    }

    function _getIntegrityInfoForCZLayout($cz_layout_path)
    {

        $params = array("cz_layout_path" => $cz_layout_path);
        $res = execQuery("SELECT_MR_INTEGRITY_INFO", $params);

        if(count($res)!=1)
        {
            return null;
        }
        else
        {
            return array_shift($res);
        };
    }

    function _loadSettings()
    {
        $sets = $this->getSettings();
        $mr_info = $this->detectModRewrite();
        if($mr_info['mod_rewrite_loaded']=='N')
        {
            $sets['ACTIVE'] = 'N';
        };
        $this->settings = $sets;
    }

    function _loadRewriteScheme()
    {


        $params = array("scheme_id" => $this->settings['REWRITE_SCHEME']);
        $res = execQuery("SELECT_MR_SCHEMES", $params);

        if(count($res) != 1)
        {
            $this->rewrite_scheme = null;
            $this->settings['ACTIVE'] = 'N';
            return;
        };

        $this->_setRewriteScheme($res[0]);
        return;
    }

    function _setRewriteScheme($scheme_info)
    {
        $this->rewrite_scheme = array(
            'category' => $scheme_info['cat_rule_tpl']
           ,'product'  => $scheme_info['prod_rule_tpl']
           ,'cmspage'  => $scheme_info['cms_rule_tpl']
        );

        $this->encode_scheme = array(
            'category' => preg_replace("/[\(\)\^\\\\]/","",$this->rewrite_scheme['category'])
           ,'product'  => preg_replace("/[\(\)\^\\\\]/","",$this->rewrite_scheme['product'])
           ,'cmspage'  => preg_replace("/[\(\)\^\\\\]/","",$this->rewrite_scheme['cmspage'])
        );

        $this->settings['rewrite_scheme_name'] = $scheme_info['scheme_name'];
        return;
    }

    function _forceSetActive($flag)
    {
        if($flag===true)
        {
            $this->settings['ACTIVE'] = 'Y';
        }
        else
        {
            $this->settings['ACTIVE'] = 'N';
        };
    }

    function _prepareQueriesSuffixesAndPrefixes()
    {
        global $application;

        # suffixes
        $this->queries_suffixes = array('category'=>'','product'=>'','cmspage'=>'');

        $cat_tpl = $application->getAppIni('SEFU_CATEGORY_QUERY_STRING_SUFFIX');
        $prod_tpl = $application->getAppIni('SEFU_PRODUCT_QUERY_STRING_SUFFIX');
        $cms_tpl = $application->getAppIni('SEFU_CMSPAGE_QUERY_STRING_SUFFIX');

        $this->queries_suffixes['category'] = str_replace(
            array('{%category_id%}','{%page_number%}')
           ,array('$1','$2')
           ,$cat_tpl
        );
        $this->queries_suffixes['product'] = str_replace(
            array('{%product_id%}')
           ,array('$1')
           ,$prod_tpl
        );
        $this->queries_suffixes['cmspage'] = str_replace(
            array('{%page_id%}')
           ,array('$1')
           ,$cms_tpl
        );

        # prefixes
        $this->queries_prefixes = array(
            'category' => $this->settings['CATS_PREFIX']
           ,'product' => $this->settings['PRODS_PREFIX']
           ,'cmspage' => $this->settings['CMS_PREFIX']
        );
    }

    function _prepareAbsFilePath($file_path)
    {
        return str_replace("\\","/",realpath($file_path));
    }

    var $queries_suffixes;
    var $queries_prefixes;
    var $settings;
    var $rewrite_scheme;
    var $encode_scheme;
};

?>