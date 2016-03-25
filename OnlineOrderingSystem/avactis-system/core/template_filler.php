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
loadCoreFile('tmpl_includer.php');

/**
 * Standard Template Engine used in the system.
 *
 * @access private
 * @author Alexey Kolesnikov
 * @package Core
 */
class TemplateFiller extends TmplIncluder
{
//------------------------------------------------
//               PUBLIC DECLARATION
//------------------------------------------------

    /**#@+
     * @access public
     */

    var $www_address = false;
    var $templates_url = false;
    var $site_url = false;
	var $TemplateDirPrefix = "";

    /**
     * @ a description for the function TemplateFiller->.
     */
    function TemplateFiller($path_templates_dir = null)
    {
        parent::TmplIncluder();
    	global $application;
		if ($path_templates_dir != null)
        {
        	$this->TemplateDirPrefix = $path_templates_dir;
        }
        $this->MessageResources = &$application->getInstance('MessageResources');
        $this->prepared_template_cache = CCacheFactory::getCache('temporary', __CLASS__);

        if (!$this->www_address)
        {
            $this->www_address = $application->getAppIni('HTTP_URL');
            if ($application->getCurrentProtocol() == "https" && $application->getAppIni('HTTPS_URL'))
            {
                $this->www_address = $application->getAppIni('HTTPS_URL');
            }
        }
        if (!$this->templates_url)
        {
//            $this->templates_url = $application->getAppIni('URL_THEME');
            $this->templates_url = $application->getAppIni('URL_TEMPLATES');
            if ($application->getCurrentProtocol() == "https" && $application->getAppIni('HTTPS_URL_TEMPLATES'))
            {
                $this->templates_url = $application->getAppIni('HTTPS_URL_TEMPLATES');
            }
        }
        if (!$this->site_url)
        {
            $this->site_url = $application->getAppIni('SITE_URL');
            if ($application->getCurrentProtocol() == "https" && $application->getAppIni('SITE_HTTPS_URL'))
            {
                $this->site_url = $application->getAppIni('SITE_HTTPS_URL');
            }
        }
    }

    /**
     * Set the template path to override the above.
     *
     * @param string $folder_name Template file path: relative template folder path.
     */
    function setTemplatePath($folder_name)
    {
       $this->TemplateDirPrefix =  modApiFunc('application', 'getAppIni','PATH_ASC_ROOT').$folder_name ;
    }

    /**
     * Sets a current template.
     */
    function setTemplate($template)
    {
    	$this->template = $template;
    }

    /**
     * Checks if the template with the specified name exists.
     */
    function isTemplateReadable($file)
    {
        global $application;
        $file = $application->getBlockTemplateFile($this->template, $file);
        if ($file === null)
        {
        	return false;
        }
        return is_readable($file);
    }

    /**
     * A main function for filling the template.
     *
     * @param string $file the template filename.
     * @param integer $product_type the product type identifier
     * @return string the filled template
     */
    function fill($file, $product_type = null,$current_template_path=null)
    {
        global $application;
        static $ADD_TEMPLATE_PATHES = null;
        if ($ADD_TEMPLATE_PATHES === null)
        {
        	$ADD_TEMPLATE_PATHES = modApiFunc('Settings','getParamValue', 'DEBUG_STORE_BLOCK', 'ADD_TEMPLATE_PATHES');
        }

		if($current_template_path === null && isset($this->TemplateDirPrefix))
		{
			$current_template_path = $this->TemplateDirPrefix;
		}
        $tmpl_file = $application->getBlockTemplateFile($this->template, $file, $product_type,$current_template_path);
        if ($tmpl_file === null)
        {
            $err_mes = new ActionMessage(array("TMPL_001", $this->template . $file));
            return $this->MessageResources->getMessage($err_mes);
        }

        //                                                       ,                                            .
        //           storefront,                                                                   URL- .
        $marker = md5($this->www_address.$this->site_url.$this->templates_url);

        //                                                 .
        //       doesCacheFileExist($tpl_path)
        //                              md5                   md5                    $tpl_path
        $code_to_include = $this->getCachedCode($tmpl_file, $marker);
        if ($code_to_include === null)
        {
            $code_to_include = '?>'.$this->prepareTemplateToFill($tmpl_file);

            $this->saveInCache($tmpl_file, $code_to_include, $marker);
        }

        $text = $this->includeTmplCode($code_to_include);

        if ($ADD_TEMPLATE_PATHES === 'Yes')
        {
            $current_template_dir = $application->appIni['PATH_THEMES'];
            $tmpl_file_relative = str_replace($current_template_dir, '', $tmpl_file);
            $tmpl_begin = "\n<!-- BEGIN  $tmpl_file_relative -->\n";
            $tmpl_end = "\n<!-- END  $tmpl_file_relative -->\n";
        }
        else
        {
            $tmpl_begin = '';
            $tmpl_end = '';
        }

        if($application->fb_request) $text = $this->addFBreferrer($text);
        return $tmpl_begin.$text.$tmpl_end;
    }

