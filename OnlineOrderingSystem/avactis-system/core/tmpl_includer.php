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

class TmplIncluder
{
    var $cache = null;

    function TmplIncluder()
    {
        //                 ,                 singleton.
        //                                     ,                                       .
        //                           ,                                                            .
        global $application;
        $this->cache = $application->getTplCache();
    }

    function saveInCache($tpl_path, $tpl_content, $marker='')
    {
        global $application;

        $file_key = $tpl_path.$marker;
        $mtime_key = $tpl_path.$marker.'.mtime';

        $this->cache->write($file_key, $tpl_content);
        $this->cache->write($mtime_key, filemtime($tpl_path));
    }

    function getCachedCode($tpl_path, $marker='')
    {
        //                                                                                  .
        static $mtime = array();

        $file_key = $tpl_path.$marker;
        $mtime_key = $tpl_path.$marker.'.mtime';

        $code_to_include = $this->cache->read($file_key);

        if ($code_to_include !== null) {
            if (! isset($mtime[$tpl_path])) {
                $mtime[$tpl_path] = filemtime($tpl_path);
            }

            if ($this->cache->read($mtime_key) == $mtime[$tpl_path]) {
                return $code_to_include;
            }
        }
        return null;
    }

    function includeTmplCode($code)
    {
        ob_start();
        eval($code);
        $out = ob_get_clean();
        return $out;
    }

    function __getKeys($tpl_path, $marker)
    {
        return array(md5($tpl_path.$marker), md5($tpl_path.$marker).'_content_md5');
    }
}


?>