    function addFBreferrer($text)
    {
        // links
        preg_match_all('/href=[\'\"]{1}(.+?)[\'\"]{1}/is', $text, $matches, PREG_OFFSET_CAPTURE);

        if(is_array($matches))
            foreach($matches as $m0)
                if(is_array($m0))
                    foreach($m0 as $m1)
                        if(is_array($m1) && count($m1)==2 && !preg_match('/(asc_fb_req|href)/',$m1[0]))
                        {
                            $s = preg_match('/\?/', $m1[0]) ? '&' : '?';
                            $text = str_replace($m1[0], $m1[0].$s.'asc_fb_req=true', $text);
                        }


        // forms
        $text = preg_replace('/<\/form>/is', '<input type = "hidden" name="asc_fb_req" value="true"/></form>', $text);

        return $text;
    }
    /**#@-*/

//------------------------------------------------
//              PRIVATE DECLARATION
//------------------------------------------------

    /**#@+
     * @access private
     */

    function prepareTemplateToFill($tmpl_file)
    {
        $cache_result = $this->prepared_template_cache->read($tmpl_file);
        if ($cache_result !== null)
        {
            return $cache_result;
        }
        else
        {
            //Open file, read contents, replace tags.
            $this->CurrentTemplateFilename = $tmpl_file;
            $tpl_file = new CFile($this->CurrentTemplateFilename);
            $text = $tpl_file->getContent();
            if ($text === FALSE)
            {
                CTrace::backtrace();
                _fatal("TMPL_FILLER_ERROR_CANNOT_READ_FILE", $this->CurrentTemplateFilename);
            }

            $text = $this->removeTemplateWrapper($text);

            // find and modify all the image references.
            // for customer zone only
            $zone = modApiFunc('Users', 'getZone');
            if ($zone == 'CustomerZone')
            {
                $text = $this->replaceImages($text);
            }
            $this->prepared_template_cache->write($tmpl_file, $text);
            return $text;
        }
    }

    /**
     * Extracts from the template text only the part, which must be used for
     * mapping.
     *
     * @param string $text raw contents of the template file.
     * @return string template contents which contain between the seperators.
     */
    function removeTemplateWrapper($text)
    {
        static $begin_tpl_tag_expr;
        static $end_tpl_tag_expr;
//        $begin_tpl_tag_expr = '/<hr>.*<!--.*BEGIN.*TPL.*\(DO.*NOT.*REMOVE!\).*-->/im';
        $begin_tpl_tag_expr = '/<\s*!\s*-\s*-\s*B\s*E\s*G\s*I\s*N\s*T\s*P\s*L\s*\(\s*D\s*O\s*N\s*O\s*T\s*R\s*E\s*M\s*O\s*V\s*E\s*!\s*\)\s*-\s*-\s*>/im';
//        $end_tpl_tag_expr = '/<hr>.*<!--.*END.*TPL.*\(DO NOT REMOVE!\).*-->/im';
        $end_tpl_tag_expr = '/<\s*!\s*-\s*-\s*E\s*N\s*D\s*T\s*P\s*L\s*\(\s*D\s*O\s*N\s*O\s*T\s*R\s*E\s*M\s*O\s*V\s*E\s*!\s*\)\s*-\s*-\s*>/im';

        if (preg_match($begin_tpl_tag_expr, $text, $begin, PREG_OFFSET_CAPTURE) != 1)
        {
            _fatal(array( "CODE" => "CORE_046"), $this->CurrentTemplateFilename);
        }

        if (preg_match($end_tpl_tag_expr, $text, $end, PREG_OFFSET_CAPTURE) != 1)
        {
            _fatal(array( "CODE" => "CORE_047"), $this->CurrentTemplateFilename);
        }
        $begin_pos = $begin[0][1] + _byte_strlen($begin[0][0]);
        $end_pos = $end[0][1];

        if($begin_pos > $end_pos)
        {
            _fatal(array( "CODE" => "CORE_048"), $this->CurrentTemplateFilename);
        }

        return _byte_substr($text, $begin_pos, $end_pos - $begin_pos);
    }

    /**
     * Fills the template with values.
     * The function searches tags within the template, defining them by the
     * standard php constructions: <code>&lt;?php function_code(); ?&gt;</code>.
     * Each function into the template will be called and will
     * be replaced  with the returned value.
     *
     * @param string $text the template with tags
     * @return string the template filled with tags.
     * @return string                                    .
     */
    function replaceTags($text)
    {
        return  preg_replace_callback("/<\?php(.*?)\?>/",array("TemplateFiller", "evalTag"),$text);
    }

    /**
     * Calls the function by Info-tags names.
     *
     * @param array $m the value array returned by the preg_replace_callback
     * function.
     * @return string the string created by Info-tag.
     */
    function evalTag($m)
    {
    	return eval("return $m[1]");
    }

    /**
     * Finds and replaces all references to images in the template, mapping
     * relative references to absolute ones.
     * All references to images in the template are relative references to
     * the template itself.
     */
    function replaceImages($text)
    {
        if(_ml_stristr($text, "src=") === FALSE &&
           _ml_stristr($text, "background") === FALSE)
        {
            return $text;
        }
        else
        {
            $image_subpattern = '([a-zA-Z0-9\\\\\/\-_\.:]+)';
            $img_pattern = "/(\s+src=['\"]{0,1})".$image_subpattern."(['\"]{0,1}\s*)/msi";
            $table_pattern = "/(\s+background=['\"]{0,1})".$image_subpattern."(['\"]{0,1}\s*)/msi";
            $css_image_pattern = "/(background-image:\s+url\s*\(\s*['\"]{0,1})".$image_subpattern."(['\"]{0,1}\s*)/msi";
            $css_image_pattern1 = "/(background:.*?\s+url\s*\(\s*['\"]{0,1})".$image_subpattern."(['\"]{0,1}\s*)/msi";

            $text = preg_replace_callback($img_pattern, array($this, "evalImage"), $text);
            $text = preg_replace_callback($table_pattern, array($this, "evalImage"), $text);
            $text = preg_replace_callback($css_image_pattern, array($this, "evalImage"), $text);
            $text = preg_replace_callback($css_image_pattern1, array($this, "evalImage"), $text);

            $lazy_image_pattern = "#<img([\w\W]+?)lazy([\w\W]+?)/>#";
            $text = preg_replace_callback($lazy_image_pattern, function($image){ return str_replace('src=','data-src=',$image[0]); },$text);
            return $text;
        }
    }

    function evalImage($image)
    {
        global $application;
        $avactis_url = $this->templates_url . $this->template['template']['directory'];

    	$replace = "";
        if (_ml_strpos($image[2],'://') != 0)
        {
        	$replace = $image[0];
        }
        elseif (_byte_substr($image[2], 0, 1) == '/')
        {
            $replace = $image[1].$this->site_url . _byte_substr($image[2], 1).$image[3];
        }
        else
        {
    	    $replace = $image[1].$avactis_url.'/'.$image[2].$image[3];
        }

    	return $replace;
    }

    /**
     * Template file currenly being filled.
     */
    var $CurrentTemplateFilename = "";

    var $template;

    var $prepared_template_cache = null;

    var $MessageResources;
    /**#@-*/
}
?>